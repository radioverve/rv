<?php
/*
Plugin Name: Band Creator
Description: Plugin to create custom fields for registered users. To create a music community with bands, artists etc.
Version: 1.0
Author: Ganesh Rao, Reverie Studio
Author URI: http://www.freewebs.com/gnrao

License:

    Copyright 2007 Ganesh Rao (email: rao.art@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/
require_once(ABSPATH."upload_class.php");
	
function gnrb_end_slash($path){
	if( strrpos($path, '/') != strlen($path) - 1 ) return ($path . '/');
	return $path;
}

function gnrb_message($message) {
	global $success;
	if( $success ) echo '<div id="message" class="updated fade"><p><strong>' . $message . ' operation success.</strong></p></div>';
	else echo '<div id="message" class="updated fade"><p><strong style="color:red">' . $message . ' operation failed.</strong></p></div>';
	$success = false;
}

$success = false;
$is_admin = false;
$gnrb_plugin_folder = "bandcreator";
$gnrb_abspath = ABSPATH . "wp-content/plugins/$gnrb_plugin_folder/";
$gnrb_path = gnrb_end_slash(get_settings('siteurl')) . "wp-content/plugins/$gnrb_plugin_folder/";
$gnrb_upload_path = gnrb_end_slash(gnrb_fix_windows_path(ABSPATH . "/band_pics/"));
$gnrb_upload_path_virtual = gnrb_end_slash(get_settings("siteurl"))."band_pics/";
$gnrb_admin_user_level= 8;
$gnrb_user_level= 4;
$gnrb_table_meta = $wpdb->prefix.'gnrb_meta';
$gnrb_table_artist = $wpdb->prefix.'gnrb_artist';
$gnrb_table_fields = $wpdb->prefix.'gnrb_fields';
$gnrb_table_bandmembers = $wpdb->prefix.'gnrb_bandmembers';
$gnrb_max_filesize = 100; //in kB

// Option defaults
add_option("gnrb_allow_edit", true, "Allow registered users to edit their band profiles", true);
add_option("gnrb_bandmember_role", "Vocals, Lead Guitar, Rhythm Guitar, Guitars, Bass, Drums, Synth, Piano, Violin, Flute, Percussion, Backing Vocals, Band Manager, Music Mixing, Recording, DJ, Composer", "Choose the band member's role", true);
add_option(
"gnrb_band_bio_format", 
'echo "<h4>$band_name</h4>";
echo "<img src=\"$band_image\" />";
echo "$band_members_list";
echo "<p>$band_bio</p>";
echo "<h5>Contact</h5><ul>";
if($band_email) echo "<li>Email <b>$band_email</b></li>";
if($band_url) echo "<li>Website <b>$band_url</b></li>"; 
echo "</ul>";', 
"HTML Formatting for band bio page", 
true);
add_option("gnrb_band_pics_path", $gnrb_upload_path_virtual, "", true);
add_option("gnrb_allowed_tags", '<p><a><b><i><ul><li>', "", true);

function gnrb_update_options() {
	global $success;
	$gnrb_allow_edit = ($_POST["gnrb_allow_edit"] == "true");
	$gnrb_bandmember_role = $_POST["gnrb_bandmember_role"];
	$gnrb_band_bio_format = $_POST["gnrb_band_bio_format"];
	$gnrb_allowed_tags = $_POST["gnrb_allowed_tags"];
	update_option("gnrb_allow_edit", $gnrb_allow_edit);
	update_option("gnrb_bandmember_role", $gnrb_bandmember_role);
	update_option("gnrb_band_bio_format", $gnrb_band_bio_format);
	update_option("gnrb_allowed_tags", $gnrb_allowed_tags);
	$success = true;
}

function gnrb_install() {
	global $wpdb, $user_level, $gnrb_table_meta, $gnrb_table_fields, $gnrb_table_bandmembers, $gnrb_admin_user_level, $gnrb_table_artist;
	get_currentuserinfo();
	if ($user_level < $gnrb_admin_user_level) {
		echo "Sorry! You don't have the rights to perform these operations!";
		return;
	}
	// Band data fields
	$sql1 = "CREATE TABLE $gnrb_table_fields (
				id bigint(5) NOT NULL auto_increment,
				field_name varchar(128) NOT NULL default '',
				field_key varchar(128) NOT NULL default '',
				field_order int(2) NOT NULL default 0,
				field_descr varchar(255) default '',
				field_editable tinyint(1) NOT NULL default '0',
				field_type varchar(128) default 'text',
				field_type_data longtext default '',
				field_default longtext default '',
				PRIMARY  KEY  (id)
			);";
	// Band meta table
	$sql2 = "CREATE TABLE $gnrb_table_meta (
				id bigint(5) NOT NULL auto_increment,
				user_id bigint(20) NOT NULL,
				field_id bigint(5) NOT NULL,
				field_value longtext default '',
				PRIMARY  KEY  (id)
			);";
	// Band members table
	$sql3 = "CREATE TABLE $gnrb_table_bandmembers (
				id bigint(5) NOT NULL auto_increment,
				user_id bigint(20) NOT NULL,
				member_name varchar(255) NOT NULL default '',
				member_type varchar(255) NOT NULL default '',
				member_descr longtext default '',
				PRIMARY  KEY  (id)
			);";
	$sql4 = "CREATE TABLE $gnrb_table_artist (
				id bigint(5) NOT NULL auto_increment,
				user_id bigint(20) NOT NULL,
				artistid bigint(20) NOT NULL,
				PRIMARY  KEY  (id)
			);";
	$first_run = ($wpdb->get_var("SHOW TABLES LIKE '$gnrb_table_fields'") != $gnrb_table_fields);
	$first_run = $first_run || ($wpdb->get_var("SHOW TABLES LIKE '$gnrb_table_meta'") != $gnrb_table_meta);
	$first_run = $first_run || ($wpdb->get_var("SHOW TABLES LIKE '$gnrb_table_bandmembers'") != $gnrb_table_bandmembers);
	$first_run = $first_run || ($wpdb->get_var("SHOW TABLES LIKE '$gnrb_table_artist'") != $gnrb_table_artist);
	if( $first_run ) {
		// Create tables
		require_once(ABSPATH.'wp-admin/upgrade-functions.php');
		dbDelta($sql1);
		dbDelta($sql2);
		dbDelta($sql3);
		dbDelta($sql4);
		$field_list = "field_key, field_name, field_order, field_descr, field_editable, field_type, field_type_data, field_default";
		$order = 1;
		// Create first field entries
		$insert = "INSERT INTO $gnrb_table_fields($field_list) VALUES( 'band_name', 'Band Name', $order, 'The name of your band', 1, 'text', '50', '');";
		$results = $wpdb->query($insert);
		
		$order++;
		$insert = "INSERT INTO $gnrb_table_fields($field_list) VALUES( 'band_bio', 'Band Bio', $order, 'A brief Bio of your band', 1, 'textarea', '50,2', '');";
		$results = $wpdb->query($insert);
		
		$order++;
		$insert = "INSERT INTO $gnrb_table_fields($field_list) VALUES( 'band_url', 'Website', $order, 'The website of your band', 1, 'text', '50', 'http://');";
		$results = $wpdb->query($insert);
		
		$order++;
		$insert = "INSERT INTO $gnrb_table_fields($field_list) VALUES( 'band_email', 'Email', $order, 'Band contact e-mail', 1, 'text', '50', '');";
		$results = $wpdb->query($insert);
		
		$order++;
		$insert = "INSERT INTO $gnrb_table_fields($field_list) VALUES( 'band_image', 'Band Image', $order, 'Picture of your band (500x300 pixels)', 1, 'file', '.jpg,.gif,.png', '');";
		$results = $wpdb->query($insert);
		
		$order++;
		$insert = "INSERT INTO $gnrb_table_fields($field_list) VALUES( 'band_icon', 'Band Icon', $order, 'Icon of your band (50x50 pixels)', 1, 'file', '.jpg,.gif,.png', '');";
		$results = $wpdb->query($insert);
	}
}

function gnrb_admin() {
	global $user_level, $gnrb_admin_user_level, $gnrb_user_level;
	get_currentuserinfo();
	if ($user_level < $gnrb_user_level) { echo "No access for you!"; return; }
	elseif($user_level >= $gnrb_admin_user_level) { gnrb_super_admin(); }
	else { gnrb_band_admin(); }
}

function gnrb_band_admin() {
	global $gnrb_user_level, $gnrb_plugin_folder, $is_admin;
	$is_admin = false;
	if(isset($_POST["gnrb_edit_band"])){
		// Edit band
		gnrb_edit_band();
	}
	if(isset($_POST["gnrb_member_add"])){
		// Add member
		gnrb_member_edit(true);
	}
	if(isset($_POST["gnrb_member_edit"])){
		// Edit member
		gnrb_member_edit(false);
	}
	if(isset($_POST["gnrb_member_delete"])){
		// Delete member
		gnrb_member_delete();
	}
	if (function_exists('add_menu_page') && get_option("gnrb_allow_edit")) {
		// Add band profile page
		add_menu_page('Band Profile', 'Band Profile', $gnrb_user_level, $gnrb_plugin_folder, 'gnrb_profile_page');
	}
}

function gnrb_super_admin() {
	global $gnrb_admin_user_level, $gnrb_plugin_folder, $is_admin;
	$is_admin = true;
	if( isset($_POST["gnrb_update_options"]) ) {
		// Update options
		gnrb_update_options();
	}
	if( isset($_POST["gnrb_add_field"]) ) {
		// Add a field
		gnrb_edit_field(true);
	}
	if( isset($_POST["gnrb_edit_field"]) ) {
		// Edit a field
		gnrb_edit_field(false);
	}
	if( isset($_POST["gnrb_delete_field"]) ) {
		// Delete a field
		gnrb_delete_field();
	}
	if(isset($_POST["gnrb_edit_band"])){
		// Edit band
		gnrb_edit_band();
	}
	if(isset($_POST["gnrb_member_add"])){
		// Add member
		gnrb_member_edit(true);
	}
	if(isset($_POST["gnrb_member_edit"])){
		// Edit member
		gnrb_member_edit(false);
	}
	if(isset($_POST["gnrb_member_delete"])){
		// Delete member
		gnrb_member_delete();
	}
	if(isset($_POST["gnrb_create_band_user"])){
		// Delete member
		gnrb_create_band_user();
	}
	if(isset($_POST["gnrb_del_dead_bands"])){
		// Delete member
		gnrb_delete_dead_bands();
	}
	
	if (function_exists('add_options_page') && function_exists('add_management_page')) {
		// Add pages under options & management
		add_options_page('Band Creator Options', 'Band Options', $gnrb_admin_user_level, $gnrb_plugin_folder, 'gnrb_options_page');
		add_management_page('Band Creator Management', 'Band Management', $gnrb_admin_user_level, $gnrb_plugin_folder, 'gnrb_management_page');
	}
}

function gnrb_edit_field($add = false) {
	global $wpdb, $gnrb_table_meta, $gnrb_table_fields, $gnrb_table_bandmembers, $success;
	$field_name = $_POST["field_name"];
	$field_order = $_POST["field_order"];
	$field_descr  = $_POST["field_descr"];
	$field_editable  = (isset($_POST["field_editable"]) ? "1" : "0");
	$field_type = $_POST["field_type"];
	$field_type_data = $_POST["field_type_data"];
	$field_default = $_POST["field_default"];
	if( $add == true ) {
		$field_key = $_POST["field_key"];
		$sql = "INSERT INTO $gnrb_table_fields
				(field_key, field_name, field_order, field_descr, field_editable, field_type, field_type_data, field_default) 
				VALUES('$field_key', '$field_name', $field_order, '$field_descr', $field_editable, '$field_type', '$field_type_data', '$field_default');";
	} else {
		$field_id = $_POST["field_id"];
		$sql = "UPDATE $gnrb_table_fields 
				SET 
					field_name = '$field_name', 
					field_order = $field_order, 
					field_descr = '$field_descr', 
					field_editable = $field_editable, 
					field_type = '$field_type', 
					field_type_data = '$field_type_data', 
					field_default = '$field_default'
				WHERE id = $field_id LIMIT 1;";
	}
	$success = $wpdb->query($sql);
}

function gnrb_member_edit($add = false) {
	global $wpdb, $gnrb_table_bandmembers, $success;
	$member_name = $_POST["member_name"];
	$member_type = $_POST["member_type"];
	$s = "";
	if( $member_type ) {
		//for( $i = 0; $i < strlen($member_type); $i++ ) $s = $s . $member_type[$i] . ( ($i < strlen($member_type)-1) ? ", " : "" );
		for( $i = 0; $i < sizeof($member_type); $i++ ) {
			$s = $s . $member_type[$i];
			if( $i < sizeof($member_type)-1) $s = $s . ", ";
		}
		$member_type = $s;
	}
	$member_descr = $_POST["member_descr"];
	$user_id = $_POST["user_id"];
	if($add) {
		$sql = "INSERT INTO $gnrb_table_bandmembers
				(user_id, member_name, member_type, member_descr)
				VALUES (
					$user_id,
					'$member_name',
					'$member_type',
					'$member_descr'
				);";
	} else {
		$member_id = $_POST["member_id"];
		$sql = "UPDATE $gnrb_table_bandmembers
				SET 
					member_name = '$member_name', 
					member_type = '$member_type',
					member_descr = '$member_descr'
				WHERE id = $member_id
				LIMIT 1;";
	}
	$result = $wpdb->query($sql);
	$success = $result;
}

function gnrb_member_delete(){
	global $wpdb, $gnrb_table_bandmembers, $success;
	$member_id = $_POST["member_id"];
	$sql = "DELETE FROM $gnrb_table_bandmembers
			WHERE id = $member_id
			LIMIT 1;";
	$success = $wpdb->query($sql);
}

function gnrb_unassociated_bands() {
	global $wpdb, $gnrb_table_artist;
	// Get all bands from icecase db
	switch_to_icecast_db();
	$sql = "SELECT id, name, numtracks 
			FROM artist, 
				(SELECT artistid, COUNT(*) AS numtracks FROM tunes GROUP BY artistid) AS x 
			WHERE artist.id = x.artistid
			ORDER BY name, numtracks;";
	$artists = mysql_query($sql);
	switch_to_wp_db();
	echo "
	<ul class=\"notice\">
		<li>Please verify all band names very carefully, else it may lead to duplication troubles later on</li>
		<li>Rows marked in BLUE are the ones that are predicted to be duplicates.</li>
		<li>Fields marked in RED may have other data besides band name.</li>
		<li>The \"Suggested Band Name\" indicates the name that will copied into the WordPress user's account. This can be changed later.</li>
		<li>WordPress Login cannot be changed later</li>
	</ul>
	<table class=\"widefat\">
		<thead>
			<th width=\"30%\" scope=\"col\">Old Data</th>
			<th scope=\"col\">Suggested<br />WordPress Login *</th>
			<th scope=\"col\">Suggested<br />Band Name</th>
			<th scope=\"col\">No. Tracks</th>
			<th scope=\"col\">Actions</th>
		</thead>
		<tbody>";
	$alternate = "";
	$prev_first_word = "";
	$all_names = "";
	$artist_id_list = "";
	$count = 0;
	while($a = mysql_fetch_object($artists) ){
		if( $count == 0 ) echo "<form action=\"\" method=\"post\">";
		$artistid = $a->id;
		$sql = "SELECT user_id FROM $gnrb_table_artist WHERE artistid = $artistid;";
		$exists = $wpdb->get_var($sql);
		// Check if is a repeat
		$match = false;
		if( $prev_first_word != "" ) {
			// search prev band's first word in current band name
			$match = preg_match("/".$prev_first_word." /i", str_replace(".", "", $a->name));
		}
		// Get first word > 2 characters
		$arr = explode(" ", $a->name);
		$str = $arr[0];
		foreach( $arr as $word ) {
			if(strlen($word) > 2) {
				$str = $word; 
				break; 
			}
		}
		$str = str_replace(".", "", $str);
		// check current band's first word in all bands check so far
		if( !$match ) $match = preg_match("/".$str." /i", $all_names);
		if( !$match ) {
			// if a new band save current band name
			$prev_first_word = $str;
			$all_names = $all_names . " " . $prev_first_word;
		}
		if( !$exists ) {
			$artist_id_list = $artist_id_list . $a->id . ", " ;
			$u_login = str_replace("-", "_", sanitize_title($a->name));
			$band_name = ucwords($a->name);
			$style_txt = strlen($band_name) >= 30 ? "style=\"background:#ffdddd;\"" : "";
			$style_rpt = $match ? "style=\"background:#ddddff;\"" : "";
			$checked = "";//$match ? "" : "checked=\"checked\"";
			echo "
			<tr$alternate $style_rpt>
				<td width=\"30%\">$a->name<input type=\"hidden\" name=\"artistid$a->id\" value=\"$a->id\" /></td>
				<td $style_txt><input type=\"text\" size=\"30\" name=\"user_login$a->id\" value=\"$u_login\" /></td>
				<td $style_txt><input type=\"text\" size=\"40\" name=\"band_name$a->id\" value=\"$band_name\" /></td>
				<td>$a->numtracks</td>
				<td><input type=\"checkbox\" name=\"check$a->id\" value=\"true\" $checked /> Create</td>
			</tr>";
			$alternate = ($alternate == "") ? " class=\"alternate\"" : "";
		}
		$count++;
		if( $count == 10 ) {
			echo "
			<tr class=\"submit\">
				<td colspan=\"5\">
					<input type=\"hidden\" name=\"artist_id_list\" value=\"$artist_id_list\" />
					<input type=\"submit\" name=\"gnrb_create_band_user\" value=\"Create Band Associations For These 10 &raquo;\" />
				</td>
			</tr>
			</form>";
			$count = 0;
		}
	}
	if( $count < 10 ) {
		$count++;
			echo "
			<tr class=\"submit\">
				<td colspan=\"5\">
					<input type=\"hidden\" name=\"artist_id_list\" value=\"$artist_id_list\" />
					<input type=\"submit\" name=\"gnrb_create_band_user\" value=\"Create Band Associations For These $count &raquo;\" />
				</td>
			</tr>
			</form>";

	}
	echo "
		</tbody>
	</table>";
}

function gnrb_create_band_user() {
	global $wpdb, $gnrb_table_meta, $gnrb_table_fields, $success, $is_admin, $gnrb_table_artist;
	// Get artist_id_list
	$ids = explode(", ", $_POST['artist_id_list']);
	foreach($ids as $a) {
		$artistid = $_POST["artistid".$a];
		$userlogin = $_POST["user_login".$a];
		$bandname = $_POST["band_name".$a];
		$create = ($_POST["check".$a] == "true");
		srand(time());
		$password = "pwd".rand()%1000;
		if( $create ) {
			$new_user_id = wp_create_user($userlogin,$password,"");
			if( $new_user_id ) {
				$sql = "INSERT INTO $gnrb_table_artist
						(user_id, artistid)
						VALUES ($new_user_id, $artistid);";
				$wpdb->query($sql);
				
				$field_id = gnrb_get_field_id('band_name');
				$sql = "INSERT INTO $gnrb_table_meta
						(user_id, field_id, field_value) 
						VALUES ($new_user_id, $field_id, '$bandname');";
				$wpdb->query($sql);
			}
		}
	}	
	$success = true;
}

function gnrb_get_field_id($field_key){
	global $wpdb, $gnrb_table_fields;
	$id = $wpdb->get_var("SELECT id FROM $gnrb_table_fields WHERE field_key = '$field_key' LIMIT 1");
	return $id;
}

function gnrb_edit_band() {
	global $wpdb, $gnrb_table_meta, $gnrb_table_fields, $gnrb_table_bandmembers, $success, $is_admin, $gnrb_table_artist;
	// Update artistid
	$artistid = $_POST["artistid"];
	$user_id = $_POST["user_id"];
	$first_entry = $wpdb->get_var("SELECT id FROM $gnrb_table_artist WHERE user_id = $user_id LIMIT 1;");
	if(!$first_entry) {
		$sql = "INSERT INTO $gnrb_table_artist
				(user_id, artistid)
				VALUES ($user_id, $artistid);";
		$wpdb->query($sql);
	} 
	/*else {
		$sql = "UPDATE $gnrb_table_artist
				SET user_id = $user_id,
					artistid = $artistid
				WHERE id = $first_entry
				LIMIT 1;";
	}*/	
	// Update field values
	$sql = "SELECT * FROM $gnrb_table_fields";
	if( !$is_admin ) $sql = $sql . " WHERE field_editable = 1";
	$results = $wpdb->get_results($sql);
	$user_id = $_POST["user_id"];
	foreach( $results as $f ) {
		$field_name = "field$f->id";
		switch($f->field_type){
			case "text": case "textarea": case "select": $value = $_POST[$field_name]; break;
			case "checkbox": $value = ($_POST[$field_name] == "on"); break;
			case "file": 
				$value = gnrb_upload($field_name, $_POST[$field_name."options"], $user_id . $f->id); 
				$value = $value["name"];
				break;
		}
		if( $value ) {
			$first_entry = $wpdb->get_var("SELECT id FROM $gnrb_table_meta WHERE field_id = $f->id AND user_id = $user_id LIMIT 1;");
			if( !$first_entry ) {
				$sql = "INSERT INTO $gnrb_table_meta
						(user_id, field_id, field_value) 
						VALUES ($user_id, $f->id, '$value');";
			} else {
				$sql = "UPDATE $gnrb_table_meta 
						SET 
							field_value = '$value' 
						WHERE id = $first_entry LIMIT 1;";
			}
			$wpdb->query($sql);
		}
	}
	$success = true;
}

