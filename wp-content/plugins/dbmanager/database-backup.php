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
|	- Database Backup																|
|	- wp-content/plugins/dbmanager/database-backup.php				|
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
$current_date = gmdate(sprintf(__('%s @ %s', 'wp-dbmanager'), get_option('date_format'), get_option('time_format')), (time() + (get_option('gmt_offset') * 3600)));
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
		case __('Backup', 'wp-dbmanager'):
			$gzip = intval($_POST['gzip']);
			if($gzip == 1) {
				$backup['filename'] = $backup['date'].'_-_'.DB_NAME.'.sql.gz';
				$backup['filepath'] = $backup['path'].'/'.$backup['filename'];
				$backup['command'] = $backup['mysqldumppath'].' --host="'.DB_HOST.'" --user="'.DB_USER.'" --password="'.DB_PASSWORD.'" --add-drop-table --skip-lock-tables '.DB_NAME.' | gzip > '.$backup['filepath'];
			} else {
				$backup['filename'] = $backup['date'].'_-_'.DB_NAME.'.sql';
				$backup['filepath'] = $backup['path'].'/'.$backup['filename'];
				$backup['command'] = $backup['mysqldumppath'].' --host="'.DB_HOST.'" --user="'.DB_USER.'" --password="'.DB_PASSWORD.'" --add-drop-table --skip-lock-tables '.DB_NAME.' > '.$backup['filepath'];
			}
			check_backup_files();
			passthru($backup['command'], $error);
			if(!is_writable($backup['path'])) {
				$text = '<font color="red">'.sprintf(__('Database Failed To Backup On \'%s\'. Backup Folder Not Writable.', 'wp-dbmanager'), $current_date).'</font>';
			} elseif(filesize($backup['filepath']) == 0) {
				unlink($backup['filepath']);
				$text = '<font color="red">'.sprintf(__('Database Failed To Backup On \'%s\'. Backup File Size Is 0KB.', 'wp-dbmanager'), $current_date).'</font>';
			} elseif(!is_file($backup['filepath'])) {
				$text = '<font color="red">'.sprintf(__('Database Failed To Backup On \'%s\'. Invalid Backup File Path.', 'wp-dbmanager'), $current_date).'</font>';
			} elseif($error) {
				$text = '<font color="red">'.sprintf(__('Database Failed To Backup On \'%s\'.', 'wp-dbmanager'), $current_date).'</font>';
			} else {
				$text = '<font color="green">'.sprintf(__('Database Backed Up Successfully On \'%s\'.', 'wp-dbmanager'), $current_date).'</font>';
			}
			break;
	}
}


### Backup File Name
$backup['filename'] = $backup['date'].'_-_'.DB_NAME.'.sql';


