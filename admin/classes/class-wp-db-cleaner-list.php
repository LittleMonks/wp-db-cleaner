<?php
/**
 * Created by PhpStorm.
 * User: spock
 * Date: 2/10/15
 * Time: 4:33 PM
 */
if ( ! class_exists( 'Wp_Duplicate_Data' ) ) {

	// WP_List_Table is not loaded automatically so we need to load it in our application
	if ( ! class_exists( 'WP_List_Table' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	}

	class Wp_Db_Cleaner_List extends WP_List_Table {

	}
}
