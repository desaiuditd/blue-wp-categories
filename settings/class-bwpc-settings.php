<?php

/**
 * Created by PhpStorm.
 * User: udit
 * Date: 7/28/17
 * Time: 2:00 PM
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'BWPC_Settings' ) ) {

    class BWPC_Settings {

	    static $section_slug = 'bwpc_';
	    static $api_endpoint_slug = 'api_endpoint';
	    static $manual_sync_slug = 'manual_sync';

        function __construct() {

	        add_filter( 'plugin_action_links_' . BWPC_BASE_PATH, array( $this, 'plugin_actions' ), 10, 4 );

	        add_action( 'admin_enqueue_scripts', array($this, 'enqueue_scripts'));

	        add_action( 'admin_init', array( $this, 'settings_init' ) );

        }

        function plugin_actions( $actions, $plugin_file, $plugin_data, $context ) {
            $actions['settings'] = '<a href="' . admin_url( 'options-general.php#' . self::$section_slug . self::$api_endpoint_slug ) . '">' . __( 'Settings', BWPC_TEXT_DOMAIN ) . '</a>';
            return $actions;
        }

	    function enqueue_scripts($hook) {
		    if ( 'options-general.php' == $hook ) {
			    wp_enqueue_script( 'bwpc-settings-script', plugins_url( '../ui/js/bwpc-settings.js', __FILE__ ), array('jquery'), time());
		    }
	    }

	    function settings_init() {

		    add_settings_section( self::$section_slug . 'section', __( 'Blue WordPress Category Sync', BWPC_TEXT_DOMAIN ), array( $this, 'bwpc_section_callback' ), 'general' );

		    add_settings_field( self::$section_slug . self::$api_endpoint_slug, __( 'Endpoint URL', BWPC_TEXT_DOMAIN ), array( $this, 'endpoint_url_callback' ), 'general', self::$section_slug . 'section' );
		    register_setting( 'general', self::$section_slug . self::$api_endpoint_slug );
		    add_filter( 'sanitize_option_' . self::$section_slug . self::$api_endpoint_slug, array( $this, 'sanitize_endpoint_url' ), 10, 2 );

		    add_settings_field( self::$section_slug . self::$manual_sync_slug, __( 'Sync Categories', BWPC_TEXT_DOMAIN ), array( $this, 'manual_sync_callback' ), 'general', self::$section_slug . 'section' );
	    }

		function bwpc_section_callback() {
			?>
			<p class="description">
				<?php _e( 'This section lets you control WordPress Category sync from external source.', BWPC_TEXT_DOMAIN ); ?><br />
				<?php _e( 'Please make sure you save the external API endpoint URL in the given setting below.', BWPC_TEXT_DOMAIN ); ?>
			</p>
			<?php
		}

	    function endpoint_url_callback() {
		    ?>
		    <input type="url" class="regular-text code" name="<?php echo self::$section_slug . self::$api_endpoint_slug; ?>" id="<?php echo self::$section_slug . self::$api_endpoint_slug; ?>" value="<?php echo get_option( self::$section_slug . self::$api_endpoint_slug ); ?>" />
		    <?php
	    }

	    function sanitize_endpoint_url( $value, $option ) {

        	if ( ! BWPC_Util::is_valid_url( $value ) ) {
		        add_settings_error( $option, 'bwpc_errors', sprintf( __( 'You need to fill valid URL for external API Endpoint URL.', BWPC_TEXT_DOMAIN ) ) );
	        }

		    return $value;
	    }

	    function manual_sync_callback() {
		    ?>
		    <input type="button" name="bwpc-sync" id="bwpc-sync" class="button button-primary" value="Sync Categories" />
		    <?php
	    }


    }

}