<?php
/**
 * Extension Name: Beaver Builder Compatibility
 * Description: Extends the functionality of Beaver Builder plugin
 * Version: 0.1
 */


if ( ! class_exists( 'BeaverBuilderCompat' ) ) {

	class BeaverBuilderCompat {

		const LIVE_EDITOR_SLUG = 'live-editor/live-editor.php';

		public static function init() {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$live_editor_active = is_plugin_active( self::LIVE_EDITOR_SLUG );

			if ( class_exists( 'FLBuilder' ) && ! $live_editor_active ) {
				// actions
				add_action( 'init', array( __CLASS__, 'load_modules' ), 1 );
				add_action( 'init', array( __CLASS__, 'add_custom_row_layouts' ) );

				// filters
				add_filter( 'fl_builder_column_custom_class', array( __CLASS__, 'column_custom_class' ), 10, 2 );
				add_filter( 'fl_builder_row_custom_class', array( __CLASS__, 'row_custom_class' ), 10, 2 );
				add_filter( 'fl_builder_register_settings_form', array( __CLASS__, 'register_settings_form' ), 10, 2 );
				add_filter( 'fl_builder_template_path', array( __CLASS__, 'template_path' ), 99, 3 );
				add_filter( 'fl_rw_row_content_class', array( __CLASS__, 'row_content_class' ), 10, 2 );
				add_filter( 'fl_rw_row_content_wrap_class', array( __CLASS__, 'row_content_wrap_class' ), 10, 2 );
				add_action( 'fl_builder_ui_panel_after_rows', array( __CLASS__, 'custom_row_type' ) );
				add_filter( 'fl_builder_settings_form_defaults', array( __CLASS__, 'settings_form_defaults' ), 10, 2 );
				add_filter( 'fl_builder_settings_form_config', array( __CLASS__, 'settings_form_config' ) );
				add_filter( 'rf_show_notice_can_activate', array( __CLASS__, 'show_plugin_installer_notice' ), 10, 3 );
				add_filter( 'rf_show_notice_can_install', array( __CLASS__, 'show_plugin_installer_notice' ), 10, 3 );
			}

			add_action( 'activate_plugin', array( __CLASS__, 'adjust_active_plugins' ), 10, 2 );
			add_action( 'activated_plugin', array( __CLASS__, 'adjust_active_plugins' ), 10, 2 );

			if ( $plugin = get_option( 'flrw_show_notice_plugin_deactivated', false ) ) {
				add_action( 'admin_notices', array( __CLASS__, 'plugin_disabled_admin_notice' ) );
				add_action( 'network_admin_notices', array( __CLASS__, 'plugin_disabled_admin_notice' ) );
			}

			// filters (with or without BB)
			add_filter( 'body_class', array( __CLASS__, 'add_body_class' ), 99 );
		}

		/**
		 * Hides Plugin Installer notices
		 *
		 * @param bool $to_show Whether to show the notice about plugin
		 * @param array $plugin Plugin data.
		 * @param bool $required Whether the plugin is required. Default false.
		 *
		 * @return bool $to_show
		 */
		public static function show_plugin_installer_notice( $to_show, $plugin, $required = false ) {
			if (
				class_exists( 'FLBuilder' )
				&& $plugin['slug'] == self::LIVE_EDITOR_SLUG
				&& ! is_plugin_active( self::LIVE_EDITOR_SLUG )
			) {
				$to_show = false;
			}

			return $to_show;
		}

		/**
		 * Deactivates Live Editor Plugin if Beaver Builder Plugin is activated
		 *
		 * @return void
		 */
		public static function adjust_active_plugins( $plugin, $network_wide ) {
			$bb_main_file = 'fl-builder.php';

			// Before Beaver Builder Plugin activation (Deactivates Live Editor Plugin if Beaver Builder Plugin will be activated)
			if ( 'activate_plugin' == current_filter() && false !== strpos( $plugin, $bb_main_file ) ) {
				if ( is_plugin_active( self::LIVE_EDITOR_SLUG ) ) {
					deactivate_plugins( self::LIVE_EDITOR_SLUG );
					$live_editor_data = get_plugin_data( trailingslashit( WP_PLUGIN_DIR ) . self::LIVE_EDITOR_SLUG );
					$bb_data          = get_plugin_data( trailingslashit( WP_PLUGIN_DIR ) . $plugin );

					update_option( 'flrw_show_notice_plugin_deactivated', array(
						'deactivated_plugin_name' => $live_editor_data['Name'],
						'activated_plugin_name'   => $bb_data['Name'],
					) );
				}
			} // After Live Editor Plugin activation (Deactivates Live Editor Plugin if Beaver Builder Plugin is activated)
			else if ( 'activated_plugin' == current_filter() && false !== strpos( $plugin, self::LIVE_EDITOR_SLUG ) ) {
				// check if Beaver Builder plugin is activated
				$active_plugins = wp_get_active_and_valid_plugins();
				$beaver_builder = false;
				foreach ( $active_plugins as $active_plugin ) {
					if ( false !== strpos( $active_plugin, $bb_main_file ) ) {
						$beaver_builder = $active_plugin;
						break;
					}
				}

				if ( false !== $beaver_builder ) {
					$live_editor_data = get_plugin_data( trailingslashit( WP_PLUGIN_DIR ) . $plugin );
					$bb_data          = get_plugin_data( $beaver_builder );
					deactivate_plugins( self::LIVE_EDITOR_SLUG );

					update_option( 'flrw_show_notice_plugin_deactivated', array(
						'deactivated_plugin_name' => $live_editor_data['Name'],
						'activated_plugin_name'   => $bb_data['Name'],
					) );
				}
			}
		}

		/**
		 * Adds admin notice if Live Editor Plugin was deactivated
		 *
		 * @return void
		 */
		public static function plugin_disabled_admin_notice() {
			$plugins = get_option( 'flrw_show_notice_plugin_deactivated', false );

			if (
				is_array( $plugins )
				&& array_key_exists( 'deactivated_plugin_name', $plugins )
				&& array_key_exists( 'activated_plugin_name', $plugins )
			) {
				$notice = sprintf(
					__( '<em>%s</em> has been automatically deactivated because <em>%s</em> and <em>%s</em> cannot work simultaneously.', 'runway' ),
					$plugins['deactivated_plugin_name'],
					$plugins['deactivated_plugin_name'],
					$plugins['activated_plugin_name']
				);
			}
			echo '<div class="updated notice">';
			echo '<p><strong>' . wp_kses_post( $notice ) . '</strong></p>';
			echo '</div>';

			update_option( 'flrw_show_notice_plugin_deactivated', false );
		}

		/**
		 * Adds special body class to specify not Live Editor
		 *
		 * @return void
		 */
		public static function add_body_class( $classes ) {


			if ( in_array( 'fl-builder', $classes) ) {
				$live_editor_active = is_plugin_active( self::LIVE_EDITOR_SLUG );
				
				if ( class_exists( 'FLBuilder' ) && ! $live_editor_active ) {
					$classes[] = 'caliber-bb';

				} else {
					$classes[] = 'caliber-le';
				}
			}

			return $classes;
		}

		/**
		 * Adds custom row layouts
		 *
		 * @return void
		 */
		public static function add_custom_row_layouts() {
			FLBuilderModel::$row_layouts['flex-row'] = array( 50, 50 );
		}

		/**
		 * Modifies a form data
		 *
		 * @param array $form The form data.
		 *
		 * @return array
		 */
		public static function settings_form_config( $form ) {
			// $node_id from attr
			if ( isset( $form['attrs'] ) ) {
				preg_match( '/data-node=\"(.*)\"/', $form['attrs'], $matches ); // get node id
				$node_id = isset( $matches[1] ) ? $matches[1] : '';
				$node    = FLBuilderModel::get_node( $node_id );

				if ( ! is_object( $node ) ) {
					return $form;
				}

				// looks like a dirty hack
				$is_row_settings = false;
				$post_data       = FLBuilderModel::get_post_data();
				if (
					array_key_exists( 'action', $post_data )
					&& $post_data['action'] == 'render_row_settings'
				) {
					$is_row_settings = true;
				}

				// Customize the editor fields for FLEX ROW settings
				if ( $is_row_settings && isset( $node->settings->custom_type ) && $node->settings->custom_type == 'flex-row' ) {

					// Column Positioning and Order
					$form['tabs']['style']['sections']['columns_align'] = array(
						'title'  => __( 'Column Positioning', 'runway' ),
						'fields' => array(
							'column_order' => array(
								'type'    => 'select',
								'label'   => __( 'Column Order', 'runway' ),
								'default' => 'row',
								'options' => array(
									'row'         => __( 'Default', 'runway' ),
									'row-reverse' => __( 'Reverse', 'runway' )
								),
								'help'    => __( 'Applies to large screens only. This can reverse the column order for alternating rows and keep the appearance consistant for mobile devices.', 'runway' ),
							),

							'column_flex_y'  => array(
								'type'    => 'select',
								'label'   => __( 'Vertical Position', 'runway' ),
								'default' => 'stretch',
								'options' => array(
									'start'    => __( 'Top', 'runway' ),
									'end'      => __( 'Bottom', 'runway' ),
									'center'   => __( 'Center', 'runway' ),
									'baseline' => __( 'Baseline', 'runway' ),
									'stretch'  => __( 'Stretch (full height)', 'runway' )
								),
								'toggle'  => array(
									'stretch' => array(
										'fields' => array( 'column_align_y' )
									)
								),
								'help'    => __( 'The vertical position of column containers.', 'runway' ),
							),
							'column_align_y' => array(
								'type'    => 'select',
								'label'   => __( 'Vertical Align', 'runway' ),
								'default' => 'center',
								'options' => array(
									'center' => __( 'Center', 'runway' ),
									'start'  => __( 'Top', 'runway' ),
									'end'    => __( 'Bottom', 'runway' ),
								),
								'help'    => __( 'The vertical alignment of content.', 'runway' ),
							)
						)
					);

					// Remove the "Content Alignment" option
					unset( $form['tabs']['style']['sections']['general']['fields']['content_alignment'] );

					// Fix the array so "Column Positioning" appears immediately after the general options
					$form['tabs']['style']['sections'] = array(
						                                     'general'            => $form['tabs']['style']['sections']['general'],
						                                     'container_defaults' => $form['tabs']['style']['sections']['container_defaults'],
						                                     'columns_align'      => $form['tabs']['style']['sections']['columns_align']
					                                     ) + $form['tabs']['style']['sections'];

				} // END flex-row

				// Customize the editor fields for FLEX COLUMN settings
				if ( $node->type == 'column' && isset( $node->parent ) ) {
					$col_group = FLBuilderModel::get_node( $node->parent );

					if ( isset( $col_group->parent ) ) {
						$row = FLBuilderModel::get_node( $col_group->parent );

						if ( isset( $row->settings->custom_type ) && $row->settings->custom_type == 'flex-row' ) {

							// Remove the "equalize heights" option
							unset( $form['tabs']['style']['sections']['general']['fields']['equal_height'] );

							// Remove the "Content Alignment" option
							unset( $form['tabs']['style']['sections']['general']['fields']['content_alignment'] );

							// Add a couple of "General" column settings for the Flex Row
							$form['tabs']['style']['sections']['general']['fields']['column_flex_y']  = array(
								'type'    => 'select',
								'label'   => __( 'Vertical Position', 'runway' ),
								'default' => 'dafault',
								'options' => array(
									'dafault'  => __( '[use row settings]', 'runway' ),
									'start'    => __( 'Top', 'runway' ),
									'end'      => __( 'Bottom', 'runway' ),
									'center'   => __( 'Center', 'runway' ),
									'baseline' => __( 'Baseline', 'runway' ),
									'stretch'  => __( 'Stretch (full height)', 'runway' )
								),
								'toggle'  => array(
									'stretch' => array(
										'fields' => array( 'column_align_y' )
									)
								),
								'help'    => __( 'The vertical position of this column (overrides main row settings).', 'runway' ),
								'preview' => array(
									'type' => 'none' // preview doesn't work on these!
								)
							);
							$form['tabs']['style']['sections']['general']['fields']['column_align_y'] = array(
								'type'    => 'select',
								'label'   => __( 'Vertical Align', 'runway' ),
								'default' => 'center',
								'options' => array(
									'center' => __( 'Center', 'runway' ),
									'start'  => __( 'Top', 'runway' ),
									'end'    => __( 'Bottom', 'runway' ),
								),
								'help'    => __( 'The vertical alignment of content.', 'runway' ),
								'preview' => array(
									'type' => 'none' // preview doesn't work on these!
								)
							);
						}
					}
				}
			}

			return $form;
		}

		/**
		 * Modifies a settings object for a form
		 *
		 * @param object $defaults The default settings
		 * @param string $form_type The type of form
		 *
		 * @return object
		 */
		public static function settings_form_defaults( $defaults, $form_type ) {
			// looks like a dirty hack
			$post_data = FLBuilderModel::get_post_data();
			if (
				$form_type == 'row'
				&& array_key_exists( 'action', $post_data )
				&& $post_data['action'] == 'render_new_row'
				&& array_key_exists( 'cols', $post_data )
				&& $post_data['cols'] == 'flex-row'
			) {
				// add custom row attribute
				$defaults->custom_type = 'flex-row';
			}

			return $defaults;
		}

		/**
		 * Adds Custom Row Type to the builder UI
		 *
		 * @return void
		 */
		public static function custom_row_type() {
			?>

			<div class="fl-builder-blocks-section">
				<span class="fl-builder-blocks-section-title">
					<?php _e( 'Advanced Row Layouts', 'runway' ); ?>
					<i class="fa fa-chevron-down"></i>
				</span>
				<div class="fl-builder-blocks-section-content fl-builder-rows">
					<span class="fl-builder-block fl-builder-block-row" data-cols="flex-row">
						<span class="fl-builder-block-title"><?php _e( 'Flex Row', 'runway' ); ?></span>
					</span>
				</div>
			</div>

			<?php
		}

		/**
		 * Loads the classes for builder modules.
		 *
		 * @return void
		 */
		public static function load_modules() {
			$path        = __DIR__ . '/modules/';
			$dir         = dir( $path );
			$module_path = '';

			while ( false !== ( $entry = $dir->read() ) ) {

				if ( ! is_dir( $path . $entry ) || $entry == '.' || $entry == '..' ) {
					continue;
				}

				// Paths to check.
				$module_path  = $entry . '/' . $entry . '.php';
				$child_path   = get_stylesheet_directory() . '/fl-builder/modules/' . $module_path;
				$theme_path   = get_template_directory() . '/fl-builder/modules/' . $module_path;
				$builder_path = __DIR__ . '/modules/' . $module_path;

				// Check for the module class in a child theme.
				if ( is_child_theme() && file_exists( $child_path ) ) {
					require_once $child_path;
				} // Check for the module class in a parent theme.
				else if ( file_exists( $theme_path ) ) {
					require_once $theme_path;
				} // Check for the module class in the builder directory.
				else if ( file_exists( $builder_path ) ) {
					require_once $builder_path;
				}
			}
		}

		/**
		 * Adds custom classes to columns
		 *
		 * @param string $column_class Сolumn classes
		 * @param object $col Сolumn node object
		 *
		 * @return string Сolumn classes
		 */
		public static function column_custom_class( $column_class, $col ) {
			$modules      = FLBuilderModel::get_nodes( 'module', $col );
			$module_types = array();
			foreach ( $modules as $module ) {
				$module_types[] = $module->settings->type;
			}

			// Posts module: Add some classes for module styling
			if ( in_array( 'posts', $module_types ) ) {
				$column_class .= ' container-post';
			}

			// Portfolio module: Add some classes for module styling
			if ( in_array( 'portfolio', $module_types ) ) {
				$column_class .= ' container-portfolio';
			}

			// Column classes for "flex-row" and "50-50" rows (embellish and pull-bottom)
			if ( isset( $col->parent ) ) {
				$col_group = FLBuilderModel::get_node( $col->parent );

				if ( isset( $col_group->parent ) ) {
					$row = FLBuilderModel::get_node( $col_group->parent );

					// Section column class for "flex-row"
					if ( isset( $row->settings->custom_type ) && $row->settings->custom_type == 'flex-row' ) {
						$column_class .= ' flex-col';

						// Check for column specific settings
						if ( isset( $col->settings->column_flex_y ) && $col->settings->column_flex_y !== 'default' ) {
							// Set column specific vertical position
							$column_class .= ' flex-self-y-' . $col->settings->column_flex_y;
						}
						if ( isset( $col->settings->column_flex_y ) && $col->settings->column_flex_y === 'stretch' && isset( $col->settings->column_align_y ) ) {
							// Set column specific vertical align
							$column_class .= ' flex-self-justify-' . $col->settings->column_align_y;
						}
					}
				}
			}

			return $column_class;
		}

		/**
		 * Adds custom classes to rows
		 *
		 * @param string $row_class Row classes
		 * @param object $row Row node object
		 *
		 * @return string Row classes
		 */
		public static function row_custom_class( $row_class, $row ) {
			$row_class .= ' section-wrapper';

			// Custom container padding horizontal
			if ( isset( $row->settings->content_padding_x ) ) {

				if ( $row->settings->content_padding_x == 'no-pad' ) {
					$row_class .= ' no-padding-x';
				}
				if ( $row->settings->content_padding_x == 'no-pad-l' ) {
					$row_class .= ' no-padding-x-l';
				}
				if ( $row->settings->content_padding_x == 'no-pad-r' ) {
					$row_class .= ' no-padding-x-r';
				}
			}

			// Custom container padding vertical
			if ( isset( $row->settings->content_padding_y ) ) {

				if ( $row->settings->content_padding_y == 'no-pad' ) {
					$row_class .= ' no-padding-y';
				}
				if ( $row->settings->content_padding_y == 'no-pad-t' ) {
					$row_class .= ' no-padding-y-t';
				}
				if ( $row->settings->content_padding_y == 'no-pad-b' ) {
					$row_class .= ' no-padding-y-b';
				}
			}

			if ( isset( $row->settings->custom_type ) && $row->settings->custom_type == 'flex-row' ) {
				$row_class .= ' flex-wrapper';
			}

			return $row_class;
		}

		/**
		 * Adds custom classes to rows content
		 *
		 * @param string $content_class Content classes
		 * @param object $row Row node object
		 *
		 * @return string Content classes
		 */
		public static function row_content_class( $content_class, $row ) {
			$theme_content_class = 'container';

			if ( is_object( $row ) ) {

				// Size settings
				if ( isset( $row->settings->width ) ) {
					// Check the custom width settings
					if ( $row->settings->width == 'full' ) {

						$theme_content_class = str_replace( 'container', 'container-xl', $theme_content_class );

						// Content container (may be legacy now, probably could be commented out)
						if ( isset( $row->settings->content_width ) && $row->settings->content_width == 'full' ) {
							$theme_content_class .= ' no-padding';
						}
					}
					if ( $row->settings->width == 'browser' ) {
						$theme_content_class = str_replace( 'container', 'container-browser', $theme_content_class );
					}
					if ( $row->settings->width == 'custom' ) {
						$theme_content_class = str_replace( 'container', 'container-custom-width',
							$theme_content_class );
					}
					// A helper class for easier work with fluid  containers
					if ( $row->settings->width !== 'fixed' && $row->settings->width !== 'custom' ) {
						$theme_content_class .= ' fluid-width';
					}
				}

				// Auto-margin fix for horizontal padding on only one side
				if ( isset( $row->settings->content_padding_x ) && strpos( $theme_content_class, 'container-xl' ) !== false ) {

					if ( $row->settings->content_padding_x == 'no-pad-r' ) {
						$theme_content_class .= ' auto-margin-l';
					}
					if ( $row->settings->content_padding_x == 'no-pad-l' ) {
						$theme_content_class .= ' auto-margin-r';
					}
				}
			}

			// Custom row types
			if ( isset( $row->settings->custom_type ) ) {

				// Helper class for "flex-row" section settings
				if ( $row->settings->custom_type == 'flex-row' ) {

					// Check column order
					if ( isset( $row->settings->column_order ) && $row->settings->column_order == 'row-reverse' ) {
						$theme_content_class .= ' flex-cols-reverse';
					}

					// Check flex column positioning
					if ( isset( $row->settings->column_flex_x ) ) {
						$theme_content_class .= ' flex-cols-x-' . $row->settings->column_flex_x; // horizontal position
					}
					if ( isset( $row->settings->column_flex_y ) ) {
						$theme_content_class .= ' flex-cols-y-' . $row->settings->column_flex_y; // vertical position

						// Vertical align
						if ( $row->settings->column_flex_y == 'stretch' && isset( $row->settings->column_align_y ) ) {
							$theme_content_class .= ' flex-justify-' . $row->settings->column_align_y;
						}
					}

					// add the flexbox container class only
					$theme_content_class .= ' flex-container-wrap';
				}
			}

			// During editing, use default editor classes
			if ( FLBuilderModel::is_builder_active() ) {
				return trim( $content_class . ' ' . $theme_content_class );
			}

			return trim( $theme_content_class );
		}

		/**
		 * Adds custom classes to rows content wrap elemtn
		 *
		 * @param string $content_class Content wrap classes
		 * @param object $row Row node object
		 *
		 * @return string Content wrap classes
		 */
		public static function row_content_wrap_class( $content_wrap_class, $row ) {
			return $content_wrap_class . ' container-wrap';
		}

		/**
		 * Modifies the default editor settings for rows, columns, modules...
		 *
		 * @param array $settings The form data.
		 * @param string $id The form id.
		 *
		 * @return string The form data
		 */
		public static function register_settings_form( $settings, $id ) {
			// Modify the default Global settings
			if ( $id == 'global' ) {
				$settings['tabs']['general']['sections']['rows']['fields']['row_width']['default']         = '1180'; // content width
				$settings['tabs']['general']['sections']['rows']['fields']['row_padding']['default']       = '';     // row padding
				$settings['tabs']['general']['sections']['rows']['fields']['row_margins']['default']       = '';     // row margin
				$settings['tabs']['general']['sections']['modules']['fields']['module_margins']['default'] = '25';   // module margin
			}

			// Modify the default Row settings text a little
			if ( $id == 'row' ) {
				// Remove the default row height setting
				unset( $settings['tabs']['style']['sections']['general']['fields']['full_height'] );

				$settings['tabs']['style']['sections']['general']['fields']['width']['label']   = 'Container Size'; // Width
				$settings['tabs']['style']['sections']['general']['fields']['width']['options'] = array(
					'fixed'   => __( 'Fixed', 'runway' ),
					'full'    => __( 'Full Width', 'runway' ),
					'browser' => __( 'Browser Width', 'runway' ),
				);
				$settings['tabs']['style']['sections']['general']['fields']['width']['toggle']  = array(
					'custom' => array(
						'fields' => array( 'custom_width' )
					)
				);
				$settings['tabs']['style']['sections']['general']['fields']['width']['help']    = __( 'Restrict to design boundry or extend to the browser width.', 'runway' );
				unset( $settings['tabs']['style']['sections']['general']['fields']['width']['preview'] ); // force a preview refresh on change

				// Custom Width
				$settings['tabs']['style']['sections']['general']['fields']['custom_width'] = array(
					'type'        => 'text',
					'label'       => __( 'Width', 'runway' ),
					'default'     => '1130',
					'description' => 'px',
					'maxlength'   => '4',
					'size'        => '5'
				);

				// Container Default Padding
				unset( $settings['tabs']['style']['sections']['general']['fields']['content_width'] );
				$settings['tabs']['style']['sections']['container_defaults'] = array(
					'title'  => __( 'Container Defaults', 'runway' ),
					'fields' => array(
						'content_padding_x' => array(
							'type'    => 'select',
							'label'   => __( 'Horizontal Padding', 'runway' ),
							'default' => 'default',
							'options' => array(
								'default'  => __( 'Theme default', 'runway' ),
								'no-pad'   => __( 'No horizontal padding', 'runway' ),
								'no-pad-l' => __( 'No horizontal - Left', 'runway' ),
								'no-pad-r' => __( 'No horizontal - Right', 'runway' )
							),
							'help'    => __( "Use the theme's horizontal padding and margins for the container.", 'runway' ),
						),
						// 'content_padding_y' => array(
						// 	'type'    => 'select',
						// 	'label'   => __( 'Vertical Padding', 'runway' ),
						// 	'default' => 'default',
						// 	'options' => array(
						// 		'default'  => __( 'Theme default', 'runway' ),
						// 		'no-pad'   => __( 'No vertical padding', 'runway' ),
						// 		'no-pad-t' => __( 'No vertical - Top', 'runway' ),
						// 		'no-pad-b' => __( 'No vertical - Bottom', 'runway' )
						// 	),
						// 	'help'    => __( "Use the theme's vertical padding and margins for the container.", 'runway' )
						// )
					)
				);

				// Fix the array so "Container Defaults" appears immediately after the general options
				$settings['tabs']['style']['sections'] = array(
					                                         'general'            => $settings['tabs']['style']['sections']['general'],
					                                         'container_defaults' => $settings['tabs']['style']['sections']['container_defaults']
				                                         ) + $settings['tabs']['style']['sections'];

			}

			return $settings;
		}

		/**
		 * Replaces the template 'row.php' with a custom one
		 *
		 * @param string $template_path
		 * @param string $template_base
		 * @param string $slug
		 *
		 * @return string Custom template
		 */
		public static function template_path( $template_path, $template_base = null, $slug = null ) {
			$includes_dir = __DIR__ . '/includes/';

			if ( $template_base == 'row' && $slug == '' && file_exists( $includes_dir . $template_base . '.php' ) ) {
				$template_path = $includes_dir . $template_base . '.php';
			}

			return $template_path;
		}

	}

	BeaverBuilderCompat::init();

}
