<?php
/**
 * Post Format Content Functions
 * ................................................................
 *
 * Functions to perform content output for Post Format specific
 * media and HTML formatting.
 */

// Hide the "poster" input under the "Video" tab in post add/edit admin
//...............................................
if ( !function_exists( 'theme_post_format_hide_video_poster_field' ) ) :
function theme_post_format_hide_video_poster_field() {
   echo '<style type="text/css">.postformat_video_poster_tr { display:none }</style>';
}
endif;
add_action('admin_head', 'theme_post_format_hide_video_poster_field');


#-----------------------------------------------------------------
# Post Format Media
#-----------------------------------------------------------------

// Output Gallery Images for Post Format
//...............................................

if ( !function_exists( 'theme_gallery_post_format' ) ) :
function theme_gallery_post_format( $id = false ) {

	$post_id = (!$id) ? get_the_ID() : $id;

	$gallery_images = get_post_meta($post_id, 'postformat_gallery_ids', true);
	$images = explode(',',$gallery_images);

	if (is_array($images) && !empty($images)) {
		// Image version
		$img_version = (is_single($id)) ? 'theme-gallery' : 'theme-blog';

		// Enqueue carousel styles and scripts
		wp_enqueue_style( 'owl-carousel', rf_get_template_directory_uri() . '/assets/css/owl-carousel.css' );
		wp_enqueue_script( 'owl-carousel', rf_get_template_directory_uri().'/assets/js/owl.carousel.min.js', array('jquery'), '2.0.0', true );

		foreach ( $images as $image) {

			// Get the media file data (returns array)
			$image_large = wp_get_attachment_image_src( $image, $img_version );
			$image_small = wp_get_attachment_image_src( $image, $img_version.'-micro' );

			if ( is_array($image_large) && isset($image_large[0]) ) {
				$img_large_src = $image_large[0]; // URL value
				$img_small_src = $image_small[0];

				if (is_single($id)) {
					// Single header
					if (theme_use_fastload()) :
						/*<div class="item fast-load" data-image="<?php echo esc_url($img_large_src) ?>">*/ ?>
						<a href="<?php echo esc_url($img_large_src) ?>" class="item fast-load" data-image="<?php echo esc_url($img_large_src) ?>" data-lightbox="gallery">
							<img class="fast-load-img" src="<?php echo esc_url($img_large_src) // FASTLOAD NOT WORKING HERE :( ?>" alt="<?php esc_attr(get_the_title()) ?>">
						</a> <?php
						/*</div>*/
					else:
						/*<div class="item" style="background-image: url(<?php echo esc_url($img_large_src) ?>)">*/ ?>
						<a href="<?php echo esc_url($img_large_src) ?>" class="item fast-load" data-image="<?php echo esc_url($img_large_src) ?>" data-lightbox="gallery">
							<img src="<?php echo esc_url($img_large_src) ?>" alt="<?php esc_attr(get_the_title()) ?>">
						</a> <?php
						/*</div>*/
					endif;
				} else {
					// Blog list
					if (theme_use_fastload()) : ?>
						<a href="<?php the_permalink(); ?>" class="item fast-load" data-image="<?php echo esc_url($img_large_src) ?>">
							<div class="fast-load-img" style="background-image: url(<?php echo esc_url($img_small_src) ?>)"></div>
						</a> <?php
					else: ?>
						<a href="<?php the_permalink(); ?>" class="item" style="background-image: url(<?php echo esc_url($img_large_src) ?>)"></a>
						<?php
					endif;
				}
			}
		}
	}
}
endif;

// Output Audio Player for Post Format
//...............................................

