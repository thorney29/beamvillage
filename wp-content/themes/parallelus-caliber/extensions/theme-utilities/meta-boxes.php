<?php

#-----------------------------------------------------------------
# Meta Boxes
#-----------------------------------------------------------------

// Layout Options (title, header, etc.)
//=================================================================

if( ! class_exists( 'Theme_Custom_Layout_Metabox' ) ) :
class Theme_Custom_Layout_Metabox {

	public $index;
	public $field_keys;
	public $post_types;
	public $nonce_id;

	public function __construct() {

		$this->init();
	}

	private function init() {

		// Options
		$this->index = 'theme_custom_layout_metabox';
		$this->field_keys = array(
			'title',
			'header_style',
			'header_bg',
		);
		$this->post_types = array(
			'page',
			'post',
			'portfolio'
		);
		$this->nonce_id = $this->index.'_nonce';

		// Register with WP
		add_action( 'add_meta_boxes', array( $this, 'custom_metabox') );
		add_action( 'save_post', array( $this, 'custom_metabox_save') );

	}

	// Add boxes
	function custom_metabox() {

		// post types to include
		$post_types = apply_filters( $this->index.'_post_types', $this->post_types );

		foreach ($post_types as $type) {
			// Sidebar meta box
			add_meta_box(
				$this->index.'_options', //'custom_sidebar',
				__( 'Content Options', 'runway' ),
				array( $this, 'metabox_fields'),
				$type, // 'post',
				'side'
			);
		}
	}

	/* Add meta box content (sidebar list) */
	function metabox_fields( $post ) {

		// Top text
		// $output = '<p>'. __( 'Make a selection...', 'runway' ) .'</p>';
		$output = '';

		// Use nonce for verification
		wp_nonce_field( $this->index, $this->nonce_id );

		// Page Title
		$options = array(
			'title'         => __("Page Title", 'runway' ),
			'default-value' => '',
			'options'       => array(
				'show'        => __('Show', 'runway'),
				'hide'        => __('Hide', 'runway'),
				'in-header'   => __('In Header', 'runway')
			)
		);
		$output .= $this->custom_metabox_field_select( $post, 'title', $options );

		// Header Size
		$options = array(
			'title'         => __("Header", 'runway' ),
			'default-value' => '',
			'options'       => array(
				'none' => __('Hide Header', 'runway'),
				'show' => __('Show Header', 'runway'),
			)
		);
		$output .= $this->custom_metabox_field_select( $post, 'header_style', $options );

		// Bottom text
		// $output .= '<p><em>'. __( 'Some notes or details...', 'runway' ) .'</em></p>';

		// Script - Show On
		$output .= "<script type='text/javascript'>
jQuery(document).ready(function($){
	var themeHeaderShowHideSelect = jQuery('#container_theme_custom_layout_metabox_options_header_style');
	var themeHeaderMetaBox = jQuery('#theme_custom_header_metabox_options');
	var themePageTemplateSelect = jQuery('select[name=\"page_template\"]');
	if ( themePageTemplateSelect.length > 0 ) {
		jQuery(themePageTemplateSelect).change(function() {
			template = jQuery(this).val();
			if (template == 'templates/cover.php' || template == 'templates/cover-with-menu.php') {
				themeHeaderShowHideSelect.slideUp();
				themeHeaderMetaBox.slideUp();
			} else {
				themeHeaderShowHideSelect.slideDown();
				themeHeaderMetaBox.slideDown();
			}
		}).trigger('change');
	}
});
</script>";
		echo rf_string($output);
	}

