<?php

// Styles for attribute: .portfolio-grid-item:before
// ==========================================================
$css_grid_item_before = '';

// Image ratio
if ($settings->ratio !== '1:1') {
	$ratio = array();
	if ($settings->ratio == 'custom') {
		$ratio[] = ((int)$settings->ratio_width > 0) ? (int)$settings->ratio_width : 1;
		$ratio[] = ((int)$settings->ratio_height > 0) ? (int)$settings->ratio_height : 1;
	} else {
		$ratio = explode(":", $settings->ratio);
	}
	$css_grid_item_before['padding-top'] = round( ((int)$ratio[1] / (int)$ratio[0]) * 100, 2 ). '%';
}

// Background color
if (!empty($settings->hover_color)) {
	$css_grid_item_before['background-color'] = '#'. $settings->hover_color;
}

if (!empty($css_grid_item_before)) : ?>
.fl-node-<?php echo $id; ?> .portfolio-grid-item:before {
	<?php
	// Output the styles
	foreach ($css_grid_item_before as $attribute => $style) {
		echo  $attribute .':'. $style .';';
	}
	?>
}
<?php endif;

// Styles for attribute: .portfolio-grid-item:hover .item-image
// ==========================================================
$css_grid_item_hover = '';

// Hover Opacity
if (!empty($settings->hover_opacity) &&  (int)$settings->hover_opacity <= 100) {
	$css_grid_item_hover['opacity'] = (int) $settings->hover_opacity / 100;
}

if (!empty($css_grid_item_hover)) : ?>
.fl-node-<?php echo $id; ?> .portfolio-grid-item:hover .item-image {
	<?php
	// Output the styles
	foreach ($css_grid_item_hover as $attribute => $style) {
		echo  $attribute .':'. $style .';';
	}
	?>
}
<?php endif;
