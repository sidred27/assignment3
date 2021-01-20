<?php
/**
 * Posttype admin class.
 *
 * @since 1.0.0
 *
 * @package Anugu_Gallery
 * @author  Anugu Gallery Team
 */
class Anugu_Gallery_Posttype_Admin {

    /**
     * Holds the class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public static $instance;

    /**
     * Path to the file.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $file = __FILE__;

    /**
     * Holds the base class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public $base;

    /**
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {

        // Load the base class object.
        $this->base = Anugu_Gallery_Lite::get_instance();
        $this->metabox = Anugu_Gallery_Metaboxes::get_instance();

        // Update post type messages.
        add_filter( 'post_updated_messages', array( $this, 'messages' ) );

        // Force the menu icon to be scaled to proper size (for Retina displays).
        add_action( 'admin_head', array( $this, 'menu_icon' ) );

        // Add the Universal Header.
        add_action( 'in_admin_header', array( $this, 'admin_header' ), 100 );

    }

    /**
     * Outputs the Anugu Gallery Header.
     *
     * @since 1.5.0
     */
    public function admin_header() {
        
        // Get the current screen, and check whether we're viewing the Anugu or Anugu Album Post Types.
        
        if ( 'anugu' !== $screen->post_type ) {
            return;
        }

        // If here, we're on an Anugu Gallery or Album screen, so output the header.
        $this->base->load_admin_partial( 'header', array(
            'logo' => plugins_url(   ),
        ) );

    }

    /**
     * Contextualizes the post updated messages.
     *
     * @since 1.0.0
     *
     * @global object $post    The current post object.
     * @param array $messages  Array of default post updated messages.
     * @return array $messages Amended array of post updated messages.
     */
    public function messages( $messages ) {

        global $post;

        // Contextualize the messages.
        $anugu_messages = array(
            0  => '',
            1  => __( 'Anugu gallery updated.', 'anugu-gallery-lite' ),
            2  => __( 'Anugu gallery custom field updated.', 'anugu-gallery-lite' ),
            3  => __( 'Anugu gallery custom field deleted.', 'anugu-gallery-lite' ),
            4  => __( 'Anugu gallery updated.', 'anugu-gallery-lite' ),
            5  => isset( $_GET['revision'] ) ? sprintf( __( 'Anugu gallery restored to revision from %s.', 'anugu-gallery-lite' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
            6  => __( 'Anugu gallery published.', 'anugu-gallery-lite' ),
            7  => __( 'Anugu gallery saved.', 'anugu-gallery-lite' ),
            8  => __( 'Anugu gallery submitted.', 'anugu-gallery-lite' ),
            9  => sprintf( __( 'Anugu gallery scheduled for: <strong>%1$s</strong>.', 'anugu-gallery-lite' ), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ) ),
            10 => __( 'Anugu gallery draft updated.', 'anugu-gallery-lite' ),
        );
        $messages['anugu'] = apply_filters( 'anugu_gallery_messages', $anugu_messages );

        return $messages;

    }

    /**
     * Forces the Anugu menu icon width/height for Retina devices.
     *
     * @since 1.0.0
     */
    public function menu_icon() {

        ?>
        <style type="text/css">#menu-posts-anugu .wp-menu-image img { width: 16px; height: 16px; }</style>
        <?php

    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Anugu_Gallery_Posttype_Admin object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Anugu_Gallery_Posttype_Admin ) ) {
            self::$instance = new Anugu_Gallery_Posttype_Admin();
        }

        return self::$instance;

    }



}

// Load the posttype admin class.
$anugu_gallery_posttype_admin = Anugu_Gallery_Posttype_Admin::get_instance();