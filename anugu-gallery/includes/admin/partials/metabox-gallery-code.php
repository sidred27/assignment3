<?php
/**
 * Outputs the Gallery Code Metabox Content.
 *
 * @since   1.5.0
 *
 * @package Anugu_Gallery
 * @author 	Anugu Team
 */
?>
<p><?php _e( 'You can place this gallery anywhere into your posts, pages, custom post types or widgets by using <strong>one</strong> the shortcode(s) below:', 'anugu-gallery-lite' ); ?></p>
<div class="anugu-code">
	<code id="anugu_shortcode_id_<?php echo $data['post']->ID; ?>"><?php echo '[anugu-gallery id="' . $data['post']->ID . '"]'; ?></code>
	<a href="#" title="<?php _e( 'Copy Shortcode to Clipboard', 'anugu-gallery-lite' ); ?>" data-clipboard-target="#anugu_shortcode_id_<?php echo $data['post']->ID; ?>" class="dashicons dashicons-clipboard anugu-clipboard">
		<span><?php _e( 'Copy to Clipboard', 'anugu-gallery-lite' ); ?></span>
	</a>
</div>

<?php
if ( ! empty( $data['gallery_data']['config']['slug'] ) ) {
	?>
	<div class="anugu-code">
		<code id="anugu_shortcode_slug_<?php echo $data['post']->ID; ?>"><?php echo '[anugu-gallery slug="' . $data['gallery_data']['config']['slug'] . '"]'; ?></code>
		<a href="#" title="<?php _e( 'Copy Shortcode to Clipboard', 'anugu-gallery-lite' ); ?>" data-clipboard-target="#anugu_shortcode_slug_<?php echo $data['post']->ID; ?>" class="dashicons dashicons-clipboard anugu-clipboard">
			<span><?php _e( 'Copy to Clipboard', 'anugu-gallery-lite' ); ?></span>
		</a>
	</div>
	<?php
}
?>

<p><?php _e( 'You can also place this gallery into your template files by using <strong>one</strong> the template tag(s) below:', 'anugu-gallery-lite' ); ?></p>
<div class="anugu-code">
	<code id="anugu_template_tag_id_<?php echo $data['post']->ID; ?>"><?php echo 'if ( function_exists( \'anugu_gallery\' ) ) { anugu_gallery( \'' . $data['post']->ID . '\' ); }'; ?></code>
	<a href="#" title="<?php _e( 'Copy Template Tag to Clipboard', 'anugu-gallery-lite' ); ?>" data-clipboard-target="#anugu_template_tag_id_<?php echo $data['post']->ID; ?>" class="dashicons dashicons-clipboard anugu-clipboard">
		<span><?php _e( 'Copy to Clipboard', 'anugu-gallery-lite' ); ?></span>
	</a>
</div>

<?php 
if ( ! empty( $data['gallery_data']['config']['slug'] ) ) {
	?>
	<div class="anugu-code">
	    <code id="anugu_template_tag_slug_<?php echo $data['post']->ID; ?>"><?php echo 'if ( function_exists( \'anugu_gallery\' ) ) { anugu_gallery( \'' . $data['gallery_data']['config']['slug'] . '\', \'slug\' ); }'; ?></code>
	    <a href="#" title="<?php _e( 'Copy Template Tag to Clipboard', 'anugu-gallery-lite' ); ?>" data-clipboard-target="#anugu_template_tag_slug_<?php echo $data['post']->ID; ?>" class="dashicons dashicons-clipboard anugu-clipboard">
			<span><?php _e( 'Copy to Clipboard', 'anugu-gallery-lite' ); ?></span>
		</a>
	</div>
    <?php
}