<?php
defined( 'ABSPATH' ) OR exit;
/*
Plugin Name: Opaque Teaser
Plugin URI: http://www.mikeselander.com/
Description: Creates a blur overlay on top of your site with landing page or under construction text so that visitors can get a taste of the upcoming site.
Version: 0.2.0
Author: Mike Selander, Richard Melick
Author URI: http://www.mikeselander.com/
License: GPL2
*/

/*
 *	@todo: Add colors/fonts to settings page
 *	@todo: Clean up language and decide on a name - make all text __()
 *	@todo: Kill the large FOUC
 */

// include the settings page
require_once( 'admin/settings-page.php' );

// define the plugin url
if (! defined('OPAQUE_URL') )
	define('OPAQUE_URL', plugins_url( '' ,  __FILE__ ) );

// set the settings
global $op_options;
$op_settings = get_option( 'op_options', $op_options );

// if the page is set to active, call a new instance of OpaqueLandingPage
if ( $op_settings['active'] == 'true' )
	$OpaqueLandingPage = new OpaqueLandingPage();

/**
 * OpaqueLandingPage
 *
 * Creates the frontend landing page and associated resources
 *
 * @package WordPress
 * @category mu_plugin
 * @author Old Town Media
 * @since 0.1.0
 */
class OpaqueLandingPage{

	const VERSION = '0.2.0';

	/**
	 * Constructor function.
	 *
	 * @access public
	 * @since 0.0.0
	 * @return void
	 */
	public function __construct() {

		add_action( 'wp_print_styles', array($this, 'landing_page_styles'), 100 );
		add_action( 'get_header', array($this, 'landing_page_print'), 100 );
		add_action( 'wp_print_footer_scripts', array($this, 'op_add_clear_class'), 100 );
		add_action( 'wp_print_footer_scripts', array($this, 'op_add_div_blurring'), 100 );

	}

	/**
	 * Load necessary stylesheets
	 *
	 * @access  public
	 * @since   0.1.0
	 */
	public function landing_page_styles() {

		// register the scripts
	    wp_register_style( 'op_blur',  OPAQUE_URL . '/assets/blur.css', array(), '1', 'all' );
		wp_register_script('cssfilter_modernizr', OPAQUE_URL . '/assets/modernizr.custom.cssfilters.js', array(), '1.0.0', true);

		// queue up the scripts
		if ( !is_user_logged_in() ){
	    	wp_enqueue_style( 'op_blur' );
	    	wp_enqueue_script('cssfilter_modernizr');
	    }

	} // end landing_page_styles()

	/**
	 * Print the landing page
	 *
	 * @access  public
	 * @since   0.1.0
	 */
	public function landing_page_print() {
		global $op_options, $op_text_options;
		$op_settings = get_option( 'op_options', $op_options );
		$op_text_settings = get_option( 'op_text_options', $op_text_options );

		if ( !is_admin() && !is_user_logged_in() ) :

			// The translucent in between background
			echo "<div class='cover'></div>";

			// the wrapper around the the content
		    echo "<div class='landing-page-modal clear'>";

				// If the default format is used
				if ( $op_settings['display_type'] == 'default' ){

					echo "<h1 class='clear'>".$op_text_settings['header_text']."</h1>";

					echo "<h2 class='clear'>".$op_text_settings['sub_text']."</h2>";

				// if custom HTML is used
				} else {

					echo do_shortcode( $op_text_settings['custom_HTML'] );

				}

	    	echo "</div>";

    	endif;

	} // end landing_page_print()

	/**
	 * Add .clear to child elements of .landing-page-modal to clarify them
	 *
	 * @access  public
	 * @since   0.1.0
	 */
	public function op_add_clear_class(){
		global $op_options;
		$op_settings = get_option( 'op_options', $op_options );

		// if custom HTML option is selected
		if ( $op_settings['display_type'] == 'custom' ) :
		?>
		<script type='text/javascript'>

			jQuery('.landing-page-modal').children().addClass('clear');

		</script>
		<?php
		endif;
	} // end op_add_clear_class()

	/**
	 * Add blurring to div elements if desired
	 *
	 * @access  public
	 * @since   0.1.0
	 */
	public function op_add_div_blurring(){
		global $op_options;
		$op_settings = get_option( 'op_options', $op_options );

		// if custom HTML option is selected
		if ( $op_settings['blur_divs'] == 'true' ) :
		?>
		<style>

			.opcssfilters body div:not(.clear){ -webkit-filter: blur(2px); -moz-filter: blur(2px); -o-filter: blur(2px); -ms-filter: blur(2px); filter: blur(2px); }
			.opno-cssfilters body div:not(.clear){ filter: progid:DXImageTransform.Microsoft.Blur(pixelRadius=2); -ms-filter:"progid:DXImageTransform.Microsoft.Blur(pixelRadius=2)";}

		</style>
		<?php
		endif;
	} // end op_add_clear_class()

} // end OpaqueLandingPage

?>