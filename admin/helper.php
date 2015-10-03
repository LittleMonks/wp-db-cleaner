<?php
/**
 * Created by PhpStorm.
 * User: spock
 * Date: 2/10/15
 * Time: 1:27 PM
 */


if ( ! function_exists( 'lm_dbc_is_active_tab' ) ) {
	function lm_dbc_is_active_tab( $value, $data, $first_selected, $echo = true ) {
		if ( ( ! isset( $_REQUEST['tab'] ) && $first_selected ) || ( ! empty( $_REQUEST['tab'] ) && $_REQUEST['tab'] == $value ) ) {
			if ( $echo ) {
				echo $data;
			}

			return $data;
		}

		return '';
	}
}

if ( ! function_exists( 'lm_dbc_get_table_content' ) ) {
	function lm_dbc_get_table_content() {
		global $orphan_data, $duplicate_data;
		$result          = null;
		$orphan_array    = Wp_Orphan_Data::get_array();
		$duplicate_array = Wp_Duplicate_Data::get_array();

		if ( ! isset( $_REQUEST['tab'] ) ) {
			if ( ! isset( $_REQUEST['subpage'] ) ) {
				$key    = reset( $orphan_array );
				$result = array( $orphan_data, $orphan_array[ $key ] );
			} else {
				$key    = reset( $duplicate_array );
				$result = array( $duplicate_data, $duplicate_array[ $key ] );
			}
		} else {
			$key = $_REQUEST['tab'];
			if ( isset( $orphan_array[ $key ] ) ) {
				$result = array( $orphan_data, $key );
			} else if ( isset( $duplicate_array[ $key ] ) ) {
				$result = array( $duplicate_data, $key );
			}
		}
		if ( ! empty( $result ) ) {
			if (isset( $_REQUEST['paged'] )){
				$page = intval( $_REQUEST['paged']) * Wp_Db_Cleaner_List::$limit;
				var_dump($page);
				return call_user_func_array( $result, array( false, $page - Wp_Db_Cleaner_List::$limit, $page ) );
			}
			return call_user_func_array( $result, array( false, 0 ,Wp_Db_Cleaner_List::$limit ) );
		}

		return $result;
	}
}

if (! function_exists('__dbc_pagination')){
	function __dbc_pagination( $query, $count, $offset, $limit ){
		if ( !$count && is_numeric($offset) && $offset > 0 ){
			$offset = ' OFFSET '.$offset;
		} else {
			$offset = '';
		}
		if (!$count && is_numeric($limit) && $limit > 0){
			$limit = ' LIMIT '.$limit;
		} else {
			$limit = '';
		}
		return $query . $limit . $offset;
	}
}
