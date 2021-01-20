<?php
/**
 * Review class.
 *
 * @since 1.7.0
 *
 * @package Anugu_Gallery
 * @author  Anugu Gallery Team <support@anugugallery.com>
 */

namespace Anugu\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Review Class
 *
 * @since 1.7.0
 */
class Anugu_Subscribe {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.7.0
	 */
	public function __construct() {

		add_action( 'admin_notices', array( $this, 'subscribe' ) );
		add_action( 'wp_ajax_anugu_dismiss_subscribe', array( $this, 'dismiss' ) );
	}

	/**
	 * Add admin notices as needed for reviews.
	 *
	 * @since 1.1.6.1
	 */
	public function subscribe() {

		if ( ! function_exists('get_current_screen') ){
			return;
		}

		// Get current screen.
		$screen = get_current_screen();

		// Bail if we're not on the Anugu Post Type screen.
		if ( 'anugu' !== $screen->post_type || 'anugu_page_anugu-gallery-lite-litevspro' === $screen->id ) {
			return;
		}

		// Verify that we can do a check for reviews.
		$subscribe = get_option( 'anugu_gallery_subscribe' );

		if ( false !== $subscribe && '' !== $subscribe ){
			return;
		}
		$gallery_count = wp_count_posts( 'anugu' );

		// We have a candidate! Output a review message.
		?>
		<style>
			#group_256{
				visibility:hidden;
				display:none;
			}
			.is-primary.anugu-button{
				background: #7cc048;
				border-color: #7cc048;
				-webkit-box-shadow: none;
				box-shadow: none;
				color: #fff;
			}
			.notice-info.anugu-subscribe-notice{
				border-left-color: #7cc048;
			}
			.anugu-subscribe-notice a{
				color: #7cc048;
			}
			.anugu-subscribe-field{
				margin-right: 10px;
			}
			#anugu-subscribe-success{
				display: none;
			}
			#anugu-subscribe-error{
				display: none;
				color:red;
			}
		</style>

		<script type="text/javascript">

			jQuery(document).ready( function($) {
				$('#anugu-subscribe-form').on('submit', function(e){

					e.preventDefault();
					var post_url = $(this).attr("action"),
						request_method = $(this).attr("method"),
						form_data = $(this).serialize();

					$.ajax({
						url : post_url,
						type: request_method,
						data : form_data,
						dataType    : 'json',
						contentType: "application/json; charset=utf-8",
						error       : function(err) { alert("Could not connect to the registration server."); },
						success     : function(data) {
		                	if (data.result != "success") {
								$('#anugu-subscribe-error').show();
		                    } else {
								$.post( ajaxurl, {
									action: 'anugu_dismiss_subscribe'
								});
								$('#anugu-subscribe-block').remove();
								$('#anugu-subscribe-success').show();
								$('.anugu-subscribe-notice').delay(2000).fadeOut("slow");

							}
						}
					})
				});
				$(document).on('click', '.anugu-dismiss-subscribe-notice, .anugu-subscribe-notice button', function( event ) {
					event.preventDefault();

					$.post( ajaxurl, {
						action: 'anugu_dismiss_subscribe'
					});

					$('.anugu-subscribe-notice').remove();
				});
			});
		</script>
		<?php
	}

	/**
	 * Dismiss the review nag
	 *
	 * @since 1.1.6.1
	 */
	public function dismiss() {

		update_option( 'anugu_gallery_subscribe', true );
		die;
	}

}

$anugu_subscirbe = new Anugu_Subscribe;