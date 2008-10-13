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
|	- Database Manager																|
|	- wp-content/plugins/dbmanager/database-manager.php				|
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


### Get MYSQL Version
$sqlversion = $wpdb->get_var("SELECT VERSION() AS version");
?>
<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$text.'</p></div>'; } ?>
<!-- Database Information -->
<div class="wrap">
	<h2><?php _e('Database Information', 'wp-dbmanager'); ?></h2>
	<table width="100%" cellspacing="3" cellpadding="3" border="0">
		<tr class="thead">
			<th align="left"><?php _e('Setting', 'wp-dbmanager'); ?></th>
			<th align="left"><?php _e('Value', 'wp-dbmanager'); ?></th>
		</tr>
		<tr>
			<td><?php _e('Database Host', 'wp-dbmanager'); ?></td>
			<td><?php echo DB_HOST; ?></td>
		</tr>
		<tr style="background-color: #eee;">
			<td><?php _e('Database Name', 'wp-dbmanager'); ?></td>
			<td><?php echo DB_NAME; ?></td>
		</tr>	
		<tr>
			<td><?php _e('Database User', 'wp-dbmanager'); ?></td>
			<td><?php echo DB_USER; ?></td>
		</tr>
		<tr style="background-color: #eee;">
			<td><?php _e('Database Type', 'wp-dbmanager'); ?></td>
			<td>MYSQL</td>
		</tr>	
		<tr>
			<td><?php _e('Database Version', 'wp-dbmanager'); ?></td>
			<td>v<?php echo $sqlversion; ?></td>
		</tr>	
	</table>
</div>
<div class="wrap">
	<h2><?php _e('Tables Information', 'wp-dbmanager'); ?></h2>
	<table width="100%" cellspacing="3" cellpadding="3" border="0">
		<tr class="thead">
			<th align="left"><?php _e('No.', 'wp-dbmanager'); ?></th>
			<th align="left"><?php _e('Tables', 'wp-dbmanager'); ?></th>
			<th align="left"><?php _e('Records', 'wp-dbmanager'); ?></th>
			<th align="left"><?php _e('Data Usage', 'wp-dbmanager'); ?></th>
			<th align="left"><?php _e('Index Usage', 'wp-dbmanager'); ?></th>
			<th align="left"><?php _e('Overhead', 'wp-dbmanager'); ?></th>
		</tr>
<?php
// If MYSQL Version More Than 3.23, Get More Info
if($sqlversion >= '3.23') {
	$tablesstatus = $wpdb->get_results("SHOW TABLE STATUS");
	foreach($tablesstatus as  $tablestatus) {
		if($no%2 == 0) {
			$style = 'style=\'background: none;\'';
		} else {
			$style = 'style=\'background-color: #eee;\'';
		}
		$no++;
		echo "<tr $style>\n";
		echo "<td>$no</td>\n";
		echo "<td>$tablestatus->Name</td>\n";
		echo '<td>'.number_format($tablestatus->Rows).'</td>'."\n";
		echo '<td>'.format_size($tablestatus->Data_length).'</td>'."\n";
		echo '<td>'.format_size($tablestatus->Index_length).'</td>'."\n";;
		echo '<td>'.format_size($tablestatus->Data_free).'</td>'."\n";
		$row_usage += $tablestatus->Rows;
		$data_usage += $tablestatus->Data_length;
		$index_usage +=  $tablestatus->Index_length;
		$overhead_usage += $tablestatus->Data_free;
		echo '</tr>'."\n";
	}	
	echo '<tr class="thead">'."\n";
	echo '<th align="left">'.__('Total:', 'wp-dbmanager').'</th>'."\n";
	echo '<th align="left">'.$no.' '.__('Tables', 'wp-dbmanager').'</th>'."\n";
	echo '<th align="left">'.number_format($row_usage).'</th>'."\n";
	echo '<th align="left">'.format_size($data_usage).'</th>'."\n";
	echo '<th align="left">'.format_size($index_usage).'</th>'."\n";
	echo '<th align="left">'.format_size($overhead_usage).'</th>'."\n";
	echo '</tr>';
} else {
	echo '<tr><td colspan="6" align="center"><strong>'.__('Could Not Show Table Status Due To Your MYSQL Version Is Lower Than 3.23.', 'wp-dbmanager').'</strong></td></tr>';
}
?>
	</table>
</div>