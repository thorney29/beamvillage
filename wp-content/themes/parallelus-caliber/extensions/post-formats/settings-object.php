<?php

class Post_Formats_Admin_Object extends Runway_Admin_Object {

	public $post_formats;

	public function __construct( $settings ) {

		parent::__construct( $settings );

		add_action( 'admin_init', array( $this, 'cfpf_init' ) );
		add_action( 'add_meta_boxes', array( $this, 'cfpf_add_meta_boxes' ) );
		add_action( 'wp_ajax_cfpf_gallery_preview', array( $this, 'cfpf_gallery_preview' ) );
		add_filter( 'pre_ping', array( $this, 'cfpf_pre_ping_post_links' ), 10, 3 );
		add_filter( 'social_broadcast_format', array( $this, 'cfpf_social_broadcast_format' ), 10, 2 );
		add_action( 'save_post', array( $this, 'add_save_post_hooks' ), 1 );

	}

	function add_save_post_hooks() {

		if ( isset( $_REQUEST['post_formats_box_nonce'] ) && wp_verify_nonce( $_REQUEST['post_formats_box_nonce'], 'post_formats_box' ) ) {
			if ( ! empty( $this->post_formats[0] ) && is_array( $this->post_formats[0] ) ) {
				if ( in_array( 'gallery', $this->post_formats[0] ) ) {//out('gallery');
					add_action( 'save_post', array( $this, 'postformat_gallery_save_post' ) );
				}
				if ( in_array( 'link', $this->post_formats[0] ) ) {//out('link');
					add_action( 'save_post', array( $this, 'postformat_link_save_post' ) );
				}
				if ( in_array( 'status', $this->post_formats[0] ) ) {//out('status');
					add_action( 'save_post', array( $this, 'postformat_status_save_post' ), 10, 2 );
				}
				if ( in_array( 'quote', $this->post_formats[0] ) ) {//out('quote');
					add_action( 'save_post', array( $this, 'postformat_quote_save_post' ), 10, 2 );
				}
				if ( in_array( 'video', $this->post_formats[0] ) ) {//out('video');
					add_action( 'save_post', array( $this, 'postformat_video_save_post' ) );
				}
				if ( in_array( 'audio', $this->post_formats[0] ) ) {//out('gallery');
					add_action( 'save_post', array( $this, 'postformat_audio_save_post' ) );
				}
			}
		}

	}

	function cfpf_init() {

		$this->post_formats = get_theme_support( 'post-formats' );

	}

	// we aren't really adding meta boxes here,
	// but this gives us the info we need to get our stuff in.
	function cfpf_add_meta_boxes( $post_type ) {

		if ( post_type_supports( $post_type, 'post-formats' ) && current_theme_supports( 'post-formats' ) ) {
			// assets
			wp_enqueue_script( 'cf-post-formats', FRAMEWORK_URL . 'extensions/post-formats/js/admin.js', array( 'jquery' ) );
			wp_enqueue_style( 'cf-post-formats', FRAMEWORK_URL . 'extensions/post-formats/css/admin.css', array() );

			wp_localize_script(
				'cf-post-formats',
				'cfpf_post_format',
				array(
					'loading'      => __( 'Loading...', 'runway' ),
					'wpspin_light' => admin_url( 'images/wpspin_light.gif' )
				)
			);

			global $translation_array;
			wp_localize_script( 'cf-post-formats', 'translations_js', $translation_array );

			add_action( 'edit_form_after_title', array( $this, 'pf_post_admin_setup' ) );
		}

	}

	function pf_post_admin_setup() {

		if ( ! empty( $this->post_formats[0] ) && is_array( $this->post_formats[0] ) ) {
			global $post;

			// See if we're specifying custom post formats for this post type
			/******************************************************************************************************
			 * This can be achieved by setting some extra data in add_post_type_support so we can test for it here
			 * and if it's found we know to have listed tabs modified to suit this case.
			 *
			 * Example: add_post_type_support( $post_type, 'post-formats', array( 'gallery', 'image', 'video' ) );
			 *
			 * Based on the following resource:
			 * http://wordpress.stackexchange.com/questions/16136/different-post-format-options-per-custom-post-type
			 *
			 */
			if ( is_array( $GLOBALS['_wp_post_type_features'][ $post->post_type ]['post-formats'] ) ) {
				// This gets the custom Post Type specific list
				$this->post_formats = $GLOBALS['_wp_post_type_features'][ $post->post_type ]['post-formats'];
			}

			$current_post_format = get_post_format( $post->ID );

			// support the possibility of people having hacked in custom
			// post-formats or that this theme doesn't natively support
			// the post-format in the current post - a tab will be added
			// for this format but the default WP post UI will be shown ~sp
			if ( ! empty( $current_post_format ) && ! in_array( $current_post_format, $this->post_formats[0] ) ) {
				array_push( $this->post_formats[0], get_post_format_string( $current_post_format ) );
			}
			array_unshift( $this->post_formats[0], 'standard' );
			$this->post_formats = $this->post_formats[0];

			include( 'views/tabs.php' );

			$format_views = array(
				'link',
				'quote',
				'video',
				'gallery',
				'audio',
			);

			foreach ( $format_views as $format ) {
				if ( in_array( $format, $this->post_formats ) ) {
					include( 'views/format-' . $format . '.php' );
				}
			}

			wp_nonce_field( 'post_formats_box', 'post_formats_box_nonce' );
		}

	}

