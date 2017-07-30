<?php
/**
 * Created by PhpStorm.
 * User: udit
 * Date: 7/28/17
 * Time: 1:12 PM
 */

/**
 * Plugin Name: Blue WordPress Categories
 * Plugin URI: https://github.com/desaiuditd/wp-blue-sync-categories
 * Description: A WordPress plugin to keep WordPress Categories in sync with external source.
 * Version: 0.0.1
 * Author: desaiuditd
 * Author URI: http://blog.incognitech.in
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Blue_WP_Categories' ) ) {

    class Blue_WP_Categories {

        /** Singleton *************************************************************/

        /**
         * @var Blue_WP_Categories The one true Blue_WP_Categories
         * @since 0.1
         */
        private static $instance;

		public $classes;

        /**
         * Main Blue_WP_Categories Instance
         *
         * Insures that only one instance of Blue_WP_Categories exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         *
         * @since 0.1
         * @static
         * @static var array $instance
         * @return Blue_WP_Categories The one true Blue_WP_Categories
         */
        public static function instance() {
            if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Blue_WP_Categories ) ) {
                self::$instance = new Blue_WP_Categories;
                self::$instance->classes = array();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();
                self::$instance->hooks();
            }
            return self::$instance;
        }

        /**
         * Throw error on object clone
         *
         * The whole idea of the singleton design pattern is that there is a single
         * object therefore, we don't want the object to be cloned.
         *
         * @since 0.1
         * @access protected
         * @return void
         */
        public function __clone() {
            // Cloning instances of the class is forbidden
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', BWPC_TEXT_DOMAIN ), BWPC_VERSION );
        }

        /**
         * Disable unserializing of the class
         *
         * @since 0.1
         * @access protected
         * @return void
         */
        public function __wakeup() {
            // Unserializing instances of the class is forbidden
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', BWPC_TEXT_DOMAIN ), BWPC_VERSION );
        }

        /**
         * Setup plugin constants
         *
         * @access private
         * @since 0.1
         * @return void
         */
        private function setup_constants() {
            // Defines BWPC_VERSION if it does not exits.
            if ( ! defined( 'BWPC_VERSION' ) ) {
                define( 'BWPC_VERSION', '0.1' );
            }
            // Defines BWPC_TEXT_DOMAIN if it does not exits.
            if ( ! defined( 'BWPC_TEXT_DOMAIN' ) ) {
                define( 'BWPC_TEXT_DOMAIN', 'blue-wp-categories' );
            }
            // Defines BWPC_PATH if it does not exits.
            if ( ! defined( 'BWPC_PATH' ) ) {
                define( 'BWPC_PATH', plugin_dir_path( __FILE__ ) );
            }
            // Defines BWPC_URL if it does not exits.
            if ( ! defined( 'BWPC_URL' ) ) {
                define( 'BWPC_URL', plugin_dir_url( __FILE__ ) );
            }
            // Defines BWPC_BASE_PATH if it does not exits.
            if ( ! defined( 'BWPC_BASE_PATH' ) ) {
                define( 'BWPC_BASE_PATH', plugin_basename( __FILE__ ) );
            }
        }

        /**
         * Include required files
         *
         * @access private
         * @since 0.1
         * @return void
         */
        private function includes() {
            include_once trailingslashit( BWPC_PATH ) . 'lib/class-bwpc-autoload.php';

            new BWPC_Autoload( trailingslashit( BWPC_PATH ) . 'lib/' );
            new BWPC_Autoload( trailingslashit( BWPC_PATH ) . 'settings/' );
            new BWPC_Autoload( trailingslashit( BWPC_PATH ) . 'sync/' );
            new BWPC_Autoload( trailingslashit( BWPC_PATH ) . 'cron/' );

	        self::$instance->classes['settings'] = new BWPC_Settings();
	        self::$instance->classes['sync'] = new BWPC_Sync();
	        self::$instance->classes['cron'] = new BWPC_Sync_Cron();
        }

        /**
         * Loads the plugin language files
         *
         * @access public
         * @since 0.1
         * @return void
         */
        public function load_textdomain() {
            // Set filter for plugin's languages directory
            $lang_dir = dirname( plugin_basename( BWPC_PATH ) ) . '/languages/';
            $lang_dir = apply_filters( 'bwpc_languages_directory', $lang_dir );
            // Traditional WordPress plugin locale filter
            $locale        = apply_filters( 'plugin_locale',  get_locale(), BWPC_TEXT_DOMAIN );
            $mofile        = sprintf( '%1$s-%2$s.mo', BWPC_TEXT_DOMAIN, $locale );
            // Setup paths to current locale file
            $mofile_local  = $lang_dir . $mofile;
            $mofile_global = WP_LANG_DIR . '/' . BWPC_TEXT_DOMAIN . '/' . $mofile;
            if ( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/blue-wp-categories folder
                load_textdomain( BWPC_TEXT_DOMAIN, $mofile_global );
            } elseif ( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/blue-wp-categories/languages/ folder
                load_textdomain( BWPC_TEXT_DOMAIN, $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( BWPC_TEXT_DOMAIN, false, $lang_dir );
            }
        }

        function hooks() {
        	// https://codex.wordpress.org/Function_Reference/get_current_screen
	        // current_screen is used because we need to identify the screen.
	        // And it's available after current_screen
	        // Also we need Settings API functions. And they are available after admin_init.
	        // Hence.
            add_action( 'current_screen', array( $this, 'check_api_endpoint' ) );

	        add_action( 'admin_notices', array( $this, 'display_admin_notice' ) );
        }

        function check_api_endpoint() {
			$url = get_option( BWPC_Settings::$section_slug . BWPC_Settings::$api_endpoint_slug );

	        // check for settings page - need this in conditional further down
	        global $current_screen;

			if ( ! BWPC_Util::is_valid_url($url) && 'options' != $current_screen->id && 'options-general' != $current_screen->id ) {
				add_settings_error( 'bwpc-errors', 'bwpc_errors', sprintf( __( 'Blue WordPress Categories: You need to fill valid URL for external API Endpoint URL. %s.', BWPC_TEXT_DOMAIN ), '<a href="' . admin_url( 'options-general.php#' . BWPC_Settings::$section_slug . BWPC_Settings::$api_endpoint_slug ) . '">' . __( 'Fix this', BWPC_TEXT_DOMAIN ) . '</a>' ) );
			}
        }

		function display_admin_notice() {

			// check for our settings page - need this in conditional further down
			global $current_screen;

			if ( 'options-general' == $current_screen->id ) {
				return;
			}

			// collect setting errors/notices: //http://codex.wordpress.org/Function_Reference/get_settings_errors
			$set_errors = get_settings_errors();

			//display admin message only for the admin to see, only when setting errors/notices are returned!
			if ( current_user_can( 'manage_options' ) && ! empty( $set_errors ) ) {
				// there maybe more than one so run a foreach loop.
				foreach ( $set_errors as $set_error ) {
					// set the title attribute to match the error "setting title" - need this in js file
					BWPC_Util::show_message( '<p><strong>' . $set_error['message'] . '</strong></p>', 'error' );
				}
			}
		}

    }

}

/**
 * The main function responsible for returning the one true Blue_WP_Categories
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $bwpc = bwpc(); ?>
 *
 * @since 0.1
 * @return object The one true Blue_WP_Categories Instance
 */
function bwpc() {
    return Blue_WP_Categories::instance();
}

// Get Blue_WP_Categories Running
bwpc();

/**
 * Look Maa! A Singleton Class Design Pattern! I'm sure you would be <3 ing design patterns.
 */
