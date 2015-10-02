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

		public static function get_array() {
			return array(
				'Attachment Meta'         => 'get_attachment_meta_duplicate',
				'Post Meta'               => 'get_post_meta_duplicate',
				'Missing Attachment Meta' => 'get_missing_attachment_meta',
				'Post Meta Locks'         => 'get_post_meta_locks',
				'Transients'              => 'get_wp_transients',
				'Post Revisions'          => 'get_post_revisions',
			);
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

			$query = "SELECT $select
							FROM {$wpdb->postmeta}
							WHERE (meta_key IN('_wp_attached_file','_wp_attachment_metadata'))
							GROUP BY post_id,meta_key
							HAVING (COUNT(post_id) > 1)";

			if ( $count ) {
				return $wpdb->get_var( $query );
			} else {
				return $wpdb->get_results( $query );
			}
		}

		public function delete_attachment_meta_duplicate() {
			global $wpdb;
			$query = "DELETE
							FROM {$wpdb->postmeta}
							WHERE (meta_key IN('_wp_attached_file','_wp_attachment_metadata'))
							GROUP BY post_id,meta_key
							HAVING (COUNT(post_id) > 1)";

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

			$query = "SELECT *,COUNT(*) AS keycount
										FROM {$wpdb->postmeta}
										GROUP BY post_id,meta_key
										HAVING (COUNT(*) > 1)";

			if ( $count ) {
				return count( $wpdb->get_results( $query ) );
			} else {
				return $wpdb->get_results( $query );
			}
		}

		public function delete_post_meta_duplicate() {
			global $wpdb;
			$query = "DELETE FROM {$wpdb->postmeta}
										WHERE (meta_id IN (
										    SELECT * FROM (
										        SELECT meta_id
										        FROM {$wpdb->postmeta} tmp
										        GROUP BY post_id,meta_key
										        HAVING (COUNT(*) > 1)
										    ) AS tmp
										))";

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

			$query = "SELECT $select FROM {$wpdb->posts} posts
										LEFT JOIN {$wpdb->postmeta} postmeta ON (
										    (posts.ID = postmeta.post_id) AND
										    ((postmeta.meta_key = '_wp_attached_file') OR postmeta.meta_key = '_wp_attachment_metadata')
										)
										WHERE (posts.post_type = 'attachment') AND (postmeta.meta_id IS NULL)";

			if ( $count ) {
				return $wpdb->get_var( $query );
			} else {
				return $wpdb->get_results( $query );
			}
		}

		public function delete_missing_attachment_meta() {
			global $wpdb;
			$query = "DELETE posts.* FROM {$wpdb->posts} posts
										LEFT JOIN {$wpdb->postmeta} postmeta ON (
										    (posts.ID = postmeta.post_id) AND
										    ((postmeta.meta_key = '_wp_attached_file') OR postmeta.meta_key = '_wp_attachment_metadata')
										)
										WHERE (posts.post_type = 'attachment') AND (postmeta.meta_id IS NULL)";

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

			$query = "SELECT $select FROM {$wpdb->postmeta}
										WHERE meta_key IN ('_edit_lock','_edit_last')";

			if ( $count ) {
				return $wpdb->get_var( $query );
			} else {
				return $wpdb->get_results( $query );
			}
		}

		public function delete_post_meta_locks() {
			global $wpdb;
			$query = "DELETE FROM {$wpdb->postmeta}
										WHERE meta_key IN ('_edit_lock','_edit_last')";

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

			$query = "SELECT $select FROM {$wpdb->options}
										WHERE option_name LIKE '%\_transient\_%'";

			if ( $count ) {
				return $wpdb->get_var( $query );
			} else {
				return $wpdb->get_results( $query );
			}
		}

		public function delete_wp_transients() {
			global $wpdb;
			$query = "DELETE FROM {$wpdb->options}
										WHERE option_name LIKE '%\_transient\_%'";

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

			$query = "SELECT $select FROM {$wpdb->posts}
										WHERE
										    (post_type = 'revision') AND
										    (post_modified_gmt < DATE_SUB(NOW(),INTERVAL 15 DAY))
										ORDER BY post_modified_gmt DESC";

			if ( $count ) {
				return $wpdb->get_var( $query );
			} else {
				return $wpdb->get_results( $query );
			}
		}

		public function delete_post_revisions() {
			global $wpdb;
			$query = "DELETE FROM {$wpdb->posts}
											WHERE
											    (post_type = 'revision') AND
											    (post_modified_gmt < DATE_SUB(NOW(),INTERVAL 15 DAY))";

			return $wpdb->query( $query );
		}

	}

}


if ( ! function_exists( 'lm_dbc_duplicate_ui' ) ) {
	function lm_dbc_duplicate_ui() {
		global $duplicate_data;
		?>
		<div class="lm-dbc-sidebar">
			<?php lm_dbc_jarvis_give_me_tabs( Wp_Duplicate_Data::get_array(), admin_url( 'tools.php?page=db-clean&subpage=2' ), $duplicate_data ); ?>
		</div>
		<div class="lm-dbc-table">
			<?php $table = lm_dbc_get_table_content();
			lm_dbc_print_table($table);
			?>
		</div>

		<div class="lm-dbc-support">
			Support us
		</div>

		<?php
	}
}

