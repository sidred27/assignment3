jQuery( document ).ready( function( $ ) {

	/**
    * Delete Multiple Images
    */
    $( document ).on( 'click', 'a.anugu-gallery-images-delete', function( e ) {

        e.preventDefault();

        // Bail out if the user does not actually want to remove the image.
        var confirm_delete = confirm(anugu_gallery_metabox.remove_multiple);
        if ( ! confirm_delete ) {
            return false;
        }

        // Build array of image attachment IDs
        var attach_ids = [];
        $( 'ul#anugu-gallery-output > li.selected' ).each( function() {
            attach_ids.push( $( this ).attr( 'id' ) );
        } );

        // Send an AJAX request to delete the selected items from the Gallery
        var attach_id = $( this ).parent().attr( 'id' );
        $.ajax( {
            url:      anugu_gallery_metabox.ajax,
            type:     'post',
            dataType: 'json',
            data: {
                action:        'anugu_gallery_remove_images',
                attachment_ids:attach_ids,
                post_id:       anugu_gallery_metabox.id,
                nonce:         anugu_gallery_metabox.remove_nonce
            },
            success: function( response ) {
                // Remove each image
                $( 'ul#anugu-gallery-output > li.selected' ).remove();

                // Hide Select Options
                $( 'nav.anugu-select-options' ).fadeOut();

                // Refresh the modal view to ensure no items are still checked if they have been removed.
                $( '.anugu-gallery-load-library' ).attr( 'data-anugu-gallery-offset', 0 ).addClass( 'has-search' ).trigger( 'click' );

                // Repopulate the Anugu Gallery Image Collection
                AnuguGalleryImagesUpdate( false );
            },
            error: function( xhr, textStatus, e ) {
                // Inject the error message into the tab settings area
                $( anugu_gallery_output ).before( '<div class="error"><p>' + textStatus.responseText + '</p></div>' );
            }
        } );

    } );

    /**
    * Delete Single Image
    */
    $( document ).on( 'click', '#anugu-gallery-main .anugu-gallery-remove-image', function( e ) {
        
        e.preventDefault();

        // Bail out if the user does not actually want to remove the image.
        var confirm_delete = confirm( anugu_gallery_metabox.remove );
        if ( ! confirm_delete ) {
            return;
        }

        // Send an AJAX request to delete the selected items from the Gallery
        var attach_id = $( this ).parent().attr( 'id' );
        $.ajax( {
            url:      anugu_gallery_metabox.ajax,
            type:     'post',
            dataType: 'json',
            data: {
                action:        'anugu_gallery_remove_image',
                attachment_id: attach_id,
                post_id:       anugu_gallery_metabox.id,
                nonce:         anugu_gallery_metabox.remove_nonce
            },
            success: function( response ) {
                $( '#' + attach_id ).fadeOut( 'normal', function() {
                    $( this ).remove();

                    // Refresh the modal view to ensure no items are still checked if they have been removed.
                    $( '.anugu-gallery-load-library' ).attr( 'data-anugu-gallery-offset', 0 ).addClass( 'has-search' ).trigger( 'click' );

                    // Repopulate the Anugu Gallery Image Collection
                    AnuguGalleryImagesUpdate( false );
                } );
            },
            error: function( xhr, textStatus, e ) {
                // Inject the error message into the tab settings area
                $( anugu_gallery_output ).before( '<div class="error"><p>' + textStatus.responseText + '</p></div>' );
            }
        } );
    } );

} );