function gnrb_options_page(){
	global $wpdb, $gnrb_table_meta, $gnrb_table_fields, $gnrb_table_bandmembers;
	// Options page
	include_once($gnrb_abspath."options.tpl.php");
}

function gnrb_management_page(){
	global $wpdb, $gnrb_table_meta, $gnrb_table_fields, $gnrb_table_bandmembers, $gnrb_upload_path_virtual, $gnrb_table_artist, $is_admin;
	// Management page
	if(isset($_POST["user_id"])) $user_id = $_POST["user_id"];
	if(isset($_POST["user_login"])) $user_login = $_POST["user_login"];
	$is_admin = true;
	include_once($gnrb_abspath."management.tpl.php");
}

function gnrb_profile_page(){
	global $wpdb, $gnrb_table_meta, $gnrb_table_fields, $gnrb_table_bandmembers, $gnrb_upload_path_virtual, $gnrb_table_artist;
	// Band profile page
	global $user_id, $user_login;
	get_currentuserinfo();
	$user_id  = $user_ID;
	include_once($gnrb_abspath."profile.tpl.php");
}

function gnrb_get_fieldtype_select($selected_value = null, $options = "text, textarea, select, checkbox, file") {
	$types = explode(", ", $options);
	$msg = "";
	foreach($types as $t) {
		$selected = $selected_value == $t ? " selected=\"selected\"" : "";
		$msg = $msg . "<option value=\"$t\"$selected>$t</option>";
	}
	return $msg;
}