	// Select Field builder
	function custom_metabox_field_select( $post, $key = 'default', $options = array() ) {
		global $wp_registered_sidebars;

		// Field settings
		$defaults = array(
			'title'         => __("Options", 'runway' ),
			'description'   => '',
			'field-name'    => $this->index.'_options_'.$key,
			'default-key'   => 'default',
			'default-value' => '(default)',
			'options'        => array()
		);
		$settings = array_merge($defaults, $options);

		$custom_data = get_post_custom($post->ID);
		if ( isset($custom_data[$settings['field-name']][0]) ) {
			$val = $custom_data[$settings['field-name']][0];
		}
		else {
			$val = $settings['default-key'];
		}

		$output  = '<div id="container_'. esc_attr($settings['field-name']) .'">';

		// The actual fields for data entry
		$output .= '<p style="margin-bottom:0.5em; font-weight:bold;"><label for="myplugin_new_field">'. $settings['title'] .'</label></p>';
		$output .= '<select name="'. esc_attr($settings['field-name']) .'">';

		// Add a default option
		$output .= '<option';
		if($val == $settings['default-key'])
			$output .= ' selected="selected"';
		$output .= ' value="'. esc_attr($settings['default-key']) .'">'. $settings['default-value'] .'</option>';

		// Fill the select element with all values
		foreach($settings['options'] as $value => $name) {
			$output .= "<option";
			if($value == $val)
				$output .= " selected='selected'";
			$output .= " value='". esc_attr($value) ."'>".$name."</option>";
		}

		$output .= "</select>";

		// Additional text at bottom of meta box.
		if (!empty($settings['description'])) {
			$output .= '<p style="margin-top:0;">'. $settings['description'] .'</p>';
		}

		$output .= "</div>";

		return $output;

	}

	/* When the post is saved, saves our custom data */
	function custom_metabox_save( $post_id ) {
		// Verify if this is an auto save routine. If not our form we dont nothing
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		  return;

		// verify this came from our screen and with proper authorization,
		// because save_post can be triggered at other times
		if ( isset($_POST[$this->nonce_id]) && !wp_verify_nonce( $_POST[$this->nonce_id], $this->index ) )
		  return;

		if ( !current_user_can( 'edit_pages', $post_id ) )
			return;

		foreach ($this->field_keys as $key) {
			$alias = $this->index.'_options_'.$key;
			if ( isset($_POST[$alias]) ) {
				$data = $_POST[$alias];
				update_post_meta( $post_id, $alias, $data);
			}
		}
	}
}
endif;

// Load the meta boxes
function theme_custom_layout_metabox_load() {
	$theme_custom_layout_metabox = new Theme_Custom_Layout_Metabox();
}
add_action( 'after_setup_theme', 'theme_custom_layout_metabox_load' );



// Header Styles
//=================================================================

if( ! class_exists( 'Theme_Custom_Header_Metabox' ) ) :
class Theme_Custom_Header_Metabox {

	public $index;
	public $field_keys;
	public $post_types;
	public $nonce_id;

	public function __construct() {

		$this->init();
	}

	private function init() {

		// Options
		$this->index = 'theme_custom_header_metabox';
		$this->field_keys = array(
			'header_size',
			'header_bg',
			'header_position',
			'header_source',
			'custom_bg'
		);
		$this->post_types = array(
			'page',
			'post',
			'portfolio'
		);
		$this->nonce_id = $this->index.'_nonce';

		// Register with WP
		add_action( 'add_meta_boxes', array( $this, 'custom_metabox') );
		add_action( 'save_post', array( $this, 'custom_metabox_save') );

		// Add some AJAX actions
		$this->custom_metabox_ajax_actions();

	}

	// Add boxes
	function custom_metabox() {

		// post types to include
		$post_types = apply_filters( $this->index.'_post_types', $this->post_types );

		foreach ($post_types as $type) {
			// Sidebar meta box
			add_meta_box(
				$this->index.'_options',
				__( 'Header Settings', 'runway' ),
				array( $this, 'metabox_fields'),
				$type, // 'post',
				'side'
			);
		}
	}

