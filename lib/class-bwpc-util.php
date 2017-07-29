<?php

/**
 * Created by PhpStorm.
 * User: udit
 * Date: 7/28/17
 * Time: 4:08 PM
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BWPC_Util' ) ) {

	/**
	 * Class BWPC_Util
	 *
	 * Contains all the utility methods.
	 *
	 * @since 0.1
	 */
	class BWPC_Util {

		/**
		 * @param $url
		 *
		 * @return mixed
		 *
		 * @since 0.1
		 */
		public static function is_valid_url($url) {
			return filter_var($url, FILTER_VALIDATE_URL);
		}

		/**
		 * @param $message
		 * @param string $type
		 *
		 * @since 0.1
		 */
		public static function show_message( $message, $type = 'info' ) {
			?>

			<div class="is-dismissible notice <?php echo $type; ?>"><?php echo $message; ?></div>
			<?php
		}

	}

}