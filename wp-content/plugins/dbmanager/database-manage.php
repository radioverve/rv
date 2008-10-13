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
|	- Database Restore																|
|	- wp-content/plugins/dbmanager/database-restore.php				|
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
	$database_file = trim($_POST['database_file']);
	$nice_file_date = gmdate(sprintf(__('%s @ %s', 'wp-dbmanager'), get_option('date_format'), get_option('time_format')), substr($database_file, 0, 10));

	// Decide What To Do
	switch($_POST['do']) {
		case __('Restore', 'wp-dbmanager'):
			if(!empty($database_file)) {
				if(stristr($database_file, '.gz')) {
					$backup['command'] = 'gunzip < '.$backup['path'].'/'.$database_file.' | '.$backup['mysqlpath'].' --host="'.DB_HOST.'" --user="'.DB_USER.'" --password="'.DB_PASSWORD.'" '.DB_NAME;
				} else {
					$backup['command'] = $backup['mysqlpath'].' --host="'.DB_HOST.'" --user="'.DB_USER.'" --password="'.DB_PASSWORD.'" '.DB_NAME.' < '.$backup['path'].'/'.$database_file;
				}
				passthru($backup['command'], $error);
				if($error) {
					$text = '<font color="red">'.sprintf(__('Database On \'%s\' Failed To Restore', 'wp-dbmanager'), $nice_file_date).'</font>';
				} else {
					$text = '<font color="green">'.sprintf(__('Database On \'%s\' Restored Successfully', 'wp-dbmanager'), $nice_file_date).'</font>';
				}
			} else {
				$text = '<font color="red">'.__('No Backup Database File Selected', 'wp-dbmanager').'</font>';
			}
			break;
		case __('E-Mail', 'wp-dbmanager'):
			if(!empty($database_file)) {
				// Get And Read The Database Backup File
				$file_path = $backup['path'].'/'.$database_file;
				$file_size = format_size(filesize($file_path));
				$file_date = gmdate(sprintf(__('%s @ %s', 'wp-dbmanager'), get_option('date_format'), get_option('time_format')), substr($database_file, 0, 10));
				$file = fopen($file_path,'rb');
				$file_data = fread($file,filesize($file_path));
				fclose($file);
				$file_data = chunk_split(base64_encode($file_data));
				// Create Mail To, Mail Subject And Mail Header
				if(!empty($_POST['email_to'])) {
					$mail_to = trim($_POST['email_to']);
				} else {
					$mail_to = get_option('admin_email');
				}
				$mail_subject = sprintf(__('%s Database Backup File For %s', 'wp-dbmanager'), get_bloginfo('name'), $file_date);
				$mail_header = 'From: '.get_bloginfo('name').' Administrator <'.get_option('admin_email').'>';
				// MIME Boundary
				$random_time = md5(time());
				$mime_boundary = "==WP-DBManager- $random_time";
				// Create Mail Header And Mail Message
				$mail_header .= "\nMIME-Version: 1.0\n" .
										"Content-Type: multipart/mixed;\n" .
										" boundary=\"{$mime_boundary}\"";
				$mail_message = __('Website Name:', 'wp-dbmanager').' '.get_bloginfo('name')."\n".
										__('Website URL:', 'wp-dbmanager').' '.get_bloginfo('siteurl')."\n".
										__('Backup File Name:', 'wp-dbmanager').' '.$database_file."\n".
										__('Backup File Date:', 'wp-dbmanager').' '.$file_date."\n".
										__('Backup File Size:', 'wp-dbmanager').' '.$file_size."\n\n".
										__('With Regards,', 'wp-dbmanager')."\n".
										get_bloginfo('name').' '. __('Administrator', 'wp-dbmanager')."\n".
										get_bloginfo('siteurl');
				$mail_message = "This is a multi-part message in MIME format.\n\n" .
										"--{$mime_boundary}\n" .
										"Content-Type: text/plain; charset=\"utf-8\"\n" .
										"Content-Transfer-Encoding: 7bit\n\n".$mail_message."\n\n";				
				$mail_message .= "--{$mime_boundary}\n" .
										"Content-Type: application/octet-stream;\n" .
										" name=\"$database_file\"\n" .
										"Content-Disposition: attachment;\n" .
										" filename=\"$database_file\"\n" .
										"Content-Transfer-Encoding: base64\n\n" .
										$file_data."\n\n--{$mime_boundary}--\n";
				if(mail($mail_to, $mail_subject, $mail_message, $mail_header)) {
					$text .= '<font color="green">'.sprintf(__('Database Backup File For \'%s\' Successfully E-Mailed To \'%s\'', 'wp-dbmanager'), $file_date, $mail_to).'</font><br />';
				} else {
					$text = '<font color="red">'.sprintf(__('Unable To E-Mail Database Backup File For \'%s\' To \'%s\'', 'wp-dbmanager'), $file_date, $mail_to).'</font>';
				}
			} else {
				$text = '<font color="red">'.__('No Backup Database File Selected', 'wp-dbmanager').'</font>';
			}
			break;
		case __('Download', 'wp-dbmanager'):
			if(empty($database_file)) {
				$text = '<font color="red">'.__('No Backup Database File Selected', 'wp-dbmanager').'</font>';
			}
			break;
		case __('Delete', 'wp-dbmanager'):
			if(!empty($database_file)) {
				$nice_file_date = gmdate(sprintf(__('%s @ %s', 'wp-dbmanager'), get_option('date_format'), get_option('time_format')), substr($database_file, 0, 10));
				if(is_file($backup['path'].'/'.$database_file)) {
					if(!unlink($backup['path'].'/'.$database_file)) {
						$text .= '<font color="red">'.sprintf(__('Unable To Delete Database Backup File On \'%s\'', 'wp-dbmanager'), $nice_file_date).'</font><br />';
					} else {
						$text .= '<font color="green">'.sprintf(__('Database Backup File On \'%s\' Deleted Successfully', 'wp-dbmanager'), $nice_file_date).'</font><br />';
					}
				} else {
					$text = '<font color="red">'.sprintf(__('Invalid Database Backup File On \'%s\'', 'wp-dbmanager'), $nice_file_date).'</font>';
				}
			} else {
				$text = '<font color="red">'.__('No Backup Database File Selected', 'wp-dbmanager').'</font>';
			}
			break;
	}
}
?>
<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$text.'</p></div>'; } ?>
<!-- Manage Backup Database -->
<div class="wrap">
	<h2><?php _e('Manage Backup Database', 'wp-dbmanager'); ?></h2>
	<p><?php _e('Choose A Backup Date To E-Mail, Restore, Download Or Delete', 'wp-dbmanager'); ?></p>
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
		<table width="100%" cellspacing="3" cellpadding="3" border="0">
			<tr class="thead">
				<th align="left"><?php _e('No.', 'wp-dbmanager'); ?></th>
				<th align="left"><?php _e('Database File', 'wp-dbmanager'); ?></th>
				<th align="left"><?php _e('Date/Time', 'wp-dbmanager'); ?></th>
				<th align="left"><?php _e('Size', 'wp-dbmanager'); ?></th>
				<th align="left"><?php _e('Select', 'wp-dbmanager'); ?></th>
			</tr>
			<?php
				if(!is_emtpy_folder($backup['path'])) {
					if ($handle = opendir($backup['path'])) {
						$database_files = array();
						while (false !== ($file = readdir($handle))) { 
							if ($file != '.' && $file != '..' && (file_ext($file) == 'sql' || file_ext($file) == 'gz')) {
								$database_files[] = $file;
							} 
						}
						closedir($handle);
						sort($database_files);
						for($i = (sizeof($database_files)-1); $i > -1; $i--) {
							if($no%2 == 0) {
								$style = 'style=\'background: none\'';								
							} else {
								$style = 'style=\'background-color: #eee\'';
							}
							$no++;
							$database_text = substr($database_files[$i], 13);
							$date_text = gmdate(sprintf(__('%s @ %s', 'wp-dbmanager'), get_option('date_format'), get_option('time_format')), substr($database_files[$i], 0, 10));
							$size_text = filesize($backup['path'].'/'.$database_files[$i]);
							echo "<tr $style>\n<td>$no</td>";
							echo "<td>$database_text</td>";
							echo "<td>$date_text</td>";
							echo '<td>'.format_size($size_text).'</td>';
							echo "<td><input type=\"radio\" name=\"database_file\" value=\"$database_files[$i]\" /></td>\n</tr>\n";
							$totalsize += $size_text;
						}
					} else {
						echo '<tr><td align="center" colspan="5">'.__('There Are No Database Backup Files Available.', 'wp-dbmanager').'</td></tr>';
					}
				} else {
					echo '<tr><td align="center" colspan="5">'.__('There Are No Database Backup Files Available.', 'wp-dbmanager').'</td></tr>';
				}
			?>
			<tr class="thead">
				<th align="left" colspan="3"><?php echo $no; ?> <?php _e('Backup File(s)', 'wp-dbmanager'); ?></th>
				<th align="left"><?php echo format_size($totalsize); ?></th>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="5"><?php _e('E-mail database backup file to:', 'wp-dbmanager'); ?> <input type="text" name="email_to" size="30" maxlength="50" value="<?php echo get_option('admin_email'); ?>" />&nbsp;&nbsp;<input type="submit" name="do" value="<?php _e('E-Mail', 'wp-dbmanager'); ?>" class="button" /></td>
			</tr>
			<tr>
				<td colspan="5" align="center">
					<input type="submit" name="do" value="<?php _e('Download', 'wp-dbmanager'); ?>" class="button" />&nbsp;&nbsp;
					<input type="submit" name="do" value="<?php _e('Restore', 'wp-dbmanager'); ?>" onclick="return confirm('<?php _e('You Are About To Restore A Database.\nThis Action Is Not Reversible.\nAny Data Inserted After The Backup Date Will Be Gone.\n\n Choose [Cancel] to stop, [Ok] to restore.', 'wp-dbmanager'); ?>')" class="button" />&nbsp;&nbsp;
					<input type="submit" class="button" name="do" value="<?php _e('Delete', 'wp-dbmanager'); ?>" onclick="return confirm('<?php _e('You Are About To Delete The Selected Database Backup Files.\nThis Action Is Not Reversible.\n\n Choose [Cancel] to stop, [Ok] to delete.', 'wp-dbmanager'); ?>')" />&nbsp;&nbsp;
					<input type="button" name="cancel" value="<?php _e('Cancel', 'wp-dbmanager'); ?>" class="button" onclick="javascript:history.go(-1)" /></td>
			</tr>					
		</table>
	</form>
</div>