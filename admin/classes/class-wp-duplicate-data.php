<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Wp_Duplicate_Data
 * @subpackage Db_Cleaner/admin/classes
 */

/**
 * The orphan data related functionality.
 *
 * Defines the duplicate functions
 *
 * @package    Db_Cleaner
 * @subpackage Db_Cleaner/admin
 * @author     Utkarsh <iamutkarsh@live.com>
 */
if ( ! class_exists( 'Wp_Duplicate_Data' ) ) {
	class Wp_Duplicate_Data {

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 */
		public function __construct() {

		}

		/**
		 * Checking for dupe _wp_attached_file / _wp_attachment_metadata keys (should only ever be one each per attachment post type).
		 *
		 * @param bool|false $count
		 *
		 * @return array|null|object|string
		 */
		public function get_attachment_meta_duplicate( $count = false ) {
			global $wpdb;

			$select = ( $count ) ? 'COUNT(*)' : 'post_id,meta_key,meta_value';

			$query = $wpdb->prepare( "SELECT $select
							FROM {$wpdb->postmeta}
							WHERE (meta_key IN('_wp_attached_file','_wp_attachment_metadata'))
							GROUP BY post_id,meta_key
							HAVING (COUNT(post_id) > 1)" );

			if ( $count ) {
				return $wpdb->get_var( $query );
			} else {
				return $wpdb->get_results( $query );
			}
		}

		public function delete_attachment_meta_duplicate() {
			global $wpdb;
			$query = $wpdb->prepare( "DELETE
							FROM {$wpdb->postmeta}
							WHERE (meta_key IN('_wp_attached_file','_wp_attachment_metadata'))
							GROUP BY post_id,meta_key
							HAVING (COUNT(post_id) > 1)" );

			return $wpdb->query( $query );
		}

		/**
		 * Where an identical meta_key exists for the same post more than once.
		 *
		 * @param bool|false $count
		 *
		 * @return array|int|null|object
		 */
		public function get_post_meta_duplicate( $count = false ) {
			global $wpdb;

			$query = $wpdb->prepare( "SELECT *,COUNT(*) AS keycount
										FROM {$wpdb->postmeta}
										GROUP BY post_id,meta_key
										HAVING (COUNT(*) > 1)" );

			if ( $count ) {
				return count( $wpdb->get_results( $query ) );
			} else {
				return $wpdb->get_results( $query );
			}
		}

		public function delete_duplicate_meta() {
			global $wpdb;
			$query = $wpdb->prepare( "DELETE FROM {$wpdb->postmeta}
										WHERE (meta_id IN (
										    SELECT * FROM (
										        SELECT meta_id
										        FROM {$wpdb->postmeta} tmp
										        GROUP BY post_id,meta_key
										        HAVING (COUNT(*) > 1)
										    ) AS tmp
										))" );

			return $wpdb->query( $query );
		}

		/**
		 * Checking for missing _wp_attached_file / _wp_attachment_metadata keys on wp_posts.post_type = 'attachment' rows.
		 *
		 * @param bool|false $count
		 *
		 * @return array|null|object|string
		 */
		public function get_missing_attachment_meta( $count = false ) {
			global $wpdb;

			$select = ( $count ) ? 'COUNT(*)' : '*';

			$query = $wpdb->prepare( "SELECT $select FROM {$wpdb->posts} posts
										LEFT JOIN {$wpdb->postmeta} postmeta ON (
										    (posts.ID = postmeta.post_id) AND
										    ((postmeta.meta_key = '_wp_attached_file') OR postmeta.meta_key = '_wp_attachment_metadata')
										)
										WHERE (posts.post_type = 'attachment') AND (postmeta.meta_id IS NULL)" );

			if ( $count ) {
				return $wpdb->get_var( $query );
			} else {
				return $wpdb->get_results( $query );
			}
		}

		public function delete_missing_attachment() {
			global $wpdb;
			$query = $wpdb->prepare( "DELETE posts.* FROM {$wpdb->posts} posts
										LEFT JOIN {$wpdb->postmeta} postmeta ON (
										    (posts.ID = postmeta.post_id) AND
										    ((postmeta.meta_key = '_wp_attached_file') OR postmeta.meta_key = '_wp_attachment_metadata')
										)
										WHERE (posts.post_type = 'attachment') AND (postmeta.meta_id IS NULL)" );

			return $wpdb->query( $query );
		}

		/**
		 * Rows created against a post when edited by a WordPress admin user. They can be safely removed.
		 * https://wordpress.org/support/topic/can-i-remove-_edit_lock-_edit_last-from-wp_postmeta
		 *
		 * @param bool|false $count
		 *
		 * @return array|null|object|string
		 */
		public function get_post_meta_locks( $count = false ) {
			global $wpdb;

			$select = ( $count ) ? 'COUNT(*)' : '*';

			$query = $wpdb->prepare( "SELECT $select FROM {$wpdb->postmeta}
										WHERE meta_key IN ('_edit_lock','_edit_last')" );

			if ( $count ) {
				return $wpdb->get_var( $query );
			} else {
				return $wpdb->get_results( $query );
			}
		}

		public function delete_post_meta_locks() {
			global $wpdb;
			$query = $wpdb->prepare( "DELETE FROM {$wpdb->postmeta}
										WHERE meta_key IN ('_edit_lock','_edit_last')" );

			return $wpdb->query( $query );
		}

		/**
		 * A transient value is one stored by WordPress and/or a plugin generated from a complex query - basically a cache. More information can be found in this answer on http://stackoverflow.com/a/11995022
		 *
		 * @param bool|false $count
		 *
		 * @return array|null|object|string
		 */
		public function get_wp_transients( $count = false ) {
			global $wpdb;

			$select = ( $count ) ? 'COUNT(*)' : '*';

			$query = $wpdb->prepare( "SELECT $select FROM {$wpdb->options}
										WHERE option_name LIKE '%\_transient\_%'" );

			if ( $count ) {
				return $wpdb->get_var( $query );
			} else {
				return $wpdb->get_results( $query );
			}
		}

		public function delete_transients() {
			global $wpdb;
			$query = $wpdb->prepare( "DELETE FROM {$wpdb->options}
										WHERE option_name LIKE '%\_transient\_%'" );

			return $wpdb->query( $query );
		}

		/**
		 * Every save of a WordPress post will create a new revision (and related wp_postmeta rows). To clear out all revisions older than 15 days
		 *
		 * @param bool|false $count
		 *
		 * @return array|null|object|string
		 */
		public function get_post_revisions( $count = false ) {
			global $wpdb;

			$select = ( $count ) ? 'COUNT(*)' : '*';

			$query = $wpdb->prepare( "SELECT $select FROM {$wpdb->posts}
										WHERE
										    (post_type = 'revision') AND
										    (post_modified_gmt < DATE_SUB(NOW(),INTERVAL 15 DAY))
										ORDER BY post_modified_gmt DESC" );

			if ( $count ) {
				return $wpdb->get_var( $query );
			} else {
				return $wpdb->get_results( $query );
			}
		}

		public function delete_reversion() {
			global $wpdb;
			$query = $wpdb->prepare( "DELETE FROM {$wpdb->posts}
											WHERE
											    (post_type = 'revision') AND
											    (post_modified_gmt < DATE_SUB(NOW(),INTERVAL 15 DAY))" );

			return $wpdb->query( $query );
		}

	}

}
