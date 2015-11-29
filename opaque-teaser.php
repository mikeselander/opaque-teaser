<?php
defined( 'ABSPATH' ) OR exit;
/*
Plugin Name: Opaque Teaser
Plugin URI: http://www.mikeselander.com/
Description: Creates a blur overlay on top of your site with landing page or under construction text so that visitors can get a taste of the upcoming site.
Version: 0.2.2
Author: Mike Selander, Richard Melick
Author URI: http://www.mikeselander.com/
License: GPL2
*/


// Include the settings page
require_once( 'admin/settings-page.php' );

// Load the settings page, but only if we are viewing an admin page
if ( is_admin() ){
	$settings = new OpaqueSettingPage();
}

// Define the plugin url
if (! defined('OPAQUE_URL') ){
	define( 'OPAQUE_URL', plugins_url( '' ,  __FILE__ ) );
}

// Set the settings
global $op_options;
$op_settings = get_option( 'op_options', $op_options );

// If the page is set to active, call a new instance of OpaqueLandingPage
if ( $op_settings['active'] == 'true' ){
	$OpaqueLandingPage = new OpaqueLandingPage();
}

/**
 * OpaqueLandingPage
 *
 * Creates the frontend landing page and associated resources.
 *
 * @package WordPress
 * @category mu_plugin
 * @author Mike Selander
 */
class OpaqueLandingPage{

	/**
	 * Constructor function.
	 *
	 * @see landing_page_styles, landing_page_print, op_add_clear_class, op_add_div_blurring
	 */
	public function __construct() {

		add_action( 'wp_print_styles', array( $this, 'landing_page_styles' ), 100 );
		add_action( 'get_header', array( $this, 'landing_page_print' ), 1 );
		add_action( 'wp_print_footer_scripts', array( $this, 'op_add_clear_class' ), 100 );
		add_action( 'wp_print_footer_scripts', array( $this, 'op_add_div_blurring' ), 100 );

	}


	/**
	 * Load our stylesheets.
	 *
	 * @see wp_register_*, wp_enqueue_*
	 */
	public function landing_page_styles() {

		// Register the scripts
	    wp_register_style( 'op_blur',  OPAQUE_URL . '/assets/blur.css', array(), '1', 'all' );
		wp_register_script( 'cssfilter_modernizr', OPAQUE_URL . '/assets/modernizr.custom.cssfilters.js', array(), '1.0.0', true );

		// Queue up the scripts
		if ( !is_user_logged_in() ){
	    	wp_enqueue_style( 'op_blur' );
	    	wp_enqueue_script( 'cssfilter_modernizr' );
	    }

	}


	/**
	 * Print the landing page on the frontend of the site.
	 *
	 * @see get_option
	 */
	public function landing_page_print(){

		global $op_options, $op_text_options;
		$op_settings = get_option( 'op_options', $op_options );
		$op_text_settings = get_option( 'op_text_options', $op_text_options );

		// Make sure we're not in the admin area or logged in
		if ( is_admin() || is_user_logged_in() ){
			return;
		}

		// The translucent in between background
		echo "<div class='cover'></div>";

		// The wrapper around the content
	    echo "<div class='landing-page-modal clear'>";

			// If the default format is used
			if ( $op_settings['display_type'] == 'default' ){

				echo "<h1 class='clear'>".$op_text_settings['header_text']."</h1>";
				echo "<h2 class='clear'>".$op_text_settings['sub_text']."</h2>";

			// If custom HTML is used
			} else {

				echo do_shortcode( $op_text_settings['custom_HTML'] );

			}

    	echo "</div>";

	}


	/**
	 * Add .clear to child elements of .landing-page-modal to clarify them.
	 *
	 * @see get_option
	 */
	public function op_add_clear_class(){

		global $op_options;
		$op_settings = get_option( 'op_options', $op_options );

		// If custom HTML option is selected
		if ( $op_settings['display_type'] == 'custom' ) :
		?>
		<script type='text/javascript'>

			jQuery( '.landing-page-modal' ).children().addClass( 'clear' );

		</script>
		<?php
		endif;

	}


	/**
	 * Add blurring to div elements if desired.
	 *
	 * @see get_option
	 */
	public function op_add_div_blurring(){

		global $op_options;
		$op_settings = get_option( 'op_options', $op_options );

		// If custom HTML option is selected
		if ( $op_settings['blur_divs'] == 'true' ) :
		?>
		<style>

			.opcssfilters body div:not(.clear){ -webkit-filter: blur(2px); -moz-filter: blur(2px); -o-filter: blur(2px); -ms-filter: blur(2px); filter: blur(2px); }
			.opno-cssfilters body div:not(.clear){ filter: progid:DXImageTransform.Microsoft.Blur(pixelRadius=2); -ms-filter:"progid:DXImageTransform.Microsoft.Blur(pixelRadius=2)";}

		</style>
		<?php
		endif;

	}

} // end OpaqueLandingPage