	/* Add meta box content (sidebar list) */
	function metabox_fields( $post ) {

		// Top text
		// $output = '<p>'. __( 'Make a selection...', 'runway' ) .'</p>';
		$output = '';

		// Use nonce for verification
		wp_nonce_field( $this->index, $this->nonce_id );

		// Header Size
		$options = array(
			'title'         => __("Size", 'runway' ),
			'default-value' => '',
			'options'       => array(
				'auto'   => __('Auto', 'runway'),
				'small'  => __('Small', 'runway'),
				'medium' => __('Medium', 'runway'),
				'large'  => __('Large', 'runway'),
				'xl'     => __('Extra Large', 'runway'),
				'full'   => __('Full Screen', 'runway')
			)
		);
		$output .= $this->custom_metabox_field_select( $post, 'header_size', $options );

		// Header Content Position
		$options = array(
			'title'         => __("Vertical Content Align", 'runway' ),
			'default-value' => '',
			'options'       => array(
				'none'   => __('Auto ', 'runway'),
				'top'    => __('Top', 'runway'),
				'middle' => __('Middle', 'runway'),
				'bottom' => __('Bottom', 'runway'),
			)
		);
		$output .= $this->custom_metabox_field_select( $post, 'header_position', $options );

		// Header Content Source
		$options = array(
			'title'         => __("Content Source", 'runway' ),
			'default-value' => '',
			'options'       => apply_filters('theme_metabox_header_content_source', array('none'=>'No Content')),
		);
		$output .= $this->custom_metabox_field_select( $post, 'header_source', $options );

		// Header Background
		$options = array(
			'title'         => __("Background Image", 'runway' ),
			'options'       => array(
				'none'           => __('No Image', 'runway'),
				'featured-image' => __('Featured image', 'runway'),
				'custom'         => __('Custom', 'runway'),
			)
		);
		$output .= $this->custom_metabox_field_select( $post, 'header_bg', $options );

		// Custom Background Image
		$options = array(
			'title'         => '',
			'default-key'   => '',
			'default-value' => '',
		);
		$output .= $this->custom_metabox_field_image( $post, 'custom_bg', $options );

		// Bottom text
		// $output .= '<p><em>'. __( 'Some notes or details...', 'runway' ) .'</em></p>';

		// Script - Show On
		$output .= "<script type='text/javascript'>
var themeHeaderMetaBox = jQuery('#theme_custom_header_metabox_options');
jQuery('select[name=\"theme_custom_layout_metabox_options_header_style\"]').change(function() {
	header = jQuery(this).val();
	if (header == 'show') {
		themeHeaderMetaBox.slideDown();
	} else {
		themeHeaderMetaBox.slideUp();
	}
}).trigger('change');

var themeHeaderValignMetaBox = jQuery('#container_theme_custom_header_metabox_options_header_position');
jQuery('select[name=\"theme_custom_header_metabox_options_header_size\"]').change(function() {
	bg_image = jQuery(this).val();
	if (bg_image !== 'auto') {
		themeHeaderValignMetaBox.slideDown();
	} else {
		themeHeaderValignMetaBox.slideUp();
	}
}).trigger('change');

var themeHeaderImageMetaBox = jQuery('#container_theme_custom_header_metabox_options_custom_bg');
jQuery('select[name=\"theme_custom_header_metabox_options_header_bg\"]').change(function() {
	bg_image = jQuery(this).val();
	if (bg_image == 'custom') {
		themeHeaderImageMetaBox.slideDown();
	} else {
		themeHeaderImageMetaBox.slideUp();
	}
}).trigger('change');
</script>";

		echo rf_string($output);
	}

	// Select Field builder
	function custom_metabox_field_select( $post, $key = 'default', $options = array() ) {
		global $wp_registered_sidebars;

		// Field settings
		$defaults = array(
			'title'         => __("Options", 'runway' ),
			'description'   => '',
			'field-name'    => $this->index.'_options_'.$key,
			'default-key'   => 'default',
			'default-value' => '(default)',
			'options'        => array()
		);
		$settings = array_merge($defaults, $options);

		$custom_data = get_post_custom($post->ID);
		if ( isset($custom_data[$settings['field-name']][0]) ) {
			$val = $custom_data[$settings['field-name']][0];
		}
		else {
			$val = $settings['default-key'];
		}

		$output  = '<div id="container_'. esc_attr($settings['field-name']) .'">';

		// The actual fields for data entry
		$output .= '<p style="margin-bottom:0.5em; font-weight:bold;"><label for="myplugin_new_field">'. $settings['title'] .'</label></p>';
		$output .= '<select name="'. esc_attr($settings['field-name']) .'">';

		// Add a default option
		$output .= '<option';
		if($val == $settings['default-key'])
			$output .= ' selected="selected"';
		$output .= ' value="'. esc_attr($settings['default-key']) .'">'. $settings['default-value'] .'</option>';

		// Fill the select element with all values
		foreach($settings['options'] as $value => $name) {
			$output .= "<option";
			if($value == $val)
				$output .= " selected='selected'";
			$output .= " value='". esc_attr($value) ."'>".$name."</option>";
		}

		$output .= "</select>";

		// Additional text at bottom of meta box.
		if (!empty($settings['description'])) {
			$output .= '<p style="margin-top:0;">'. $settings['description'] .'</p>';
		}

		$output .= "</div>";

		return $output;

	}

