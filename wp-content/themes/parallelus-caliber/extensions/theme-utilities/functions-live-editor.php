<?php
/**
 * FrontEnd Editor Functions - Custom Theme Modifications
 * ................................................................
 *
 * Functions for custom changes to the output and functionality
 * of the FrontEnd Editor plugin.
 */


#-----------------------------------------------------------------
# Classes
#-----------------------------------------------------------------

// Section Class (added to the editor "Row" element)
//................................................................
if ( ! function_exists( 'theme_custom_section_class_for_fee' ) ) :
function theme_custom_section_class_for_fee( $section_class, $row ) {

	$section_class .= ' section-wrapper';

	// Custom container padding horizontal
	if ( isset($row->settings->content_padding_x) ) {

		if ( $row->settings->content_padding_x == 'no-pad' ) {
			$section_class .= ' no-padding-x';
		}
		if ( $row->settings->content_padding_x == 'no-pad-l' ) {
			$section_class .= ' no-padding-x-l';
		}
		if ( $row->settings->content_padding_x == 'no-pad-r' ) {
			$section_class .= ' no-padding-x-r';
		}
	}

	// Custom container padding vertical
	if ( isset($row->settings->content_padding_y) ) {

		if ( $row->settings->content_padding_y == 'no-pad' ) {
			$section_class .= ' no-padding-y';
		}
		if ( $row->settings->content_padding_y == 'no-pad-t' ) {
			$section_class .= ' no-padding-y-t';
		}
		if ( $row->settings->content_padding_y == 'no-pad-b' ) {
			$section_class .= ' no-padding-y-b';
		}
	}

	if ( isset($row->settings->custom_type) && $row->settings->custom_type == 'flex-row' ) {
		$section_class .= ' flex-wrapper';
	}

	return $section_class; // add section container class
}
endif;
add_filter( 'fee_section_class', 'theme_custom_section_class_for_fee', 10, 2 );


// Container Wrap Class
//................................................................
if ( ! function_exists( 'theme_custom_container_wrap_class_for_fee' ) ) :
function theme_custom_container_wrap_class_for_fee( $container_wrap_class ) {

	// During editing, use default editor classes
	if ( FLBuilderModel::is_builder_active() ) {
		return $container_wrap_class . ' container-wrap';
	}

	return 'container-wrap';
}
endif;
add_filter( 'fee_container_wrap_class', 'theme_custom_container_wrap_class_for_fee' );


// Container Class
//................................................................
if ( ! function_exists( 'theme_custom_container_class_for_fee' ) ) :
function theme_custom_container_class_for_fee( $container_class, $row ) {

	$theme_container_class = 'container';

	if ( is_object($row) ) {

		// Size settings
		if ( isset($row->settings->width) ) {
			// Check the custom width settings
			if ( $row->settings->width == 'full' ) {

				$theme_container_class = str_replace("container", "container-xl", $theme_container_class);

				// Content container (may be legacy now, probably could be commented out)
				if ( isset($row->settings->content_width) && $row->settings->content_width == 'full' ) {
					$theme_container_class .= ' no-padding';
				}
			}
			if ( $row->settings->width == 'browser' ) {
				$theme_container_class = str_replace("container", "container-browser", $theme_container_class);
			}
			if ( $row->settings->width == 'custom' ) {
				$theme_container_class = str_replace("container", "container-custom-width", $theme_container_class);
			}
			// A helper class for easier work with fluid  containers
			if ( $row->settings->width !== 'fixed' && $row->settings->width !== 'custom' ) {
				$theme_container_class .= ' fluid-width';
			}
		}

		// Auto-margin fix for horizontal padding on only one side
		if ( isset($row->settings->content_padding_x) && strpos($theme_container_class, 'container-xl') !== false ) {

			if ( $row->settings->content_padding_x == 'no-pad-r' ) {
				$theme_container_class .= ' auto-margin-l';
			}
			if ( $row->settings->content_padding_x == 'no-pad-l' ) {
				$theme_container_class .= ' auto-margin-r';
			}
		}

		// Custom row types
		if ( isset($row->settings->custom_type) ) {

			// Helper class for "flex-row" section settings
			if ( $row->settings->custom_type == 'flex-row' ) {

				// Check column order
				if ( isset($row->settings->column_order) && $row->settings->column_order == 'row-reverse' ) {
					$theme_container_class .= ' flex-cols-reverse';
				}

				// Check flex column positioning
				if ( isset($row->settings->column_flex_x) ) {
					$theme_container_class .= ' flex-cols-x-'.$row->settings->column_flex_x; // horizontal position
				}
				if ( isset($row->settings->column_flex_y) ) {
					$theme_container_class .= ' flex-cols-y-'.$row->settings->column_flex_y; // vertical position

					// Vertical align
					if ( $row->settings->column_flex_y == 'stretch' && isset($row->settings->column_align_y) ) {
						$theme_container_class .= ' flex-justify-'.$row->settings->column_align_y;
					}
				}

				// add the flexbox container class only
				$theme_container_class .= ' flex-container-wrap';
			}
		}
	}

	// During editing, use default editor classes
	if ( FLBuilderModel::is_builder_active() ) {
		return trim($container_class .' '. $theme_container_class);
	}

	return trim($theme_container_class);
}
endif;
add_filter( 'fee_container_class', 'theme_custom_container_class_for_fee', 10, 2 );


