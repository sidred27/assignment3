<?php
/**
 * Welcome class.
 *
 * @since 1.8.1
 *
 * @package Anugu_Gallery
 * @author  Anugu Gallery Team
 */

// namespace Anugu\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Welcome Class
 *
 * @since 1.7.0
 *
 * @package Anugu_Gallery
 * @author  Anugu Gallery Team <support@anugugallery.com>
 */
class Anugu_Welcome {

	/**
	 * Holds the submenu pagehook.
	 *
	 * @since 1.7.0
	 *
	 * @var string`
	 */
	public $hook;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.8.1
	 */
	public function __construct() {

		if ( ( defined( 'ANUGU_WELCOME_SCREEN' ) && false === ANUGU_WELCOME_SCREEN ) || apply_filters( 'anugu_whitelabel', false ) === true ) {
			return;
		}

		// Add custom addons submenu.
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 15 );

		// Add custom CSS class to body.
		add_filter( 'admin_body_class', array( $this, 'admin_welcome_css' ), 15 );

		// Add scripts and styles.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_head', array( $this, 'anugu_menu_styles' ) );

		// Misc.
		add_action( 'admin_print_scripts', array( $this, 'disable_admin_notices' ) );

		//echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( "https://anugugallery.com/docs/how-to-configure-your-gallery-settings", "whatsnewtab", "checkoutourchangelog", "anugu-changelog" ); exit;

	}

	/**
	 * Add custom CSS to admin body tag.
	 *
	 * @since 1.8.1
	 * @param array $classes CSS Classes.
	 * @return array
	 */
	public function admin_welcome_css( $classes ) {

		if ( ! is_admin() ) {
			return;
		}

		$classes .= ' anugu-welcome-enabled ';

		return $classes;

	}

	/**
	 * Register and enqueue addons page specific CSS.
	 *
	 * @since 1.8.1
	 */
	public function enqueue_admin_styles() {

		$welcome_pages = array( 'anugu-gallery-lite-get-started', 'anugu-gallery-lite-welcome', 'anugu-gallery-lite-support', 'anugu-gallery-lite-welcome-addons', 'anugu-gallery-lite-changelog', 'anugu-gallery-lite-upgrade', 'anugu-gallery-lite-litevspro' );

		if ( isset( $_GET['post_type'] ) && isset( $_GET['page'] ) && 'anugu' === wp_unslash( $_GET['post_type'] ) && in_array( wp_unslash( $_GET['page'] ), $welcome_pages ) ) { // @codingStandardsIgnoreLine

			wp_register_style( ANUGU_SLUG . '-welcome-style', plugins_url( 'assets/css/welcome.css', ANUGU_FILE ), array(), ANUGU_VERSION );
			wp_enqueue_style( ANUGU_SLUG . '-welcome-style' );

			// wp_register_style( ANUGU_SLUG . '-addons-style', plugins_url( 'assets/css/addons.css', ANUGU_FILE ), array(), ANUGU_VERSION );
			// wp_enqueue_style( ANUGU_SLUG . '-addons-style' );

		}

        // Run a hook to load in custom styles.
        do_action( 'anugu_gallery_addons_styles' );

	}

	/**
	 * Add custom CSS to block out certain menu items ONLY when welcome screen is activated.
	 *
	 * @since 1.8.1
	 */
	public function anugu_menu_styles() { 

		if ( is_admin() ) {

		?>

			<style>

			/* ==========================================================================
			Menu
			========================================================================== */
			li#menu-posts-anugu ul li:last-child,
			li#menu-posts-anugu ul li:nth-last-child(2),
			li#menu-posts-anugu ul li:nth-last-child(3),
			li#menu-posts-anugu ul li:nth-last-child(4) {
				display: none;
			}

			</style>

		<?php

		}

	}



	/**
	 * Making page as clean as possible
	 *
	 * @since 1.8.1
	 */
	public function disable_admin_notices() {

		global $wp_filter;

		$welcome_pages = array( 'anugu-gallery-lite-get-started', 'anugu-gallery-lite-welcome', 'anugu-gallery-lite-support', 'anugu-gallery-lite-changelog', 'anugu-gallery-lite-upgrade' );

		if ( isset( $_GET['post_type'] ) && isset( $_GET['page'] ) && 'anugu' === wp_unslash( $_GET['post_type'] ) && in_array( wp_unslash( $_GET['page'] ), $welcome_pages ) ) { // @codingStandardsIgnoreLine

			if ( isset( $wp_filter['user_admin_notices'] ) ) {
				unset( $wp_filter['user_admin_notices'] );
			}
			if ( isset( $wp_filter['admin_notices'] ) ) {
				unset( $wp_filter['admin_notices'] );
			}
			if ( isset( $wp_filter['all_admin_notices'] ) ) {
				unset( $wp_filter['all_admin_notices'] );
			}
		}

	}

	/**
	 * Register the Welcome submenu item for Anugu.
	 *
	 * @since 1.8.1
	 */
	public function admin_menu() {
		$whitelabel = apply_filters( 'anugu_whitelabel', false ) ? '' : __( 'Anugu Gallery ', 'anugu-gallery-lite' );
		// Register the submenus.
		

		

		
	

		add_submenu_page(
			'edit.php?post_type=anugu',
			$whitelabel . __( 'Support', 'anugu-gallery-lite' ),
			'<span style="color:#FFA500"> ' . __( 'Support', 'anugu-gallery-lite' ) . '</span>',
			apply_filters( 'anugu_gallery_menu_cap', 'manage_options' ),
			ANUGU_SLUG . '-support',
			array( $this, 'support_page' )
		); 

	}

	/**
	 * Output welcome text and badge for What's New and Credits pages.
	 *
	 * @since 1.8.1
	 */
	public static function welcome_text() {

		// Switch welcome text based on whether this is a new installation or not.
		$welcome_text = ( self::is_new_install() )
			? esc_html( 'Thank you for installing Anugu Lite! Anugu provides great gallery features for your WordPress site!', 'anugu-gallery-lite' )
			: esc_html( 'Thank you for updating! Anugu Lite %s has many recent improvements that you will enjoy.', 'anugu-gallery-lite' );

		?>
		<?php /* translators: %s: version */ ?>
		<h1 class="welcome-header"><?php printf( esc_html__( 'Welcome to %1$s Anugu Gallery Lite %2$s', 'anugu-gallery-lite' ), '<span class="anugu-leaf"></span>&nbsp;', esc_html( self::display_version() ) ); ?></h1>

		<div class="about-text">
			<?php
			if ( self::is_new_install() ) {
				echo esc_html( $welcome_text );
			} else {
				printf( $welcome_text, self::display_version() ); // @codingStandardsIgnoreLine
			}
			?>
		</div>

		<?php
	}

	/**
	 * Output tab navigation
	 *
	 * @since 2.2.0
	 *
	 * @param string $tab Tab to highlight as active.
	 */
	public static function tab_navigation( $tab = 'whats_new' ) {
		?>

		<h3 class="nav-tab-wrapper">
			<a class="nav-tab
			<?php
			if ( isset( $_GET['page'] ) && 'anugu-gallery-lite-welcome' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) : // @codingStandardsIgnoreLine
				?>
				nav-tab-active<?php endif; ?>" href="
				<?php
				echo esc_url(
					admin_url(
						add_query_arg(
							array(
								'post_type' => 'anugu',
								'page'      => 'anugu-gallery-lite-welcome',
							),
							'edit.php'
						)
					)
				);
				?>
														">
				<?php esc_html_e( 'What&#8217;s New', 'anugu-gallery-lite' ); ?>
			</a>
			<a class="nav-tab
			<?php
			if ( isset( $_GET['page'] ) && 'anugu-gallery-lite-get-started' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) : // @codingStandardsIgnoreLine
				?>
				nav-tab-active<?php endif; ?>" href="
				<?php
				echo esc_url(
					admin_url(
						add_query_arg(
							array(
								'post_type' => 'anugu',
								'page'      => 'anugu-gallery-lite-get-started',
							),
							'edit.php'
						)
					)
				);
				?>
														">
				<?php esc_html_e( 'Get Started', 'anugu-gallery-lite' ); ?>
			</a>
			<a class="nav-tab
			<?php
			if ( isset( $_GET['page'] ) && 'anugu-gallery-lite-litevspro' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) : // @codingStandardsIgnoreLine
				?>
				nav-tab-active<?php endif; ?>" href="
				<?php
				echo esc_url(
					admin_url(
						add_query_arg(
							array(
								'post_type' => 'anugu',
								'page'      => 'anugu-gallery-lite-litevspro',
							),
							'edit.php'
						)
					)
				);
				?>
														">
				<?php esc_html_e( 'Lite vs Pro', 'anugu-gallery-lite' ); ?>
			</a>
			<a class="nav-tab
			<?php
			if ( isset( $_GET['page'] ) && 'anugu-gallery-lite-upgrade' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) : // @codingStandardsIgnoreLine
				?>
				nav-tab-active<?php endif; ?>" href="
				<?php
				echo esc_url(
					admin_url(
						add_query_arg(
							array(
								'post_type' => 'anugu',
								'page'      => 'anugu-gallery-lite-upgrade',
							),
							'edit.php'
						)
					)
				);
				?>
														">
				<?php esc_html_e( 'Upgrade Anugu Gallery', 'anugu-gallery-lite' ); ?>
			</a>

		</h3>

		<?php
	}

	/**
	 * Output the sidebar.
	 *
	 * @since 1.8.5
	 */
	public function sidebar() {

		global $wp_version;

		if ( isset( $_GET['page'] ) && $_GET['page'] === 'anugu-gallery-lite-litevspro' ) {
			return;
		}

		?>

			<div class="anugu-welcome-sidebar">

				<?php

				if ( version_compare( PHP_VERSION, '5.6.0', '<' ) ) {

					?>

					<div class="sidebox warning php-warning">

					<h4><?php esc_html_e( 'Please Upgrade Your PHP Version!', 'anugu-gallery-lite' ); ?></h4>
					<p><?php echo wp_kses( 'Your hosting provider is using PHP <strong>' . PHP_VERSION . '</strong>, an outdated and unsupported version. Soon Anugu Gallery will need a minimum of PHP <strong>5.6</strong>.', wp_kses_allowed_html( 'post' ) ); ?></p>
					<a target="_blank" href="https://anugugallery.com/docs/update-php" class="button button-primary">Learn More</a>

					</div>

				<?php } ?>

				<?php

				if ( ! empty( $wp_version ) && version_compare( $wp_version, '4.8', '<' ) ) {

					?>

				<div class="sidebox warning php-warning">

					<h4><?php esc_html_e( 'Please Upgrade Your WordPress Version!', 'anugu-gallery-lite' ); ?></h4>
					<p><?php echo wp_kses( 'You are currently using WordPress <strong>' . $wp_version . '</strong>, an outdated version. Soon Anugu Gallery will need a minimum of WordPress <strong>4.8</strong>.', wp_kses_allowed_html( 'post' ) ); ?></p>
					<a target="_blank" href="https://anugugallery.com/docs/update-wordpress" class="button button-primary">Learn More</a>

				</div>

				<?php } ?>

				<?php

				if ( class_exists( 'Anugu_Gallery' ) && anugu_get_license_key() === false ) {

					?>

				<div class="sidebox">
					<form id="anugu-settings-verify-key" method="post" action="<?php echo esc_url( admin_url( 'edit.php?post_type=anugu&page=anugu-gallery-settings' ) ); ?>">
						<h4><?php esc_html_e( 'Activate License Key', 'anugu-gallery-lite' ); ?></h4>
						<p><?php esc_html_e( 'License key to enable automatic updates for Anugu. License key to enable automatic updates for Anugu. ', 'send-system-info' ); ?></p>
						<input type="password" name="anugu-license-key" id="anugu-settings-key" value="" />
						<?php wp_nonce_field( 'anugu-gallery-key-nonce', 'anugu-gallery-key-nonce' ); ?>
						<?php submit_button( __( 'Verify Key', 'anugu-gallery-lite' ), 'primary', 'anugu-gallery-verify-submit', false ); ?>
					</form>
				</div>

					<?php

				}
				?>
				<?php

				$url = 'https://wordpress.org/support/plugin/anugu-gallery-lite/reviews/';

				?>
					<div class="sidebox">

							<h4><?php esc_html_e( 'We Need Your Help', 'anugu-gallery-lite' ); ?></h4>
							<?php /* translators: %1$s: url, %2$s url */ ?>
							
							<a target="_blank" href="<?php echo esc_url( $url ); ?>" class="button button-primary">Rate It</a>

					</div>
				<div class="sidebox">
					<form action="https://anugugallery.us3.list-manage.com/subscribe/post?u=beaa9426dbd898ac91af5daca&amp;id=2ee2b5572e" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
						<h4><?php esc_html_e( 'Join 58,709 web developers, photographers and artists who already have a head start.', 'send-system-info' ); ?></h4>
						<p><?php esc_html_e( 'Get free tips and resources on how to get the most out of Anugu Gallery and WordPress delivered directly to your inbox.', 'send-system-info' ); ?></p>
						<div class="form-row"><input type="text" value="" name="FNAME" placeholder="Name" id="mce-FNAME"></div>
						<div class="form-row"><input type="email" placeholder="Email" name="EMAIL" required /></div>

						<input type="submit" class="button button-primary" value="Sign Up" />
					</form>
				</div>
			</div>


		<?php
	}

	/**
	 * Output the about screen.
	 *
	 * @since 1.8.5
	 */
	public function welcome_page() {
		?>

		<div class="anugu-welcome-wrap anugu-welcome">

				<div class="anugu-title">

					<?php self::welcome_text(); ?>

				</div>

				<div class="anugu-welcome-main">

					<?php self::tab_navigation( __METHOD__ ); ?>

					<div class="anugu-welcome-panel">

						<div class="wraps about-wsrap">

							<div class="anugu-recent-section">

								<h3 class="headline-title"><?php esc_html_e( 'Anugu Gallery is the most beginner-friendly drag &amp; drop WordPress gallery plugin.', 'anugu-gallery-lite' ); ?></h3>

								<h3 class="title"><?php esc_html_e( 'Recent Updates To Anugu Lite:', 'anugu-gallery-lite' ); ?></h3>

								<div class="anugu-recent anuguthree-column">
									<div class="anugucolumn">
											<h4 class="title"><?php esc_html_e( 'Bug Fixes', 'anugu-gallery-lite' ); ?> <span class="badge updated">UPDATED</span></h4>
											<?php /* translators: %1$s: link */ ?>
											<p><?php printf( esc_html__( 'Bugs involving automatic and column galleries on the same page, certain character displaying in the admin, and Gutenberg Block tweaks.' ) ); ?></p>
									</div>
									<div class="anugucolumn">
											<h4 class="title"><?php esc_html_e( 'Gutenberg Block', 'anugu-gallery-lite' ); ?></h4>
											<?php /* translators: %1$s: link */ ?>
											<p><?php printf( esc_html__( 'Improved support and additional features for the Anugu Lite Gutenberg block. Bug fixes involving the gallery preview and items that were appearing out of order.' ) ); ?></p>
									</div>

									<div class="anugucolumn">
											<h4 class="title"><?php esc_html_e( 'Enhancements', 'anugu-gallery-lite' ); ?></h4>
											<p><?php printf( esc_html__( 'Ability to set margins for Automatic Layouts. Also better workings with various popular WordPress plugins and themes.', 'anugu-gallery-lite' ) ); ?></p>
									</div>
								</div>

							</div>


							<div class="anugu-recent-section last-section">

								<h3>Recent Updates To Anugu Pro:</h3>

								<div class="anugu-feature">
									<img class="icon" src="https://anugugallery.com/wp-content/uploads/2015/08/drag-drop-icon.png" />
									<h4 class="feature-title"><?php esc_html_e( 'Getting Better And Better!', 'anugu-gallery-lite' ); ?></h4>
									<?php /* translators: %1$s: url, %2$s url */ ?>
									<p><?php printf( esc_html__( 'This latest update contains enhancements and improvements - some of which are based on your user feedback! Check out %1$s.', 'anugu-gallery-lite' ), '<a target="_blank" href="' . Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( "https://anugugallery.com/docs/how-to-configure-your-gallery-settings", "whatsnewtab", "checkoutourchangelog", "#anugu-changelog" ) . '">our changelog</a>' ); ?></p>
								</div>

								<div class="anugu-feature opposite">
									<img class="icon" src="https://anugugallery.com/wp-content/uploads/2015/10/proofing-icon.png" />
									<h4 class="feature-title">
										<?php esc_html_e( 'Proofing Addon', 'anugu-gallery-lite' ); ?>
									</h4>
									<p>
										<?php /* translators: %1$s: url, %2$s url */ ?>
										<?php printf( esc_html__( 'New and improved features and functions make client image proofing even easier for your photography business.', 'anugu-gallery-lite' ) ); ?>
										</p>
								</div>

								<div class="anugu-feature">
								<img class="icon" src="<?php echo esc_url( plugins_url( 'assets/images/icons/automatic-layout.png', ANUGU_FILE ) ); ?>" />
								<h4 class="feature-title"><?php esc_html_e( 'Gallery Layouts', 'anugu-gallery-lite' ); ?> <span class="badge updated">NEW</span> </h4>
								<?php /* translators: %1$s: button */ ?>
								<p><?php printf( esc_html__( 'New and improved features and functions make client image proofing even easier for your photography business.' ) ); ?></p>
								</div>

								<div class="anugu-feature opposite">
								<img class="icon" src="https://anugugallery.com/wp-content/uploads/2020/09/audio_icon.png" style="border: 1px solid #000;" />
								<h4 class="feature-title"><?php esc_html_e( 'Audio Addon', 'anugu-gallery-lite' ); ?> <span class="badge updated">NEW</span> </h4>
								<?php /* translators: %1$s: button */ ?>
								<p><?php printf( esc_html__( 'This addon allows you to easily add an audio track (such as background music or a narration) to the lightboxes in your Anugu galleries. %s', 'anugu-gallery-lite' ), '<a target="_blank" href="' . Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( "https://anugugallery.com/addons/audio-addon/", "whatsnewtab", "audioaddonreadmore", "" ) . '">Read More</a>' ); ?></p>
								</div>

							</div>

													



							<?php $this->anugu_assets(); ?>

						</div>

					</div>

				</div>

				<?php $this->sidebar(); ?>

		</div> <!-- wrap -->

		<?php
	}

	/**
	 * Output the support screen.
	 *
	 * @since 1.8.1
	 */
	public function support_page() {
		?>

		<div class="anugu-welcome-wrap anugu-support">

			<div class="anugu-title">

				<?php self::welcome_text(); ?>

			</div>

			<?php $this->sidebar(); ?>

			<div class="anugu-support-main">

				<?php self::tab_navigation( __METHOD__ ); ?>

				<div class="anugu-support-panel">

					<div class="wraps about-wsrap">

						<h3 class="headline-title"><?php esc_html_e( 'Got A Question? We Can Help!', 'anugu-gallery-lite' ); ?></h3>

						<div class="anugu-recent-section">

							<h3 class="title"><?php esc_html_e( 'Functionality:', 'anugu-gallery-lite' ); ?></h3>

							<article class="docs">

								<ul>
									<li>
									<a href="https://anugugallery.com/docs/how-to-add-animated-gifs-to-your-gallery/" title="How to Add Animated GIFs to Your Gallery">
									How to Add Animated GIFs to Your Gallery							</a>
									</li>
									<li>
									<a href="https://anugugallery.com/docs/add-facebook-application-id/" title="How to Add Your Facebook Application ID to the Social Addon">
									How to Add Your Facebook Application ID to the Social Addon							</a>
									</li>
									<li>
									<a href="https://anugugallery.com/docs/how-to-bulk-edit-gallery-images/" title="How to Bulk Edit Gallery Images">
									How to Bulk Edit Gallery Images							</a>
									</li>
									<li>
									<a href="https://anugugallery.com/docs/justified-image-grid-gallery/" title="How to Create a Justified Image Grid Gallery">
									How to Create a Justified Image Grid Gallery							</a>
									</li>
									<li>
									<a href="https://anugugallery.com/docs/import-export-galleries/" title="How to Import and Export Galleries">
									How to Import and Export Galleries							</a>
									</li>
									<li>
									<a href="https://anugugallery.com/docs/supersize-addon/" title="How to Supersize Lightbox Images">
									How to Supersize Lightbox Images							</a>
									</li>
									<li>
									<a href="https://anugugallery.com/docs/how-to-use-the-bulk-apply-settings/" title="How to Use the Bulk Apply Settings">
									How to Use the Bulk Apply Settings							</a>
									</li>
									<li>
									<a href="https://anugugallery.com/docs/add-anugu-gallery-widget/" title="How to Use the Anugu Gallery Widget">
									How to Use the Anugu Gallery Widget							</a>
									</li>
									<li>
									<a href="https://anugugallery.com/docs/standalone-addon/" title="How to Use the Standalone Feature in Anugu Gallery">
									How to Use the Standalone Feature in Anugu Gallery							</a>
									</li>
									<li>
									<a href="https://anugugallery.com/docs/display-tag-based-dynamic-gallery/" title="Display a Tag Based Dynamic Gallery">
									Display a Tag Based Dynamic Gallery							</a>
									</li>
									<li>
									<a href="https://anugugallery.com/docs/display-image-thumbnails-random-order/" title="Display Image Thumbnails in a Random Order">
									Display Image Thumbnails in a Random Order							</a>
									</li>
									<li>
									<a href="https://anugugallery.com/docs/lightbox-arrows-inside-outside/" title="Display Lightbox Nav Arrows Inside/Outside of Image">
									Display Lightbox Nav Arrows Inside/Outside of Image							</a>
									</li>
									<li>
									<a href="https://anugugallery.com/docs/how-to-turn-off-the-lightbox-for-anugu/" title="How to Turn Off the Lightbox for Anugu">
									How to Turn Off the Lightbox for Anugu							</a>
									</li>
									<li>
									<a href="https://anugugallery.com/docs/using-a-wordpress-user-role/" title="Using A WordPress User Role">
									Using A WordPress User Role							</a>
									</li>
									<li>
									<a href="https://anugugallery.com/docs/anugu-gallery-lightbox-options/" title="Anugu Gallery Lightbox Options">
									Anugu Gallery Lightbox Options							</a>
									</li>
									<li>
									<a href="https://anugugallery.com/docs/using-anugu-galleries-and-page-builder-tabbed-content/" title="Using Anugu Galleries and Page Builder Tabbed Content">
									Using Anugu Galleries and Page Builder Tabbed Content							</a>
									</li>
									<li>
									<a href="https://anugugallery.com/docs/how-to-enable-rtl-support/" title="How to Enable RTL Support">
									How to Enable RTL Support							</a>
									</li>
									<li>
									<a href="https://anugugallery.com/docs/how-to-preview-anugu-galleries/" title="How to Preview Anugu Galleries">
									How to Preview Anugu Galleries							</a>
									</li>
									<li>
									<a href="https://anugugallery.com/docs/enable-shortcodes-in-gallery-descriptions/" title="Enable Shortcodes in Gallery Descriptions">
									Enable Shortcodes in Gallery Descriptions							</a>
									</li>
								</ul>
								</article>

								<div style="margin: 20px auto 0 auto;">
									<a  target="_blank" href="https://anugugallery.com/categories/docs/functionality/" class="button button-primary">See More Guides On Functionality</a>
								</div>

								<h3 class="title" style="margin-top: 30px;"><?php esc_html_e( 'Addons:', 'anugu-gallery-lite' ); ?></h3>

								<article class="docs">
									<ul>
										<li>
										<a href="https://anugugallery.com/docs/how-to-add-animated-gifs-to-your-gallery/" title="How to Add Animated GIFs to Your Gallery">
										How to Add Animated GIFs to Your Gallery							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/add-facebook-application-id/" title="How to Add Your Facebook Application ID to the Social Addon">
										How to Add Your Facebook Application ID to the Social Addon							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/how-to-bulk-edit-gallery-images/" title="How to Bulk Edit Gallery Images">
										How to Bulk Edit Gallery Images							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/justified-image-grid-gallery/" title="How to Create a Justified Image Grid Gallery">
										How to Create a Justified Image Grid Gallery							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/import-export-galleries/" title="How to Import and Export Galleries">
										How to Import and Export Galleries							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/supersize-addon/" title="How to Supersize Lightbox Images">
										How to Supersize Lightbox Images							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/how-to-use-the-bulk-apply-settings/" title="How to Use the Bulk Apply Settings">
										How to Use the Bulk Apply Settings							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/add-anugu-gallery-widget/" title="How to Use the Anugu Gallery Widget">
										How to Use the Anugu Gallery Widget							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/standalone-addon/" title="How to Use the Standalone Feature in Anugu Gallery">
										How to Use the Standalone Feature in Anugu Gallery							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/display-tag-based-dynamic-gallery/" title="Display a Tag Based Dynamic Gallery">
										Display a Tag Based Dynamic Gallery							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/display-image-thumbnails-random-order/" title="Display Image Thumbnails in a Random Order">
										Display Image Thumbnails in a Random Order							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/lightbox-arrows-inside-outside/" title="Display Lightbox Nav Arrows Inside/Outside of Image">
										Display Lightbox Nav Arrows Inside/Outside of Image							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/how-to-turn-off-the-lightbox-for-anugu/" title="How to Turn Off the Lightbox for Anugu">
										How to Turn Off the Lightbox for Anugu							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/using-a-wordpress-user-role/" title="Using A WordPress User Role">
										Using A WordPress User Role							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/anugu-gallery-lightbox-options/" title="Anugu Gallery Lightbox Options">
										Anugu Gallery Lightbox Options							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/using-anugu-galleries-and-page-builder-tabbed-content/" title="Using Anugu Galleries and Page Builder Tabbed Content">
										Using Anugu Galleries and Page Builder Tabbed Content							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/how-to-enable-rtl-support/" title="How to Enable RTL Support">
										How to Enable RTL Support							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/how-to-preview-anugu-galleries/" title="How to Preview Anugu Galleries">
										How to Preview Anugu Galleries							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/enable-shortcodes-in-gallery-descriptions/" title="Enable Shortcodes in Gallery Descriptions">
										Enable Shortcodes in Gallery Descriptions							</a>
										</li>
									</ul>
								</article>

								<div style="margin: 20px auto 0 auto;">
									<a  target="_blank" href="https://anugugallery.com/categories/docs/addons/" class="button button-primary">See More Guides On Addons</a>
								</div>

								<h3 class="title" style="margin-top: 30px;"><?php esc_html_e( 'Styling:', 'anugu-gallery-lite' ); ?></h3>

								<article class="docs">
									<ul>
										<li>
										<a href="https://anugugallery.com/docs/how-to-add-animated-gifs-to-your-gallery/" title="How to Add Animated GIFs to Your Gallery">
										How to Add Animated GIFs to Your Gallery							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/add-facebook-application-id/" title="How to Add Your Facebook Application ID to the Social Addon">
										How to Add Your Facebook Application ID to the Social Addon							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/how-to-bulk-edit-gallery-images/" title="How to Bulk Edit Gallery Images">
										How to Bulk Edit Gallery Images							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/justified-image-grid-gallery/" title="How to Create a Justified Image Grid Gallery">
										How to Create a Justified Image Grid Gallery							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/import-export-galleries/" title="How to Import and Export Galleries">
										How to Import and Export Galleries							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/supersize-addon/" title="How to Supersize Lightbox Images">
										How to Supersize Lightbox Images							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/how-to-use-the-bulk-apply-settings/" title="How to Use the Bulk Apply Settings">
										How to Use the Bulk Apply Settings							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/add-anugu-gallery-widget/" title="How to Use the Anugu Gallery Widget">
										How to Use the Anugu Gallery Widget							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/standalone-addon/" title="How to Use the Standalone Feature in Anugu Gallery">
										How to Use the Standalone Feature in Anugu Gallery							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/display-tag-based-dynamic-gallery/" title="Display a Tag Based Dynamic Gallery">
										Display a Tag Based Dynamic Gallery							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/display-image-thumbnails-random-order/" title="Display Image Thumbnails in a Random Order">
										Display Image Thumbnails in a Random Order							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/lightbox-arrows-inside-outside/" title="Display Lightbox Nav Arrows Inside/Outside of Image">
										Display Lightbox Nav Arrows Inside/Outside of Image							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/how-to-turn-off-the-lightbox-for-anugu/" title="How to Turn Off the Lightbox for Anugu">
										How to Turn Off the Lightbox for Anugu							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/using-a-wordpress-user-role/" title="Using A WordPress User Role">
										Using A WordPress User Role							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/anugu-gallery-lightbox-options/" title="Anugu Gallery Lightbox Options">
										Anugu Gallery Lightbox Options							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/using-anugu-galleries-and-page-builder-tabbed-content/" title="Using Anugu Galleries and Page Builder Tabbed Content">
										Using Anugu Galleries and Page Builder Tabbed Content							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/how-to-enable-rtl-support/" title="How to Enable RTL Support">
										How to Enable RTL Support							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/how-to-preview-anugu-galleries/" title="How to Preview Anugu Galleries">
										How to Preview Anugu Galleries							</a>
										</li>
										<li>
										<a href="https://anugugallery.com/docs/enable-shortcodes-in-gallery-descriptions/" title="Enable Shortcodes in Gallery Descriptions">
										Enable Shortcodes in Gallery Descriptions							</a>
										</li>
									</ul>
								</article>

								<div style="margin: 20px auto 0 auto;">
									<a target="_blank" href="https://anugugallery.com/categories/docs/styling/" class="button button-primary">See More Guides On Styling</a>
								</div>

								</div>

								<hr/>

				</div>

			</div>

		</div> <!-- wrap -->

		<?php
	}

	/**
	 * Output the about screen.
	 *
	 * @since 1.8.1
	 */
	public function help_page() {
		?>

		<div class="anugu-welcome-wrap anugu-help">

			<div class="anugu-title">

				<?php self::welcome_text(); ?>

			</div>

			<?php $this->sidebar(); ?>

			<div class="anugu-get-started-main">

				<?php self::tab_navigation( __METHOD__ ); ?>

				<div class="anugu-get-started-section">

						<div class="anugu-admin-get-started-panel">

							<div class="section-text text-left">

								<h2>Creating your first gallery</h2>

								<p>Want to get started creating your first gallery? By following the step by step instructions in this walkthrough, you can easily publish your first gallery on your site.</p>

								<p>To begin, you’ll need to be logged into the WordPress admin area. Once there, click on Anugu Gallery in the admin sidebar to go the Add New page.</p>

								<p>This will launch the Anugu Gallery Builder.</p>

								<ul class="list-of-links">
									<li><a target="_blank" href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( "https://anugugallery.com/how-to-use-anugu-gallery/", "gettingstartedtab", "howtouseanugugallery", "" ); ?>">How to get started with Anugu Gallery</a></li>
									<li><a target="_blank" href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( "https://anugugallery.com/how-to-optimize-image-galleries-for-mobile-using-anugu-gallery", "gettingstartedtab", "howtooptimizeimagegalleriesformobile", "" ); ?>">How to optimize image galleries for mobile using Anugu Gallery</a></li>
									<li><a target="_blank" href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( "https://anugugallery.com/how-to-protect-your-website-from-image-theft/", "gettingstartedtab", "howtoprotectyourgalleriesfromimagetheft", "" ); ?>">How to protect your galleries and images from online theft</a></li>
									<li><a target="_blank" href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( "https://anugugallery.com/how-to-sell-your-photos-in-wordpress", "gettingstartedtab", "howtosellyourphotosinwordpress", "" ); ?>">How to sell your photos in WordPress</a></li>
									<li><a target="_blank" href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( "https://anugugallery.com/how-to-enhance-gallery-layouts-with-customizable-gallery-themes/", "gettingstartedtab", "howtoenhancegallerylayouts", "" ); ?>">How to enhance gallery layouts with customizable gallery themes</a></li>
									</li>
								</ul>

							</div>

							<div class="feature-photo-column">
									<img class="feature-photo" src="<?php echo esc_url( plugins_url( 'assets/images/get-started/creating.png', ANUGU_FILE ) ); ?>" />
							</div>

						</div> <!-- panel -->

						<div class="anugu-admin-get-started-panel">

							<div class="section-text-column text-left">

								<h2>Upgrade to a complete Anugu Gallery experience</h2>

								<p>Get the most out of Anugu Gallery by <a target="_blank" href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( false, 'gettingstartedtab', 'upgradetounlockallitspowerfulfeatures' ); ?>">upgrading to unlock all of its powerful features</a>.</p>

								<p>With Anugu Gallery Pro, you can unlock amazing features like:</p>

								<ul>
									<li>Get your gallery set up in minutes with pre-built customizable templates </li>
									<li>Have more people find you on Google by making your galleries SEO friendly </li>
									<li>Display your photos in all their glory on mobile with a true full-screen experience. No bars, buttons or small arrows</li>
									<li>Tag your images for better organization and gallery display</li>
									<li>Improve load times and visitor experience by splitting your galleries into multiple pages </li>
									<li>Streamline your workflow by sharing your gallery images directly on your favorite social media networks </li>
									</li>
								</ul>

							</div>

							<div class="feature-photo-column">
									<img class="feature-photo" src="<?php echo esc_url( plugins_url( 'assets/images/get-started/upgrade.png', ANUGU_FILE ) ); ?>" />
							</div>

						</div> <!-- panel -->

						<div class="anugu-admin-get-started-banner middle">

							<div class="banner-text">
								<h3>Upgrade To Unleash the Power of Anugu</h3>
								<p>Pricing starts at just $29... What are you waiting for?</p>
							</div>
							<div class="banner-button">
								<a target="_blank" href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( false, 'getstartedtab', 'upgradenowbutton' ); ?>" class="button button-primary">Upgrade Now</a>
							</div>

						</div> <!-- banner -->

						<div class="anugu-admin-get-started-panel mini-panel">

							<div class="feature-photo-column photo-left">
								<a href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( "https://anugugallery.com/how-to-create-a-masonry-image-gallery-in-wordpress/", "gettingstartedtab", "createamasonrygallerybutton", "" ); ?>"><img class="feature-photo" src="<?php echo esc_url( plugins_url( 'assets/images/get-started/how-to-create-a-masonry-image-gallery-in-wordpress.jpg', ANUGU_FILE ) ); ?>" /></a>
							</div>

							<div class="section-text-column text-left">

								<h2>How to Create a Masonry Image Gallery in WordPress</h2>

								<p>Do you want to create a masonry style gallery in WordPress? Sometimes you need to display full-view thumbnails without cropping the height or width. In this tutorial, we will share with you how to create a masonry image gallery in WordPress.</p>

								<div class="banner-button">
									<a target="_blank" href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( "https://anugugallery.com/how-to-create-a-masonry-image-gallery-in-wordpress/", "gettingstartedtab", "createamasonrygallerybutton", "" ); ?>" class="button button-primary">Read Documentation</a>
								</div>

							</div>

						</div> <!-- panel -->

						<div class="anugu-admin-get-started-panel mini-panel">

							<div class="feature-photo-column photo-left">
								<a href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( "https://anugugallery.com/how-to-create-an-image-slider-for-your-wordpress-galleries/", "gettingstartedtab", "createimageslidersforyourgalleries", "" ); ?>"><img class="feature-photo" src="<?php echo esc_url( plugins_url( 'assets/images/get-started/how-to-create-image-slider-for-your-wordpress-galleries.jpg', ANUGU_FILE ) ); ?>" /></a>
							</div>

							<div class="section-text-column text-left">

								<h2>How to Create an Image Slider for Your WordPress Galleries</h2>

								<p>Do you want to create an image slider in WordPress? Want to display your photo galleries in a slideshow? In this article, we will show you how to create an image slider for your WordPress galleries.</p>

								<div class="banner-button">
									<a target="_blank" href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( "https://anugugallery.com/how-to-create-an-image-slider-for-your-wordpress-galleries/", "gettingstartedtab", "createimageslidersforyourgalleries", "" ); ?>" class="button button-primary">Read Documentation</a>
								</div>

							</div>

						</div> <!-- panel -->

						<div class="anugu-admin-get-started-panel mini-panel">

							<div class="feature-photo-column photo-left">
								<a href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( "https://anugugallery.com/add-gallery-tags-wordpress/", "gettingstartedtab", "addgallerytagsinwordpress", "" ); ?>"><img class="feature-photo" src="<?php echo esc_url( plugins_url( 'assets/images/get-started/add-gallery-tags-in-wordpress.jpg', ANUGU_FILE ) ); ?>" /></a>
							</div>

							<div class="section-text-column text-left">

								<h2>How to Make Your Images Easier for Visitors To Find</h2>

								<p>Do you want to add tags to your images in WordPress galleries? With image tagging, you can give your visitors a way to sort through them easily. In this tutorial, we will share how to add gallery tags in WordPress by using Anugu Gallery.</p>

								<div class="banner-button">
									<a target="_blank" href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( "https://anugugallery.com/add-gallery-tags-wordpress/", "gettingstartedtab", "addgallerytagsinwordpress", "" ); ?>" class="button button-primary">Read Documentation</a>
								</div>

							</div>

						</div> <!-- panel -->

						<div class="anugu-admin-get-started-banner bottom">

							<div class="banner-text">
								<h3>Start Creating Responsive Photo Galleries</h3>
								<p>Customize and Publish in Minutes... What are you waiting for?</p>
							</div>
							<div class="banner-button">
								<a target="_blank" href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( false, 'getstartedtab', 'getanugugallerynowbutton' ); ?>" class="button button-primary">Get Anugu Gallery Now</a>
							</div>

						</div> <!-- banner -->


					<?php //$this->anugu_posts(); ?>

					<?php $this->anugu_assets(); ?>

			</div>

		</div> <!-- wrap -->


		<?php
	}

	/**
	 * Output the upgrade screen.
	 *
	 * @since 1.8.1
	 */
	public function upgrade_page() {
		?>

		<div class="anugu-welcome-wrap anugu-help">

			<div class="anugu-title">

				<?php self::welcome_text(); ?>

			</div>

			<?php $this->sidebar(); ?>

			<div class="anugu-get-started-main">

				<?php self::tab_navigation( __METHOD__ ); ?>

				<div class="anugu-get-started-panel">

					<div class="wraps upgrade-wrap">

						<h3 class="headline-title"><?php esc_html_e( 'Make Your Galleries Amazing!', 'anugu-gallery-lite' ); ?></h3>

						<h4 class="headline-subtitle"><?php esc_html_e( 'Upgrade To Anugu Pro and can get access to our full suite of features.', 'anugu-gallery-lite' ); ?></h4>

						<a target="_blank" href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( false, 'upgradeanugugallerytab', 'upgradetoanuguprobutton' ); ?>" class="button button-primary">Upgrade To Anugu Pro</a>

					</div>

					<div class="upgrade-list">

						<ul>
							<li>
								<div class="interior">
									<h5><a href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( "https://anugugallery.com/addons/albums-addon/", "upgradeanugugallerytab", "albumsaddon", "" ); ?>">Albums Addon</a></h5>
									<p>Organize your galleries in Albums, choose cover photos and more.</p>
								</div>
							</li>
							<li>
								<div class="interior">
									<h5><a href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( "https://anugugallery.com/demo/masonry-gallery-demo/", "upgradeanugugallerytab", "masonrygallery", "" ); ?>">Masonry Gallery</a></h5>
									<p>Display your photo galleries in a masonry layout.</p>
								</div>
							</li>
							<li>
								<div class="interior">
									<h5><a href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( "https://anugugallery.com/demo/anugu-gallery-theme-demo/", "upgradeanugugallerytab", "gallerythemesandlayouts", "" ); ?>">Gallery Themes/Layouts</a></h5>
									<p>Build responsive WordPress galleries that work on mobile, tablet and desktop devices.</p>
								</div>
							</li>
							<li>
								<div class="interior">
									<h5><a href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( "https://anugugallery.com/addons/videos-addon/", "upgradeanugugallerytab", "videogalleries", "" ); ?>">Video Galleries</a></h5>
									<p>Not just for photos! Embed YouTube, Vimeo, Wistia, DailyMotion, Facebook, Instagram, Twitch, VideoPress, and self-hosted videos in your gallery.</p>
								</div>
							</li>
							<li>
								<div class="interior">
									<h5><a href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( "https://anugugallery.com/addons/social-addon/", "upgradeanugugallerytab", "socialaddon", "" ); ?>">Social Addon</a></h5>
									<p>Allows users to share photos via email, Facebook, Twitter, Pinterest, LinkedIn and WhatsApp.</p>
								</div>
							</li>
							<li>
								<div class="interior">
									<h5><a href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( "https://anugugallery.com/addons/proofing-addon/", "upgradeanugugallerytab", "imageproofing", "" ); ?>">Image Proofing</a></h5>
									<p>Client image proofing made easy for your photography business.</p>
								</div>
							</li>
							<li>
								<div class="interior">
									<h5><a href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( "https://anugugallery.com/addons/woocommerce-addon/", "upgradeanugugallerytab", "ecommerce", "" ); ?>">Ecommerce</a></h5>
									<p>Instantly display and sell your photos with our native WooCommerce integration.</p>
								</div>
							</li>
							<li>
								<div class="interior">
									<h5><a href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( "https://anugugallery.com/addons/deeplinking-addon/", "upgradeanugugallerytab", "deeplinking", "" ); ?>">Deeplinking</a></h5>
									<p>Make your gallery SEO friendly and easily link to images with deeplinking.</p>
								</div>
							</li>
							<li>
								<div class="interior">
									<h5><a href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( "https://anugugallery.com/addons/slideshow-addon/", "upgradeanugugallerytab", "slideshows", "" ); ?>">Slideshows</a></h5>
									<p>Enable slideshows for your galleries, controls autoplay settings and more.</p>
								</div>
							</li>
							<li>
								<div class="interior">
									<h5><a href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( "https://anugugallery.com/addons/lightroom-addon/", "upgradeanugugallerytab", "lightroomintegration", "" ); ?>">Lightroom Integration</a></h5>
									<p>Automatically create & sync photo galleries from your Adobe Lightroom collections.</p>
								</div>
							</li>
							<li>
								<div class="interior">
									<h5><a href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( "https://anugugallery.com/addons/protection-addon/", "upgradeanugugallerytab", "downloadprotection", "" ); ?>">Download Protection</a></h5>
									<p>Prevent visitors from downloading your images without permission.</p>
								</div>
							</li>
							<li>
								<div class="interior">
									<h5><a href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( false, 'upgradeanugugallerytab', 'dedicatedcustomersupport' ); ?>">Dedicated Customer Support... and much more!</a></h5>
									<p>Top notch customer support and dozens of pro features.</p>
								</div>
							</li>
						</ul>

					</div>

					<div class="upgrade-video">
						<iframe width="100%" src="https://www.youtube.com/embed/CLxxh_-7uFQ" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
					</div>

					<?php $this->anugu_assets(); ?>

				</div>

			</div>

		</div> <!-- wrap -->


		<?php
	}

	/**
	 * Output the upgrade screen.
	 *
	 * @since 1.8.1
	 */
	public function lite_vs_pro_page() {
		?>

		<div class="anugu-welcome-wrap anugu-help">

			<div class="anugu-title">

				<?php self::welcome_text(); ?>

			</div>

			<?php $this->sidebar(); ?>

			<div class="anugu-get-started-main">

				<?php self::tab_navigation( __METHOD__ ); ?>

				<div class="anugu-get-started-panel">

				<div id="anugu-admin-litevspro" class="wrap anugu-admin-wrap">

				<div class="anugu-admin-litevspro-section no-bottom litevspro-table-header">
					<h3 class="centered">
						<strong>Lite</strong> vs <strong>Pro</strong>
					</h3>

					<p class="centered">Get the most out of Anugu by upgrading to Pro and unlocking all of the powerful features.</p>
				</div>

				<div class="anugu-admin-litevspro-section no-bottom anugu-admin-litevspro-section-table">

						<table cellspacing="0" cellpadding="0" border="0">
							<thead>
								<th>Feature</th>
								<th>Lite</th>
								<th>Pro</th>
							</thead>
							<tbody>
								<tr class="anugu-admin-columns">
									<td class="anugu-admin-litevspro-first-column">
										<p>Gallery Themes And Layouts</p>
									</td>
									<td class="anugu-admin-litevspro-lite-column">
										<p class="features-partial">
											<strong>Basic Gallery Theme</strong>
										</p>
									</td>
									<td class="anugu-admin-litevspro-pro-column">
										<p class="features-full">
											<strong>All Gallery Themes &amp; Layouts</strong> 
											More themes to make your Galleries unique and professional.
										</p>
									</td>
								</tr>

								<tr class="anugu-admin-columns">
									<td class="anugu-admin-litevspro-first-column">
										<p>Lightbox Features</p>
									</td>
									<td class="anugu-admin-litevspro-lite-column">
										<p class="features-partial">
											<strong>Basic Lightbox</strong>
										</p>
									</td>
									<td class="anugu-admin-litevspro-pro-column">
										<p class="features-full">
											<strong>All Advanced Lightbox Features</strong>
											Multiple themes for your Gallery Lightbox display, Titles, Transitions, Fullscreen, Counter, Thumbnails  
										</p>
									</td>
								</tr>
   
								<tr class="anugu-admin-columns">
									<td class="anugu-admin-litevspro-first-column">
										<p>Mobile Features</p>
									</td>
									<td class="anugu-admin-litevspro-lite-column">
										<p class="features-partial">
											<strong>Basic Mobile Gallery	</strong>
										</p>
									</td>
									<td class="anugu-admin-litevspro-pro-column">
										<p class="features-full">
											<strong>All Advanced Mobile Settings</strong>Customize all aspects of your user's mobile gallery display experience to be different than the default desktop</p>
									</td>
								</tr>
								<tr class="anugu-admin-columns">
									<td class="anugu-admin-litevspro-first-column">
										<p>Import/Export Options	</p>
									</td>
									<td class="anugu-admin-litevspro-lite-column">
										<p class="features-none">
											<strong>Limited Import/Export	</strong>
										</p>
									</td>
									<td class="anugu-admin-litevspro-pro-column">
										<p class="features-full">
											<strong>All Import/Export </strong> Instagram, Dropbox, NextGen, Flickr, Zip and more</p>
									</td>
								</tr>
								<tr class="anugu-admin-columns">
									<td class="anugu-admin-litevspro-first-column">
										<p>Video Galleries	</p>
									</td>
									<td class="anugu-admin-litevspro-lite-column">
										<p class="features-none">
											<strong> No Videos	</strong>
										</p>
									</td>
									<td class="anugu-admin-litevspro-pro-column">
										<p class="features-full">
											<strong>All Videos Gallery </strong> Import your own videos or from any major video sharing platform</p>
									</td>
								</tr>
								<tr class="anugu-admin-columns">
									<td class="anugu-admin-litevspro-first-column">
										<p>Social Sharing	</p>
									</td>
									<td class="anugu-admin-litevspro-lite-column">
										<p class="features-none">
											<strong>No Social Sharing	</strong>
										</p>
									</td>
									<td class="anugu-admin-litevspro-pro-column">
										<p class="features-full">
											<strong>All Social Sharing Features</strong>Share your photos on any major social sharing platform</p>
									</td>
								</tr>
								<tr class="anugu-admin-columns">
									<td class="anugu-admin-litevspro-first-column">
										<p>Advanced Gallery Features	</p>
									</td>
									<td class="anugu-admin-litevspro-lite-column">
										<p class="features-none">
											<strong>  No Advanced Features	</strong>
										</p>
									</td>
									<td class="anugu-admin-litevspro-pro-column">
										<p class="features-full">
											<strong>All Advanced Features</strong>Albums, Ecommerce, Pagination, Deeplinking, and Expanded Gallery Configurations</p>
									</td>
								</tr>
								<tr class="anugu-admin-columns">
									<td class="anugu-admin-litevspro-first-column">
										<p>Anugu Gallery Addons 	</p>
									</td>
									<td class="anugu-admin-litevspro-lite-column">
										<p class="features-none">
											<strong>  No Addons Included 	</strong>
										</p>
									</td>
									<td class="anugu-admin-litevspro-pro-column">
										<p class="features-full">
											<strong> All Addons Included</strong>WooCommerce, Tags and Filters, Proofing, Schedule, Password Protection, Lightroom, Slideshows, Watermarking and more (28 total)            </p>
									</td>
								</tr>
								<tr class="anugu-admin-columns">
									<td class="anugu-admin-litevspro-first-column">
										<p>Customer Support	</p>
									</td>
									<td class="anugu-admin-litevspro-lite-column">
										<p class="features-none">
											<strong>Limited Customer Support</strong>
										</p>
									</td>
									<td class="anugu-admin-litevspro-pro-column">
										<p class="features-full">
											<strong> Priority Customer Support</strong>Dedicated prompt service via email from our top tier support team. Your request is assigned the highest priority</p>
									</td>
								</tr>
								
							</tbody>
						</table>

				</div>

				<div class="anugu-admin-litevspro-section anugu-admin-litevspro-section-hero">
					<div class="anugu-admin-about-section-hero-main no-border">
						<h3 class="call-to-action">
						<a href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( false, 'litevsprotab', 'getanugugalleryprotoday' ); ?>" target="_blank" rel="noopener noreferrer">Get Anugu Pro Today and Unlock all the Powerful Features					</a>
					</h3>

						<p>
							<strong>Bonus:</strong> Anugu Lite users get <span class="anugu-deal 20-percent-off">20% off regular price</span>, using the code in the link above.
						</p>
					</div>
				</div>

				</div>

					<?php $this->anugu_assets(); ?>

				</div>

			</div>

		</div> <!-- wrap -->


		<?php
	}

	/**
	 * Output the changelog screen.
	 *
	 * @since 1.8.1
	 */
	public function changelog_page() {

		?>

		<div class="anugu-welcome-wrap anugu-changelog">

			<div class="anugu-title">

				<?php self::welcome_text(); ?>

			</div>

			<?php $this->sidebar(); ?>

			<div class="anugu-welcome-main changelog-main">

				<?php self::tab_navigation( __METHOD__ ); ?>

				<div class="anugu-welcome-panel">

					<div class="wraps about-wsrap">

						<?php $this->return_changelog(); ?>

					</div>

				</div>

			</div>

		</div> <!-- wrap -->


		<?php
	}

	/**
	 * Changelog display.
	 *
	 * @since 1.8.1
	 */
	public function return_changelog() {
		?>

			<div id="changelog-anugu-gallery">

			<h3>1.8.4.5 (10-31-2018)</h3>
				<ul>
				<li>Fix: Issue w/ standalone function and older versions of the Album addon.</li>
				</ul>
				<h3>1.8.4.4 (10-31-2018)</h3>
				<ul>
				<li>Added: When Lightbox is not in infinite loop, first previous and last next arrows no longer appear.</li>
				<li>Fix: Gallery title shows in gallery toolbar instead of page title.</li>
				<li>Fix: Minor bug fixes.</li>
				</ul>
				<h3>1.8.4.3 (10-18-2018)</h3>
				<ul>
				<li>Fix: Resolved issues for installs using older PHP versions.</li>
				</ul>
				
			</div>

		<?php
	}

	/**
	 * Output the addon screen.
	 *
	 * @since 1.8.1
	 */
	public function addon_page() {
		?>

		<div class="anugu-welcome-wrap anugu-help">

			<div class="anugu-title">

				<?php self::welcome_text(); ?>

			</div>

			<?php $this->sidebar(); ?>

			<div class="anugu-get-started-main">

				<?php self::tab_navigation( __METHOD__ ); ?>

				<h3>Unlock More Addons</h3>

				<?php do_action('anugu_gallery_addons_section'); ?> 

			</div>

		</div>

		</div> <!-- wrap -->


		<?php
	}



	/**
	 * Returns a common row for posts from anugugallery.com.
	 *
	 * @since 1.8.5
	 */
	public function anugu_posts() {
		?>

			<div class="anugu-posts">

				<h3 class="title"><?php esc_html_e( 'Helpful Articles For Beginners:', 'anugu-gallery-lite' ); ?></h3>
				<div class="anugu-recent anuguthree-column">


					<div class="anugucolumn">
						<img class="post-image" src="https://anugugallery.com/wp-content/uploads/2016/11/How-to-Fix-Flipped-or-Upside-Down-Images-in-WordPress-1.png" />
						<h4 class="title"><?php esc_html_e( 'How to Fix Flipped or Upside Down Images in WordPress', 'anugu-gallery-lite' ); ?></h4>
						<?php /* Translators: %s */ ?>
						<p><?php printf( esc_html__( 'Do the images you upload to WordPress appear flipped? In this tutorial, we will show you how to fix flipped or upside down images in WordPress. %s', 'anugu-gallery-lite' ), '<a href="https://anugugallery.com/how-to-fix-flipped-or-upside-down-images-in-wordpress/" target="_blank">Read More</a>' ); ?></p>
					</div>

					<div class="anugucolumn">
						<img class="post-image" src="https://anugugallery.com/wp-content/uploads/2017/12/best-photo-editing-software-for-photographers.jpg" />
						<h4 class="title"><?php esc_html_e( '17 Best Photo Editing Software for Photographers', 'anugu-gallery-lite' ); ?></h4>
						<?php /* Translators: %s */ ?>
						<p><?php printf( esc_html__( 'Are you looking for professional photo editing software for your photos on Mac or Windows? In this guide, we will share the best photo editing software for photographers. %s', 'anugu-gallery-lite' ), '<a href="https://anugugallery.com/best-photo-editing-software-for-photographers/" target="_blank">Read More</a>' ); ?></p>
					</div>

					<div class="anugucolumn">
						<img class="post-image" src="https://anugugallery.com/wp-content/uploads/2018/09/vidoe-gallery.jpg" />
						<h4 class="title"><?php esc_html_e( 'Announcing New Video Integrations', 'anugu-gallery-lite' ); ?></h4>
						<?php /* Translators: %s */ ?>
						<p><?php printf( esc_html__( 'We’re pleased to introduce our expanded video gallery support options for Anugu Gallery 1.8.1. More video platform integrations allow you to add more video sources for your galleries. %s', 'anugu-gallery-lite' ), '<a href="https://anugugallery.com/announcing-new-video-integrations/" target="_blank">Read More</a>' ); ?></p>
					</div>


				</div>

			</div>

		<?php
	}


	/**
	 * Returns a common footer
	 *
	 * @since 1.8.5
	 */
	public function anugu_assets() {
		?>

		<div class="anugu-assets">
			<p>
				<?php esc_html_e( 'Learn more:', 'anugu-gallery-lite' ); ?>&nbsp;<a href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( "https://anugugallery.com/blog/", "learnmore", "blog", "" ); ?>"><?php esc_html_e( 'Blog', 'anugu-gallery-lite' ); ?></a>
				&bullet; <a href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( "https://anugugallery.com/docs/", "learnmore", "documentation", "" ); ?>"><?php esc_html_e( 'Documentation', 'anugu-gallery-lite' ); ?></a>
			<?php /* &bullet; <a href="https://anugugallery.com/dev/"><?php _ex( 'Development Blog', 'About screen, link to development blog', 'anugu-gallery-lite' ); ?></a> */ ?>
			</p>

			<div class="icons-container">
				<div class="label"><?php esc_html_e( 'Social:', 'anugu-gallery-lite' ); ?></div>

				<ul class="social-icons">
					<li class="facebook">
						<a href="http://facebook.com/anugugallery" title="Facebook" target="_blank" class="facebook">
							Facebook</a>
					</li>
					<li class="twitter">
						<a href="http://twitter.com/anugugallery" title="Twitter" target="_blank" class="twitter">
							Twitter</a>
					</li>
					<li class="youtube">
						<a href="http://youtube.com/anugugallery" title="YouTube" target="_blank" class="youtube">
							YouTube</a>
					</li>
					<li class="pinterest">
						<a href="https://www.pinterest.com/anugugallery/" title="Pinterest" target="_blank" class="pinterest">
							Pinterest</a>
					</li>
					<li class="instagram">
						<a href="http://instagram.com/anugugallery" title="Instagram" target="_blank" class="instagram">
							Instagram</a>
					</li>
				</ul>

			</div>

			<p>

				<?php esc_html_e( 'Also by us: ', 'anugu-gallery-lite' ); ?>

				<a target="_blank" href="<?php echo Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( "http://soliloquywp.com", "alsobyus", "soliloquyslider", "" ); ?>"><?php esc_html_e( 'Soliloquy Slider', 'anugu-gallery-lite' ); ?></a>

			</p>

		</div>

		<?php
	}

	/**
	 * Return true/false based on whether a query argument is set.
	 *
	 * @return bool
	 */
	public static function is_new_install() {

		if ( get_transient( '_anugu_is_new_install' ) ) {
			delete_transient( '_anugu_is_new_install' );
			return true;
		}

		if ( isset( $_GET['is_new_install'] ) && 'true' === strtolower( sanitize_text_field( wp_unslash( $_GET['is_new_install'] ) ) ) ) { // @codingStandardsIgnoreLine
			return true;
		} elseif ( isset( $_GET['is_new_install'] ) ) { // @codingStandardsIgnoreLine
			return false;
		}

	}

	/**
	 * Return a user-friendly version-number string, for use in translations.
	 *
	 * @since 2.2.0
	 *
	 * @return string
	 */
	public static function display_version() {

		return ANUGU_VERSION;

	}


}

$anugu_welcome = new Anugu_Welcome;