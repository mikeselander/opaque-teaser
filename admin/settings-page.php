<?php
defined( 'ABSPATH' ) OR exit;

if (! defined('OP_PLUGIN_BASENAME') )
	define('OP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

class OpaqueSettingPage{

	/**
	 * Constructor function
	 *
	 * @access  public
	 * @since   0.1.0
	 */

	public function __construct() {

		add_action( 'admin_init', array($this, 'op_register_settings') );
		add_action( 'admin_menu', array($this, 'op_options_page') );
		add_filter( 'plugin_action_links_opaque-teaser/opaque-teaser.php', array($this, 'op_plugin_settings_link') );

	}


	/**
	 * Register the settings
	 *
	 * @access  public
	 * @since   0.1.0
	 */
    function op_register_settings() {

    	register_setting( 'op_main_options', 'op_options', array($this, 'op_validate_options') );
    	register_setting( 'op_main_options', 'op_text_options', array($this, 'op_validate_text_options') );

    } // end op_register_settings()


	/**
	 * Create the settings page
	 *
	 * @access  public
	 * @since   0.1.0
	 */
    function op_options_page() {

    	add_submenu_page( 'options-general.php', 'Opaque Teaser Settings', 'Teaser', 'manage_options', 'op_settings', array($this, 'op_settings_page') );

    } // end op_options_page()

    /**
	 * Generate the settings page
	 *
	 * @access  public
	 * @since   0.1.0
	 */
    function op_settings_page() {
    	global $op_options, $op_text_options;

    	// establish defaults if the settings haven't yet been set
		$op_options = array(
			'active' 			=> 'false',
			'display_type'		=> 'default',
			'blur_divs'			=> 'false',
			'whitelisted_IPs'	=> ''	// coming soon
		);

		$op_text_options = array(
			'header_text'			=> 'Coming Soon!',
			'sub_text'				=> 'This site will be up and running in no time at all!',
			'custom_HTML'			=> '<h1>Custom Title</h1><h3>Custom Subtext</h3>',
			'header_text_color'		=> '',	// coming soon
			'header_text_font'		=> '',	// coming soon
			'sub_text_color'		=> '',	// coming soon
			'sub_text_font'			=> '',	// coming soon
		);

		// Check to see whether the form has just been submitted
    	if ( ! isset( $_REQUEST['updated'] ) )
    		$_REQUEST['updated'] = false;  ?>

    	<div class="wrap">

    	<h2><?php _e( 'Opaque Teaser', 'opaqueteaser' ); ?></h2>

    	<?php
    	// Success if the settings were updated
    	if ( false !== $_REQUEST['updated'] ) :
    		echo "<div class='updated fade'><p><strong>".__( 'Settings saved' )."</strong></p></div>";
    	endif;
    	?>

    	<form method="post" action="options.php">

    	<?php $settings = get_option( 'op_options', $op_options ); ?>
    	<?php $text_settings = get_option( 'op_text_options', $op_text_options ); ?>

    	<?php
    	if ($settings['display_type'] == 'default'){
			$default_display = 'block';
			$custom_display = 'none';
		} else if ($settings['display_type'] == 'custom'){
			$custom_display = 'block';
			$default_display = 'none';
		}
    	?>

    	<?php settings_fields( 'op_main_options' ); ?>

		<h3><?php _e( 'Activate or Deactivate?', 'opaqueteaser' ); ?></h3>
    	<table class="form-table">

	    	<tr valign="top">
	        	<td>
					<fieldset>
						<label title="true">
		        		<input type="radio" id="active" name="op_options[active]" <?php if($settings['active'] == 'true') echo 'checked="checked"'; ?> value="true" /><span><?php _e( 'Activated', 'opaqueteaser' ); ?></span>
						</label>
		        		<br>
		        		<label title="false">
						<input type="radio" id="active-2" name="op_options[active]" <?php if($settings['active'] == 'false') echo 'checked="checked"'; ?> value="false" /><span><?php _e( 'Deactivated', 'opaqueteaser' ); ?></span>
		        		</label>
					</fieldset>
	        	</td>
	    	</tr>

    	</table>

    	<h3><?php _e( 'Display Options', 'opaqueteaser' ); ?></h3>

		<table class="form-table">

	    	<tr valign="top">
	        	<td>
	        		<fieldset>
	        			<label title="default">
		        		<input type="radio" id="showDefault" name="op_options[display_type]" <?php if($settings['display_type'] == 'default') echo 'checked="checked"'; ?> value="default" onClick='show_default();' /><?php _e( 'Display the default teaser, with custom text', 'opaqueteaser' ); ?>
	        			</label>
		        		<br>
		        		<label title="custom">
						<input type="radio" id="showCustom" name="op_options[display_type]" <?php if($settings['display_type'] == 'custom') echo 'checked="checked"'; ?> value="custom" onClick='show_custom();'  /><?php _e( 'Display custom html on the teaser', 'opaqueteaser' ); ?>
		        		</label>
	        		</fieldset>
	        	</td>
	    	</tr>

			<tr valign="top">
				<td>
					<h4><?php _e( 'Blur Divs?', 'opaqueteaser' ); ?></h4>

					<p><?php _e( 'This is only necessary if you\'re using complex background images - may potentially cause issues in situations with complex z-index arrangements.', 'opaqueteaser' ); ?></p>
				</td>
			</tr>

	    	<tr valign="top">
	        	<td>
	        		<fieldset>
	        			<label title="true">
		        		<input type="radio" id="yesblurring" name="op_options[blur_divs]" <?php if($settings['blur_divs'] == 'true') echo 'checked="checked"'; ?> value="true" /><?php _e( 'Apply blur to divs', 'opaqueteaser' ); ?>
	        			</label>
		        		<br>
		        		<label title="false">
						<input type="radio" id="noblurring" name="op_options[blur_divs]" <?php if($settings['blur_divs'] == 'false') echo 'checked="checked"'; ?> value="false" /><?php _e( 'Do not blur divs', 'opaqueteaser' ); ?>
		        		</label>
	        		</fieldset>
	        	</td>
	    	</tr>

    	</table>

    	<h3><?php _e( 'Teaser Page Content', 'opaqueteaser' ); ?></h3>

    	<table class="form-table">
	    	<tr valign="top"  id="header_text_tr" style="display:<?php echo $default_display; ?>;" class="opdefault">
	    		<th scope="row"><?php _e( 'Header Text', 'opaqueteaser' ); ?></th>
				<td>
					<fieldset>
					<label title="header_text" >
					<input type="text" id="header_text" name="op_text_options[header_text]" value="<?php echo stripslashes($text_settings['header_text']); ?>">
					</label>
					</label>
	        	</td>
	    	</tr>

	    	<tr valign="top" id="sub_text_tr" style="display:<?php echo $default_display; ?>;" class="opdefault">
	    		<th scope="row"><?php _e( 'Sub Text', 'opaqueteaser' ); ?></th>
				<td>
					<fieldset>
					<label title="sun_text" >
					<input type="text" id="sub_text" name="op_text_options[sub_text]" value="<?php echo stripslashes($text_settings['sub_text']); ?>">
					</label>
					</label>
	        	</td>
	    	</tr>

	    	<tr valign="top" id="customHTML_tr" style="display:<?php echo $custom_display; ?>;" class="opcustom">
				<td>
					<fieldset>
					<label title="customHTML" >
					<textarea cols="60" rows="15" id="customHTML" name="op_text_options[custom_HTML]"><?php echo $text_settings['custom_HTML']; ?></textarea>
					</label>
					</fieldset>
	        	</td>
	    	</tr>

    	</table>

    	<p class="submit"><input type="submit" class="button-primary" value="Save Settings" /></p>

    	</form>

    	</div>

    	<script type='text/javascript'>

			function show_default(){
				document.getElementById("header_text_tr").style.display="block";
				document.getElementById("sub_text_tr").style.display="block";
				document.getElementById("customHTML_tr").style.display="none";
    		}

    		function show_custom(){
				document.getElementById("header_text_tr").style.display="none";
				document.getElementById("sub_text_tr").style.display="none";
				document.getElementById("customHTML_tr").style.display="block";
    		}

    	</script>

    	<?php
    } // end op_settings_page()


	/**
	 * Validate the mainad options
	 *
	 * @access  public
	 * @since   0.1.0
	 */
    function op_validate_options( $input ) {
    	global $op_options, $op_text_options;

    	$settings = get_option( 'op_options', $op_options );

    	// We strip all tags from the text field, to avoid vulnerablilties like XSS & convert all values to positive integers
    	$input['active'] = wp_filter_nohtml_kses( $input['active'] );
    	$input['display_type'] = wp_filter_nohtml_kses( $input['display_type'] );
    	$input['blur_divs'] = wp_filter_nohtml_kses( $input['blur_divs'] );

    	return $input;
    } // end op_validate_options()


    /**
	 * Validate the text options
	 *
	 * @access  public
	 * @since   0.1.0
	 */
    function op_validate_text_options( $input ) {
    	global $op_options, $op_text_options;

    	$text_settings = get_option( '$op_text_options', $op_text_options );

    	// We strip all tags from the text field, to avoid vulnerablilties like XSS & convert all values to positive integers
    	$input['header_text'] = wp_filter_nohtml_kses( $input['header_text'] );
    	$input['sub_text'] = wp_filter_nohtml_kses( $input['sub_text'] );
    	$input['custom_HTML'] = $input['custom_HTML'];

    	return $input;
    } // end op_validate_text_options()


    /**
	 * Add a settings link to the plugin page
	 *
	 * @access  public
	 * @since   0.1.0
	 */
	function op_plugin_settings_link( $links ) {

	  $settings_link = "<a href='options-general.php?page=op_settings'>".__('Settings')."</a>";
	  array_unshift( $links, $settings_link );

	  return $links;

	} // end op_plugin_settings_link()

} // end

// Load only if we are viewing an admin page
if ( is_admin() )
	$settings = new OpaqueSettingPage();

?>