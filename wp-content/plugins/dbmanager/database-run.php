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
|	- Database Run Query															|
|	- wp-content/plugins/dbmanager/database-run.php					|
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
	// Decide What To Do
	switch($_POST['do']) {
		case __('Run', 'wp-dbmanager'):
			$sql_queries2 = trim($_POST['sql_query']);
			$totalquerycount = 0;
			$successquery = 0;
			if($sql_queries2) {
				$sql_queries = array();
				$sql_queries2 = explode("\n", $sql_queries2);
				foreach($sql_queries2 as $sql_query2) {
					$sql_query2 = trim(stripslashes($sql_query2));
					$sql_query2 = preg_replace("/[\r\n]+/", '', $sql_query2);
					if(!empty($sql_query2)) {
						$sql_queries[] = $sql_query2;
					}
				}
				if($sql_queries) {
					foreach($sql_queries as $sql_query) {			
						if (preg_match("/^\\s*(insert|update|replace|delete|create|alter) /i",$sql_query)) {
							$run_query = $wpdb->query($sql_query);
							if(!$run_query) {
								$text .= "<font color=\"red\">$sql_query</font><br />";
							} else {
								$successquery++;
								$text .= "<font color=\"green\">$sql_query</font><br />";
							}
							$totalquerycount++;
						} elseif (preg_match("/^\\s*(select|drop|show|grant) /i",$sql_query)) {
							$text .= "<font color=\"red\">$sql_query</font><br />";
							$totalquerycount++;						
						}
					}
					$text .= "<font color=\"blue\">$successquery/$totalquerycount ".__('Query(s) Executed Successfully', 'wp-dbmanager').'</font>';
				} else {
					$text = '<font color="red">'.__('Empty Query', 'wp-dbmanager').'</font>';
				}
			} else {
				$text = '<font color="red">'.__('Empty Query', 'wp-dbmanager').'</font>';
			}
			break;
	}
}
?>
<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$text.'</p></div>'; } ?>
<!-- Run SQL Query -->
<div class="wrap">
	<h2><?php _e('Run SQL Query', 'wp-dbmanager'); ?></h2>
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
		<p>
			<strong><?php _e('Seperate Multiple Queries With A New Line', 'wp-dbmanager'); ?></strong><br />
			<font color="green"><?php _e('Use Only INSERT, UPDATE, REPLACE, DELETE, CREATE and ALTER statements.', 'wp-dbmanager'); ?></font>
		</p>
		<p align="center"><textarea cols="120" rows="30" name="sql_query"></textarea></p>
		<p align="center"><input type="submit" name="do" value="<?php _e('Run', 'wp-dbmanager'); ?>" class="button" />&nbsp;&nbsp;<input type="button" name="cancel" value="<?php _e('Cancel', 'wp-dbmanager'); ?>" class="button" onclick="javascript:history.go(-1)" /></p>
		<p>
			<?php _e('1. CREATE statement will return an error, which is perfectly normal due to the database class. To confirm that your table has been created check the Manage Database page.', 'wp-dbmanager'); ?><br />
			<?php _e('2. UPDATE statement may return an error sometimes due to the newly updated value being the same as the previous value.', 'wp-dbmanager'); ?><br />
			<?php _e('3. ALTER statement will return an error because there is no value returned.', 'wp-dbmanager'); ?></p>
	</form>
</div>