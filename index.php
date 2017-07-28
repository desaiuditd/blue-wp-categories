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
//            new WM_Autoload( trailingslashit( BWPC_PATH ) . 'revision/' );
//            new WM_Autoload( trailingslashit( BWPC_PATH ) . 'settings/' );
//            new WM_Settings();
//            new WM_Admin();
//            new WM_Revision();
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