// Column Custom Class
//................................................................
if ( ! function_exists( 'theme_column_custom_class_for_fee' ) ) :
function theme_column_custom_class_for_fee( $class, $col ) {

	if ( is_object($col) ) {

		// Column classes for "flex-row" and "50-50" rows (embellish and pull-bottom)
		if (isset($col->parent)) {
			$col_group = FLBuilderModel::get_node($col->parent);

			if (isset($col_group->parent)) {
				$row = FLBuilderModel::get_node($col_group->parent);

				// Section column class for "flex-row"
				if (isset($row->settings->custom_type) && $row->settings->custom_type == 'flex-row' ) {
					$class .= ' flex-col';

					// Check for column specific settings
					if (isset($col->settings->column_flex_y) && $col->settings->column_flex_y !== 'default') {
						// Set column specific vertical position
						$class .= ' flex-self-y-'.$col->settings->column_flex_y;
					}
					if (isset($col->settings->column_flex_y) && $col->settings->column_flex_y === 'stretch' && isset($col->settings->column_align_y)) {
						// Set column specific vertical align
						$class .= ' flex-self-justify-'.$col->settings->column_align_y;
					}
				}
			}
		}
	}

	return $class;
}
endif;
add_filter( 'fl_builder_column_custom_class', 'theme_column_custom_class_for_fee', 10, 2 );


// Row Size Class
//................................................................
if ( ! function_exists( 'theme_custom_row_class_for_fee' ) ) :
function theme_custom_row_class_for_fee( $row_class, $row ) {

	// During editing, use default editor classes
	if ( FLBuilderModel::is_builder_active() ) {
		return $row_class . ' row';
	}

	return $row_class . ' row'; // add row class to column container
}
endif;
add_filter( 'fee_row_class', 'theme_custom_row_class_for_fee', 10, 2 );


// Column Class
//................................................................
if ( ! function_exists( 'theme_custom_column_class_for_fee' ) ) :
function theme_custom_column_class_for_fee( $column_class, $col ) {

	// Check what modules are in this column...
	$modules = FLBuilderModel::get_nodes('module', $col);
	$module_types = array();
	foreach ( $modules as $module ) {
		$module_types[] = $module->settings->type;
	}

	// Posts module: Add some classes for module styling
	if ( in_array('posts', $module_types) ) {
			$column_class .= ' container-post';
	}
	// Portfolio module: Add some classes for module styling
	if ( in_array('portfolio', $module_types) ) {
			$column_class .= ' container-portfolio';
	}

	// During editing, use default editor classes and return here.
	// if ( FLBuilderModel::is_builder_active() ) {
	// 	return $column_class;
	// }

	return $column_class;

}
endif;
add_filter( 'fee_column_class', 'theme_custom_column_class_for_fee', 10, 2 );

// Column Width Inline Style
//................................................................
/*if ( ! function_exists( 'theme_custom_col_style_width' ) ) :
function theme_custom_col_style_width( $size = false, $column_class = false, $col = false ) {

	// If this is a 50-50 row we ditch the inline width
	if ( strpos($column_class, 'section-half') !== false ) {
		return false;
	}
}
endif;
add_filter( 'fee_col_style_width', 'theme_custom_col_style_width', 10, 3 );*/


#-----------------------------------------------------------------
# Elements
#-----------------------------------------------------------------

