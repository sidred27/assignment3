<?php
/**
 * Addons class.
 *
 * @since 1.5.0
 *
 * @package Anugu_Gallery
 * @author  David Bisset
 */
class Anugu_Gallery_Addons {

    /**
     * Holds the class object.
     *
     * @since 1.5.0
     *
     * @var object
     */
    public static $instance;

    /**
     * Path to the file.
     *
     * @since 1.5.0
     *
     * @var string
     */
    public $file = __FILE__;

    /**
     * Holds the base class object.
     *
     * @since 1.5.0
     *
     * @var object
     */
    public $base;

    /**
     * Holds the submenu pagehook.
     *
     * @since 1.5.0
     *
     * @var string
     */
    public $hook;
	public $key;
    /**
     * Primary class constructor.
     *
     * @since 1.5.0
     */
    public function __construct() {

        // Load the base class object.
        $this->base = Anugu_Gallery_Lite::get_instance();

        // Add custom addons submenu.
        add_action( 'admin_menu', array( $this, 'admin_menu' ), 12 );

        // Add callbacks for addons tabs.
        add_action( 'anugu_gallery_addons_section', array( $this, 'addons_content' ) );

        // Add the addons menu item to the Plugins table.
        add_filter( 'plugin_action_links_' . plugin_basename( $this->base->file ), array( $this, 'addons_link' ) );

    }

    /**
     * Register the Addons submenu item for Anugu.
     *
     * @since 1.5.0
     */
    public function admin_menu() {

        // Register the submenu.
        $this->hook = add_submenu_page(
            'edit.php?post_type=anugu',
            __( 'Anugu Gallery Addons', 'anugu-gallery-lite' ),
            '<span style="color:#7cc048"> ' . __( 'Addons', 'anugu-gallery-lite' ) . '</span>',
            apply_filters( 'anugu_gallery_menu_cap', 'manage_options' ),
            $this->base->plugin_slug . '-addons',
            array( $this, 'addons_page' )
        );

        // If successful, load admin assets only on that page and check for addons refresh.
        if ( $this->hook ) {
            add_action( 'load-' . $this->hook, array( $this, 'maybe_refresh_addons' ) );
            add_action( 'load-' . $this->hook, array( $this, 'addons_page_assets' ) );
        }

    }

    /**
     * Maybe refreshes the addons page.
     *
     * @since 1.5.0
     *
     * @return null Return early if not refreshing the addons.
     */
    public function maybe_refresh_addons() {

        if ( ! $this->is_refreshing_addons() ) {
            return;
        }

        if ( ! $this->refresh_addons_action() ) {
            return;
        }

        $this->get_addons_data( $this->base->get_license_key() );

    }

    /**
     * Loads assets for the addons page.
     *
     * @since 1.5.0
     */
    public function addons_page_assets() {

        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

    }

    /**
     * Register and enqueue addons page specific CSS.
     *
     * @since 1.5.0
     */
    public function enqueue_admin_styles() {

        wp_register_style( $this->base->plugin_slug . '-addons-style', plugins_url( 'assets/css/addons.css', $this->base->file ), array(), $this->base->version );
        wp_enqueue_style( $this->base->plugin_slug . '-addons-style' );

        // Run a hook to load in custom styles.
        do_action( 'anugu_gallery_addons_styles' );

    }

    /**
     * Register and enqueue addons page specific JS.
     *
     * @since 1.5.0
     */
    public function enqueue_admin_scripts() {

        // List.js
        wp_register_script( $this->base->plugin_slug . '-list-script', plugins_url( 'assets/js/min/list-min.js', $this->base->file ), array( 'jquery' ), $this->base->version, true );
        wp_enqueue_script( $this->base->plugin_slug . '-list-script' );

        // Addons
        wp_register_script( $this->base->plugin_slug . '-addons-script', plugins_url( 'assets/js/addons.js', $this->base->file ), array( 'jquery' ), $this->base->version, true );
        wp_enqueue_script( $this->base->plugin_slug . '-addons-script' );
        wp_localize_script(
            $this->base->plugin_slug . '-addons-script',
            'anugu_gallery_addons',
            array(
                'activate_nonce'      => wp_create_nonce( 'anugu-gallery-activate' ),
                'active'           => __( 'Status: Active', 'anugu-gallery-lite' ),
                'activate'         => __( 'Activate', 'anugu-gallery-lite' ),
                'get_addons_nonce'   => wp_create_nonce( 'anugu-gallery-get-addons' ),
                'activating'       => __( 'Activating...', 'anugu-gallery-lite' ),
                'ajax'             => admin_url( 'admin-ajax.php' ),
                'deactivate'       => __( 'Deactivate', 'anugu-gallery-lite' ),
                'deactivate_nonce' => wp_create_nonce( 'anugu-gallery-deactivate' ),
                'deactivating'     => __( 'Deactivating...', 'anugu-gallery-lite' ),
                'inactive'         => __( 'Status: Inactive', 'anugu-gallery-lite' ),
                'install'          => __( 'Install', 'anugu-gallery-lite' ),
                'install_nonce'    => wp_create_nonce( 'anugu-gallery-install' ),
                'installing'       => __( 'Installing...', 'anugu-gallery-lite' ),
                'proceed'          => __( 'Proceed', 'anugu-gallery-lite' ),
            )
        );

        // Run a hook to load in custom scripts.
        do_action( 'anugu_gallery_addons_scripts' );

    }

