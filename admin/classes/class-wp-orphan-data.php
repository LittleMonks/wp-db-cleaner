<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Db_Cleaner
 * @subpackage Db_Cleaner/admin/orphan
 */

/**
 * The orphan data related functionality.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Db_Cleaner
 * @subpackage Db_Cleaner/admin
 * @author     Utkarsh <iamutkarsh@live.com>
 */
if ( ! class_exists( 'Wp_Orphan_Data' ) ) {
	class Wp_Orphan_Data {

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 */
		public function __construct() {

		}

		/**
		 * Get all orphan data from wp_posts table
		 *
		 * @param bool|false $count
		 *
		 * @return mixed
		 */
		public function get_wp_posts_orphan_rows ( $count = false ) {

			global $wpdb;

			$select = ( $count ) ? 'COUNT(*)' : '*';

			$query = $wpdb->prepare( "SELECT $select FROM {$wpdb->posts} LEFT JOIN {$wpdb->posts} child ON ({$wpdb->posts}.post_parent = child.ID) WHERE ({$wpdb->posts}.post_parent <> 0) AND (child.ID IS NULL)" );

			if ( $count ) {
				return $wpdb->get_var( $query );
			} else {
				return $wpdb->get_results( $query );
			}

		}

	}

}