	// Image Select Field builder
	function custom_metabox_field_image( $post, $key = 'default', $options = array() ) {
		global $wp_registered_sidebars;

		// Field settings
		$defaults = array(
			'title'         => __("Options", 'runway' ),
			'description'   => '',
			'field-name'    => $this->index.'_options_'.$key,
			'default-key'   => 'default',
			'default-value' => '(default)',
			'options'        => array()
		);
		$settings = array_merge($defaults, $options);

		$custom_data = get_post_custom($post->ID);
		if ( isset($custom_data[$settings['field-name']][0]) ) {
			$val = $custom_data[$settings['field-name']][0];
		}
		else {
			$val = $settings['default-key'];
		}

		$image = $this->get_thumbnail_image($val);
		$show_select_image = (!empty($image)) ? ' style="display:none;"' : '';

		$output = '';

		// The actual fields for data entry
		$output .= '<div id="container_'. esc_attr($settings['field-name']) .'">';
		$output .= '<p style="margin-bottom:0.5em; font-weight:bold;"><label for="myplugin_new_field">'. $settings['title'] .'</label></p>';

		$output .= '<input id="'. esc_attr($settings['field-name']) .'" class="custom-data-type" data-type="fileupload-type" type="hidden" name="'. esc_attr($settings['field-name']) .'" value="'. esc_attr($val) .'" />';
		$output .= '<div class="field_label"><a href="#" id="upload_image_button-'. esc_attr($settings['field-name']) .'">';
		$output .= '<span class="text"'. $show_select_image .'>'. __('Select background image', 'runway') .'</span>';
		$output .= '<div class="header-bg-preview" id="preview_'. esc_attr($settings['field-name']) .'">'.$image.'</div>';
		$output .= '</a></div>';
		$output .= '<p class="header-bg-preview" id="preview_'. esc_attr($settings['field-name']) .'"></p>';
		$output .= '<div class="field_label"><a href="#" id="remove_image_button-'. esc_attr($settings['field-name']) .'">'. __('Remove background image', 'runway') .'</a></div>';

		// Bind lightbox to File select
		$output .= "<script type='text/javascript'>
var file_frame;
var current_button;
var attached_input;

(function(){
	jQuery('#upload_image_button-". esc_attr($settings['field-name']) ."').click(function(e) {
		e.preventDefault();
		current_button = jQuery(this);
		attached_input = current_button.parent().prev();

		if ( file_frame ) {
			file_frame.open();
			return;
		}

		file_frame = wp.media.frames.file_frame = wp.media({
			multiple: false
		});

		file_frame.on( 'select', function() {
			attachment = file_frame.state().get('selection').first().toJSON();
			attached_input.val(attachment.id);
			attached_input.trigger('change');

			if ( wp.customize ) {
				var api = wp.customize;
				var mysetting = api.instance(attached_input.attr('name'));
				api.instance(attached_input.attr('name')).set(attachment.id);
			}

			var e = jQuery.Event('keypress');
			e.which = 13;
			e.keyCode = 13;
			attached_input.trigger(e);
		});

		file_frame.open();
	});

	jQuery('#remove_image_button-". esc_attr($settings['field-name']) ."').click(function(e) {
		e.preventDefault();
		jQuery('#". esc_attr($settings['field-name']) ."').val('').trigger('change');
	});

	jQuery('#". esc_attr($settings['field-name']) ."').change(function(e) {
		var media_id = jQuery(this).val();

		if (media_id !== '' && media_id !== 0) {
			var data = {
				action: 'header_metabox_thumbnail',
				media_id: media_id
			};

			jQuery.post( ajaxurl, data, function( response ) {
				if (response !== '') {
					jQuery('#preview_". esc_attr($settings['field-name']) ."').html( response );
					jQuery('#upload_image_button-". esc_attr($settings['field-name']) ." .text').css('display','none');
					jQuery('#remove_image_button-". esc_attr($settings['field-name']) ."').css('display','inline');
				}
			});
		} else {
			jQuery('#preview_". esc_attr($settings['field-name']) ."').html('');
			jQuery('#upload_image_button-". esc_attr($settings['field-name']) ." .text').css('display','inline');
			jQuery('#remove_image_button-". esc_attr($settings['field-name']) ."').css('display','none');
		}
	}).trigger('change');
})(jQuery);

</script>";

		// Additional text at bottom of meta box.
		if (!empty($settings['description'])) {
			$output .= '<p style="margin-top:0;">'. $settings['description'] .'</p>';
		}

		$output .= '</div>';

		return $output;

	}