    /**
     * Callback to output the Anugu addons page.
     *
     * @since 1.5.0
     */
    public function addons_page() {

        do_action('anugu_head');
        ?>

        <div id="addon-heading" class="subheading clearfix">
            <h1><?php esc_html_e( 'Anugu Gallery Addons', 'anugu-gallery-lite' ); ?></h1>
            <form id="add-on-search">
                <span class="spinner"></span>
                <input id="add-on-searchbox" name="anugu-addon-search" value="" placeholder="<?php _e( 'Search Anugu Addons', 'anugu-gallery-lite' ); ?>" />
                <select id="anugu-filter-select">
                    <option value="sort-order"><?php esc_html_e( 'Most Popular', 'anugu-gallery-lite' ); ?></option>
                    <option value="asc"><?php esc_html_e( 'Sort Ascending (A-Z)', 'anugu-gallery-lite' ); ?></option>
                    <option value="desc"><?php esc_html_e( 'Sort Descending (Z-A)', 'anugu-gallery-lite' ); ?></option>
                </select>
            </form>
        </div>

        <div id="anugu-gallery-addons" class="wrap">
            <div class="anugu-gallery anugu-clear">
                <?php do_action( 'anugu_gallery_addons_section' ); ?>
            </div>
        </div>
        <?php

    }