// Custom Row Types (sizing applied based on data-cols attribute name)
//................................................................
if ( ! function_exists( 'theme_custom_row_types_for_fee' ) ) :
function theme_custom_row_types_for_fee( $row_layouts ) {

	// "flex-row", default column sizing
	$row_layouts['flex-row'] = array(50, 50);

	// Full Width Row, default column sizing
	// $row_layouts['flush-row'] = array(100);

	return $row_layouts;
}
endif;
add_filter( 'fee_row_layouts', 'theme_custom_row_types_for_fee' );

// Custom Row Types (added to layout builder UI)
//................................................................
if ( ! function_exists( 'theme_custom_row_types_for_fee_ui' ) ) :
function theme_custom_row_types_for_fee_ui( $row_layouts ) {

	echo '<span class="fl-builder-block fl-builder-block-row" data-cols="flex-row"><span class="fl-builder-block-title">'. __('Flex Row', 'runway') .'</span></span>';
}
endif;
add_action( 'fee_ui_panel_after_row_layouts', 'theme_custom_row_types_for_fee_ui' );

// Custom Row Attributes on Add New
//................................................................
if ( ! function_exists( 'theme_custom_fee_row_settings_on_add' ) ) :
function theme_custom_fee_row_settings_on_add( $settings, $cols ) {

	// Custom Row Types (these are theme specific)
	// $custom_rows = array('50-50', 'flex-row', 'flush-row');
	$custom_rows = array('flex-row');

	// Add custom type setting for reference point
	if ( in_array($cols, $custom_rows)) {
		$settings->custom_type = $cols;
	}

	// Pre-set some variables for "Flush Row"
	if ( $cols == 'flush-row') {
		$settings->width = 'full';
		$settings->content_width = 'full';
		$settings->padding_top = '0';
		$settings->padding_bottom = '0';
	}

	return $settings;
}
endif;
add_filter( 'fee_add_row_settings', 'theme_custom_fee_row_settings_on_add', 10, 2 );

#-----------------------------------------------------------------
# Settings
#-----------------------------------------------------------------

// Customize the editor fields for ROW settings
//................................................................
if ( ! function_exists( 'theme_custom_fee_render_row_settings_form' ) ) :
function theme_custom_fee_render_row_settings_form( $form, $node ) {

	// Custom settings controls for "flex-row" rows
	if ( isset($node->settings->custom_type) && $node->settings->custom_type == 'flex-row' ) {

		// Column Positioning and Order
		$form['tabs']['style']['sections']['columns_align'] = array(
				'title'         => __('Column Positioning', 'runway'),
				'fields'        => array(
					'column_order'    => array(
						'type'          => 'select',
						'label'         => __('Column Order', 'runway'),
						'default'       => 'row',
						'options'       => array(
							'row'         => __('Default', 'runway'),
							'row-reverse' => __('Reverse', 'runway')
						),
						'help'          => __("Applies to large screens only. This can reverse the column order for alternating rows and keep the appearance consistant for mobile devices.", 'runway'),
						// 'preview'         => array(
						// 	'type'            => 'none'
						// )
					),

					// *** HIDDEN IN "fl-lightbox.css" UNTIL UPDATE TO ALLOW ROW WIDTH TO BE SET FOR EACH COLUMN, INDEPENDANT OF OTHER COLUMNS *** //
					'column_flex_x'    => array(
						'type'          => 'select',
						'label'         => __('Horizontal Position', 'runway'),
						'default'       => 'around',
						'options'       => array(
							'start'       => __('Left', 'runway'),
							'end'         => __('Right', 'runway'),
							'center'      => __('Center', 'runway'),
							'between'     => __('Spaced (full width)', 'runway'),
							'around'      => __('Spaced (even)', 'runway')
						),
						'help'          => __("The horizontal placement and spacing of column containers.", 'runway'),
					),
					// *** HIDDEN IN "fl-lightbox.css" UNTIL UPDATE TO ALLOW ROW WIDTH TO BE SET FOR EACH COLUMN, INDEPENDANT OF OTHER COLUMNS *** //


					'column_flex_y'    => array(
						'type'          => 'select',
						'label'         => __('Vertical Position', 'runway'),
						'default'       => 'stretch',
						'options'       => array(
							'start'       => __('Top', 'runway'),
							'end'         => __('Bottom', 'runway'),
							'center'      => __('Center', 'runway'),
							'baseline'    => __('Baseline', 'runway'),
							'stretch'     => __('Stretch (full height)', 'runway')
						),
						'toggle'        => array(
							'stretch'       => array(
								'fields'      => array('column_align_y')
							)
						),
						'help'          => __("The vertical position of column containers.", 'runway'),
					),
					'column_align_y'    => array(
						'type'          => 'select',
						'label'         => __('Vertical Align', 'runway'),
						'default'       => 'center',
						'options'       => array(
							'center'      => __('Center', 'runway'),
							'start'       => __('Top', 'runway'),
							'end'         => __('Bottom', 'runway'),
						),
						'help'          => __("The vertical alignment of content.", 'runway'),
					)
				)
			);

		// Fix the array so "Column Positioning" appears immediately after the general options
		$form['tabs']['style']['sections'] = array(
				'general' => $form['tabs']['style']['sections']['general'],
				'container_defaults' => $form['tabs']['style']['sections']['container_defaults'],
				'columns_align' => $form['tabs']['style']['sections']['columns_align']
			) + $form['tabs']['style']['sections'];

	} // END flex-row

	return $form;
}
endif;
add_filter( 'fee_render_row_settings_form', 'theme_custom_fee_render_row_settings_form', 10, 2);

