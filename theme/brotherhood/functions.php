   <?php
/**
 * Brotherhood theme functions and definitions
 *
 * Sets up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development
 * and http://codex.wordpress.org/Child_Themes), you can override certain
 * functions (those wrapped in a function_exists() call) by defining them first
 * in your child theme's functions.php file. The child theme's functions.php
 * file is included before the parent theme's file, so the child theme
 * functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters, @link http://codex.wordpress.org/Plugin_API
 *
 */

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
   wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}

/**
 * Return the Google font stylesheet URL, if available.
 *
 * @return string Font stylesheet.
 */
function brotherhood_fonts_url() {
   $fonts_url = '';

   $font_families = array();
   $font_families[] = 'PT Serif:400,700,400italic,700italic';
   $font_families[] = 'Cantata One:400';

   $query_args = array(
      'family' => urlencode( implode( '|', $font_families ) ),
      'subset' => urlencode( 'latin,latin-ext' ),
   );
   $fonts_url = add_query_arg( $query_args, "//fonts.googleapis.com/css" );

   return $fonts_url;
}

/**
 * Enqueues scripts and styles for front end.
 *
 * @return void
 */
function brotherhood_scripts_styles() {
   
	// Add fonts used in the main stylesheet.
	wp_enqueue_style( 'brotherhood-fonts', brotherhood_fonts_url(), array(), null );
}
add_action( 'wp_enqueue_scripts', 'brotherhood_scripts_styles' );


/**
 * Set up the WordPress core custom header arguments and settings.
 *
 * @uses add_theme_support() to register support for 3.4 and up.
 * @uses twentythirteen_header_style() to style front-end.
 * @uses twentythirteen_admin_header_style() to style wp-admin form.
 * @uses twentythirteen_admin_header_image() to add custom markup to wp-admin form.
 * @uses register_default_headers() to set up the bundled header images.
 *
 * @since Twenty Thirteen 1.0
 */
function brotherhood_custom_header_setup() {
	$args = array(
		// Text color and image (empty to use none).
      'header-text'            => false,
		'default-image'          => '%2$s/images/headers/ihs.jpg',

		// Set height and width, with a maximum value for the width.
		'height'                 => 230,
		'width'                  => 1600,

		// Callbacks for styling the header and the admin preview.
		'wp-head-callback'       => 'twentythirteen_header_style',
		'admin-head-callback'    => 'twentythirteen_admin_header_style',
		'admin-preview-callback' => 'twentythirteen_admin_header_image',
	);

	add_theme_support( 'custom-header', $args );

	/*
	 * Default custom headers packaged with the theme.
	 * %s is a placeholder for the theme template directory URI.
	 */
	register_default_headers( array(
		'gwiazda' => array(
			'url'           => '%2$s/images/headers/ihs.jpg',
			'thumbnail_url' => '%2$s/images/headers/ihs-thumbnail.png',
			'description'   => _x( 'Gwiazda', 'header image description', 'brotherhood' )
		),
	) );
}
add_action( 'after_setup_theme', 'brotherhood_custom_header_setup', 11 );
