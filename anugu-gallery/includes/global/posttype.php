<?php
/**
 * Posttype class.
 *
 * @since 1.0.0
 *
 * @package Anugu_Gallery
 * @author  Anugu Gallery Team
 */
class Anugu_Gallery_Posttype {

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

        // Build the labels for the post type.
        $labels =  array(
            'name'               => __( 'Galleries', 'anugu-gallery-lite' ),
            'singular_name'      => __( 'Gallery', 'anugu-gallery-lite' ),
            'add_new'            => __( 'Add New', 'anugu-gallery-lite' ),
            'add_new_item'       => __( 'Add New Gallery', 'anugu-gallery-lite' ),
            'edit_item'          => __( 'Edit Gallery', 'anugu-gallery-lite' ),
            'new_item'           => __( 'New Gallery', 'anugu-gallery-lite' ),
            'view_item'          => __( 'View Gallery', 'anugu-gallery-lite' ),
            'search_items'       => __( 'Search Galleries', 'anugu-gallery-lite' ),
            'not_found'          => __( 'No galleries found.', 'anugu-gallery-lite' ),
            'not_found_in_trash' => __( 'No galleries found in trash.', 'anugu-gallery-lite' ),
            'parent_item_colon'  => '',
            'menu_name'          => __( 'Anugu Gallery', 'anugu-gallery-lite' ),
        );
        $labels = apply_filters( 'anugu_gallery_post_type_labels', $labels );

        // Build out the post type arguments.
        $args = array(
            'labels'              => $labels,
            'public'              => false,
            'exclude_from_search' => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_admin_bar'   => true,
            'rewrite'             => false,
			'query_var'           => false,
			'show_in_rest'        => true,
			'rest_base'           => 'anugu-gallery',
            'menu_position'       => apply_filters( 'anugu_gallery_post_type_menu_position', 247 ),
            'menu_icon'           => plugins_url( 'assets/css/images/menu-icon@2x.png', $this->base->file ),
            'supports'            => array( 'title' ),
        );

        // Filter arguments.
        $args = apply_filters( 'anugu_gallery_post_type_args', $args );

        // Register the post type with WordPress.
        register_post_type( 'anugu', $args );

        // Change the names of items in the CPT menu.
        add_filter( '_admin_menu', array( $this, 'custom_menu_order' ) );

    }


    /**
     * Allows us to rename 'Anugu Galleries' to 'Galleries' without creating a submenu or fancy CSS tricks.
     *
     * @since 1.0.0
     *
     */
	function custom_menu_order( ){

        global $submenu;

        foreach ( $submenu as $item => $item_submenu ) {
            if ( 'edit.php?post_type=anugu' !== $item ) {
                continue;
            }
            foreach ( $item_submenu as $index => $item_submenu_info ) {
                if ( $item_submenu_info[0] == 'Anugu Galleries' || $item_submenu_info[0] == 'Anugu Gallery' ) {
                    $submenu[ $item][ $index ][0] = 'All Galleries';
                }
            }
        }
    }


    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Anugu_Gallery_Posttype object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Anugu_Gallery_Posttype ) ) {
            self::$instance = new Anugu_Gallery_Posttype();
        }

        return self::$instance;

    }

}

// Load the posttype class.
$anugu_gallery_posttype = Anugu_Gallery_Posttype::get_instance();