/**
 * Hooks into the global Plupload instance ('uploader'), which is set when includes/admin/metaboxes.php calls media_form()
 * We hook into this global instance and apply our own changes during and after the upload.
 *
 * @since 1.3.1.3 
 */
(function( $ ) {
    $(function() {

        if ( typeof uploader !== 'undefined' ) {

            // Change "Select Files" button in the pluploader to "Select Files from Your Computer"
            $( 'input#plupload-browse-button' ).val( anugu_gallery_metabox.uploader_files_computer );

            // Set a custom progress bar
            var anugu_bar      = $( '#anugu-gallery .anugu-progress-bar' ),
                anugu_progress = $( '#anugu-gallery .anugu-progress-bar div.anugu-progress-bar-inner' ),
                anugu_status   = $( '#anugu-gallery .anugu-progress-bar div.anugu-progress-bar-status' ),
                anugu_output   = $( '#anugu-gallery-output' ),
                anugu_error    = $( '#anugu-gallery-upload-error' ),
                anugu_file_count = 0;

            // Uploader has initialized
            uploader.bind( 'Init', function( up ) {

                // Fade in the uploader, as it's hidden with CSS so the user doesn't see elements reposition on screen and look messy.
                $( '#drag-drop-area' ).fadeIn();
                $( 'a.anugu-media-library.button' ).fadeIn();

            } );

            // Files Added for Uploading
            uploader.bind( 'FilesAdded', function ( up, files ) {

                // Hide any existing errors
                $( anugu_error ).html( '' );

                // Get the number of files to be uploaded
                anugu_file_count = files.length;

                // Set the status text, to tell the user what's happening
                $( '.uploading .current', $( anugu_status ) ).text( '1' );
                $( '.uploading .total', $( anugu_status ) ).text( anugu_file_count );
                $( '.uploading', $( anugu_status ) ).show();
                $( '.done', $( anugu_status ) ).hide();

                // Fade in the upload progress bar
                $( anugu_bar ).fadeIn( "fast", function() {
                    $( 'p.max-upload-size' ).css('padding-top', '10px');
                });

                

            } );

            // File Uploading - show progress bar
            uploader.bind( 'UploadProgress', function( up, file ) {

                // Update the status text
                $( '.uploading .current', $( anugu_status ) ).text( ( anugu_file_count - up.total.queued ) + 1 );

                // Update the progress bar
                $( anugu_progress ).css({
                    'width': up.total.percent + '%'
                });

            });

            // File Uploaded - AJAX call to process image and add to screen.
            uploader.bind( 'FileUploaded', function( up, file, info ) {

                // AJAX call to Anugu to store the newly uploaded image in the meta against this Gallery
                $.post(
                    anugu_gallery_metabox.ajax,
                    {
                        action:  'anugu_gallery_load_image',
                        nonce:   anugu_gallery_metabox.load_image,
                        id:      info.response,
                        post_id: anugu_gallery_metabox.id
                    },
                    function(res){
                        // Prepend or append the new image to the existing grid of images,
                        // depending on the media_position setting
                        switch ( anugu_gallery_metabox.media_position ) {
                            case 'before':
                                $(anugu_output).prepend(res);
                                break;
                            case 'after':
                            default:
                                $(anugu_output).append(res);
                                break;
                        }

                        // Repopulate the Anugu Gallery Image Collection
                        AnuguGalleryImagesUpdate( false );

                    },
                    'json'
                );
            });

            // Files Uploaded
            uploader.bind( 'UploadComplete', function() {

                // Update status
                $( '.uploading', $( anugu_status ) ).hide();
                $( '.done', $( anugu_status ) ).show();

                // Hide Progress Bar
                setTimeout( function() {
                    $( anugu_bar ).fadeOut( "fast", function() {
                        $( 'p.max-upload-size' ).css('padding-top', '0');
                    });
                }, 1000 );

            });

            // File Upload Error
            uploader.bind('Error', function(up, err) {

                // Show message
                $('#anugu-gallery-upload-error').html( '<div class="error fade"><p>' + err.file.name + ': ' + err.message + '</p></div>' );
                up.refresh();

            });

        }

    });
})( jQuery );