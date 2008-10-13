<?php
/*
+----------------------------------------------------------------+
|																							|
|	WordPress 2.1 Plugin: WP-DBManager 2.20								|
|	Copyright (c) 2007 Lester "GaMerZ" Chan									|
|																							|
|	File Written By:																	|
|	- Lester "GaMerZ" Chan															|
|	- http://lesterchan.net															|
|																							|
|	File Information:																	|
|	- Database Empty																|
|	- wp-content/plugins/dbmanager/database-empty.php				|
|																							|
+----------------------------------------------------------------+
*/


### Check Whether User Can Manage Database
if(!current_user_can('manage_database')) {
	die('Access Denied');
}


### Variables Variables Variables
$base_name = plugin_basename('dbmanager/database-manager.php');
$base_page = 'admin.php?page='.$base_name;
$backup = array();
$backup_options = get_option('dbmanager_options');
$backup['date'] = current_time('timestamp');
$backup['mysqldumppath'] = $backup_options['mysqldumppath'];
$backup['mysqlpath'] = $backup_options['mysqlpath'];
$backup['path'] = $backup_options['path'];


### Form Processing 
if($_POST['do']) {
	// Lets Prepare The Variables
	$emptydrop = $_POST['emptydrop'];

	// Decide What To Do
	switch($_POST['do']) {
		case __('Empty/Drop', 'wp-dbmanager'):
			$empty_tables = array();
			if(!empty($emptydrop)) {
				foreach($emptydrop as $key => $value) {
					if($value == 'empty') {
						$empty_tables[] = $key;
					} elseif($value == 'drop') {
						$drop_tables .=  ', '.$key;
					}
				}
			} else {
				$text = '<font color="red">'.__('No Tables Selected.', 'wp-dbmanager').'</font>';
			}
			$drop_tables = substr($drop_tables, 2);
			if(!empty($empty_tables)) {
				foreach($empty_tables as $empty_table) {
					$empty_query = $wpdb->query("TRUNCATE $empty_table");
					$text .= '<font color="green">'.sprintf(__('Table \'%s\' Emptied', 'wp-dbmanager'), $empty_table).'</font><br />';
				}
			}
			if(!empty($drop_tables)) {
				$drop_query = $wpdb->query("DROP TABLE $drop_tables");
				$text = '<font color="green">'.sprintf(__('Table(s) \'%s\' Dropped', 'wp-dbmanager'), $drop_tables).'</font>';
			}
			break;
	}
}


### Show Tables
$tables = $wpdb->get_col("SHOW TABLES");
?>
<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$text.'</p></div>'; } ?>
<!-- Empty/Drop Tables -->
<div class="wrap">
	<h2><?php _e('Empty/Drop Tables', 'wp-dbmanager'); ?></h2>
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
		<table width="100%" cellspacing="3" cellpadding="3" border="0">
			<tr class="thead">
			<th align="left"><?php _e('Tables', 'wp-dbmanager'); ?></th>
			<th align="left"><?php _e('Empty', 'wp-dbmanager'); ?></th>
			<th align="left"><?php _e('Drop', 'wp-dbmanager'); ?></th>
		</tr>
				<?php
					foreach($tables as $table_name) {
						if($no%2 == 0) {
							$style = 'style=\'background: none;\'';							
						} else {
							$style = 'style=\'background-color: #eee;\'';
						}
						$no++;
						echo "<tr $style><th align=\"left\" scope=\"row\">$table_name</th>\n";
						echo "<td><input type=\"radio\" name=\"emptydrop[$table_name]\" value=\"empty\" />&nbsp;".__('Empty', 'wp-dbmanager').'</td>';
						echo "<td><input type=\"radio\" name=\"emptydrop[$table_name]\" value=\"drop\" />&nbsp;".__('Drop', 'wp-dbmanager').'</td></tr>';
					}
				?>
			<tr>
				<td colspan="3">
					<?php _e('1. DROPPING a table means deleting the table. This action is not REVERSIBLE.', 'wp-dbmanager'); ?><br />
					<?php _e('2. EMPTYING a table means all the rows in the table will be deleted. This action is not REVERSIBLE.', 'wp-dbmanager'); ?></td>
			</tr>
			<tr>
				<td colspan="3" align="center"><input type="submit" name="do" value="<?php _e('Empty/Drop', 'wp-dbmanager'); ?>" class="button" onclick="return confirm('<?php _e('You Are About To Empty Or Drop The Selected Databases.\nThis Action Is Not Reversible.\n\n Choose [Cancel] to stop, [Ok] to delete.', 'wp-dbmanager'); ?>')" />&nbsp;&nbsp;<input type="button" name="cancel" value="<?php _e('Cancel', 'wp-dbmanager'); ?>" class="button" onclick="javascript:history.go(-1)" /></td>
			</tr>
		</table>
	</form>
</div>