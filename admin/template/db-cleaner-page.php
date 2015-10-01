<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Db_Cleaner
 * @subpackage Db_Cleaner/admin/template
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class='wrap'>

	<h2 class="nav-tab-wrapper">
		<a href="<?php echo esc_url( admin_url( 'tools.php?page=db-clean' ) ); ?>"
		   class="nav-tab <?php echo ( ! isset( $_REQUEST['subpage'] ) ) ? 'nav-tab-active' : ''; ?>">Find/Remove Orphan
			Data</a>
		<a href="<?php echo esc_url( admin_url( 'tools.php?page=db-clean&subpage=2' ) ); ?>"
		   class="nav-tab <?php echo isset( $_REQUEST['subpage'] ) ? 'nav-tab-active' : ''; ?>">Find/Remove Duplicate
			Data</a>
	</h2>

	<?php
	if ( ! isset( $_REQUEST['subpage'] ) ) { ?>
		<h1>Orhpan tab</h1>
		<?php
	} else { ?>
		<h1>Duplicate tab</h1>
		<?php
	}
	?>
</div>
