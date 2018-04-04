<?php
/**
 * Actions to apply and output theme behavior and content on the
 * template files and other WP specific areas.
 */


#-----------------------------------------------------------------
# Outputs the default template layout container wrapper
#-----------------------------------------------------------------
/**
 * This helps the theme decide if it needs to include a content container for specific pages, templates and content sources.
 * The container acts as a content wrapper for any templates without built in content containers.
 *
 * For templates not needing wrapper elements, before the get_header() function, include the following filter:
 *
 * add_filter('theme_template_has_layout', function(){ return true; });
 */
if ( ! function_exists( 'rf_default_template_wrapper' ) ) :
function rf_default_template_wrapper( $position = 'start' ) {

	// Templates not needing wrappers, return nothing.
	if (apply_filters('theme_template_has_layout', false)) {
		return;
	}
	// The opening container
	if ($position == 'start') {
		echo apply_filters('theme_template_wrapper_1', '<div class="section-wrapper"><div class="container">');
	}
	// The closing container
	if ($position == 'end') {
		echo '</div></div> <!-- / .section-wrapper -->';
	}
}
endif; // rf_default_template_wrapper
add_action('output_layout', 'rf_default_template_wrapper', 1 );


#-----------------------------------------------------------------
# Home Page Posts Query
#-----------------------------------------------------------------

// Edit the default home page posts query based on theme options
// ................................................................
if ( ! function_exists( 'theme_home_posts_query' ) ) :
function theme_home_posts_query( $query ) {

	// Make sure we're not querying another post type
	if (isset($query->query['post_type']) && $query->query['post_type'] !== 'post') {

		return;

	} else {

		// Only use on home page
		if ($query->is_main_query() && $query->is_home && !$query->is_posts_page) {

			// Number of posts to show
			$post_count = get_options_data('options-page', 'first-page-post-count');
			if (empty($post_count) || $post_count == 'auto') {
				return; // use default WP setting, or not specified
			}

			$home_posts = (!empty($post_count)) ?(int) $post_count : 10;

			if ($query->is_paged) {

				// pages 2, 3, etc...
				$ppp = get_option('posts_per_page'); // posts per page (using WP setting)

				//Manually determine query offset (home posts + current page (minus 2) x posts per page)
				$page_offset = $home_posts + ( ($query->query_vars['paged']-2) * $ppp );

				//Apply page offset
				$query->set( 'offset', $page_offset );
				$query->set( 'posts_per_page', $ppp );

			} else {

				// first page shows number specified in theme options
				$query->set( 'posts_per_page', $home_posts );

			}

			// The Categoreis set in theme options
			$category = (array) get_options_data('options-page', 'blog-post-categories');
			$categories = (is_array($category) && !empty($category[0])) ? $category : '';
			if ( !empty($categories) && $categories[0] !== 'none' && $query->is_main_query() ) {
				$query->set( 'category__and', $categories );
			}
		}
	}
}
endif;
add_action( 'pre_get_posts', 'theme_home_posts_query' );


// Adjust pagination for home page posts
// ................................................................
function theme_home_posts_query_pagination_adjust($found_posts, $query) {

	// Make sure we're querying posts and not another post type
	if (isset($query->query['post_type']) && $query->query['post_type'] !== 'post')
		return $found_posts;

	// Only use on home page
	if ($query->is_main_query() && $query->is_home && !$query->is_posts_page && $query->is_paged) {

		// Number of posts to show
		$post_count = get_options_data('options-page', 'first-page-post-count');
		if (empty($post_count) || $post_count == 'auto') {
			return $found_posts;; // use default WP setting, or not specified
		}
		$home_posts = (!empty($post_count)) ? (int) $post_count : 10;
		$ppp = get_option('posts_per_page'); // posts per page (using WP setting)

		// Adjust total to appear page 1 has same number as pages 2, 3, etc.
		return ($found_posts - $home_posts) + $ppp;
	}

	return $found_posts;
}
add_filter( 'found_posts', 'theme_home_posts_query_pagination_adjust', 1, 2 );

if ( ! function_exists( 'parallelus_display_promotion_area' ) ) :
	function parallelus_display_promotion_area( $page ) {
		$show = true;

		if ( defined( 'PARALLELUS_HIDE_PROMOTIONS' ) && PARALLELUS_HIDE_PROMOTIONS ) {
			$show = false;
		}

		if ( true === $show ) {
			?>

			<div class="parallelus-promotions-area">
				<strong>
					<a href="http://para.llel.us/special-offers" target="_blank">
						<?php _e( 'Exclusive discounts on great WordPress products and services.', 'runway' ); ?>
					</a>
				</strong>
			</div>
			<div class="clear"></div>

			<?php
		}
	}
endif;
add_action( 'after-dynamic-page-render_options-page', 'parallelus_display_promotion_area' );

if ( ! function_exists( 'parallelus_display_promotion_notice' ) ) :
	function parallelus_display_promotion_notice() {

		$show = false;

		// update option
		$promo_trigger_date = (int) get_option( 'parallelus_caliber_promo_trigger_date', 0 );
		if ( empty( $promo_trigger_date ) ) {
			update_option( 'parallelus_caliber_promo_trigger_date', time() + 1209600 ); // plus two weeks
		} else {
			if ( time() > $promo_trigger_date ) {
				$show = true;
			}
		}

		// don't show if constant is defined
		if ( defined( 'PARALLELUS_HIDE_PROMOTIONS' ) && PARALLELUS_HIDE_PROMOTIONS ) {
			$show = false;
		}

		// don't show if user clicked dismiss links
		if ( 1 == get_user_meta( get_current_user_id(), 'parallelus_caliber_hide_promotion', true ) ) {
			$show = false;
		}

		if ( true === $show ) {
			$dismiss_url = add_query_arg( array(
				'dismiss-promotion' => 'dismiss_admin_notices',
				'dismiss-promotion-nonce' => wp_create_nonce( 'dismiss-promotion' )
			) );

			$dismiss_btn_html  = sprintf(
				'<a class="notice-dismiss" href="%s" target="_parent" style="text-decoration: none;"></a>',
				$dismiss_url
			);

			$dismiss_link_html  = sprintf(
				'<strong><a class="dismiss-notice" href="%s" target="_parent">%s</a></strong>',
				$dismiss_url,
				__( 'Dismiss this notice', 'runway' )
			);
			?>

			<div class="updated notice" style="position: relative;">
				<p>
					<strong>
						<a href="http://para.llel.us/special-offers" target="_blank">
							<?php _e( 'Exclusive discounts on great WordPress products and services.', 'runway' ); ?>
						</a>
					</strong>
				</p>
				<p>
					<?php echo $dismiss_link_html; ?>
				</p>

				<?php echo $dismiss_btn_html; ?>
			</div>

			<?php
		}
	}
endif;
add_action( 'admin_notices', 'parallelus_display_promotion_notice');

if ( ! function_exists( 'parallelus_dismiss_promotion_notices' ) ) :
	function parallelus_dismiss_promotion_notices() {
		if (
			isset( $_GET['dismiss-promotion'] )
			&& $_GET['dismiss-promotion'] == 'dismiss_admin_notices'
			&& isset( $_GET['dismiss-promotion-nonce'] )
			&& wp_verify_nonce( $_GET['dismiss-promotion-nonce'], 'dismiss-promotion' )
		) {

			update_user_meta( get_current_user_id(), 'parallelus_caliber_hide_promotion', 1 );
		}
	}
endif;
add_action( 'admin_init', 'parallelus_dismiss_promotion_notices' );
