/**
 * Handles tabbed interfaces within Anugu:
 * - Settings Screen
 * - Add/Edit Screen: Native/External
 * - Add/Edit Screen: Configuration Tabs
 */

;( function( $ ) {
    $( function() {

        // Define some general vars
        var anugu_tabs_nav         = '.anugu-tabs-nav',  // Container of tab navigation items (typically an unordered list)
            anugu_tabs_hash        = window.location.hash,
            anugu_tabs_current_tab = window.location.hash.replace( '!', '' );

        // If the URL contains a hash beginning with anugu-tab, mark that tab as open
        // and display that tab's panel.
        if ( anugu_tabs_hash && anugu_tabs_hash.indexOf( 'anugu-tab-' ) >= 0 ) {
            // Find the tab panel that the tab corresponds to
            var anugu_tabs_section = $( anugu_tabs_current_tab ).parent(),
                anugu_tab_nav      = $( anugu_tabs_section ).data( 'navigation' );

            // Remove the active class from everything in this tab navigation and section
            $( anugu_tab_nav ).find( '.anugu-active' ).removeClass( 'anugu-active' );
            $( anugu_tabs_section ).find( '.anugu-active' ).removeClass( 'anugu-active' );

            // Add the active class to the chosen tab and section
            $( anugu_tab_nav ).find( 'a[href="' + anugu_tabs_current_tab + '"]').addClass( 'anugu-active' );
            $( anugu_tabs_current_tab ).addClass( 'anugu-active' );

            // Update the form action to contain the selected tab as a hash in the URL
            // This means when the user saves their Gallery, they'll see the last selected
            // tab 'open' on reload
            var anugu_post_action = $( '#post' ).attr( 'action' );
            if ( anugu_post_action ) {
                // Remove any existing hash from the post action
                anugu_post_action = anugu_post_action.split( '#' )[0];

                // Append the selected tab as a hash to the post action
                $( '#post' ).attr( 'action', anugu_post_action + window.location.hash );
            } 
        }

        // Change tabs on click.
        // Tabs should be clickable elements, such as an anchor or label.
        $( anugu_tabs_nav ).on( 'click', '.nav-tab, a', function( e ) {

            // Prevent the default action
            e.preventDefault();

            // Destroy all instances of Anugu Video iframes
            $( 'div.anugu-video-help' ).remove();

            // Get the clicked element and the nav tabs
            var anugu_tabs                 = $( this ).closest( anugu_tabs_nav ),
                anugu_tabs_section         = $( anugu_tabs ).data( 'container' ),
                anugu_tabs_update_hashbang = $( anugu_tabs ).data( 'update-hashbang' ),
                anugu_tab                  = ( ( typeof $( this ).attr( 'href' ) !== 'undefined' ) ? $( this ).attr( 'href' ) : $( this ).data( 'tab' ) );

            // Don't do anything if we're clicking the already active tab.
            if ( $( this ).hasClass( 'anugu-active' ) ) {
                return;
            }

            // If the tab that was clicked is a label, check its corresponding input element, if it isn't already checked
            if ( typeof $( this ).attr( 'for' ) !== 'undefined' ) {
                if ( ! $( 'input#' + $( this ).attr( 'for' ) ).prop( 'checked' ) ) {
                    $( 'input#' + $( this ).attr( 'for' ) ).prop( 'checked', true ).trigger( 'change' );
                }
            }

            // Remove the active class from everything in this tab navigation and section
            $( anugu_tabs ).find( '.anugu-active' ).removeClass( 'anugu-active' );
            $( anugu_tabs_section ).find( '.anugu-active' ).removeClass( 'anugu-active' );

            // Add the active class to the chosen tab and section
            $( this ).addClass( 'anugu-active' );
            $( anugu_tabs_section ).find( anugu_tab ).addClass( 'anugu-active' );

            // Update the window URL to contain the selected tab as a hash in the URL.
            if ( anugu_tabs_update_hashbang == '1' ) {
                window.location.hash = anugu_tab.split( '#' ).join( '#!' );

                // Update the form action to contain the selected tab as a hash in the URL
                // This means when the user saves their Gallery, they'll see the last selected
                // tab 'open' on reload
                var anugu_post_action = $( '#post' ).attr( 'action' );
                if ( anugu_post_action ) {
                    // Remove any existing hash from the post action
                    anugu_post_action = anugu_post_action.split( '#' )[0];

                    // Append the selected tab as a hash to the post action
                    $( '#post' ).attr( 'action', anugu_post_action + window.location.hash );
                }  
            }      

        } );
    } );
} ( jQuery ) );