	/* When the post is saved, saves our custom data */
	function custom_metabox_save( $post_id ) {
		// Verify if this is an auto save routine. If not our form we dont nothing
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		  return;

		// verify this came from our screen and with proper authorization,
		// because save_post can be triggered at other times
		if ( isset($_POST[$this->nonce_id]) && !wp_verify_nonce( $_POST[$this->nonce_id], $this->index ) )
		  return;

		if ( !current_user_can( 'edit_pages', $post_id ) )
			return;

		foreach ($this->field_keys as $key) {
			$alias = $this->index.'_options_'.$key;
			if ( isset($_POST[$alias]) ) {
				$data = $_POST[$alias];
				update_post_meta( $post_id, $alias, $data);
			}
		}
	}


	// AJAX CONTROLS FOR THUMBNAIL IMAGES
	function custom_metabox_ajax_actions() {
		add_action( 'wp_ajax_nopriv_header_metabox_thumbnail', array( $this, 'custom_metabox_ajax_thumbnail') );
		add_action( 'wp_ajax_header_metabox_thumbnail', array( $this, 'custom_metabox_ajax_thumbnail') );
	}
	function custom_metabox_ajax_thumbnail() {

		// get the media ID
		$media_id = $_POST['media_id'];

		if (!empty($media_id)) {
			header( "Content-Type: text/plain" );
			echo rf_string($this->get_thumbnail_image($media_id));
		}

		exit; // all done!
	}

	function get_thumbnail_image( $media_id = 0 ) {

		if (!empty($media_id)) {
			return wp_get_attachment_image($media_id, 'large', false, array('style' => 'max-width: 100%; height: auto;'));
		}

		return;
	}
}
endif;

// Load the meta boxes
function theme_custom_header_metabox_load() {
	$theme_custom_header_metabox = new Theme_Custom_Header_Metabox();
}
add_action( 'after_setup_theme', 'theme_custom_header_metabox_load' );



// Custom Sidebar Select
//================================================================

if( ! class_exists( 'Theme_Custom_Sidebar_Metabox' ) ) :
class Theme_Custom_Sidebar_Metabox {

	public function __construct() {

		$this->init();
	}

	private function init() {

		add_action( 'add_meta_boxes', array( $this, 'theme_custom_sidebar_metabox') );
		add_action( 'save_post', array( $this, 'theme_select_custom_meta_sidebar_save') );

	}

	// Add boxes
	function theme_custom_sidebar_metabox() {

		// post types to include
		$post_types = array('page', 'post', 'portfolio');
		$post_types = apply_filters( 'theme_custom_sidebar_metabox_post_types', $post_types );

		foreach ($post_types as $type) {
			// Sidebar meta box
			add_meta_box(
				'theme_custom_sidebar_options', //'custom_sidebar',
				__( 'Sidebar Options', 'runway' ),
				array( $this, 'theme_select_custom_meta_sidebar'),
				$type, // 'post',
				'side'
			);
		}
	}