    /**
     * Callback for displaying the UI for Addons.
     *
     * @since 1.5.0
     */
    public function addons_content() {

        // If error(s) occured during license key verification, display them and exit now.
        if ( false !== $this->base->get_license_key_errors() ) {
            ?>
            <div class="error below-h2">
                <p>
                    <?php esc_html_e( 'In order to get access to Addons, you need to resolve your license key errors.', 'anugu-gallery-lite' ); ?>
                </p>
            </div>
            <?php
            return;
        }

        // Get Addons
        $addons = $this->get_addons();

        // If no Addon(s) were returned, our API call returned an error.
        // Show an error message with a button to reload the page, which will trigger another API call.
        if ( ! $addons ) {
            ?>
            <form id="anugu-addons-refresh-addons-form" method="post">
                <p>
                    <?php _e( 'There was an issue retrieving the addons for this site. Please click on the button below the refresh the addons data.', 'anugu-gallery-lite' ); ?>
                </p>
                <p>
                    <a href="<?php echo $_SERVER['REQUEST_URI']; ?>" class="button button-primary"><?php esc_html_e( 'Refresh Addons', 'anugu-gallery-lite' ); ?></a>
                </p>
            </form>
            <?php
            return;
        }

        // If here, we have Addons to display, so let's output them now.
        // Get installed plugins and upgrade URL
        $installed_plugins = get_plugins();
        $upgrade_url = Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link();
        ?>
        <div id="anugu-addons">
            <?php
            // Output Addons the User is licensed to use.
            if ( count( $addons['licensed'] )> 0 ) {

                // sort by sort order (Popular) first by default.
                usort( $addons['licensed'], array( $this, 'sort_data_by_sort_order' ) );

                ?>
                <div id="anugu-addons-area-license" class="anugu-addons-area licensed" class="anugu-clear">
                    <h3><?php _e( 'Available Addons', 'anugu-gallery-lite' ); ?></h3>

                    <div id="anugu-addons-licensed" class="anugu-addons">
                        <!-- list container class required for list.js -->
                        <div class="list">
                            <?php
                            foreach ( (array) $addons['licensed'] as $i => $addon ) {
                                $this->get_addon_card( $addon, $i, true, $installed_plugins );
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
            } // Close licensed addons

            // Output Addons the User isn't licensed to use.
            if ( count( $addons['unlicensed'] )> 0 ) {

                // sort by sort order (Popular) first by default.
                usort( $addons['unlicensed'], array( $this, 'sort_data_by_sort_order' ) );

                ?>
                <div id="anugu-addons-area-unlicensed" class="anugu-addons-area unlicensed" class="anugu-clear">
                    <h3><?php _e( 'Unlock More Addons', 'anugu-gallery-lite' ); ?></h3>
                    <p><?php echo sprintf( __( '<strong>Want even more addons?</strong> <a href="%s">Upgrade your Anugu Gallery account</a> and unlock the following addons.', 'anugu-gallery-lite' ), $upgrade_url ); ?></p>

                    <div id="anugu-addons-unlicensed" class="anugu-addons">                       
                        <?php
                        foreach ( (array) $addons['unlicensed'] as $i => $addon ) {
                            $this->get_addon_card( $addon, $i, false, $installed_plugins );
                        }
                        ?>
                    </div>
                </div>
                <?php
            } // Close unlicensed addons
            ?>
        </div>
        <?php

    }

    /**
     * Sorts by Sort order.
     *
     * @since 1.5.0
     *
     * @param   array   $a    First element.
     * @param   array   $b    First element.
     */
    public function sort_data_by_sort_order( $a, $b ) {
        return $b->sort_order - $a->sort_order;
    }

    /**
     * Retrieves addons from the stored transient or remote server.
     *
     * @since 1.5.0
     *
     * @return bool | array    false | Array of licensed and unlicensed Addons.
     */
    public function get_addons() {

        // Get license key and type.
        $key = $this->base->get_license_key();
        $type = $this->base->get_license_key_type();

        // Get addons data from transient or perform API query if no transient.
        //if ( false === ( $addons = get_transient( '_eg_addons' ) ) ) {
            $addons = $this->get_addons_data( $key );
        //}

        // If no Addons exist, return false
        if ( ! $addons ) {
            return false;
        }

        // Iterate through Addons, to build two arrays:
        // - Addons the user is licensed to use,
        // - Addons the user isn't licensed to use.
        $results = array(
            'licensed'  => array(),
            'unlicensed'=> array(),
        );
        foreach ( (array) $addons as $i => $addon ) {

            // Determine whether the user is licensed to use this Addon or not.
            if (
                empty( $type ) ||
                ( in_array( 'advanced', $addon->categories ) && $type != 'gold' && $type != 'platinum' ) ||
                ( in_array( 'basic', $addon->categories ) && ( $type != 'silver' && $type != 'gold' && $type != 'platinum' ) )
            ) {
                // Unlicensed
                $results['unlicensed'][] = $addon;
                continue;
            }

            // Licensed
            $results['licensed'][] = $addon;

        }

        // Return Addons, split by licensed and unlicensed.
        return $results;

    }

    /**
     * Pings the remote server for addons data.
     *
     * @since 1.5.0
     *
     * @param   string      $key    The user license key.
     * @return  array               Array of addon data otherwise.
     */
    public function get_addons_data( $key ) {

		$this->key = $key;

        // Get Addons
        // If the key is valid, we'll get personalised upgrade URLs for each Addon (if necessary) and plugin update information.
        $addons = $this->perform_remote_request( 'get-addons-data-v15', array( 'tgm-updater-key' => $key ) );

        // If there was an API error, set transient for only 10 minutes.
        if ( ! $addons ) {
            set_transient( '_eg_addons', false, 10 * MINUTE_IN_SECONDS );
            return false;
        }

        // If there was an error retrieving the addons, set the error.
        if ( isset( $addons->error ) ) {
            set_transient( '_eg_addons', false, 10 * MINUTE_IN_SECONDS );
            return false;
        }

        // Otherwise, our request worked. Save the data and return it.
        set_transient( '_eg_addons', $addons, DAY_IN_SECONDS );
        return $addons;

    }

    /**
     * Flag to determine if addons are being refreshed.
     *
     * @since 1.5.0
     *
     * @return bool True if being refreshed, false otherwise.
     */
    public function is_refreshing_addons() {

        return isset( $_POST['anugu-gallery-refresh-addons-submit'] );

    }

    /**
     * Verifies nonces that allow addon refreshing.
     *
     * @since 1.5.0
     *
     * @return bool True if nonces check out, false otherwise.
     */
    public function refresh_addons_action() {

        return isset( $_POST['anugu-gallery-refresh-addons-submit'] ) && wp_verify_nonce( $_POST['anugu-gallery-refresh-addons'], 'anugu-gallery-refresh-addons' );

    }

    /**
     * Retrieve the plugin basename from the plugin slug.
     *
     * @since 1.5.0
     *
     * @param string $slug The plugin slug.
     * @return string      The plugin basename if found, else the plugin slug.
     */
    public function get_plugin_basename_from_slug( $slug ) {

        $keys = array_keys( get_plugins() );

        foreach ( $keys as $key ) {
            if ( preg_match( '|^' . $slug . '|', $key ) ) {
                return $key;
            }
        }

        return $slug;

    }

    /**
     * Add Addons page to plugin action links in the Plugins table.
     *
     * @since 1.5.0
     *
     * @param   array   $links    Default plugin action links.
     * @return  array   $links    Amended plugin action links.
     */
    public function addons_link( $links ) {

        $addons_link = sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( array( 'post_type' => 'anugu', 'page' => 'anugu-gallery-lite-addons' ), admin_url( 'edit.php' ) ) ), __( 'Addons', 'anugu-gallery-lite' ) );
        array_unshift( $links, $addons_link );

