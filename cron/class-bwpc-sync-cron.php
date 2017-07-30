<?php

/**
 * Created by PhpStorm.
 * User: udit
 * Date: 7/29/17
 * Time: 10:20 AM
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BWPC_Sync_Cron' ) ) {

	/**
	 * Class BWPC_Sync_Cron
	 *
	 * This class sets up the cron functionality to sync the categories.
	 *
	 * @since 0.1
	 */
	class BWPC_Sync_Cron {

		/**
		 * BWPC_Sync_Cron constructor.
		 *
		 * @since 0.1
		 */
		public function __construct() {

			add_action( 'init', array( $this, 'setup_cron_schedule' ) );

			add_filter( 'cron_schedules', array( $this, 'add_cron_interval' ) );

			register_deactivation_hook( __FILE__, array( $this, 'unschedule_cron_hook' ) );
		}

		function setup_cron_schedule() {
			add_action( 'bwpc_sync_cron_hook', array( $this, 'execute_cron' ) );

			if ( ! wp_next_scheduled( 'bwpc_sync_cron_hook' ) ) {
				wp_schedule_event( time(), 'halfhourly', 'bwpc_sync_cron_hook' );
			}
		}

		function add_cron_interval( $schedules ) {
			$schedules['halfhourly'] = array(
				'interval' => 1800, // 30 minutes * 60 seconds = 1800 seconds
				'display'  => esc_html__( 'Half Hourly - Every 30 Minutes' ),
			);

			return $schedules;
		}

		function execute_cron() {
			$isSynced = bwpc()->classes['sync']->sync_categories();
			error_log( 'Blue WordPress Categories Cron: ' . var_export( $isSynced, true ) );
		}

		function unschedule_cron_hook() {
			wp_clear_scheduled_hook( 'bwpc_sync_cron_hook' );
		}
	}

}