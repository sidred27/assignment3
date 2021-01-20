/**
 * Handles:
 * - Copy to Clipboard functionality
 * - Dismissable Notices
 *
 * @since 1.5.0
 */
jQuery( document ).ready( function( $ ) {

    $("#screen-meta-links").prependTo("#anugu-header-temp");
    $("#screen-meta").prependTo("#anugu-header-temp");

	/**
    * Copy to Clipboard
    */
    if ( typeof Clipboard !== 'undefined' ) {
        $( document ).on( 'click', '.anugu-clipboard', function( e ) {
            var anugu_clipboard = new Clipboard('.anugu-clipboard');
            e.preventDefault();
        } );
    }

	/**
    * Dismissable Notices
    * - Sends an AJAX request to mark the notice as dismissed
    */
    $( 'div.anugu-notice' ).on( 'click', '.notice-dismiss', function( e ) {

        e.preventDefault();

        $( this ).closest( 'div.anugu-notice' ).fadeOut();

        // If this is a dismissible notice, it means we need to send an AJAX request
        if ( $( this ).hasClass( 'is-dismissible' ) ) {
            $.post(
                anugu_gallery_admin.ajax,
                {
                	action: 'anugu_gallery_ajax_dismiss_notice',
                	nonce: 	anugu_gallery_admin.dismiss_notice_nonce,
                	notice: $( this ).parent().data( 'notice' )
                },
                function( response ) {
    			},
                'json'
            );
        }

    } );

});