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
|	- Database Optimize																|
|	- wp-content/plugins/dbmanager/database-optimize.php				|
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

### Form Processing 
if($_POST['do']) {
	// Lets Prepare The Variables
	$optimize = $_POST['optimize'];

	// Decide What To Do
	switch($_POST['do']) {
		case 'Optimize':
			if(!empty($optimize)) {
				foreach($optimize as $key => $value) {
					if($value == 'yes') {
						$tables_string .=  ', '.$key;
					}
				}
			} else {
				$text = '<font color="red">'.__('No Tables Selected', 'wp-dbmanager').'</font>';
			}
			$selected_tables = substr($tables_string, 2);
			if(!empty($selected_tables)) {
				$optimize2 = $wpdb->query("OPTIMIZE TABLE $selected_tables");
				if(!$optimize2) {
					$text = '<font color="red">'.sprintf(__('Table(s) \'%s\' NOT Optimized', 'wp-dbmanager'), $selected_tables).'</font>';
				} else {
					$text = '<font color="green">'.sprintf(__('Table(s) \'%s\' Optimized', 'wp-dbmanager'), $selected_tables).'</font>';
				}
			}
			break;
	}
}


### Show Tables
$tables = $wpdb->get_col("SHOW TABLES");
?>
<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$text.'</p></div>'; } ?>
<!-- Optimize Database -->
<div class="wrap">
	<h2><?php _e('Optimize Database', 'wp-dbmanager'); ?></h2>
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
		<table width="100%" cellspacing="3" cellpadding="3" border="0">
			<tr class="thead">
				<th align="left"><?php _e('Tables', 'wp-dbmanager'); ?></th>
				<th align="left"><?php _e('Options', 'wp-dbmanager'); ?></th>
			</tr>
				<?php
					foreach($tables as $table_name) {
						if($no%2 == 0) {
							$style = 'style=\'background: none\'';							
						} else {
							$style = 'style=\'background-color: #eee;\'';
						}
						$no++;
						echo "<tr $style><th align=\"left\" scope=\"row\">$table_name</th>\n";
						echo "<td><input type=\"radio\" name=\"optimize[$table_name]\" value=\"no\" />".__('No', 'wp-dbmanager')."&nbsp;&nbsp;&nbsp;<input type=\"radio\" name=\"optimize[$table_name]\" value=\"yes\" checked=\"checked\" />".__('Yes', 'wp-dbmanager').'</td></tr>';
					}
				?>
			<tr>
				<td colspan="2" align="center"><?php _e('Database should be optimize once every month.', 'wp-dbmanager'); ?></td>
			</tr>
			<tr>
				<td colspan="2" align="center"><input type="submit" name="do" value="<?php _e('Optimize', 'wp-dbmanager'); ?>" class="button" />&nbsp;&nbsp;<input type="button" name="cancel" value="<?php _e('Cancel', 'wp-dbmanager'); ?>" class="button" onclick="javascript:history.go(-1)" /></td>
			</tr>
		</table>
	</form>
</div>