<?php

/**
 * Created by PhpStorm.
 * User: udit
 * Date: 7/28/17
 * Time: 1:50 PM
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'BWPC_Sync' ) ) {

    /**
     * Class BWPC_Sync
     *
     * Handles all the functionality to sync categories with external source.
     *
     * @since 0.1
     */
    class BWPC_Sync {

        /**
         * @since 0.1
         */
        function __construct() {
			// handle manual sync with AJAX calls from General Options page
	        add_action( 'wp_ajax_bwpc_sync_categories', array( $this, 'sync_categories_manual' ) );
        }

	    /**
	     * @since 0.1
	     */
		function sync_categories_manual() {

        	$isSynced = $this->sync_categories();

        	if ($isSynced) {

        		wp_send_json_success();

	        } else {

        		wp_send_json_error();

	        }

		}

	    /**
	     * @return bool
	     *
	     * @since 0.1
	     */
	    public function sync_categories() {

        	$default_category = get_option( 'default_category' );

			$categories = $this->fetch_categories();

			$flag = true;

			$new_terms = array();

	        if ($categories && ! $categories['error']) {

	        	foreach ($categories as $category) {

	        		$term = $this->does_category_exist( $category['id'] );

                    if ( ! $term ) {
                        $term = $this->create_category( $category, $categories );
			        } else {
                    	$this->check_parent( $term, $category );
                    }

			        if ( ! $term ) {
                    	$flag = false;
                    	return $flag;
			        }

			        array_push( $new_terms, $term );
		        }

				$pending_terms = get_categories( array(
					'taxonomy' => 'category',
					'hide_empty' => false,
					'fields' => 'ids',
					'exclude' => $new_terms,
				) );

				foreach ( $pending_terms as $term ) {

					if ( $term == $default_category ) {
						update_option( 'default_category', $new_terms[0] );
					}

					$isDeleted = wp_delete_term( $term, 'category' );

					if ( true != $isDeleted ) {
						$flag = false;
						return $flag;
					}
				}

	        } else {

	        	error_log( var_export( $categories, true ) );
	        	$flag = false;

	        }

			return $flag;
        }

	    /**
	     * @return array|mixed|object
	     *
	     * @since 0.1
	     */
	    function fetch_categories() {

			$api_url = get_option( BWPC_Settings::$section_slug . BWPC_Settings::$api_endpoint_slug );
			$res = wp_safe_remote_get( $api_url );

			if ( ! $res instanceof WP_Error ) {

				if ( 200 == $res['response']['code'] ) {

					$categories = json_decode( $res['body'], true );

				} else {

					$categories = array( 'error' => $res['response']['message'] );

				}

			} else {

				$categories = array( 'error' => $res );

			}

			return $categories;
		}

	    /**
	     * @param $external_id
	     *
	     * @return bool
	     *
	     * @since 0.1
	     */
	    function does_category_exist( $external_id ) {

        	$categories = get_categories( array(
		        'taxonomy' => 'category',
		        'hide_empty' => false,
		        'fields' => 'ids',
		        'meta_key' => '_blue_cat_id',
		        'meta_value' => $external_id,
	        ) );

			$flag = count( $categories ) > 0 ? $categories[0] : false;

        	return $flag;
		}


	    /**
	     * @param $category_to_create
	     * @param $external_categories
	     *
	     * @return mixed
	     *
	     * @since 0.1
	     */
	    function create_category( $category_to_create, $external_categories ) {

        	// No Parent. So directly create the category.
        	if ( NULL == $category_to_create['parent_id'] ) {

        		$term = wp_insert_term( $category_to_create['name'], 'category' );
        		add_term_meta( $term['term_id'], '_blue_cat_id', $category_to_create['id'] );

	        } else {
        		// First create the parent, if not already existing. Then create the category.
		        // Check if parent exists or not.
		        $parent = $this->does_category_exist( $category_to_create['parent_id'] );
		        if ($parent) {
			        $term = wp_insert_term( $category_to_create['name'], 'category', array( 'parent' => $parent ) );
			        add_term_meta( $term['term_id'], '_blue_cat_id', $category_to_create['id'] );
		        } else {
		        	// https://stackoverflow.com/a/2408945
			        // use outer variable in inner annonymous function
		        	$parent_cat = array_filter($external_categories, function( $cat ) use ( $category_to_create ) {
						return $cat['id'] == $category_to_create['parent_id'];
			        });

		        	if ( count( $parent_cat ) > 0 ) {
		        		// get the first element from the associative array.
				        // https://stackoverflow.com/questions/1617157/how-to-get-the-first-item-from-an-associative-php-array
		        		$parent_cat = reset( $parent_cat );

		        		$parent = $this->create_category( $parent_cat, $external_categories );

				        $term = wp_insert_term( $category_to_create['name'], 'category', array( 'parent' => $parent ) );
				        add_term_meta( $term['term_id'], '_blue_cat_id', $category_to_create['id'] );

			        } else {

		        		// No Parent Category found with this ID.
				        // So category will be created without parent.
				        $term = wp_insert_term( $category_to_create['name'], 'category' );
				        add_term_meta( $term['term_id'], '_blue_cat_id', $category_to_create['id'] );
			        }
		        }
	        }

	        return $term['term_id'];
		}


	    /**
	     * @param $wp_term
	     * @param $external_category
	     *
	     * @since 0.1
	     */
	    function check_parent( $wp_term, $external_category ) {
        	if ( $external_category['parent_id'] == NULL ) {
        		$parent = 0;
	        } else {
		        $parent = $this->does_category_exist( $external_category['parent_id'] );
	        }

			wp_update_term( $wp_term, 'category', array( 'parent' => $parent ) );
		}

    }

}
