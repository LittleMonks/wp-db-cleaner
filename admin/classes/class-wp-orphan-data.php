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

		public static function get_array(){
			return  array(
			'get_function' => 'Tab name',
			);
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

			$query = ( "SELECT $select FROM {$wpdb->posts} posts LEFT JOIN {$wpdb->posts} child ON (posts.post_parent = child.ID) WHERE (posts.post_parent <> 0) AND (child.ID IS NULL)" );

			if ( $count ) {
				return $wpdb->get_var( $query );
			} else {
				return $wpdb->get_results( $query );
			}

		}

		/**
		 * Get all orphan data fro wp_postmeta table
		 *
		 * @param bool|false $count
		 *
		 * @return mixed
		 */
		public function get_wp_postmeta_orhpan_rows ( $count = false ) {

			global $wpdb;

			$select = ( $count ) ? 'COUNT(*)' : '*';

			$query = ( "SELECT $select FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON (meta.post_id = posts.ID) WHERE (posts.ID IS NULL)" );

			if ( $count ) {
				return $wpdb->get_var( $query );
			} else {
				return $wpdb->get_results( $query );
			}

		}

		/**
		 * Get all orphan data from wp_term_taxonomy table
		 *
		 * @param bool|false $count
		 *
		 * @return mixed
		 */
		public function get_wp_term_taxonomy_orphan_rows ( $count = false ) {

			global $wpdb;

			$select = ( $count ) ? 'COUNT(*)' : '*';

			$query = ( "SELECT $select FROM {$wpdb->term_taxonomy} taxonomy LEFT JOIN {$wpdb->terms} terms ON (taxonomy.term_id = terms.term_id) WHERE (terms.term_id IS NULL)" );

			if ( $count ) {
				return $wpdb->get_var( $query );
			} else {
				return $wpdb->get_results( $query );
			}

		}

		/**
		 * Get all orphan data from wp_term_relationships table
		 *
		 * @param bool|false $count
		 *
		 * @return mixed
		 */
		public function get_wp_term_relationships_orphan_rows ( $count = false ) {

			global $wpdb;

			$select = ( $count ) ? 'COUNT(*)' : '*';

			$query = ( "SELECT $select FROM {$wpdb->term_relationships} relationships LEFT JOIN {$wpdb->term_taxonomy} taxonomy ON (relationships.term_taxonomy_id = taxonomy.term_taxonomy_id) WHERE (taxonomy.term_taxonomy_id IS NULL)" );

			if ( $count ) {
				return $wpdb->get_var( $query );
			} else {
				return $wpdb->get_results( $query );
			}

		}

		/**
		 * Get all orphan data fro wp_usermeta table
		 *
		 * @param bool|false $count
		 *
		 * @return mixed
		 */
		public function get_wp_usermeta_orphan_rows ( $count = false ) {

			global $wpdb;

			$select = ( $count ) ? 'COUNT(*)' : '*';

			$query = ( "SELECT $select FROM {$wpdb->usermeta} usermeta LEFT JOIN {$wpdb->users} users ON (usermeta.user_id = users.ID) WHERE (users.ID IS NULL)" );

			if ( $count ) {
				return $wpdb->get_var( $query );
			} else {
				return $wpdb->get_results( $query );
			}

		}

		/**
		 * Get all orphan data fro wp_posts table with no author id
		 *
		 * @param bool|false $count
		 *
		 * @return mixed
		 */
		public function get_wp_posts_author_orphan_rows ( $count = false ) {

			global $wpdb;

			$select = ( $count ) ? 'COUNT(*)' : '*';

			$query = ( "SELECT $select FROM {$wpdb->posts} posts LEFT JOIN {$wpdb->users} users ON (posts.post_author = users.ID) WHERE (users.ID IS NULL)" );

			if ( $count ) {
				return $wpdb->get_var( $query );
			} else {
				return $wpdb->get_results( $query );
			}

		}

	}

}

if ( !function_exists('lm_dbc_orphan_ui') ) {
	function lm_dbc_orphan_ui(){
		?>
		<h1>Orhpan tab</h1>
	<?php
	}
}