	function postformat_gallery_save_post( $post_id ) {

		if ( ! defined( 'XMLRPC_REQUEST' ) && isset( $_POST['postformat_gallery_ids'] ) ) {
			update_post_meta( $post_id, 'postformat_gallery_ids', $_POST['postformat_gallery_ids'] );
		}

	}

	function postformat_link_save_post( $post_id ) {

		if ( ! defined( 'XMLRPC_REQUEST' ) && isset( $_POST['postformat_link_url'] ) ) {
			update_post_meta( $post_id, 'postformat_link_url', $_POST['postformat_link_url'] );
		}

	}
	// action added in cfpf_admin_init()

	function postformat_auto_title_post( $post_id, $post ) {

		remove_action( 'save_post', array( $this, 'postformat_status_save_post', 10, 2 ) );

		$content = trim( strip_tags( $post->post_content ) );
		$title   = substr( $content, 0, 50 );
		if ( strlen( $content ) > 50 ) {
			$title .= '...';
		}
		$title = apply_filters( 'postformat_auto_title', $title, $post );
		error_log( 'call' );
		wp_update_post( array(
			'ID'         => $post_id,
			'post_title' => $title
		) );

		add_action( 'save_post', array( $this, 'postformat_status_save_post', 10, 2 ) );

	}

	function postformat_status_save_post( $post_id, $post ) {

		if ( has_post_format( 'status', $post ) ) {
			$this->postformat_auto_title_post( $post_id, $post );
		}

	}
	// action added in cfpf_admin_init()

	function postformat_quote_save_post( $post_id, $post ) {

		if ( ! defined( 'XMLRPC_REQUEST' ) ) {
			$keys = array(
				'postformat_quote_text',
				'postformat_quote_source',
			);

			foreach ( $keys as $key ) {
				if ( isset( $_POST[ $key ] ) ) {
					update_post_meta( $post_id, $key, $_POST[ $key ] );
				}
			}
		}
		// if (has_post_format('quote', $post)) {
		// 	postformat_auto_title_post($post_id, $post);
		// }

	}
	// action added in cfpf_admin_init()

	function postformat_video_save_post( $post_id ) {

		if ( ! defined( 'XMLRPC_REQUEST' ) ) {
			$keys = array(
				'postformat_video_height',
				'postformat_video_m4v',
				'postformat_video_ogv',
				'postformat_video_webm',
				'postformat_video_poster',
				'postformat_video_embed'
			);

			foreach ( $keys as $key ) {
				if ( isset( $_POST[ $key ] ) ) {
					update_post_meta( $post_id, $key, $_POST[ $key ] );
				}
			}
		}

	}
	// action added in cfpf_admin_init()

	function postformat_audio_save_post( $post_id ) {

		if ( ! defined( 'XMLRPC_REQUEST' ) ) {
			$keys = array(
				'postformat_audio_mp3',
				'postformat_audio_ogg',
				'postformat_audio_embedded'
			);

			foreach ( $keys as $key ) {
				if ( isset( $_POST[ $key ] ) ) {
					update_post_meta( $post_id, $key, $_POST[ $key ] );
				}
			}
		}

	}

	function cfpf_gallery_preview() {

		if ( empty( $_POST['id'] ) || ! ( $post_id = intval( $_POST['id'] ) ) ) {
			exit;
		}

		global $post;
		$post->ID = $post_id;
		ob_start();
		include( 'views/format-gallery.php' );
		$html = ob_get_clean();
		header( 'Content-type: text/javascript' );
		echo json_encode( compact( 'html' ) );
		die();

	}

	function cfpf_post_has_gallery( $post_id = null ) {

		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}

		$ids    = esc_attr( get_post_meta( $post_id, '_format_gallery_ids', true ) );
		$images = get_posts( array(
			'post__in'       => explode( ',', $ids ),
			'orderby'        => 'post__in',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			// 'post_status' => 'any',
			'post_status'    => 'inherit',
			'posts_per_page' => 1, // -1 to show all
			'post_mime_type' => 'image%',
			'numberposts'    => - 1
		) );

		/*$images = new WP_Query(array(
			'post_parent' => $post_id,
			'post_type' => 'attachment',
			'post_status' => 'inherit',
			'posts_per_page' => 1, // -1 to show all
			'post_mime_type' => 'image%',
			'orderby' => 'menu_order',
			'order' => 'ASC'
		));*/

		return (bool) $images->post_count;

	}

	function cfpf_pre_ping_post_links( $post_links, $pung, $post_id = null ) {

		// return if we don't get a post ID (pre WP 3.4)
		if ( empty( $post_id ) ) {
			return;
		}

		$url = get_post_meta( $post_id, '_format_link_url', true );

		if ( ! empty( $url ) && ! in_array( $url, $pung ) && ! in_array( $url, $post_links ) ) {
			$post_links[] = $url;
		}

	}

	// For integration with Social plugin (strips {title} from broadcast format on status posts)
	function cfpf_social_broadcast_format( $format, $post ) {

		if ( get_post_format( $post ) == 'status' ) {
			$format = trim( str_replace(
				array(
					'{title}:',
					'{title} -',
					'{title}',
				),
				'',
				$format
			) );
		}

		return $format;

	}

}