function gnrb_fix_windows_path($path){
	$path = str_replace("\\","/",$path);
	$path = str_replace("//","/",$path);
	$path = str_replace("//","/",$path);
	$path = str_replace("//","/",$path);
	if(substr($path, -1)=='/'){
		$path = substr($path,0,strlen($path)-1);
	}
	return $path;
}

function gnrb_upload($field_name, $file_types, $new_name) {
	// File Uploader class from http://www.finalwebsites.com/
	global $gnrb_max_filesize, $gnrb_upload_path;

	$max_size = 1024*$gnrb_max_filesize;
	$my_upload = new file_upload;

	$my_upload->upload_dir = $gnrb_upload_path;
	$my_upload->extensions = explode(",", str_replace(" ", "", $file_types));
	$my_upload->max_length_filename = 50;
	$my_upload->rename_file = true;

	$my_upload->the_temp_file = $_FILES[$field_name]['tmp_name'];
	$my_upload->the_file = $_FILES[$field_name]['name'];
	$my_upload->http_error = $_FILES[$field_name]['error'];
	$my_upload->replace = "y";
	$my_upload->do_filename_check = "y";
	
	if ($my_upload->upload($new_name)) {
		$full_path = $my_upload->upload_dir.$my_upload->file_copy;
		$info = $my_upload->get_uploaded_file_info($full_path);
		return $info;
	}
	return false;
}

