<?php
/**
 * Author: Parallelus
 * Info: Saves a folder to the WP Uploads directory for the current theme.
 */

/**
 * Creates, saves and retrieves the paths to custom cache files. Useful for custom CSS and JS files.
 */
class ThemeCacheFiles {

	static public $cache_dir = null;

	/**
	 * Instantiates the class
	 */
	function __construct( ) {

		// get/create the cache directory
		self::$cache_dir = self::get_cache_dir();
	}


	/**
	 * Checks to see if the site has SSL enabled or not.
	 *
	 * @since 1.0
	 * @return bool
	 */
	static public function is_ssl()
	{
		if ( is_ssl() ) {
			return true;
		}
		else if ( 0 === stripos( get_option( 'siteurl' ), 'https://' ) ) {
			return true;
		}
		else if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {
			return true;
		}

		return false;
	}


	/**
	 * Returns an array of paths for the upload directory
	 * of the current site.
	 *
	 * @since 1.0
	 * @return array
	 */
	static public function get_upload_dir() {
		$wp_info  = wp_upload_dir();
		$dir_name = apply_filters( 'theme_cache_files_dir_name', basename( get_stylesheet_directory() ) );

		// SSL workaround.
		if ( self::is_ssl() ) {
			$wp_info['baseurl'] = str_ireplace( 'http://', 'https://', $wp_info['baseurl'] );
		}

		// Build the paths.
		$dir_info = array(
			'path'	 => $wp_info['basedir'] . '/' . $dir_name . '/',
			'url'	 => $wp_info['baseurl'] . '/' . $dir_name . '/'
		);

		// Create the upload dir if it doesn't exist.
		if ( ! file_exists( $dir_info['path'] ) ) {
			mkdir( $dir_info['path'] );
		}

		return $dir_info;
	}


	/**
	 * Returns an array of paths for the cache directory
	 * of the current site.
	 *
	 * @since 1.0
	 * @param string $name The name of the cache directory to get paths for.
	 * @return array
	 */
	static public function get_cache_dir( $name = 'cache' ) {
		$upload_info = self::get_upload_dir();
		$allowed	 = apply_filters( 'theme_cache_files_allowed_dir', array( 'cache', 'temp' ));

		// Make sure the dir name is allowed.
		if ( ! in_array( $name, $allowed ) ) {
			return false;
		}

		// Build the paths.
		$dir_info = array(
			'path'	 => $upload_info['path'] . $name . '/',
			'url'	 => $upload_info['url'] . $name . '/'
		);

		// Create the cache dir if it doesn't exist.
		if( ! file_exists( $dir_info['path'] ) ) {
			mkdir( $dir_info['path'] );
		}

		return $dir_info;
	}


	/**
	 * Returns true/false if a cache file exists
	 *
	 * @since 1.0
	 * @param string $filename The name of the cache file
	 * @return bool
	 */
	static public function cache_file_exists( $filename = false ) {

		$cache_dir = self::$cache_dir;

		if ( $filename ) {
			$file = $cache_dir['path'] . $filename;
			return ( file_exists($file) ) ? true : false;
		}

		return false;
	}


	/**
	 * Returns the version number to be applied to the query string
	 * of a CSS or JS asset. If Customizer is active a random hash
	 * is returned to prevent caching, otherwise a hash of the theme
	 * version number is applied.
	 *
	 * @since 1.0
	 * @return string
	 */
	static public function get_asset_version( $filename = false ) {
		global $wp_customize;

		$theme = wp_get_theme();
		$theme_version = ( $theme->exists() ) ? $theme->get( 'Version' ) : '1.0.0';

		if( !empty($wp_customize) ) {
			// keep it fresh for the Customizer
			return md5(uniqid());
		}
		else {

			$cache_dir = self::$cache_dir;

			if ( $filename ) {
				$file = $cache_dir['path'] . $filename;
				$theme_version .= ( file_exists($file) ) ? filemtime($file) : date();
			}

			return md5($theme_version);
		}
	}


	/**
	 * Deletes a cache file for the supplied filename.
	 *
	 * @since 1.0.0
	 * @param string $filename
	 * @return void
	 */
	static public function delete_cache_file( $filename = false ) {

		$cache_dir = self::$cache_dir;

		if ( $filename ) {

			$path = $cache_dir['path'] . $filename;

			if ( file_exists( $path ) ) {
				unlink( $path );
			}
		}
	}


	/**
	 * Deletes all cache files for the specified file type using the
	 * file extension.
	 *
	 * @since 1.0.0
	 * @param string $extension
	 * @return void
	 */
	static public function delete_cache_file_type( $extension = false ) {

		$cache_dir = self::$cache_dir;

		if ( $extension ) {
			$files = glob( $cache_dir['path'] . '*.' . $extension ); // if "css" is provided, deletes all 'filename.css' files

			if ( is_array( $files ) ) {
				array_map( 'unlink', $css );
			}
		}
	}


	/**
	 * Saves a file to the cache directory
	 *
	 * @since 1.0.0
	 * @return void
	 */
	static public function save_cache_file( $filename = false, $content = false )	{

		$cache_dir 	= self::$cache_dir;
		$content = apply_filters( 'theme_cache_files_save_content', $content, $filename );

		// Save the css
		if(!empty($filename) && !empty($content)) {

			// Delete the old file
			self::delete_cache_file($filename);

			// Save the file
			global $wp_filesystem;
			return $wp_filesystem->put_contents($cache_dir['path'].$filename, $content, FS_CHMOD_FILE); // file _put_ contents($cache_dir['path'].$filename, $content);
		}
	}
}
