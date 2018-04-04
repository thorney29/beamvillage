<?php

global $developer_tools, $Themes_Manager, $wp_version;
// Look up theme data
if ( isset( $_REQUEST['name'] ) && !isset( $options ) ) {
	$options = $developer_tools->load_settings( $_REQUEST['name'] );
}
// Convert data to variables
if ( isset( $options ) ) {
	extract( $options );
}

$exploded_version = explode('.', $wp_version);

if($exploded_version[0] <= 3 && (isset($exploded_version[1]) && $exploded_version[1] < 8)) {
	wp_enqueue_style('dashicons_css', FRAMEWORK_URL.'framework/includes/themes-manager/css/dashicons.css');
}
wp_enqueue_style('dashicons_custom_style_css', FRAMEWORK_URL.'framework/includes/themes-manager/css/custom-style.css');
wp_enqueue_script('dashicons', FRAMEWORK_URL.'framework/includes/themes-manager/js/dashicons.js');
wp_enqueue_script('theme-conf', FRAMEWORK_URL.'framework/includes/themes-manager/js/theme-conf.js');

$custom_icon_src = isset($Folder)? (file_exists(get_home_path() . 'wp-content/themes/' . $Folder . '/custom-icon.png')? home_url() . '/wp-content/themes/' . $Folder . '/custom-icon.png' : '') : '';

// A few defaults and error checking
$Name = (isset($Name)) ? $Name : '';

// Beadcrumbs
$navEdit = ($Name) ? __( 'Edit', 'runway' ) .": ". $Name : __( 'Edit Theme', 'runway' );
$navText = ($developer_tools->navigation == 'edit-theme') ? $navEdit  : __( 'Create new', 'runway' );
$developer_tools->navigation_bar( array($navText) );

$required = '<p class="description required">' . __( 'Required', 'runway' ) . '</p>';
?>

<p>
	<?php echo __('Fill out the options below to create a new Runway child theme. The new child theme folder will be created in the', 'runway'); ?> <code>wp-content/themes</code> <?php echo __('folder', 'runway'); ?>.
</p>

<form method="post" enctype="multipart/form-data">

	<?php if ( isset( $errors ) ) { ?>
		<ul class="errors" style="border: solid 3px maroon; border-radius: 15px; width: 600px; padding: 10px; background-color: yellow;">
	<?php
	foreach ( $errors as $error ) {
		?>

				<li><?php echo rf_string( $error); ?></li>

			<?php } ?>
		</ul>
	<?php } ?>

	<input type="hidden" name="save" value="true">
	
	<?php wp_nonce_field( 'edit-theme' ); ?>

	<table class="form-table">

		<?php
		$row = array( __( 'Title', 'runway' ) . $required, $html->settings_input( 'theme_options[Name]', isset( $Name ) ? $Name : '' ) );
		$html->setting_row( $row );

		$row = array(
			__( 'Menu icon', 'runway' ) . $required,

				$html->settings_select( 'theme_options[Icon]',
					array(
					'default-wordpress-icon' => __( 'Default WordPress Icon', 'runway' ),
					'custom-icon' => __( 'Custom icon', 'runway' ),
				),
			isset( $Icon ) ? $Icon : null ),
		);

		$html->setting_row( $row );
		?>

		<tr class='choose-default-wordpress' style="display: none;">
			<td>
				<input class='dashicon-code-selected' name="theme_options[default-wordpress-icon-code]" type="hidden" value=<?php echo isset($options['default-wordpress-icon-code'])? esc_attr($options['default-wordpress-icon-code']) : '';?> >
				<input class='dashicon-class-selected' name="theme_options[default-wordpress-icon-class]" type="hidden" value=<?php echo isset($options['default-wordpress-icon-class'])? esc_attr($options['default-wordpress-icon-class']) : '';?> >
			</td>
			<td>
				<?php require_once(get_template_directory().'/framework/templates/dashicons.php'); ?>
			</td>
		</tr>
		<tr class='choose-another' style="display: none;">
			<td><?php echo __('Custom icon', 'runway'); ?>:</td>
			<td>
				<?php if(isset($custom_icon_src) && !empty($custom_icon_src)): ?>
					<div>
						<img src="<?php echo esc_url($custom_icon_src); ?>" width="18" height="18" />
					</div>
					<a href="#" class='choose-another-link' ><?php echo __('Choose Another Icon', 'runway'); ?></a>
				<?php else: ?>
					<a href="#" class='choose-another-link' ><?php echo __('Choose Custom Icon', 'runway'); ?></a>
				<?php endif; ?>
			</td>
		</tr>
		<tr class='custom-icon-upload' style="display: none;">
			<td><?php echo __('Custom icon', 'runway'); ?>:</td>
			<td>
				<input type="file" name="theme_options[CustomIcon]" value="" />
				<p class="description"><?php echo __('Recommended size 36x36px for HiDPI screens. Icon displayed at 16x16px.', 'runway'); ?></p>
			</td>
		</tr>