function gnrb_get_bands_list() {
	// Returns a list of bands in the wordpress users list
	global $wpdb, $gnrb_plugin_folder, $gnrb_table_artist, $gnrb_table_meta;
	$band_name_id = gnrb_get_field_id("band_name");
	$sql = "SELECT ID, user_login, band_name
			FROM {$wpdb->prefix}users, (SELECT id AS meta_id, user_id, field_id, field_value AS band_name FROM $gnrb_table_meta) AS meta
			WHERE EXISTS (
				SELECT id 
				FROM $gnrb_table_artist 
				WHERE user_id = {$wpdb->prefix}users.ID
			) 
			AND {$wpdb->prefix}users.ID = meta.user_id
			AND meta.field_id = $band_name_id
			ORDER BY user_login;";
	$results = $wpdb->get_results($sql);
	foreach($results as $user){
		echo "
		<li>
			<form action=\"\" method=\"post\">
				<input type=\"hidden\" name=\"page\" value=\"$gnrb_plugin_folder\" />
				<input type=\"hidden\" name=\"user_id\" value=\"$user->ID\" />
				<input type=\"hidden\" name=\"user_login\" value=\"$user->user_login\" />
				<input type=\"submit\" name=\"gnrb_edit_band_admin\" value=\"$user->band_name\" />
			</form>
		</li>";
	}
	return $results;
}

