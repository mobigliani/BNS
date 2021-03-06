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

// Remove version info for security reasons.
remove_action('wp_head', 'wp_generator');

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
      'header-text'            => true,
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


/**
 * Display navigation to next/previous post when applicable, in the same category.
 *
 */
function twentythirteen_post_nav() {
	global $post;

	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous )
		return;
	?>
	<nav class="navigation post-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'twentythirteen' ); ?></h1>
		<div class="nav-links">

			<?php previous_post_link( '%link', _x( '<span class="meta-nav">&larr;</span> %title', 'Previous post link', 'twentythirteen' ), TRUE ); ?>
			<?php next_post_link( '%link', _x( '%title <span class="meta-nav">&rarr;</span>', 'Next post link', 'twentythirteen' ), TRUE ); ?>

		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}

/**
 * Converts date and time to proper Polish notation according to specified
 * format. New 'f' format character is introduced to allow month name in
 * genitive form.
 * To use it throughout the whole Wordpress site add filter for date_i18n
 * routine.
 * Thanks to Vokiel.
 *
 * @param string $format Date and time format string.
 * @param int $timestamp Unix timestamp.
 * @return string Converted date and time string.
 */
function date_pl($format, $timestamp = null) {
   $to_convert = array(
      'l'=>array('dat'=>'N','str'=>array('Poniedziałek','Wtorek','Środa','Czwartek','Piątek','Sobota','Niedziela')),
      'F'=>array('dat'=>'n','str'=>array('styczeń','luty','marzec','kwiecień','maj','czerwiec','lipiec','sierpień','wrzesień','październik','listopad','grudzień')),
      'f'=>array('dat'=>'n','str'=>array('stycznia','lutego','marca','kwietnia','maja','czerwca','lipca','sierpnia','września','października','listopada','grudnia'))
   );
   if ($pieces = preg_split('#[:/.\-, ]#', $format, -1, PREG_SPLIT_NO_EMPTY)) {
      if ($timestamp === null) {
         $timestamp = time();
      }
      foreach ($pieces as $datepart) {
         if (array_key_exists($datepart, $to_convert)) {
            $replace[] = $to_convert[$datepart]['str'][(date($to_convert[$datepart]['dat'],$timestamp)-1)];
         } else {
            $replace[] = date($datepart, $timestamp);
         }
      }
      $result = strtr($format, array_combine($pieces, $replace));
      return $result;
   }
}

/**
 * Polish date filter.
 * @uses date_pl() to format date and time.
 */
function polish_date_filter($j, $req_format, $i) {
   return date_pl( $req_format, $i );
}

// Add Polish date filter.
add_filter( 'date_i18n',  polish_date_filter, 10, 3 );


/* ***************************************************************************
 * Image size generation handling.
 *****************************************************************************/

// Filter out unneeded WP images sizes.
function bns_filter_image_sizes($sizes) {

	// unset( $sizes['thumbnail']); // 150x150
	unset( $sizes['medium']); // 300x300
	unset( $sizes['large']); // 1024x1024
	unset( $sizes['post-thumbnail']); // 604x270
	// wc-gallery:
	// unset( $sizes['wcsquare']);
	// unset( $sizes['wcstandard']);
	// unset( $sizes['wcicon']);
	// unset( $sizes['wcbig']);
	// unset( $sizes['wcsmall']);
	// unset( $sizes['fixedheightsmall']);
	// unset( $sizes['fixedheightmedium']);
	// unset( $sizes['fixedheight']);
	// unset( $sizes['carouselsmall']);
	// unset( $sizes['carousel']);
	// unset( $sizes['slider']);

	return $sizes;
}
add_filter('intermediate_image_sizes_advanced', 'bns_filter_image_sizes');


// Check if wc-gallery is available.
if (function_exists('wc_gallery_shortcode')) {
	global $wc_gallery_theme_support;
	// Empty the array to not to generate additional sizes.
	$wc_gallery_theme_support = array();
}
