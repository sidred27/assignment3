<?php
/**
 * Plugin Name: Anugu Gallery
 * Description: This Plugin is developed for Assignment 3 of Content Management Systems and the use of this plugin is to create a gallery view and upload images into it. It uses Php 5.3.
 * Author:      Siddharth Reddy Anugu
 * Author URI:  https://www.linkedin.com/in/siddharth-reddy-anugu/
 * Version:     1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Anugu_Gallery_Lite {


	public static $instance;

	public $version = '1.0';

	public $plugin_name = 'Anugu Gallery Lite';

	
	public $plugin_slug = 'anugu-gallery-lite';


	public $file = __FILE__;

	
	public function __construct() {

	
		do_action( 'anugu_gallery_pre_init' );

	
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );

	
		add_action( 'init', array( $this, 'init' ), 0 );

		if ( ! defined( 'ANUGU_VERSION' ) ) {

			define( 'ANUGU_VERSION', $this->version );

		}

		if ( ! defined( 'ANUGU_SLUG' ) ) {

			define( 'ANUGU_SLUG', $this->plugin_slug );

		}

		if ( ! defined( 'ANUGU_FILE' ) ) {

			define( 'ANUGU_FILE', $this->file );

		}

		if ( ! defined( 'ANUGU_DIR' ) ) {

			define( 'ANUGU_DIR', plugin_dir_path( __FILE__ ) );

		}

		if ( ! defined( 'ANUGU_URL' ) ) {

			define( 'ANUGU_URL', plugin_dir_url( __FILE__ ) );

		}

	}

	
	public function load_plugin_textdomain() {

		load_plugin_textdomain( 'anugu-gallery-lite' );

	}


	public function init() {

	
        if ( class_exists( 'Anugu_Gallery' ) ) {
            return;
        }

     
        do_action( 'anugu_gallery_lite_init' );

	
		$this->require_global();

     
        if ( is_admin() ) {
            $this->require_admin();
        }

	
		do_action( 'anugu_gallery_lite_loaded' );

	}


	public function require_admin() {

		require plugin_dir_path( __FILE__ ) . 'includes/admin/addons.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/common.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/editor.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/media.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/media-view.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/metaboxes.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/notice.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/posttype.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/table.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/review.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/gutenberg.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/subscribe.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/promotion.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/welcome.php';

	}

	public function load_admin_partial( $template, $data = array() ){

		$dir = trailingslashit( plugin_dir_path( __FILE__ ) . 'includes/admin/partials' );

		if ( file_exists( $dir . $template . '.php' ) ) {
			require_once(  $dir . $template . '.php' );
			return true;
		}

		return false;

	}

	
	public function require_global() {

		require plugin_dir_path( __FILE__ ) . 'includes/global/common.php';
		require plugin_dir_path( __FILE__ ) . 'includes/global/posttype.php';
		require plugin_dir_path( __FILE__ ) . 'includes/global/shortcode.php';
		require plugin_dir_path( __FILE__ ) . 'includes/global/rest.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/ajax.php';

	}

	
	public function get_gallery( $id ) {

		
		if ( false === ( $gallery = get_transient( '_eg_cache_' . $id ) ) ) {
			$gallery = $this->_get_gallery( $id );
			if ( $gallery ) {
				$expiration = Anugu_Gallery_Common::get_instance()->get_transient_expiration_time();
				set_transient( '_eg_cache_' . $id, $gallery, $expiration );
			}
		}

		
		return $gallery;

	}

	
	public function _get_gallery( $id ) {

		$meta = get_post_meta( $id, '_eg_gallery_data', true );

		
		if ( empty( $meta ) ) {
			$gallery_id = get_post_meta( $id, '_eg_gallery_id', true );
			$meta = get_post_meta( $gallery_id, '_eg_gallery_data', true );
		}

		return $meta;

	}

	
	public function get_gallery_image_count( $id ) {

		$gallery = $this->get_gallery( $id );
	    return isset( $gallery['gallery'] ) ? count( $gallery['gallery'] ) : 0;

	}

	
	public function get_gallery_by_slug( $slug ) {

		
		if ( false === ( $gallery = get_transient( '_eg_cache_' . $slug ) ) ) {
			$gallery = $this->_get_gallery_by_slug( $slug );
			if ( $gallery ) {
				$expiration = Anugu_Gallery_Common::get_instance()->get_transient_expiration_time();
				set_transient( '_eg_cache_' . $slug, $gallery, $expiration );
			}
		}

		
		return $gallery;

	}

	
	public function _get_gallery_by_slug( $slug ) {

		
		$galleries = new WP_Query( array(
			'post_type'    => 'anugu',
			'name'              => $slug,
			'fields'        => 'ids',
			'posts_per_page' => 1,
		) );
		if ( $galleries->posts ) {
			return get_post_meta( $galleries->posts[0], '_eg_gallery_data', true );
		}

		
		$galleries = new WP_Query( array(
			'post_type'     => 'anugu',
			'no_found_rows' => true,
			'cache_results' => false,
			'fields'        => 'ids',
			'meta_query'    => array(
				array(
					'key'     => '_eg_gallery_old_slug',
					'value'   => $slug,
				),
			),
			'posts_per_page' => 1,
		) );
		if ( $galleries->posts ) {
			return get_post_meta( $galleries->posts[0], '_eg_gallery_data', true );
		}

	
		return false;

	}

	
	public function get_galleries( $skip_empty = true, $ignore_cache = false, $search_terms = '' ) {

		
		if ( $ignore_cache || ! empty( $search_terms ) || false === ( $galleries = get_transient( '_eg_cache_all' ) ) ) {
			$galleries = $this->_get_galleries( $skip_empty, $search_terms );

			
			if ( $galleries && empty( $search_terms ) ) {
				$expiration = Anugu_Gallery_Common::get_instance()->get_transient_expiration_time();
				set_transient( '_eg_cache_all', $galleries, $expiration );
			}
		}

		
		return $galleries;

	}

	
	public function _get_galleries( $skip_empty = true, $search_terms = '' ) {

		
		$args = array(
			'post_type'     => 'anugu',
			'post_status'   => 'publish',
			'posts_per_page'=> 99,
			'no_found_rows' => true,
			'fields'        => 'ids',
			'meta_query'    => array(
				array(
					'key'   => '_eg_gallery_data',
					'compare' => 'EXISTS',
				),
			),
		);

		
		if ( ! empty( $search_terms ) ) {
			$args['s'] = $search_terms;
		}

	
		$galleries = new WP_Query( $args );
		if ( ! isset( $galleries->posts ) || empty( $galleries->posts ) ) {
			return false;
		}

		
		$ret = array();
		foreach ( $galleries->posts as $id ) {
			$data = get_post_meta( $id, '_eg_gallery_data', true );

			if ( $skip_empty && empty( $data['gallery'] ) ) {
				continue;
			}

			
			$type = Anugu_Gallery_Shortcode::get_instance()->get_config( 'type', $data );
			if ( 'defaults' === Anugu_Gallery_Shortcode::get_instance()->get_config( 'type', $data ) || 'dynamic' === Anugu_Gallery_Shortcode::get_instance()->get_config( 'type', $data ) ) {
				continue;
			}

			
			$ret[] = $data;
		}


		return $ret;

	}

	
	public function get_license_key() {

		return '';

	}

	
	public function get_license_key_type() {

		return '';

	}

	
	public function get_license_key_errors() {

		return false;
	}

	
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Anugu_Gallery_Lite ) ) {
			self::$instance = new Anugu_Gallery_Lite();
		}

		return self::$instance;

	}
}

register_activation_hook( __FILE__, 'anugu_gallery_lite_activation_hook' );

function anugu_gallery_lite_activation_hook( $network_wide ) {

	global $wp_version;
	if ( version_compare( $wp_version, '4.0', '<' ) && ! defined( 'ANUGU_FORCE_ACTIVATION' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( sprintf( __( 'Sorry, but your version of WordPress does not meet Anugu Gallery\'s required version of <strong>4.0</strong> to run properly. The plugin has been deactivated. <a href="%s">Click here to return to the Dashboard</a>.', 'anugu-gallery-lite' ), get_admin_url() ) );
	}

	
	deactivate_plugins( 'anugu-gallery/anugu-gallery.php' );

}


$anugu_gallery_lite = Anugu_Gallery_Lite::get_instance();


if ( ! function_exists( 'anugu_mobile_detect' ) ) {

	
	function anugu_mobile_detect(){

		
		if ( ! class_exists( 'Mobile_Detect' ) ) {

			require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/global/Mobile_Detect.php';

		}

		return new Mobile_Detect;

	}

}


if ( ! function_exists( 'anugu_wp_upe_upgrade_completed' ) ) {

	
	function anugu_wp_upe_upgrade_completed( $upgrader_object, $options ) {
		
		$our_plugin = plugin_basename( __FILE__ );
		
		if( $options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'] ) ) {
			
			foreach( $options['plugins'] as $plugin ) {
				if( $plugin == $our_plugin ) {
					
					set_transient( 'anugu_lite_updated', 1, 60 ); 
				}
			}
		}
	}
	add_action( 'upgrader_process_complete', 'anugu_wp_upe_upgrade_completed', 10, 2 );

}


if ( ! function_exists( 'anugu_gallery' ) ) {
	
	function anugu_gallery( $id, $type = 'id', $args = array(), $return = false ) {

		
		$args_string = '';
		if ( ! empty( $args ) ) {
			foreach ( (array) $args as $key => $value ) {
				$args_string .= ' ' . $key . '="' . $value . '"';
			}
		}

		
		$shortcode = ! empty( $args_string ) ? '[anugu-gallery ' . $type . '="' . $id . '"' . $args_string . ']' : '[anugu-gallery ' . $type . '="' . $id . '"]';

		
		if ( $return ) {
			return do_shortcode( $shortcode );
		} else {
			echo do_shortcode( $shortcode );
		}

	}
}
