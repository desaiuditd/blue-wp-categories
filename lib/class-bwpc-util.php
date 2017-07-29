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

	class BWPC_Util {

		public static function is_valid_url($url) {
			return filter_var($url, FILTER_VALIDATE_URL);
		}

		public static function show_message( $message, $type = 'info' ) {
			?>

			<div class="is-dismissible notice <?php echo $type; ?>"><?php echo $message; ?></div>
			<?php
		}

	}

}