// Customize the editor fields for COLUMN settings
//................................................................
if ( ! function_exists( 'theme_custom_fee_render_column_settings_form' ) ) :
function theme_custom_fee_render_column_settings_form( $form, $node ) {


	// See if this is a "flex-row" and add extra settings if so...
	if (isset($node->parent)) {
		$col_group = FLBuilderModel::get_node($node->parent);

		if (isset($col_group->parent)) {
			$row = FLBuilderModel::get_node($col_group->parent);

			if (isset($row->settings->custom_type) && $row->settings->custom_type == 'flex-row') {

				// Remove the "equalize heights" option
				unset($form['tabs']['style']['sections']['general']['fields']['equal_height']);

				// Add a couple of "General" column settings for the Flex Row
				$form['tabs']['style']['sections']['general']['fields']['column_flex_y'] = array(
						'type'          => 'select',
						'label'         => __('Vertical Position', 'runway'),
						'default'       => 'dafault',
						'options'       => array(
							'dafault'     => __('[use row settings]', 'runway'),
							'start'       => __('Top', 'runway'),
							'end'         => __('Bottom', 'runway'),
							'center'      => __('Center', 'runway'),
							'baseline'    => __('Baseline', 'runway'),
							'stretch'     => __('Stretch (full height)', 'runway')
						),
						'toggle'        => array(
							'stretch'       => array(
								'fields'      => array('column_align_y')
							)
						),
						'help'          => __("The vertical position of this column (overrides main row settings).", 'runway'),
						'preview'         => array(
							'type'            => 'none' // preview doesn't work on these!
						)
					);
				$form['tabs']['style']['sections']['general']['fields']['column_align_y'] = array(
						'type'          => 'select',
						'label'         => __('Vertical Align', 'runway'),
						'default'       => 'center',
						'options'       => array(
							'center'      => __('Center', 'runway'),
							'start'       => __('Top', 'runway'),
							'end'         => __('Bottom', 'runway'),
						),
						'help'          => __("The vertical alignment of content.", 'runway'),
						'preview'         => array(
							'type'            => 'none' // preview doesn't work on these!
						)
					);
			}
		}
	}


	return $form;
}
endif;
add_filter( 'fee_render_column_settings_form', 'theme_custom_fee_render_column_settings_form', 10, 2);


