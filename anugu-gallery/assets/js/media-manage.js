/**
 * Handles:
 * - Selection and deselection of media in an Anugu Gallery
 * - Toggling edit / delete button states when media is selected / deselected,
 * - Toggling the media list / grid view
 * - Storing the user's preferences for the list / grid view
 */

 // Setup some vars
var anugu_gallery_output = '#anugu-gallery-output',
    anugu_gallery_shift_key_pressed = false,
    anugu_gallery_last_selected_image = false;

jQuery( document ).ready( function( $ ) {

    // Toggle List / Grid View
    $( document ).on( 'click', 'nav.anugu-tab-options a', function( e ) {

        e.preventDefault();

        // Get the view the user has chosen
        var anugu_tab_nav          = $( this ).closest( '.anugu-tab-options' ),
            anugu_tab_view         = $( this ).data( 'view' ),
            anugu_tab_view_style   = $( this ).data( 'view-style' );

        // If this view style is already displayed, don't do anything
        if ( $( anugu_tab_view ).hasClass( anugu_tab_view_style ) ) {
            return;
        }

        // Update the view class
        $( anugu_tab_view ).removeClass( 'list' ).removeClass( 'grid' ).addClass( anugu_tab_view_style );

        // Mark the current view icon as selected
        $( 'a', anugu_tab_nav ).removeClass( 'selected' );
        $( this ).addClass( 'selected' );

        // Send an AJAX request to store this user's preference for the view
        // This means when they add or edit any other Gallery, the image view will default to this setting
        $.ajax( {
            url:      anugu_gallery_metabox.ajax,
            type:     'post',
            dataType: 'json',
            data: {
                action:  'anugu_gallery_set_user_setting',
                name:    'anugu_gallery_image_view',
                value:   anugu_tab_view_style,
                nonce:   anugu_gallery_metabox.set_user_setting_nonce
            },
            success: function( response ) {
            },
            error: function( xhr, textStatus, e ) {
                // Inject the error message into the tab settings area
                $( anugu_gallery_output ).before( '<div class="error"><p>' + textStatus.responseText + '</p></div>' );
            }
        } );

    } );

    // Toggle Select All / Deselect All
    $( document ).on( 'change', 'nav.anugu-tab-options input', function( e ) {

        if ( $( this ).prop( 'checked' ) ) {
            $( 'li', $( anugu_gallery_output ) ).addClass( 'selected' );
            $( 'nav.anugu-select-options' ).fadeIn();
        } else {
            $( 'li', $( anugu_gallery_output ) ).removeClass( 'selected' );
            $( 'nav.anugu-select-options' ).fadeOut();
        }

    } );
	
    // Enable sortable functionality on images
	anugu_gallery_sortable( $ );

    // When the Gallery Type is changed, reinitialise the sortable
    $( document ).on( 'anuguGalleryType', function() {

        if ( $( anugu_gallery_output ).length > 0 ) {
            // Re-enable sortable functionality on images, now we're viewing the default gallery type
            anugu_gallery_sortable( $ );
        }
        
    } );

    // Select / deselect images
    $( document ).on( 'click', 'ul#anugu-gallery-output li.anugu-gallery-image > img, li.anugu-gallery-image > div, li.anugu-gallery-image > a.check', function( e ) {

        // Prevent default action
        e.preventDefault();

        // Get the selected gallery item
        var gallery_item = $( this ).parent();

        if ( $( gallery_item ).hasClass( 'selected' ) ) {
            $( gallery_item ).removeClass( 'selected' );
            anugu_gallery_last_selected_image = false;
        } else {
            
            // If the shift key is being held down, and there's another image selected, select every image between this clicked image
            // and the other selected image
            if ( anugu_gallery_shift_key_pressed && anugu_gallery_last_selected_image !== false ) {
                // Get index of the selected image and the last image
                var start_index = $( 'ul#anugu-gallery-output li' ).index( $( anugu_gallery_last_selected_image ) ),
                    end_index = $( 'ul#anugu-gallery-output li' ).index( $( gallery_item ) ),
                    i = 0;

                // Select images within the range
                if ( start_index < end_index ) {
                    for ( i = start_index; i <= end_index; i++ ) {
                        $( 'ul#anugu-gallery-output li:eq( ' + i + ')' ).addClass( 'selected' );
                    }
                } else {
                    for ( i = end_index; i <= start_index; i++ ) {
                        $( 'ul#anugu-gallery-output li:eq( ' + i + ')' ).addClass( 'selected' );
                    }
                }
            }

            // Select the clicked image
            $( gallery_item ).addClass( 'selected' );
            anugu_gallery_last_selected_image = $( gallery_item );

        }
        
        // Show/hide buttons depending on whether
        // any galleries have been selected
        if ( $( 'ul#anugu-gallery-output > li.selected' ).length > 0 ) {
            $( 'nav.anugu-select-options' ).fadeIn();
        } else {
            $( 'nav.anugu-select-options' ).fadeOut();
        }
    } );

    // Determine whether the shift key is pressed or not
    $( document ).on( 'keyup keydown', function( e ) {
        anugu_gallery_shift_key_pressed = e.shiftKey;
    } );

} );

/**
 * Enables sortable functionality on a grid of Anugu Gallery Images
 *
 * @since 1.5.0
 */
function anugu_gallery_sortable( $ ) {

    // Add sortable support to Anugu Gallery Media items
    $( anugu_gallery_output ).sortable( {
        containment: anugu_gallery_output,
        items: 'li',
        cursor: 'move',
        forcePlaceholderSize: true,
        placeholder: 'dropzone',
        helper: function( e, item ) {

            // Basically, if you grab an unhighlighted item to drag, it will deselect (unhighlight) everything else
            if ( ! item.hasClass( 'selected' ) ) {
                item.addClass( 'selected' ).siblings().removeClass( 'selected' );
            }
            
            // Clone the selected items into an array
            var elements = item.parent().children( '.selected' ).clone();
            
            // Add a property to `item` called 'multidrag` that contains the 
            // selected items, then remove the selected items from the source list
            item.data( 'multidrag', elements ).siblings( '.selected' ).remove();
            
            // Now the selected items exist in memory, attached to the `item`,
            // so we can access them later when we get to the `stop()` callback
            
            // Create the helper
            var helper = $( '<li/>' );
            return helper.append( elements );

        },
        stop: function( e, ui ) {
            // Remove the helper so we just display the sorted items
            var elements = ui.item.data( 'multidrag' );
            ui.item.after(elements).remove();

            // Remove the selected class from everything
            $( 'li.selected', $( anugu_gallery_output ) ).removeClass( 'selected' );
            
            // Send AJAX request to store the new sort order
            $.ajax( {
                url:      anugu_gallery_metabox.ajax,
                type:     'post',
                async:    true,
                cache:    false,
                dataType: 'json',
                data: {
                    action:  'anugu_gallery_sort_images',
                    order:   $( anugu_gallery_output ).sortable( 'toArray' ).toString(),
                    post_id: anugu_gallery_metabox.id,
                    nonce:   anugu_gallery_metabox.sort
                },
                success: function( response ) {
                    // Repopulate the Anugu Gallery Backbone Image Collection
                    AnuguGalleryImagesUpdate( false );
                    return;
                },
                error: function( xhr, textStatus, e ) {
                    // Inject the error message into the tab settings area
                    $( anugu_gallery_output ).before( '<div class="error"><p>' + textStatus.responseText + '</p></div>' );
                }
            } );
        }
    } );

}