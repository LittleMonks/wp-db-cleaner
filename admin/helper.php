<?php
/**
 * Created by PhpStorm.
 * User: spock
 * Date: 2/10/15
 * Time: 1:27 PM
 */


if ( ! function_exists( 'lm_dbc_jarvis_give_me_tabs' ) ) {
	function lm_dbc_jarvis_give_me_tabs( $tab_array, $url, $obj ) {
		if ( empty( $tab_array ) ) {
			return;
		}
		?>
		<h3 class="nav-tab-wrappe">
			<?php
			$first_selected = false;
			if ( ! isset( $_REQUEST['tab'] ) ) {
				$first_selected = true;
			}
			foreach ( $tab_array as $key => $val ) {
				?>
				<a class="nav-tab <?php lm_dbc_is_active_tab( $val, 'nav-tab-active', $first_selected ); ?>"
				   href="<?php echo esc_url( $url . '&tab=' . $val ); ?>"><?php echo $key; ?> (<?php echo call_user_func_array(array($obj,$val ), array(true)) ?>)</a>
				<?php
				if ( $first_selected ) {
					$first_selected = false;
				}
			} ?>
		</h3>
		<?php
	}
}

if ( ! function_exists( 'lm_dbc_is_active_tab' ) ) {
	function lm_dbc_is_active_tab( $value, $data, $first_selected ) {
		if ( ( ! isset( $_REQUEST['tab'] ) && $first_selected ) || ( ! empty( $_REQUEST['tab'] ) && $_REQUEST['tab'] == $value ) ) {
			echo $data;
		}
	}
}

if ( ! function_exists( 'lm_dbc_get_table_content' ) ) {
	function lm_dbc_get_table_content() {
		global $orphan_data, $duplicate_data;
		$result          = null;
		$first_time      = false;
		$orphan_array    = Wp_Orphan_Data::get_array();
		$duplicate_array = Wp_Duplicate_Data::get_array();
		if ( ! isset( $_REQUEST['tab'] ) ) {
			if ( ! isset( $_REQUEST['subpage'] ) ) {
				$key    = reset( $orphan_array );
				$result = call_user_func_array( array( $orphan_data, $key ), array( false ) );
			} else {
				$key    = reset( $duplicate_array );
				$result = call_user_func_array( array( $duplicate_data, $key ), array( false ) );
			}
		} else {
			$key = $_REQUEST['tab'];
			if ( array_search( $key, $orphan_array ) ) {
				$result = call_user_func_array( array( $orphan_data, $key ), array( false ) );
			} else if ( array_search( $key, $duplicate_array ) ) {
				$result = call_user_func_array( array( $duplicate_data, $key ), array( false ) );
			}
		}

		return $result;
	}
}


if ( ! function_exists( 'lm_dbc_print_table' ) ) {
	function lm_dbc_print_table( $table ) {
		if ( ! empty( $table ) ) {
			$first = $table[0]; ?>
			<table class="lm_dbc_table wp-list-table widefat fixed striped">
				<thead>
				<tr>
					<?php foreach ( $first as $key => $val ) { ?>
						<th><?php echo $key; ?></th>
					<?php } ?>
				</tr>
				</thead>

				<tbody>
				<?php foreach ( $table as $row ) { ?>
				<tr>
					<?php foreach ( $row as $key => $val ) { ?>
						<td><?php echo esc_attr( $val ); ?></td>
					<?php }
					echo '</tr>';
					} ?>
				</tbody>
			</table>
		<?php
		}
	}
}
