<?php
/**
 * Outputs the Gallery Settings Tabs and Config options.
 *
 * @since   1.5.0
 *
 * @package Anugu_Gallery
 * @author 	Anugu Team
 */
?>
<!-- Tabs -->
<ul id="anugu-tabs-nav" class="anugu-tabs-nav" data-container="#anugu-tabs" data-update-hashbang="1">
	<?php
	// Iterate through the available tabs, outputting them in a list.
    $i = 0;
	foreach ( $data['tabs'] as $id => $title ) {
		$class = ( 0 === $i ? ' anugu-active' : '' );
		?>
		<li class="anugu-<?php echo $id; ?>">
			<a href="#anugu-tab-<?php echo $id; ?>" title="<?php echo $title; ?>"<?php echo ( ! empty( $class ) ? ' class="' . $class . '"' : '' ); ?>>
				<?php echo $title; ?>
			</a>
		</li>
		<?php

		$i++;
	}
	?>
</ul>

<!-- Settings -->
<div id="anugu-tabs" data-navigation="#anugu-tabs-nav">
    <?php
    // Iterate through the registered tabs, outputting a panel and calling a tab-specific action,
    // which renders the settings view for that tab.
    $i = 0;
    foreach ( $data['tabs'] as $id => $title ) {
        $class = ( 0 === $i ? 'anugu-active' : '' );
        ?>
        <div id="anugu-tab-<?php echo $id; ?>" class="anugu-tab anugu-clear <?php echo $class; ?>">
        	<?php do_action( 'anugu_gallery_tab_' . $id, $data['post'] ); ?>
        	
        </div>
        <?php
        $i++;
    }
    ?>
</div>

<div class="anugu-clear"></div>