// Modify the default editor settings for rows, columns, modules...
//................................................................
if ( ! function_exists( 'theme_custom_fee_settings_forms' ) ) :
function theme_custom_fee_settings_forms( $settings, $id ) {

	// Modify the default Global settings
	if ($id == 'global') {
		$settings['tabs']['general']['sections']['rows']['fields']['row_width']['default']         = '1180';  // content width
		$settings['tabs']['general']['sections']['rows']['fields']['row_padding']['default']       = '';     // row padding
		$settings['tabs']['general']['sections']['rows']['fields']['row_margins']['default']       = '';     // row margin
		$settings['tabs']['general']['sections']['modules']['fields']['module_margins']['default'] = '';     // module margin
	}

	// Modify the default Row settings text a little
	if ($id == 'row') {
		// Remove the default row height setting
		unset($settings['tabs']['style']['sections']['general']['fields']['full_height']);

		$settings['tabs']['style']['sections']['general']['fields']['width']['label'] = 'Container Size'; // Width
		$settings['tabs']['style']['sections']['general']['fields']['width']['options'] = array(
				'fixed'         => __('Fixed', 'runway'),
				'full'          => __('Full Width', 'runway'),
				'browser'       => __('Browser Width', 'runway'),
				// 'custom'        => __('Custom', 'runway')			//*** DISABLED TEMPORARILY ***//
			);
		$settings['tabs']['style']['sections']['general']['fields']['width']['toggle'] = array(
				'custom'       => array(
					'fields'   => array('custom_width')
				)
			);
		$settings['tabs']['style']['sections']['general']['fields']['width']['help'] = __('Restrict to design boundry or extend to the browser width.', 'runway');
		unset($settings['tabs']['style']['sections']['general']['fields']['width']['preview']); // force a preview refresh on change

		// Custom Width
		$settings['tabs']['style']['sections']['general']['fields']['custom_width'] = array(
				'type'          => 'text',
				'label'         => __('Width', 'runway'),
				'default'       => '1130',
				'description'   => 'px',
				'maxlength'     => '4',
				'size'          => '5'
			);

		// Container Default Padding
		unset($settings['tabs']['style']['sections']['general']['fields']['content_width']);
		$settings['tabs']['style']['sections']['container_defaults'] = array(
				'title'         => __('Container Defaults', 'runway'),
				'fields'        => array(
					'content_padding_x' => array(
						'type'          => 'select',
						'label'         => __('Horizontal Padding', 'runway'),
						'default'       => 'default',
						'options'       => array(
							'default'     => __('Theme default', 'runway'),
							'no-pad'      => __('No horizontal padding', 'runway'),
							'no-pad-l'    => __('No horizontal - Left', 'runway'),
							'no-pad-r'    => __('No horizontal - Right', 'runway')
						),
						'help'          => __("Use the theme's horizontal padding and margins for the container.", 'runway'),
					),
					'content_padding_y' => array(
						'type'          => 'select',
						'label'         => __('Vertical Padding', 'runway'),
						'default'       => 'default',
						'options'       => array(
							'default'     => __('Theme default', 'runway'),
							'no-pad'      => __('No vertical padding', 'runway'),
							'no-pad-t'    => __('No vertical - Top', 'runway'),
							'no-pad-b'    => __('No vertical - Bottom', 'runway')
						),
						'help'          => __("Use the theme's vertical padding and margins for the container.", 'runway')
					)
				)
			);

		// Fix the array so "Container Defaults" appears immediately after the general options
		$settings['tabs']['style']['sections'] = array(
				'general' => $settings['tabs']['style']['sections']['general'],
				'container_defaults' => $settings['tabs']['style']['sections']['container_defaults']
			) + $settings['tabs']['style']['sections'];

	}

	return $settings;
}
endif;
add_filter( 'fl_builder_register_settings_form', 'theme_custom_fee_settings_forms', 10, 2);

// Default Post Types activated in Admin Options
//................................................................
if ( ! function_exists( 'theme_fee_post_types_defaults' ) ) :
function theme_fee_post_types_defaults( $value ) {

	// Add Portfolio CPT
	$value[] = 'portfolio';

	return $value;
}
endif;
add_filter( 'fee_post_types_defaults', 'theme_fee_post_types_defaults' );

#-----------------------------------------------------------------
# Actions
#-----------------------------------------------------------------

// Custom action to add a 50/50 content module when a new row is added
//................................................................
if ( ! function_exists( 'theme_custom_module_after_add_col_group' ) ) :
function theme_custom_module_after_add_col_group( $node_id, $cols, $position ) {

	// Flex Row - Add a default modules to columns.
	if ($cols == 'flex-row') {
		$columns = FLBuilderModel::get_nodes('column', $node_id->node);
		$i = 0;
		foreach ($columns as $col) {
			$module = ($i !== 0) ? 'photo' : 'rich-text';
			FLBuilderModel::add_default_module( $col->node, $module );
			$i++;
		}
	}

}
endif;
add_action( 'after_fee_add_col_group', 'theme_custom_module_after_add_col_group', 10, 3);