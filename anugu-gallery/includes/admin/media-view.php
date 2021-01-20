<?php
/**
 * Media View class.
 *
 * @since 1.0.3
 *
 * @package Anugu_Gallery
 * @author  Anugu Team
 */
class Anugu_Gallery_Media_View {

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

        // Base
        $this->base = Anugu_Gallery_Lite::get_instance();

        // Modals
        add_filter( 'anugu_gallery_media_view_strings', array( $this, 'media_view_strings' ) );
        add_action( 'print_media_templates', array( $this, 'print_media_templates' ) );

    }

    /**
    * Adds media view (modal) strings
    *
    * @since 1.0.3
    *
    * @param    array   $strings    Media View Strings
    * @return   array               Media View Strings
    */ 
    public function media_view_strings( $strings ) {

        // Get the current screen, and check whether we're viewing the Anugu or Anugu Album Post Types.
        $screen = get_current_screen(); 
        if ( 'anugu' !== $screen->post_type ) {
            return $strings;
        }

        // Remove The "insertFromUrlTitle" option

        unset( $strings['insertFromUrlTitle'] );

        return $strings;

    }

    /**
    * Outputs backbone.js wp.media compatible templates, which are loaded into the modal
    * view
    *
    * @since 1.0.3
    */
    public function print_media_templates() {

        // Always output certain print media templates
        // Insert Gallery (into Visual / Text Editor)
        // Use: wp.media.template( 'anugu-selection' )
        ?>
        <script type="text/html" id="tmpl-anugu-selection">
            <div class="media-frame-title">
                <h1>{{data.modal_title}}</h1>
            </div>
            <div class="media-frame-content">
                <div class="attachments-browser anugu-gallery anugu-gallery-editor">
                    <!-- Galleries -->
                    <ul class="attachments">
                    </ul>

                    <!-- Sidebar -->
                    <div class="media-sidebar attachment-info">
                    </div>

                    <!-- Search -->
                    <div class="media-toolbar">
                        <div class="media-toolbar-secondary">
                            <span class="spinner"></span>
                        </div>
                        <div class="media-toolbar-primary search-form">
                            <label for="anugu-gallery-search" class="screen-reader-text"><?php _e( 'Search', 'anugu-gallery-lite' ); ?></label>
                            <input type="search" placeholder="<?php _e( 'Search', 'anugu-gallery-lite' ); ?>" id="anugu-gallery-search" class="search" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Bar -->
            <div class="media-frame-toolbar">
                <div class="media-toolbar">
                    <div class="media-toolbar-primary search-form">
                        <button type="button" class="button media-button button-primary button-large media-button-insert" disabled="disabled">
                            {{data.insert_button_label}}
                        </button>
                    </div>
                </div>
            </div>
        </script>
        <?php

        // Single Selection Item (Gallery or Album)
        // Use: wp.media.template( 'anugu-selection-item' )
        ?>
        <script type="text/html" id="tmpl-anugu-selection-item"> 
            <div class="attachment-preview" data-id="{{ data.id }}">
                <div class="thumbnail">
                    <# 
                    if ( data.thumbnail != '' ) { 
                        #>
                        <img src="{{ data.thumbnail }}" alt="{{ data.title }}" />
                        <# 
                    } 
                    #>
                    <strong>
                        <span>{{ data.title }}</span>
                    </strong>
                    <code>
                        [anugu-{{ data.action }} id="{{ data.id }}"]
                    </code>
                </div>
            </div>

            <a class="check">
                <div class="media-modal-icon"></div>
            </a>
        </script>
        <?php

        // Selection Sidebar
        // Use: wp.media.template( 'anugu-selection-sidebar' )
        ?>
        <script type="text/html" id="tmpl-anugu-selection-sidebar"> 
            <!-- Helpful Tips -->
            <h3><?php _e( 'Helpful Tips', 'anugu-gallery-lite' ); ?></h3>
            <strong><?php _e( 'Choosing Your Gallery', 'anugu-gallery-lite' ); ?></strong>
            <p>
                <?php _e( 'To choose your gallery, simply click on one of the boxes to the left. Ctrl / cmd and click to select multiple Galleries.  The "Insert Gallery" button will be activated once you have selected a gallery.', 'anugu-gallery-lite' ); ?>
            </p>
            <strong><?php _e( 'Inserting Your Gallery', 'anugu-gallery-lite' ); ?></strong>
            <p>
                <?php _e( 'To insert your gallery into the editor, click on the "Insert Gallery" button below.', 'anugu-gallery-lite' ); ?>
            </p>

            <!-- Insert Options -->
            <h3><?php _e( 'Insert Options', 'anugu-gallery-lite' ); ?></h3>
            <div class="settings">
                <!-- Display Title -->
                <label class="setting">
                    <span class="name"><?php _e( 'Display Title', 'anugu-gallery-lite' ); ?></span>
                    <select name="title" size="1">
                        <option value="0" selected><?php _e( 'No', 'anugu-gallery-lite' ); ?></option>
                        <?php
                        for( $i = 1; $i <= 6; $i++ ) {
                            ?>
                            <option value="h<?php echo $i; ?>"><?php echo sprintf( __( 'Yes, as Heading H%s', 'anugu-gallery-lite'), $i ); ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </label>
                <p class="description">
                    <?php _e( 'Prepends each inserted Gallery with the Gallery Title.', 'anugu-gallery-lite' ); ?>
                </p>
            </div>
        </script>

        <?php
        // Error
        // Use: wp.media.template( 'anugu-gallery-error' )
        ?>
        <script type="text/html" id="tmpl-anugu-gallery-error">
            <p>
                {{ data.error }}
            </p>
        </script> 

        <?php
    	// Only load other Backbone templates if we're on an Anugu CPT.
    	global $post;
    	if ( isset( $post ) ) {
    		$post_id = absint( $post->ID );
    	} else {
    		$post_id = 0;
    	}

    	// Bail if we're not editing an Anugu Gallery
    	if ( get_post_type( $post_id ) != 'anugu' ) {
    		return;
    	}

        // Single Image Editor
        // Use: wp.media.template( 'anugu-meta-editor' )
        ?>
        <script type="text/html" id="tmpl-anugu-meta-editor">
			<div class="edit-media-header">
				<button class="left dashicons"><span class="screen-reader-text"><?php _e( 'Edit previous media item' ); ?></span></button>
				<button class="right dashicons"><span class="screen-reader-text"><?php _e( 'Edit next media item' ); ?></span></button>
			</div>
			<div class="media-frame-title">
				<h1><?php _e( 'Edit Metadata', 'anugu-gallery-lite' ); ?></h1>
			</div>
			<div class="media-frame-content">
				<div class="attachment-details save-ready">
					<!-- Left -->
	                <div class="attachment-media-view portrait">
	                    <div class="thumbnail thumbnail-image">
	                        <img class="details-image" src="{{ data.src }}" draggable="false" />
	                    </div>
	                </div>
	                
	                <!-- Right -->
	                <div class="attachment-info">
	                    <!-- Settings -->
	                    <div class="settings">
	                    	<!-- Attachment ID -->
	                    	<input type="hidden" name="id" value="{{ data.id }}" />
	                        
	                        <!-- Image Title -->
	                        <label class="setting">
	                            <span class="name"><?php _e( 'Title', 'anugu-gallery-lite' ); ?></span>
	                            <input type="text" name="title" value="{{ data.title }}" />
	                            <div class="description">
	                            	<?php _e( 'Image titles can take any type of HTML. You can adjust the position of the titles in the main Lightbox settings.', 'anugu-gallery-lite' ); ?>
	                            </div>
	                        </label>
	                      
	                        
	                        <!-- Alt Text -->
	                        <label class="setting">
	                            <span class="name"><?php _e( 'Alt Text', 'anugu-gallery-lite' ); ?></span>
	                            <input type="text" name="alt" value="{{ data.alt }}" />
	                            <div class="description">
									<?php _e( 'Very important for SEO, the Alt Text describes the image.', 'anugu-gallery-lite' ); ?>
								</div>
	                        </label>
	                        
	                        <!-- Link -->
	                        <label class="setting">
	                            <span class="name"><?php _e( 'URL', 'anugu-gallery-lite' ); ?></span>
	                            <input type="text" name="link" value="{{ data.link }}" />
	                            <# if ( typeof( data.id ) === 'number' ) { #>
		                            <span class="buttons">
		                            	<button class="button button-small media-file"><?php _e( 'Media File', 'anugu-gallery-lite' ); ?></button>
										<button class="button button-small attachment-page"><?php _e( 'Attachment Page', 'anugu-gallery-lite' ); ?></button>
									</span>
								<# } #>
								<span class="description">
									<?php _e( 'Enter a hyperlink if you wish to link this image to somewhere other than its full size image.', 'anugu-gallery-lite' ); ?>
								</span>
							</label>

                            <!-- Link in New Window -->
                            <label class="setting">
                                <span class="name"><?php _e( 'Open URL in New Window?', 'anugu-gallery-lite' ); ?></span>
                                <span class="description">
                                    <input type="checkbox" name="link_new_window" value="1"<# if ( data.link_new_window == '1' ) { #> checked <# } #> />
                                    <?php _e( 'Opens your image links in a new browser window / tab.', 'anugu-gallery-lite' ); ?>
                                </span>
                            </label>

							<!-- Addons can populate the UI here -->
							<div class="anugu-addons"></div>
	                    </div>
	                    <!-- /.settings -->     
	                   
	                    <!-- Actions -->
	                    <div class="actions">
	                        <a href="#" class="anugu-gallery-meta-submit button media-button button-large button-primary media-button-insert" title="<?php esc_attr_e( 'Save Metadata', 'anugu-gallery-lite' ); ?>">
	                        	<?php _e( 'Save Metadata', 'anugu-gallery-lite' ); ?>
	                        </a>

							<!-- Save Spinner -->
	                        <span class="settings-save-status">
		                        <span class="spinner"></span>
		                        <span class="saved"><?php _e( 'Saved.', 'anugu-gallery-lite' ); ?></span>
	                        </span>
	                    </div>
	                    <!-- /.actions -->
	                </div>
	            </div>
			</div>
		</script> 

        <?php
        // Bulk Image Editor
        // Use: wp.media.template( 'anugu-meta-bulk-editor' )
        ?>
        <script type="text/html" id="tmpl-anugu-meta-bulk-editor">
			<div class="media-frame-title">
				<h1><?php _e( 'Bulk Edit', 'anugu-gallery-lite' ); ?></h1>
			</div>
			<div class="media-frame-content">
				<div class="attachment-details save-ready">
					<!-- Left -->
	                <div class="attachment-media-view portrait">
	                	<ul class="attachments anugu-bulk-edit">
	                	</ul>
	                </div>
	                
	                <!-- Right -->
	                <div class="attachment-info">
	                    <!-- Settings -->
	                    <div class="settings">
	                    	<!-- Image Title -->
	                        <label class="setting">
	                            <span class="name"><?php _e( 'Title', 'anugu-gallery-lite' ); ?></span>
	                            <input type="text" name="title" value="" />
	                            <div class="description">
	                            	<?php _e( 'Image titles can take any type of HTML. You can adjust the position of the titles in the main Lightbox settings.', 'anugu-gallery-lite' ); ?>
	                            </div>
	                        </label>
	                        
	                        <!-- Caption -->
	                        <div class="setting">
	                            <span class="name"><?php _e( 'Caption', 'anugu-gallery-lite' ); ?></span>	
	                            <?php 
                                wp_editor( '', 'caption', array( 
                                	'media_buttons' => false, 
                                	'wpautop' 		=> false, 
                                	'tinymce' 		=> false, 
                                	'textarea_name' => 'caption', 
                                	'quicktags' => array( 
                                		'buttons' => 'strong,em,link,ul,ol,li,close' 
                                	),
                                	'editor_height'  => 100,
                                ) ); 
                                ?>
                                <div class="description">
									<?php _e( 'Captions can take any type of HTML, and are displayed when an image is clicked.', 'anugu-gallery-lite' ); ?>
								</div>
	                        </div>
	                        
	                        <!-- Alt Text -->
	                        <label class="setting">
	                            <span class="name"><?php _e( 'Alt Text', 'anugu-gallery-lite' ); ?></span>
	                            <input type="text" name="alt" value="" />
	                            <div class="description">
									<?php _e( 'Very important for SEO, the Alt Text describes the image.', 'anugu-gallery-lite' ); ?>
								</div>
	                        </label>
	                        
	                        <!-- Link -->
	                        <label class="setting">
	                            <span class="name"><?php _e( 'URL', 'anugu-gallery-lite' ); ?></span>
	                            <input type="text" name="link" value="" />
	                            <# if ( typeof( data.id ) === 'number' ) { #>
		                            <span class="buttons">
		                            	<button class="button button-small media-file"><?php _e( 'Media File', 'anugu-gallery-lite' ); ?></button>
										<button class="button button-small attachment-page"><?php _e( 'Attachment Page', 'anugu-gallery-lite' ); ?></button>
									</span>
								<# } #>
								<span class="description">
									<?php _e( 'Enter a hyperlink if you wish to link this image to somewhere other than its full size image.', 'anugu-gallery-lite' ); ?>
								</span>
							</label>
							
							<!-- Link in New Window -->
                            <label class="setting">
                            	<span class="name"><?php _e( 'Open URL in New Window?', 'anugu-gallery-lite' ); ?></span>
								<span class="description">
                                    <input type="checkbox" name="link_new_window" value="1" />
                            		<?php _e( 'Opens your image links in a new browser window / tab.', 'anugu-gallery-lite' ); ?>
                            	</span>
                            </label>

							<!-- Addons can populate the UI here -->
							<div class="anugu-addons"></div>
	                    </div>
	                    <!-- /.settings -->     
	                   
	                    <!-- Actions -->
	                    <div class="actions">
	                        <a href="#" class="anugu-gallery-meta-submit button media-button button-large button-primary media-button-insert" title="<?php esc_attr_e( 'Save Metadata to Items', 'anugu-gallery-lite' ); ?>">
	                        	<?php _e( 'Save Metadata', 'anugu-gallery-lite' ); ?>
	                        </a>

							<!-- Save Spinner -->
	                        <span class="settings-save-status">
		                        <span class="spinner"></span>
		                        <span class="saved"><?php _e( 'Saved.', 'anugu-gallery-lite' ); ?></span>
	                        </span>
	                    </div>
	                    <!-- /.actions -->
	                </div>
	            </div>
			</div>
		</script> 
		
		<?php
        // Bulk Image Editor Image
        // Use: wp.media.template( 'anugu-meta-bulk-editor-image' )
        ?>
        <script type="text/html" id="tmpl-anugu-meta-bulk-editor-image">
        	<div class="attachment-preview">
        		<div class="thumbnail">
        			<div class="centered">
        				<img src={{ data._thumbnail }} />
        			</div>
        		</div>
        	</div>
        </script>
        
        <?php

        /**
        * Move Images to Gallery
        */
        // Selection Sidebar
        // Use: wp.media.template( 'anugu-meta-move-media-sidebar' )
        ?>
        <script type="text/html" id="tmpl-anugu-meta-move-media-sidebar"> 
            <!-- Helpful Tips -->
            <h3><?php _e( 'Helpful Tips', 'anugu-gallery-lite' ); ?></h3>
            <p>
                <?php _e( 'Select the Gallery to move the selected images to by clicking on one of the boxes to the left.', 'anugu-gallery-lite' ); ?>
            </p>
            <p>
                <?php _e( 'Once done, click the Move button, and the selected images will be moved to the chosen Gallery.', 'anugu-gallery-lite' ); ?>
            </p>
        </script>

        <?php
        /**
        * Insert from Third Party Sources
        */

        // Search
        // Use: wp.media.template( 'anugu-gallery-search-bar' )
        ?>
        <script type="text/html" id="tmpl-anugu-gallery-search-bar">
            <div class="media-toolbar">
                <div class="media-toolbar-secondary">
                    <span class="spinner"></span>
                </div>
                <div class="media-toolbar-primary search-form">
                    <label for="anugu-gallery-search" class="screen-reader-text"><?php _e( 'Search', 'anugu-gallery-lite' ); ?></label>
                    <input type="search" placeholder="<?php _e( 'Search', 'anugu-gallery-lite' ); ?>" id="anugu-gallery-search" class="search" />
                </div>
            </div> 
        </script>

        <?php
        // Folders and Items
        // Use: wp.media.template( 'anugu-gallery-items' )
        ?>
        <script type="text/html" id="tmpl-anugu-gallery-items">
            <ul class="attachments anugu-gallery-attachments"></ul>
        </script>
        
        <?php
		// Single Folder or Image (Item)
        // Use: wp.media.template( 'anugu-gallery-item' )
        ?>
        <script type="text/html" id="tmpl-anugu-gallery-item">
            <# if ( ! data.is_dir ) { #>
                <div class="attachment-preview js--select-attachment type-image subtype-<# data.mime_type #>" data-id="{{ data.id }}" data-is-dir="{{ data.is_dir }}">
                    <div class="thumbnail">
                        <div class="centered">
                            <img src="{{ data.thumbnail }}" draggable="false" alt="{{ data.title }}" />
                        </div>
                    </div>
                </div>
                <button type="button" class="button-link check" tabindex="-1">
                    <span class="media-modal-icon"></span>
                </button>
            <# } else { #>
                <div class="attachment-preview" data-id="{{ data.id }}" data-is-dir="{{ data.is_dir }}">
                    <div class="thumbnail">
                        <span class="dashicons dashicons-portfolio"></span>
                        <span>{{ data.title }}</span>
                    </div>
                </div>
            <# } #>
        </script>
        <?php

    }
	
    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Anugu_Gallery_Media_View object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Anugu_Gallery_Media_View ) ) {
            self::$instance = new Anugu_Gallery_Media_View();
        }

        return self::$instance;

    }

}

// Load the media class.
$anugu_gallery_media_view = Anugu_Gallery_Media_View::get_instance();