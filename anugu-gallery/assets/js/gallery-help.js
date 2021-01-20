/**
 * Handles:
 * - Inline Video Help
 *
 * @since 1.5.0
 */

// Setup vars
var anugu_video_link       = 'p.anugu-intro a.anugu-video',
    anugu_close_video_link = 'a.anugu-video-close';

jQuery( document ).ready( function( $ ) {
    /**
    * Display Video Inline on Video Link Click
    */
    $( document ).on( 'click', anugu_video_link, function( e ) {

        // Prevent default action
        e.preventDefault();

        // Get the video URL
        var anugu_video_url = $( this ).attr( 'href' );

        // Check if the video has the autoplay parameter included
        // If not, add it now - this will play the video when it's inserted to the iframe.
        if ( anugu_video_url.search( 'autoplay=1' ) == -1 ) {
            if ( anugu_video_url.search( 'rel=' ) == -1 ) {
                anugu_video_url += '?rel=0&autoplay=1';
            } else {
                anugu_video_url += '&autoplay=1';
            }
        }

        // Destroy any other instances of Anugu Video iframes
        $( 'div.anugu-video-help' ).remove();

        // Get the intro paragraph
        var anugu_video_paragraph = $( this ).closest( 'p.anugu-intro' );

        // Load the video below the intro paragraph on the current tab
        $( anugu_video_paragraph ).append( '<div class="anugu-video-help"><iframe src="' + anugu_video_url + '" /><a href="#" class="anugu-video-close dashicons dashicons-no"></a></div>' );

    } );

    /**
    * Destroy Video when closed
    */
    $( document ).on( 'click', anugu_close_video_link, function( e ) {
        
        e.preventDefault();
        
        $( this ).closest( '.anugu-video-help' ).remove();

    } );

} );