if ( !function_exists( 'theme_audio_player' ) ) :
function theme_audio_player( $id = false ) {

	$post_id = (!$id) ? get_the_ID() : $id;

	// Embedded media content
	$embed  = get_post_meta($post_id, 'postformat_audio_embedded', TRUE);
	if ( isset($embed) && $embed != '' ) {
		// Embed Audio
		if( !empty($embed) ) {
			// run oEmbed for known sources to generate embed code from audio links
			$embed_url = parse_url($embed, PHP_URL_HOST);
			$embed_url = preg_replace("/^www\./", "", $embed_url); // no www needed
			$embed_url = str_replace('.', '_', $embed_url); // no dots either
			echo '<div class="audio-container embed_'. $embed_url .'">'. $GLOBALS['wp_embed']->autoembed( stripslashes(htmlspecialchars_decode($embed)) ) .'</div>';

			return; // and.... Done!
		}

	} else {
		// jPlayer audio
		$mp3 = get_post_meta($post_id, 'postformat_audio_mp3', TRUE);
		$ogg = get_post_meta($post_id, 'postformat_audio_ogg', TRUE);

		// Enqueue media player JS
		wp_enqueue_script( 'jplayer', rf_get_template_directory_uri().'/assets/js/jquery.jplayer.min.js', array('jquery'), '2.9.2', true );

		// Include player content ?>
		<div id="jp_container_<?php echo esc_attr($post_id); ?>" class="jp-container jp-audio" role="application" aria-label="media player">
			<div class="jp-type-single">
				<div id="jquery_jplayer_<?php echo esc_attr($post_id); ?>" class="jp-jplayer jp-jplayer-audio" data-mp3="<?php echo esc_url($mp3); ?>" data-ogg="<?php echo esc_url($ogg); ?>"></div>
				<div class="jp-gui jp-interface">
					<div class="jp-controls">
						<div class="seperator-first"></div>
						<div class="seperator-second"></div>
						<button class="jp-play" tabindex="0"><i class="fa fa-play"></i><span><?php _e('play', 'runway'); ?></span></button>
						<button class="jp-pause" tabindex="0"><i class="fa fa-pause"></i><span><?php _e('pause', 'runway'); ?></span></button>
						<button class="jp-mute" tabindex="0"><i class="fa fa-volume-up"></i><span><?php _e('mute', 'runway'); ?></span></button>
						<button class="jp-unmute" tabindex="0"><i class="fa fa-volume-off"></i><span><?php _e('unmute', 'runway'); ?></span></button>
					</div>
					<div class="jp-progress-container">
						<div class="jp-progress">
							<div class="jp-seek-bar">
								<div class="jp-play-bar"></div>
							</div>
						</div>
					</div>
					<div class="jp-volume-bar-container">
						<div class="jp-volume-bar">
							<div class="jp-volume-bar-value"></div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php
	} // End if embedded/else
}
endif;

// Video Player / Embeds (self-hosted, jPlayer)
//...............................................

if ( !function_exists( 'theme_video_player' ) ) :
function theme_video_player( $id = false ) {

	$post_id = (!$id) ? get_the_ID() : $id;

	// Check for embedded video
	$embed = get_post_meta($post_id, 'postformat_video_embed', true);
	if( !empty($embed) ) {
		// run oEmbed for known sources to generate embed code from video links
		$embed_url = parse_url($embed, PHP_URL_HOST);
		$embed_url = preg_replace("/^www\./", "", $embed_url); // no www needed
		$embed_url = str_replace('.', '_', $embed_url); // no dots either
		echo '<div class="video-container embed_'. $embed_url .'">'. $GLOBALS['wp_embed']->autoembed( stripslashes(htmlspecialchars_decode($embed)) ) .'</div>';

		return; // and.... Done!
	}

	// Get the player media options
	$m4v = get_post_meta($post_id, 'postformat_video_m4v', true);
	$ogv = get_post_meta($post_id, 'postformat_video_ogv', true);
	$webm = get_post_meta($post_id, 'postformat_video_webm', true);

	// Enqueue media player JS
	wp_enqueue_script( 'jplayer', rf_get_template_directory_uri().'/assets/js/jquery.jplayer.min.js', array('jquery'), '2.9.2', true );

	// Include player content ?>
	<div id="jp_container_<?php echo esc_attr($post_id); ?>" class="jp-container jp-video" role="application" aria-label="media player">
		<div class="jp-type-single">
			<div id="jquery_jplayer_<?php echo esc_attr($post_id); ?>" class="jp-jplayer jp-jplayer-video"
				data-bg="<?php rf_blog_image_url( $post_id, 'theme-blog' ); ?>"
				data-fast-bg="<?php rf_blog_image_url( $post_id, 'theme-blog-micro' ); ?>"
				data-mp4="<?php echo esc_url($m4v); ?>"
				data-ogg="<?php echo esc_url($ogv); ?>"
				data-webm="<?php echo esc_url($webm); ?>"
			></div>
			<div class="jp-gui jp-interface">
				<div class="jp-controls">
					<div class="seperator-first"></div>
					<div class="seperator-second"></div>
					<div class="seperator-third"></div>
					<button class="jp-play" tabindex="0"><i class="fa fa-play"></i><span><?php _e('play', 'runway') ?></span></button>
					<button class="jp-pause" tabindex="0"><i class="fa fa-pause"></i><span><?php _e('pause', 'runway') ?></span></button>
					<button class="jp-full-screen" tabindex="0"><i class="fa fa-expand"></i><i class="fa fa-compress"></i><span><?php _e('full screen', 'runway') ?></span></button>
					<button class="jp-mute" tabindex="0"><i class="fa fa-volume-up"></i><span><?php _e('mute', 'runway') ?></span></button>
					<button class="jp-unmute" tabindex="0"><i class="fa fa-volume-off"></i><span><?php _e('unmute', 'runway') ?></span></button>
				</div>
				<div class="jp-progress-container">
					<div class="jp-progress">
						<div class="jp-seek-bar">
							<div class="jp-play-bar"></div>
						</div>
					</div>
				</div>
				<div class="jp-volume-bar-container">
					<div class="jp-volume-bar">
						<div class="jp-volume-bar-value"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
endif;