function gnrb_get_artists_icecast($current_id = null) {
	global $wpdb;
	switch_to_icecast_db();
	$sql = "SELECT id, name FROM artist ORDER BY name";
	$result = $wpdb->get_results($sql);
	$options = "";
	foreach($result as $obj) {
		$selected = ($current_id == $obj->id) ? " selected=\"selected\"" : "";
		$options = $options . "<option value=\"$obj->id\" $selected>$obj->name &raquo; $obj->id</option>";
	}
	switch_to_wp_db();
	return $options;
}

function gnrb_get_artistname_icecast($current_id = null) {
	global $wpdb;
	switch_to_icecast_db();
	$sql = "SELECT name FROM artist WHERE id = $current_id LIMIT 1";
	$result = mysql_fetch_object(mysql_query($sql));
	switch_to_wp_db();
	return $result->name;
}

/*
function gnrb_get_($current_id = null) {
	global $wpdb;
	switch_to_icecast_db();
	$sql = "SELECT id, name FROM artist ORDER BY name";
	$result = $wpdb->get_results($sql);
	$options = "";
	foreach($result as $obj) {
		$selected = ($current_id == $obj->id) ? " selected=\"selected\"" : "";
		$options = $options . "<option value=\"$obj->id\" $selected>$obj->name &raquo; $obj->id</option>";
	}
	switch_to_wp_db();
	return $options;
}
*/

