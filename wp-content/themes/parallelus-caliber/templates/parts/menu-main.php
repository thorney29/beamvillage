<?php
/**
 * The template part for the MAIN MENU
 */

$navbarClass = array('navbar','navbar-default','top-nav','navbar-sticky');
$navbarClass = apply_filters('theme_navbar_class', $navbarClass);
$navbarClass = implode(" ", (array) $navbarClass);

?>

<div class="navbar-wrapper">
	<header class="<?php echo esc_attr($navbarClass) ?>" id="top">
		<div class="container-fluid container-xl">

			<div class="navbar-header">

				<?php
				// Logo
				rf_theme_logo();

				// Note: The logo output can be modified using filters: 'theme_logo', 'theme_logo_image', 'theme_logo_title', 'theme_logo_link' and 'theme_logo_brand_title'
				// Reference: extensions/theme-utilities/functions-template.php
				?>
				<button class="navbar-toggle squeeze collapsed" type="button" data-toggle="collapse" data-target="#navbar-main">
					<span class="sr-only"><?php _e('Toggle navigation', 'parallelus-caliber'); ?></span>
					<span class="squeeze-box">
						<span class="squeeze-inner"></span>
					</span>
				</button>
			</div>


			<nav class="collapse navbar-collapse" id="navbar-main">
			<?php

				// Left Menu
				do_action( 'theme_before_menu_left' );
				if (class_exists('wp_bootstrap_navwalker')) {
					// Main navbar (left)
					wp_nav_menu( array(
						'menu'              => 'menu-left',
						'theme_location'    => 'menu-left',
						'container'         => false,
						'menu_class'        => 'nav navbar-nav navbar-left',
						'menu_id'           => 'nav-left',
						'fallback_cb'       => 'null', // 'wp_bootstrap_navwalker::fallback',
						'walker'            => new wp_bootstrap_navwalker()
					));
				} else {
					_e('Please make sure the Bootstrap Navigation extension is active. Go to "Runway > Extensions" to activate.', 'parallelus-caliber');
				}
				do_action( 'theme_after_menu_left' );

				// Right Menu (DEFAULT)
				do_action( 'theme_before_menu_right' );
				if (class_exists('wp_bootstrap_navwalker')) {
					// Main navbar (right)
					wp_nav_menu( array(
						'menu'              => 'primary',
						'theme_location'    => 'primary',
						'container'         => false,
						'menu_class'        => 'nav navbar-nav navbar-right',
						'menu_id'           => 'nav-right',
						'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
						'walker'            => new wp_bootstrap_navwalker()
					));
				} else {
					_e('Please make sure the Bootstrap Navigation extension is active. Go to "Runway > Extensions" to activate.', 'parallelus-caliber');
				}
				do_action( 'theme_after_menu_right' );
			?>
			</nav>

		</div>
	</header>
</div>
