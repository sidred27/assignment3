<?php
/**
 * Outputs the Gallery Type Tab Selector and Panels
 *
 * @since   1.5.0
 *
 * @package Anugu_Gallery
 * @author 	Anugu Team
 */

?>


<!-- Types -->
<div id="anugu-types" data-navigation="#anugu-types-nav">
	<!-- Native Anugu Gallery - Drag and Drop Uploader -->
	<div id="anugu-gallery-native" class="anugu-tab anugu-clear<?php echo ( ( $data['instance']->get_config( 'type', $data['instance']->get_config_default( 'type' ) ) == 'default' ) ? ' anugu-active' : '' ); ?>">
		<!-- Errors -->
	    <div id="anugu-gallery-upload-error"></div>

	    <!-- WP Media Upload Form -->
	    <?php 
	    media_upload_form();
	    ?>
	    <script type="text/javascript">
	        var post_id = <?php echo $data['post']->ID; ?>, shortform = 3;
	    </script>
	    <input type="hidden" name="post_id" id="post_id" value="<?php echo $data['post']->ID; ?>" />
	</div>

	<!-- External Gallery -->
	
</div>