function gnrb_delete_dead_bands() {
	global $wpdb, $gnrb_table_meta, $gnrb_table_fields, $gnrb_table_bandmembers, $gnrb_upload_path_virtual, $gnrb_table_artist, $success;
	$sql = "SELECT user_id FROM $gnrb_table_artist WHERE NOT EXISTS (SELECT ID FROM {$wpdb->prefix}users WHERE ID = {$gnrb_table_artist}.user_id);";
	$dead = $wpdb->get_var($sql);
	if( $dead ) {
		$sql = "DELETE FROM $gnrb_table_artist 
				WHERE 
				NOT EXISTS (
					SELECT ID
					FROM {$wpdb->prefix}users 
					WHERE ID = {$gnrb_table_artist}.user_id
				)";
		$result = $wpdb->query($sql);
		$sql = "DELETE FROM $gnrb_table_bandmembers 
				WHERE 
				NOT EXISTS (
					SELECT ID 
					FROM {$wpdb->prefix}users 
					WHERE ID = {$gnrb_table_bandmembers}.user_id
				)";
		$wpdb->query($sql);
		$sql = "DELETE FROM $gnrb_table_meta 
				WHERE 
				NOT EXISTS (
					SELECT ID 
					FROM {$wpdb->prefix}users 
					WHERE ID = {$gnrb_table_meta}.user_id
				)";
		$wpdb->query($sql);	
	}
	$success = $result;
}

function gnrb_get_num_dead_bands() {
	global $wpdb, $gnrb_table_artist;
	$sql = "SELECT COUNT(*) FROM $gnrb_table_artist WHERE NOT EXISTS (SELECT ID FROM {$wpdb->prefix}users WHERE ID = {$gnrb_table_artist}.user_id);";
	return $wpdb->get_var($sql);
}


// Hook to actions
add_action("activate_{$gnrb_plugin_folder}/{$gnrb_plugin_folder}.php", "gnrb_install");
add_action("admin_menu", "gnrb_admin");
?>