        return $links;

    }

    /**
     * Outputs the addon "box" on the addons page.
     *
     * @since 1.5.0
     *
     * @param   object  $addon              Addon data from the API / transient call
     * @param   int     $counter            Index of this Addon in the collection
     * @param   bool    $is_licensed        Whether the Addon is licensed for use
     * @param   array   $installed_plugins  Installed WordPress Plugins
     */
    public function get_addon_card( $addon, $counter = 0, $is_licensed = false, $installed_plugins = false ) {

        // Setup some vars
        $plugin_basename   = $this->get_plugin_basename_from_slug( $addon->slug );
        $categories = implode( ',', $addon->categories );
        if ( ! $installed_plugins ) {
            $installed_plugins = get_plugins();
        }

        // If the Addon doesn't supply an upgrade_url key, it's because the user hasn't provided a license
        // get_upgrade_link() will return the Lite or Pro link as necessary for us.
        if ( ! isset( $addon->upgrade_url ) ) {
            $addon->upgrade_url = Anugu_Gallery_Common_Admin::get_instance()->get_upgrade_link( false, 'addonspage', str_replace( '-', '', str_replace( 'anugu-', '', $addon->slug ) ) . 'addonupgradenowbutton' );
        }

        $sort_order   = isset( $addon->sort_order ) && false !== $addon->sort_order ? intval( $addon->sort_order ) : 0;
        $most_popular = isset( $addon->most_popular ) && false !== $addon->most_popular ? true : false;

        // Output the card
        ?>
        <div class="anugu-addon" data-addon-title="<?php echo esc_html( $addon->title ); ?>" data-sort-order="<?php echo ( $sort_order ); ?>">
            <?php if ( $most_popular ) { ?>
                <div class="addon-tag">Most Popular</div>
            <?php } ?>
            <h3 class="anugu-addon-title"><?php echo esc_html( $addon->title ); ?></h3>
            <?php
            if ( ! empty( $addon->image ) ) {
                ?>
                <img class="anugu-addon-thumb" src="<?php echo esc_url( $addon->image ); ?>" alt="<?php echo esc_attr( $addon->title ); ?>" />
                <?php
            }
            ?>

            <p class="anugu-addon-excerpt"><?php echo esc_html( $addon->excerpt ); ?></p>

            <?php
            // If the Addon is unlicensed, show the upgrade button
            if ( ! $is_licensed ) {
                ?>
                <div class="anugu-addon-active anugu-addon-message">
                    <div class="interior">
                        <div class="anugu-addon-upgrade">
                            <a href="<?php echo esc_url( $addon->upgrade_url ); ?>" target="_blank" class="button button-primary anugu-addon-upgrade-button"  rel="<?php echo esc_attr( $plugin_basename ); ?>">
                                <?php _e( 'Upgrade Now', 'anugu-gallery-lite' ); ?>
                            </a>
                            <span class="spinner anugu-gallery-spinner"></span>
                        </div>
                    </div>
                </div>
                <?php
            } else {
                // Addon is licensed

                // If the plugin is not installed, display an install message and button.
                if ( ! isset( $installed_plugins[ $plugin_basename ] ) ) {
                    ?>
                    <div class="anugu-addon-not-installed anugu-addon-message">
                        <div class="interior">
                            <span class="addon-status"><?php _e( 'Status: <span>Not Installed</span>', 'anugu-gallery-lite' ); ?></span>
                            <div class="anugu-addon-action">
                                <a class="button button-primary anugu-addon-action-button anugu-install-addon" href="#" rel="<?php echo esc_url( $addon->url ); ?>">
                                    <i class="anugu-cloud-download"></i>
                                    <?php _e( 'Install', 'anugu-gallery-lite' ); ?>
                                </a>
                                <span class="spinner anugu-gallery-spinner"></span>
                            </div>
                        </div>
                    </div>
                    <?php
                } else {
                    // Plugin is installed.
                    if ( is_plugin_active( $plugin_basename ) ) {
                        // Plugin is active. Display the active message and deactivate button.
                        ?>
                        <div class="anugu-addon-active anugu-addon-message">
                            <div class="interior">
                                <span class="addon-status"><?php _e( 'Status: <span>Active</span>', 'anugu-gallery-lite' ); ?></span>
                                <div class="anugu-addon-action">
                                    <a class="button button-primary anugu-addon-action-button anugu-deactivate-addon" href="#" rel="<?php echo esc_attr( $plugin_basename ); ?>">
                                        <i class="anugu-toggle-on"></i>
                                        <?php _e( 'Deactivate', 'anugu-gallery-lite' ); ?>
                                    </a>
                                    <span class="spinner anugu-gallery-spinner"></span>
                                </div>
                            </div>
                        </div>
                        <?php
                    } else {
                        // Plugin is inactivate. Display the inactivate mesage and activate button.
                        ?>
                        <div class="anugu-addon-inactive anugu-addon-message">
                            <div class="interior">
                                <span class="addon-status"><?php _e( 'Status: <span>Inactive</span>', 'anugu-gallery-lite' ); ?></span>
                                <div class="anugu-addon-action">
                                    <a class="button button-primary anugu-addon-action-button anugu-activate-addon" href="#" rel="<?php echo esc_attr( $plugin_basename ); ?>">
                                        <i class="anugu-toggle-on"></i>
                                        <?php _e( 'Activate', 'anugu-gallery-lite' ); ?>
                                    </a>
                                    <span class="spinner anugu-gallery-spinner"></span>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
            }
            ?>
        </div>
        <?php

    }
    /**
     * Queries the remote URL via wp_remote_post and returns a json decoded response.
     *
     * @since 1.0.0
     *
     * @param string $action        The name of the $_POST action var.
     * @param array $body           The content to retrieve from the remote URL.
     * @param array $headers        The headers to send to the remote URL.
     * @param string $return_format The format for returning content from the remote URL.
     * @return string|bool          Json decoded response on success, false on failure.
     */
    public function perform_remote_request( $action, $body = array(), $headers = array(), $return_format = 'json' ) {

        // Build the body of the request.
        $body = wp_parse_args(
            $body,
            array(
                'tgm-updater-action'     => $action,
                'tgm-updater-key'        => $this->key,
                'tgm-updater-wp-version' => get_bloginfo( 'version' ),
                'tgm-updater-referer'    => site_url()
            )
        );
        $body = http_build_query( $body, '', '&' );

        // Build the headers of the request.
        $headers = wp_parse_args(
            $headers,
            array(
                'Content-Type'   => 'application/x-www-form-urlencoded',
                'Content-Length' => strlen( $body )
            )
        );

        // Setup variable for wp_remote_post.
        $post = array(
            'headers' => $headers,
            'body'    => $body
        );

        // Perform the query and retrieve the response.
        $response      = wp_remote_post( 'https://anugugallery.com', $post );
        $response_code = wp_remote_retrieve_response_code( $response );
        $response_body = wp_remote_retrieve_body( $response );

        // Bail out early if there are any errors.
        if ( 200 != $response_code || is_wp_error( $response_body ) ) {
            return false;
        }

        // Return the json decoded content.
        return json_decode( $response_body );

    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.5.0
     *
     * @return object The Anugu_Gallery_Addons object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Anugu_Gallery_Addons ) ) {
            self::$instance = new Anugu_Gallery_Addons();
        }

        return self::$instance;

    }

}

// Load the addons class.
$anugu_gallery_addons = Anugu_Gallery_Addons::get_instance();