	/* Add meta box content (sidebar list) */
	function theme_select_custom_meta_sidebar( $post ) {

		// Top text
		// $output = '<p>'. __( 'Make a selection...', 'runway' ) .'</p>';
		$output = '';

		// Use nonce for verification
		wp_nonce_field( plugin_basename( __FILE__ ), 'theme_custom_sidebar_options_nonce' );

		// Left Sidebar Select
		$options = array(
			'title' => __("Left Sidebar", 'runway' ),
		);
		$output .= $this->theme_select_custom_meta_sidebar_field( $post, 'left', $options );

		// Right Sidebar Select
		$options = array(
			'title' => __("Right Sidebar", 'runway' ),
		);
		$output .= $this->theme_select_custom_meta_sidebar_field( $post, 'right', $options );

		// Bottom text
		$output .= '<p><em>'. __( 'The template must support the sidebar location or it will have no effect.', 'runway' ) .'</em></p>';

		echo rf_string($output);
	}

	// Sidebar select builder
	function theme_select_custom_meta_sidebar_field( $post, $key = 'default', $options = array() ) {
		global $wp_registered_sidebars;

		// Field settings
		$defaults = array(
			'title'         => __("Choose a sidebar", 'runway' ),
			'description'   => '',
			'field-name'    => 'theme_custom_sidebar_options_'.$key,
			'default-key'   => 'default',
			'default-value' => '(default)'
		);
		$settings = array_merge($defaults, $options);

		$custom_data = get_post_custom($post->ID);
		if ( isset($custom_data[$settings['field-name']][0]) ) {
			$val = $custom_data[$settings['field-name']][0];
		}
		else {
			$val = $settings['default-key'];
		}

		// The actual fields for data entry
		$output = '<p style="margin-bottom:0.5em; font-weight:bold;"><label for="myplugin_new_field">'. $settings['title'] .'</label></p>';
		$output .= '<select name="'. esc_attr($settings['field-name']) .'">';

		// Add a default option
		$output .= '<option';
		if($val == $settings['default-key'])
			$output .= ' selected="selected"';
		$output .= ' value="'. esc_attr($settings['default-key']) .'">'. $settings['default-value'] .'</option>';

		// Fill the select element with all registered sidebars
		foreach($wp_registered_sidebars as $sidebar_id => $sidebar) {
			$output .= "<option";
			if($sidebar_id == $val)
				$output .= " selected='selected'";
			$output .= " value='". esc_attr($sidebar_id) ."'>".$sidebar['name']."</option>";
		}

		$output .= "</select>";

		// Description text below field.
		if (!empty($settings['description'])) {
			$output .= '<p style="margin-top:0;">'. $settings['description'] .'</p>';
		}

		return $output;

	}

	/* When the post is saved, saves our custom data */
	function theme_select_custom_meta_sidebar_save( $post_id ) {
		// Verify if this is an auto save routine. If not our form we dont nothing
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		  return;

		// verify this came from our screen and with proper authorization,
		// because save_post can be triggered at other times
		$nonce_id = 'theme_custom_sidebar_options_nonce';

		if ( isset($_POST[$nonce_id]) && !wp_verify_nonce( $_POST[$nonce_id], plugin_basename( __FILE__ ) ) )
		  return;

		if ( !current_user_can( 'edit_pages', $post_id ) )
			return;

		$keys = array('left', 'right');
		foreach ($keys as $key) {
			$alias = 'theme_custom_sidebar_options_'.$key;
			if ( isset($_POST[$alias]) ) {
				$data = $_POST[$alias];
				update_post_meta( $post_id, $alias, $data);
			}
		}
	}
}
endif;

// Load the meta boxes
function theme_custom_sidebar_metabox_load() {
	$theme_custom_sidebar_metabox = new Theme_Custom_Sidebar_Metabox();
}
add_action( 'after_setup_theme', 'theme_custom_sidebar_metabox_load' );