### MYSQL Base Dir
$status_count = 0;
$stats_function_disabled = 0;
?>
<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$text.'</p></div>'; } ?>
<!-- Checking Backup Status -->
<div class="wrap">
	<h2><?php _e('Checking Backup Status', 'wp-dbmanager'); ?></h2>
	<p>
		<?php _e('Checking Backup Folder', 'wp-dbmanager'); ?> (<strong><?php echo stripslashes($backup['path']); ?></strong>) ...<br />
		<?php
			if(is_dir(stripslashes($backup['path']))) {
				echo '<font color="green">'.__('Backup folder exists', 'wp-dbmanager').'</font><br />';
				$status_count++;
			} else {
				echo '<font color="red">'.__('Backup folder does NOT exist. Please create \'backup-db\' folder in \'wp-content\' folder and CHMOD it to \'777\' or change the location of the backup folder under DB Option.', 'wp-dbmanager').'</font><br />';
			}
			if(is_writable(stripslashes($backup['path']))) {
				echo '<font color="green">'.__('Backup folder is writable', 'wp-dbmanager').'</font>';
				$status_count++;
			} else {
				echo '<font color="red">'.__('Backup folder is NOT writable. Please CHMOD it to \'777\'.', 'wp-dbmanager').'</font>';
			}
		?>
	</p>
	<p>		
		<?php			
			if(file_exists(stripslashes($backup['mysqldumppath']))) {
				echo __('Checking MYSQL Dump Path', 'wp-dbmanager').' (<strong>'.stripslashes($backup['mysqldumppath']).'</strong>) ...<br />';
				echo '<font color="green">'.__('MYSQL dump path exists.', 'wp-dbmanager').'</font>';
				$status_count++;
			} else {
				echo __('Checking MYSQL Dump Path', 'wp-dbmanager').' ...<br />';
				echo '<font color="red">'.__('MYSQL dump path does NOT exist. Please check your mysqldump path under DB Options. If uncertain, contact your server administrator.', 'wp-dbmanager').'</font>';
			}
		?>
	</p>
	<p>
		<?php
			if(file_exists(stripslashes($backup['mysqlpath']))) {
				echo __('Checking MYSQL Path', 'wp-dbmanager').' (<strong>'.stripslashes($backup['mysqlpath']).'</strong>) ...<br />';
				echo '<font color="green">'.__('MYSQL path exists.', 'wp-dbmanager').'</font>';
				$status_count++;
			} else {
				echo __('Checking MYSQL Path', 'wp-dbmanager').' ...<br />';
				echo '<font color="red">'.__('MYSQL path does NOT exist. Please check your mysql path under DB Options. If uncertain, contact your server administrator.', 'wp-dbmanager').'</font>';
			}
		?>
	</p>
	<p>
		<?php _e('Checking PHP Functions', 'wp-dbmanager'); ?> (<strong>passthru()</strong>, <strong>system()</strong> <?php _e('and', 'wp-dbmanager'); ?> <strong>exec()</strong>) ...<br />
		<?php
			if(function_exists('passthru')) {
				echo '<font color="green">passthru() '.__('enabled', 'wp-dbmanager').'.</font><br />';
				$status_count++;
			} else {
				echo '<font color="red">passthru() '.__('disabled', 'wp-dbmanager').'.</font><br />';
				$stats_function_disabled++;
			}
			if(function_exists('system')) {
				echo '<font color="green">system() '.__('enabled', 'wp-dbmanager').'.</font><br />';
			} else {
				echo '<font color="red">system() '.__('disabled', 'wp-dbmanager').'.</font><br />';
				$stats_function_disabled++;
			}
			if(function_exists('exec')) {
				echo '<font color="green">exec() '.__('enabled', 'wp-dbmanager').'.</font>';
			} else {
				echo '<font color="red">exec() '.__('disabled', 'wp-dbmanager').'.</font>';
				$stats_function_disabled++;
			}
		?>	
	</p>
	<p>
		<?php
			if($status_count == 5) {
				echo '<strong><font color="green">'.__('Excellent. You Are Good To Go.', 'wp-dbmanager').'</font></strong>';
			} else if($stats_function_disabled == 3) {
				echo '<strong><font color="red">'.__('I\'m sorry, your server administrator has disabled passthru(), system() and exec(), thus you cannot use this backup script. You may consider using the default WordPress database backup script instead.', 'wp-dbmanager').'</font></strong>';
			} else {
				echo '<strong><font color="red">'.__('Please Rectify The Error Highlighted In Red Before Proceeding On.', 'wp-dbmanager').'</font></strong>';
			}
		?>
	</p>
	<p><i><?php _e('Note: The checking of backup status is still undergoing testing, it may not be accurate.', 'wp-dbmanager'); ?></i></p>
</div>
<!-- Backup Database -->
<div class="wrap">
	<h2><?php _e('Backup Database', 'wp-dbmanager'); ?></h2>
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
	<table width="100%" cellspacing="3" cellpadding="3" border="0">
		<tr>
			<th align="left" scope="row"><?php _e('Database Name:', 'wp-dbmanager'); ?></th>
			<td><?php echo DB_NAME; ?></td>
		</tr>
		<tr style="background-color: #eee;">
			<th align="left" scope="row"><?php _e('Database Backup To:', 'wp-dbmanager'); ?></th>
			<td><?php echo stripslashes($backup['path']); ?></td>
		</tr>
		<tr>
			<th align="left" scope="row"><?php _e('Database Backup Date:', 'wp-dbmanager'); ?></th>
			<td><?php echo gmdate(sprintf(__('%s @ %s', 'wp-dbmanager'), get_option('date_format'), get_option('time_format')), $backup['date']); ?></td>
		</tr>
		<tr style="background-color: #eee;">
			<th align="left" scope="row"><?php _e('Database Backup File Name:', 'wp-dbmanager'); ?></th>
			<td><?php echo $backup['filename']; ?></td>
		</tr>
		<tr>
			<th align="left" scope="row"><?php _e('Database Backup Type:', 'wp-dbmanager'); ?></th>
			<td><?php _e('Full (Structure and Data)', 'wp-dbmanager'); ?></td>
		</tr>
		<tr style="background-color: #eee;">
			<th align="left" scope="row"><?php _e('MYSQL Dump Location:', 'wp-dbmanager'); ?></th>
			<td><?php echo stripslashes($backup['mysqldumppath']); ?></td>
		</tr>
		<tr>
			<th align="left" scope="row"><?php _e('GZIP Database Backup File?', 'wp-dbmanager'); ?></th>
			<td><input type="radio" name="gzip" value="1" /><?php _e('Yes', 'wp-dbmanager'); ?>&nbsp;&nbsp;<input type="radio" name="gzip" value="0" checked="checked" /><?php _e('No', 'wp-dbmanager'); ?></td>
		</tr>
		<tr>
			<td colspan="2" align="center"><input type="submit" name="do" value="<?php _e('Backup', 'wp-dbmanager'); ?>" class="button" />&nbsp;&nbsp;<input type="button" name="cancel" value="<?php _e('Cancel', 'wp-dbmanager'); ?>" class="button" onclick="javascript:history.go(-1)" /></td>
		</tr>
	</table>
	</form>
</div>