<?php
$comment = __( 'An optional webpage associated with the theme.', 'runway' );
$comment = $html->format_comment( $comment );
$row = array( __( 'Theme URI', 'runway' ), $html->settings_input( 'theme_options[URI]', isset( $URI ) ? $URI : '' ) . $comment );
$html->setting_row( $row );

$row = array( __( 'Description', 'runway' ), $html->settings_textarea( 'theme_options[Description]', isset( $Description ) ? $Description : '' ) );
$html->setting_row( $row );

$row = array( __( 'Author name', 'runway' ), $html->settings_input( 'theme_options[AuthorName]', isset( $AuthorName ) ? $AuthorName : '' ) );
$html->setting_row( $row );

$comment = __( 'An optional link to the author\'s website.', 'runway' );
$comment = $html->format_comment( $comment );
$row = array( __( 'Author URI', 'runway' ), $html->settings_input( 'theme_options[AuthorURI]', isset( $AuthorURI ) ? $AuthorURI : '' ) . $comment );
$html->setting_row( $row );

$row = array( __( 'Version', 'runway' ), $html->settings_input( 'theme_options[Version]', isset( $Version ) ? $Version : '' ) );
$html->setting_row( $row );


if ( isset( $Tags ) && is_array( $Tags ) ) {
	$Tags = implode( ' ', $Tags );
}

$comment = __( 'Keywords and template tags associated with this theme', 'runway' );
$comment = $html->format_comment( $comment );
$row = array( __( 'Tags', 'runway' ), $html->settings_textarea( 'theme_options[Tags]', isset( $Tags ) ? $Tags : '' ) . $comment );
$html->setting_row( $row );

$comment = __( 'Optional notes to leave in the style.css file.', 'runway' );
$comment = $html->format_comment( $comment );
$row = array( __( 'Comments', 'runway' ), $html->settings_textarea( 'theme_options[Comments]', isset( $Comments ) ? $Comments : '' ) . $comment );
$html->setting_row( $row );
?>

		<tr>
			<th scope="row" valign="top"><?php echo __('Screenshot', 'runway'); ?>:</th>
			<td>
				<?php
if ( isset( $Screenshot ) ) { ?>
					<a href="<?php echo esc_url( home_url() . '/wp-content/themes/' . $Folder . '/screenshot.png' ) ?>"><?php echo __('View Screenshot', 'runway'); ?></a><br>
				<?php } ?>
				<input type="file" name="theme_options[Screenshot]" value="<?php echo isset($_FILES['theme_options']['name']['Screenshot'])? esc_attr($_FILES['theme_options']['name']['Screenshot']) : ''; ?>" />
				<p class="description"><?php echo __('Recommended size: 600&#215;450 for HiDPI. Displayed at 300&#215;225.', 'runway'); ?></p>
			</td>
		</tr>

	</table>

<!--  Advanced Settings  -->
	<div class="meta-box-sortables metabox-holder" style="width: 800px;">
	<div class="postbox">
		<div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle no-move"><span><?php echo __('Advanced', 'runway'); ?></span></h3>
		<div class="inside" style="display: none;">

				<?php
if ( isset( $Folder ) ) { ?>
						<input type="hidden" name="base_name" value="<?php echo isset( $Folder ) ? esc_attr($Folder) : '' ?>" />
					<?php }
?>

				<table class="form-table">
					<?php
$comment = __( 'Select the folder name for the theme.', 'runway' );
$comment = $html->format_comment( $comment );
$row = array( __( 'Folder name', 'runway' ), $html->settings_input( 'theme_options[Folder]', isset( $Folder ) ? $Folder : '' ) . $comment );
$html->setting_row( $row );

$comment = __( 'Optional. Specify a license for the theme.', 'runway' );
$comment = $html->format_comment( $comment );
$row = array( __( 'License', 'runway' ), $html->settings_input( 'theme_options[License]', isset( $License ) ? $License : '' ) . $comment );
$html->setting_row( $row );

$comment = __( 'An optional link to the license details.', 'runway' );
$comment = $html->format_comment( $comment );
$row = array( __( 'License URI', 'runway' ), $html->settings_input( 'theme_options[LicenseURI]', isset( $LicenseURI ) ? $LicenseURI : '' ) . $comment );
$html->setting_row( $row );

$comment = __( 'Google Web Fonts Developer API Key', 'runway' );
$comment = $html->format_comment( $comment );
$row = array( __( 'Google API Key', 'runway' ), $html->settings_input( 'theme_options[WebFontAPIKey]', isset( $WebFontAPIKey ) ? $WebFontAPIKey : '' ) . $comment );
$html->setting_row( $row );

?>
				</table>
			</div>
		</div>
	</div>
	<input type="hidden" name="theme_options[old_folder_name]" value="<?php echo isset( $Folder ) ? esc_attr($Folder) : ''; ?>" />
	<?php

	// Save button
	$submitText = ($html->object->navigation == 'new-theme') ? __( 'Create Theme', 'runway' )  : __( 'Update', 'runway' );
	echo '<input class="button-primary" type="submit" value="'.esc_attr($submitText).'">';

	?>

</form>
