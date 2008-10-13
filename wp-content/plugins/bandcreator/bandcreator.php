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
        if( strrpos($path, '/') != strlen($path) - 1 )
                return ($path . '/');
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
				artist_id bigint(20) NOT NULL,
				field_key varchar(128) NOT NULL default '',
				field_id bigint(5) NOT NULL,
				field_value longtext default '',
				PRIMARY  KEY  (id)
			);";
	// Band members table
	$sql3 = "CREATE TABLE $gnrb_table_bandmembers (
				id bigint(5) NOT NULL auto_increment,
				artist_id bigint(20) NOT NULL,
				member_name varchar(255) NOT NULL default '',
				member_type varchar(255) NOT NULL default '',
				member_descr longtext default '',
				PRIMARY  KEY  (id)
			);";
	$sql4 = "CREATE TABLE $gnrb_table_artist (
				id bigint(5) NOT NULL auto_increment,
				user_id bigint(20) NOT NULL,
				centova_artistid bigint(20) NOT NULL,
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
                
                $order++;
		$insert = "INSERT INTO $gnrb_table_fields($field_list) VALUES( 'band_genre', 'Band Genre', $order, 'Band Genre', 1, 'text', '50', '');";
		$results = $wpdb->query($insert);
                
                $order++;
		$insert = "INSERT INTO $gnrb_table_fields($field_list) VALUES( 'band_files', 'Band Music Files', $order, 'Band Music Files', 1, 'file', '.mp3,.MP3', '');";
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
        //print_r($_POST);
	$is_admin = true;
	if( isset($_POST["gnrb_update_options"]) ) {
		// Update options
                //echo("1");
		gnrb_update_options();
	}
	if( isset($_POST["gnrb_add_field"]) ) {
		// Add a field
                //echo("2");
		gnrb_edit_field(true);
	}
	if( isset($_POST["gnrb_edit_field"]) ) {
		// Edit a field
                //echo("3");
		gnrb_edit_field(false);
	}
	if( isset($_POST["gnrb_delete_field"]) ) {
		// Delete a field
                //echo("4");
		gnrb_delete_field();
	}
	if(isset($_POST["gnrb_edit_band"])){
		// Edit band
                //echo("5");
		gnrb_edit_band();
	}
	if(isset($_POST["gnrb_member_add"])){
		// Add member
                //echo("6");
		gnrb_member_edit(true);
	}
	if(isset($_POST["gnrb_member_edit"])){
		// Edit member
                //echo("7");
		gnrb_member_edit(false);
	}
	if(isset($_POST["gnrb_member_delete"])){
		// Delete member
                //echo("8");
		gnrb_member_delete();
	}
	if(isset($_POST["gnrb_create_band_user"])){
		// Delete member
                //echo("9");
		gnrb_create_band_user();
	}
	if(isset($_POST["gnrb_del_dead_bands"])){
		// Delete member
                //echo("10");
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
				(artist_id, member_name, member_type, member_descr)
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
			FROM track_artists, 
                                (SELECT artistid, COUNT(*) AS numtracks FROM tracks GROUP BY artistid) AS x 
			WHERE track_artists.id = x.artistid
			GROUP BY name;";
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
		$sql = "SELECT id FROM $gnrb_table_artist WHERE centova_artistid = $artistid;";
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


function show_registered_artists($userid){
        global $wpdb, $gnrb_table_meta, $gnrb_table_fields, $success, $is_admin, $gnrb_table_artist;
        
        $results = $wpdb->get_results("select id,name,songcount,views,fans from $gnrb_table_artist where user_id='$userid'");
        
        if($results){
        ?>
        <div id="userartists">
        <h3>Registered Artists</h3>
                <ul>
                <?php
                        foreach($results as $result){
                                echo("<li>");
                                echo('<div id="userartist">');
                                echo('<b>'.$result->name.'</b>');
                                echo('<p>Total Listeners: '.$result->views.'</p');
                                echo('<p>Fans: '.$result->fans.'</p>');
                                echo('<p>Songs: '.$result->songcount.'</p>');
                                //get_artist_statistics($result->id);
                                echo('<div id="userartistactions"><a class="artistactions" href="' . get_option('siteurl') . '/add-music?aid=' . $result->id . '">Music Manager</a><a class="artistactions" href="' . get_option('siteurl') . '/update-profile?aid=' . $result->id . '">Update Profile</a><a class="artistactions" href="' . get_option('siteurl') . '/remove-artist">Delete Artist</a></div>');
                                echo('</div>');
                                echo("</li>");
                        }
                }?>
                </ul>
        </div>
<?php        
}

function is_not_registered_artist($userid){
        global $wpdb, $gnrb_table_meta, $gnrb_table_fields, $success, $is_admin, $gnrb_table_artist;
        
        if($wpdb->get_var("select id from $gnrb_table_artist where $user_id='$userid'"))
                return TRUE;
        else
                return FALSE;
}

function insert_artist($userid) {
        global $wpdb, $gnrb_table_meta, $gnrb_table_fields, $success, $is_admin, $gnrb_table_artist;
        
        $artistname = $wpdb->escape($_POST["artistname"]);
        $firstname = $wpdb->escape($_POST["firstname"]);
        $lastname = $wpdb->escape($_POST["lastname"]);
        $email = $wpdb->escape($_POST["email"]);
        $phonenumber = $wpdb->escape($_POST["phonenumber"]);
        $address = $wpdb->escape($_POST["address"]);
        
        $sql = "INSERT INTO $gnrb_table_artist
					(user_id, name, centova_artistid,featured,firstname,lastname,email,phonenumber,address)
					VALUES ($userid, '$artistname', 0,0,'$firstname','$lastname','$email','$phonenumber','$address');";
//        echo "Jesus how?".$sql;
        $wpdb->query($sql);
}

function check_if_exists_artist($nicename)
{
        global $wpdb, $gnrb_table_meta, $gnrb_table_fields, $success, $is_admin, $gnrb_table_artist;
	$sql = "Select id from $gnrb_table_artist where nicename='$nicename'";
        $result=$wpdb->get_var($sql);
        if($result)
                return FALSE;
        return TRUE;
}

function gnrb_create_band_user() {
	global $wpdb, $gnrb_table_meta, $gnrb_table_fields, $success, $is_admin, $gnrb_table_artist;
	// Get artist_id_list
        echo("Why am i inserting again?");
	$ids = explode(", ", $_POST['artist_id_list']);
	foreach($ids as $a) {
                //print_r($a);
		$artistid = $_POST["artistid".$a];
		$userlogin = $_POST["user_login".$a];
		$bandname = $_POST["band_name".$a];
                $featured=0;
                if(isset($_POST["featured"]))
                        $featured=1;
		$create = ($_POST["check".$a] == "true");
		//srand(time());
		//$password = "pwd".rand()%1000;
		if( $create ) {
			//$new_user_id = wp_create_user($userlogin,$password,"");
			//if( $new_user_id ) {
                        $nicename=sanitize_title_with_dashes($bandname);
                        if(check_if_exists_artist($nicename)){
                                $sql = "INSERT INTO $gnrb_table_artist
                                		(user_id, centova_artistid,featured,name,nicename)
                                		VALUES (0, $artistid,$featured,'$bandname','$nicename');";
                                $wpdb->query($sql);
                                echo($sql);
                                $field_id = gnrb_get_field_id('band_name');
                                $sql = "INSERT INTO $gnrb_table_meta
                                	(artist_id, field_key, field_id, field_value) 
                                	VALUES ($wpdb->insert_id, 'band_name', $field_id, '$bandname');";
                                $wpdb->query($sql);
                        }
			//}
		}
	}	
	$success = true;
}

function gnrb_get_field_id($field_key){
	global $wpdb, $gnrb_table_fields;
	$id = $wpdb->get_var("SELECT id FROM $gnrb_table_fields WHERE field_key = '$field_key' LIMIT 1");
	return $id;
}

function gnrb_create_page($artist_id,$user_id){
        global $wpdb, $gnrb_table_meta, $gnrb_table_fields, $gnrb_table_bandmembers, $success, $is_admin, $gnrb_table_artist;
	
        //$sql="Select field_value from $gnrb_table_meta where user_id=$user_id and field_key='band_name'";
        $sql = "Select name from $gnrb_table_artist where id='$artist_id'";
        echo $sql;
        $artist_name = $wpdb->get_var($sql);
        //echo("Huh what?".$artist_name.$user_id);
        if($artist_name){
                //echo($artist_name);
                $sql = "select ID from  {$wpdb->prefix}posts where post_title='$artist_name'";
                if(!$wpdb->get_var($sql)){
                        //echo("2");
                        $my_post = array();
                        $my_post['post_title'] = $artist_name;
                        $my_post['post_type']='page';
                        $my_post['post_author']=$user_id;
                        $my_post['post_status'] = 'publish';
                        $my_post['post_parent']='32';
                        $postid=wp_insert_post( $my_post );
                        if($postid){
                                $sql="insert into {$wpdb->prefix}postmeta (post_id,meta_key,meta_value) VALUES('$postid','_wp_page_template','artistinfo.php')";
                                $wpdb->query($sql);
                        }
                }
        }
}

function gnrb_edit_band() {
	global $wpdb, $gnrb_table_meta, $gnrb_table_fields, $gnrb_table_bandmembers, $success, $is_admin, $gnrb_table_artist;
	// Update artistid
        //echo("Huh what happened here?");
	$artistid = $_POST["artistid"];
	echo("So what the fuck?".$_POST["artistid"]."/".$_POST["user_id"]."\n");
	$user_id = $_POST["user_id"];
        $featured=0;
        if(isset($_POST["featured"]))
                $featured=1;
        //echo($featured);
	$first_entry = $wpdb->get_results("SELECT id FROM $gnrb_table_artist WHERE id = $artistid LIMIT 1;");
	//echo("SELECT id FROM $gnrb_table_artist WHERE id = $artist_id LIMIT 1;");
	//print_r($first_entry);
	if(!$first_entry) {
		
		/*$sql = "INSERT INTO $gnrb_table_artist
				(id, centova_artistid,featured)
				VALUES ($user_id, $artistid,$featured);";
		$wpdb->query($sql);*/
		echo("Ok, something went really wrong. How could this happen");

	} else {
                $sql = "UPDATE $gnrb_table_artist set featured=$featured where id= $artistid;";
                echo($sql);
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
        if($_POST["import_band_fromdb"]!=-1){
                $import_userid=$_POST["import_band_fromdb"];
                echo("I need to update".$_POST["import_band_fromdb"]);
                $success=true;
                //$import_artistid=$_POST["user_id"]
                $sql = "delete from $gnrb_table_meta where artist_id='$artistid'";
                $wpdb->query($sql);
                $sql = "select * from old_wp_gnrb_meta where user_id='$import_userid'";
                $imp_result=$wpdb->get_results($sql);
                foreach($imp_result as $impr){
                        echo("Printing...This is weird!");
                        if($impr->field_key=="band_name")
                        	$wpdb->query("update $gnrb_table_artist set name='$impr->field_value' where id=$artistid");
                        if($impr->field_key=="band_email")
                        	$wpdb->query("update $gnrb_table_artist set email='$impr->field_value' where id=$artistid");
                
                        $sql = "INSERT INTO $gnrb_table_meta
					(artist_id, field_key, field_id, field_value) 
					VALUES ($artistid, '$impr->field_key', '$impr->field_id', '$impr->field_value')";
                        $wpdb->query($sql);
                                                
                }
                $sql = "delete from $gnrb_table_bandmembers where artist_id='$artistid'";
                $wpdb->query($sql);
                $sql = "select * from old_wp_gnrb_bandmembers where user_id='$import_userid'";
                $imp_result=$wpdb->get_results($sql);
                //print_r($imp_result);
                foreach($imp_result as $impr){
                        $sql = "INSERT INTO $gnrb_table_bandmembers
                                (artist_id,member_name,member_type,member_descr)
                                VALUES($artistid,'$impr->member_name','$impr->member_type','$impr->member_descr')";
                        $wpdb->query($sql);
                }
                
                $success=true;
                gnrb_create_page($artistid,user_id);
                return;
        }
	foreach( $results as $f ) {
                //print_r($f);
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
                        if($f->id!=9){
                                if($f->id==1){
                                       echo("update $gnrb_table_artist set name='$value' where id=$artistid");
                                       $wpdb->query("update $gnrb_table_artist set name='$value' where id=$artistid");
                                }        
                               
                                if($f->id==4)
                                       $wpdb->query("update $gnrb_table_artist set email='$value' where id=$artistid");        
                                
                                $first_entry = $wpdb->get_var("SELECT id FROM $gnrb_table_meta WHERE field_id = $f->id AND artist_id = $artistid LIMIT 1;");
                                if( !$first_entry ) {
                                        $sql = "INSERT INTO $gnrb_table_meta
                                                        (artist_id, field_key, field_id, field_value) 
                                                        VALUES ($artistid, '$f->field_key', $f->id, '$value');";
                                        
                                } else {
                                        $sql = "UPDATE $gnrb_table_meta 
                                                        SET 
                                                                field_value = '$value' 
                                                        WHERE id = $first_entry LIMIT 1;";
                                }
                                $wpdb->query($sql);
                        }else {
                                if($value){
                                        $tag_array=split(",",$value);
                                        print_r($value);
                                        $wpdb->query("delete from $gnrb_table_meta where field_id=$f->id and artist_id=$artistid and field_key='band_genre'");
                                        foreach($tag_array as $tag){
                                                //echo("insert into $gnrb_table_meta (artist_id,field_key,field_id,field_value)
                                                //             VALUES ($artistid, '$f->field_key',$f->id,'$tag')");
                                                        
                                                $wpdb->query("insert into $gnrb_table_meta (artist_id,field_key,field_id,field_value)
                                                             VALUES ($artistid, '$f->field_key',$f->id,'$tag')");
                                        }
                                        
                                }
                        }
		}
	}
        gnrb_create_page($artistid,$user_id);
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
        //echo("DUM DUM DUM DUM");
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
        echo("So i didnt even reach here?");
	$max_size = 1024*$gnrb_max_filesize;
	$my_upload = new file_upload;

	$my_upload->upload_dir = $gnrb_upload_path;
	$my_upload->extensions = explode(",", str_replace(" ", "", $file_types));
	$my_upload->max_length_filename = 50;
	$my_upload->rename_file = false;

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
        print_r($my_upload->message);
        //echo("Returning false");
	return false;
}

function gnrb_get_bands_list() {
	// Returns a list of bands in the wordpress users list
	global $wpdb, $gnrb_plugin_folder, $gnrb_table_artist, $gnrb_table_meta;
	/*$sql = "SELECT ID, user_login, band_name
			FROM {$wpdb->prefix}users, (SELECT id AS meta_id, user_id, field_id, field_key, field_value AS band_name FROM $gnrb_table_meta) AS meta
			WHERE EXISTS (
				SELECT id 
				FROM $gnrb_table_artist 
				WHERE user_id = {$wpdb->prefix}users.ID
			) 
			AND {$wpdb->prefix}users.ID = meta.user_id
			AND meta.field_key = 'band_name'
			ORDER BY user_login;";*/
        //$sql = "select $gnrb_table_artist.id,field_value from $gnrb_table_artist,$gnrb_table_meta where $gnrb_table_artist.id = $gnrb_table_meta.artist_id and $gnrb_table_meta.field_key=\"band_name\"";
	$sql = "select id,name from $gnrb_table_artist order by name";
        $results = $wpdb->get_results($sql);
	foreach($results as $user){
		echo "
		<li>
			<form action=\"\" method=\"post\">
				<input type=\"hidden\" name=\"page\" value=\"$gnrb_plugin_folder\" />
				<input type=\"hidden\" name=\"user_id\" value=\"$user->id\" />
				<!--<input type=\"hidden\" name=\"user_login\" value=\"$user->user_login\" />-->
				<input type=\"submit\" name=\"gnrb_profile_page\" value=\"$user->name\" />
			</form>
		</li>";
	}
	return $results;
}

function gnrvp_get_artist_info($user_id) {
	//returns the wordpress user's info for $artist_id
	global $wpdb, $gnrb_table_meta, $gnrb_table_fields, $gnrb_table_artist, $gnrb_table_meta, $gnrb_table_bandmembers;

	$no_band_data = "<h2>Uh oh!</h2><p>Looks like the RV admins did not do their homework on this band.</p><p>If you have got info on this band then use the form below to alert the admins.</p>";
	
	if( $user_id ) {
		// Generate band members list
		$sql = "SELECT field_key, field_value 
				FROM $gnrb_table_meta
				WHERE 
				artist_id = $user_id;";
                //echo $sql;
		$result = $wpdb->get_results($sql);
		$output = "";
		$format = stripslashes(get_option("gnrb_band_bio_format"));
		// Define variables
		foreach( $result as $row ) extract(array("$row->field_key"=>"$row->field_value"));
                
		$band_bio = strip_tags($band_bio, get_option("gnrb_allowed_tags"));
		if( $band_image_url == "http://" or $band_image_url == "" ) {
                        $band_image = get_option("gnrb_band_pics_path") . $band_image;
                }else {
                        $band_image = $band_image_url;
                }
		$band_icon = get_option("gnrb_band_pics_path") . $band_icon;
                
                $bio="<div id=\"artistcapsule\">";
                $popened=0;
                if($band_image == get_option('site_url')."/band_pics/")
                	$bio = $bio. "<h2>$band_name</h2> ";
                else{
                    $band_url=get_option("siteurl")."/artist/".sanitize_title_with_dashes($band_name);

                        $bio = $bio. "<a href=\"$band_url\"><img width=\"200\" src=\"$band_image\"/></a><div id=\"artistcapcontent\"><h2>$band_name</h2>";
                        $popened=1;
                }

                //$band_bio = str_replace("\\n", "<br />", $band_bio);
                //$band_bio=substr($band_bio,0,200)."<a href=\"$user_url\"> More...</a>";

                //if($band_bio!="") {
                //        $bio .= "<p style=\"clear:both;\">$band_bio</p>";
                //}else {
                //        $bio .= $no_band_data;
                //}

                if($band_url != "http:" and $band_url != "" and $band_email){
                        if($popened){
                                if($band_email) $bio .= "<span>Email: </span><a href=\"mailto:$band_email\" target=\"blank\">$band_email</a>";
                                if($band_url != "http:" and $band_url != "") $bio .= "<br /><span>Website: </span><a href=\"$band_url\" target=\"blank\">$band_url</a>";
                        }else{
                                if($band_email) $bio .= "<div id=\"artistcapcontent\"><br /><span>Email: </span><a href=\"mailto:$band_email\" target=\"blank\">$band_email</a>";
                                if($band_url != "http:" and $band_url != "") $bio .= "<br /><span>Website: </span><a href=\"$band_url\" target=\"blank\">$band_url</a>";
                                $popened=1;
                        }
                }
                
                
                //$bio = str_replace("'", "\'", $bio);
                if($popened){
                        $bio .= "<br /><span class=\"listenbutton\"><a href=\"$user_url\">Listen Now</a></span></div></div>";
                        //echo("Closed p");
                }else
                        $bio .= "</div>";
                        
                return $bio;
	} else {
		return $no_band_data;
	}
}

function gnrvp_get_artist_name($user_id) {
	//returns the wordpress user's info for $artist_id
	global $wpdb, $gnrb_table_meta, $gnrb_table_fields, $gnrb_table_artist, $gnrb_table_meta, $gnrb_table_bandmembers;

	$no_band_data = "Untitled";
	
	if( $user_id ) {
		// Generate band members list
		$sql = "SELECT * FROM $gnrb_table_bandmembers
				WHERE user_id = $user_id";
		$members = $wpdb->get_results($sql);
		$bandm = "<ul class=\"bandmembers\">";
		foreach( $members as $m ) {
			$bandm = $bandm . "<li><b>$m->member_name</b> <span>$m->member_type</span></li>";
		}
		$bandm = $bandm . "</ul>";
		$band_members_list = $bandm;
		$sql = "SELECT field_key, field_value 
				FROM $gnrb_table_meta
				WHERE 
					user_id = $user_id;";
		$result = $wpdb->get_results($sql);
		$output = "";
		$format = stripslashes(get_option("gnrb_band_bio_format"));
		// Define variables
		foreach( $result as $row ) extract(array("$row->field_key"=>"$row->field_value"));
		return $band_name;

	} else {
		return $no_band_data;
	}
}

function gnrb_get_band_navigator($current){
	global $wpdb,$gnrb_table_artist;
        echo("<ul>");
	
	foreach(range('A','Z') as $letter){
                if($letter==$current)
                        print("<li><a class=\"currentartistpage\" href=\"".get_option('site_url')."/artists?start=$letter\">$letter</a></li>");
                else
                        print("<li><a href=\"".get_option('site_url')."/artists?start=$letter\">$letter</a></li>");
        }
	
	echo("</ul>");
}

function gnrb_get_bands_shortdata($start) {
          global $wpdb,$gnrb_table_artist,$gnrb_table_meta;
        //$start=$start*10;                                                                                                                                                        

//        $sql = "select user_id,user_nicename from wp_gnrb_artist, wp_users where wp_gnrb_artist.user_id=wp_users.id and wp_users.user_nicename like '$start%' order by wp_users.user_nicename";
        
        $sql = "select id,name,nicename from wp_gnrb_artist where name like '$start%' order by name";
        //echo($sql);
        $res = $wpdb->get_results($sql);


        foreach( $res as $row ) {
            //echo "<div style=\"position:relative;clear:both;padding-top:10px;\">";
            //echo("<li>");
            //$content = gnrvp_get_artist_info($row->id,$row->name);
            //    $band_name = gnrvp_get_artist_name($row->user_id);
            $sql="Select field_key,field_value from $gnrb_table_meta where (field_key='band_genre' or field_key='band_icon' or field_key='band_bio') and artist_id=$row->id";
            $results = $wpdb->get_results($sql);
            //print_r($results);
            if($results){
                $genre=null;
                $bio=null;
                foreach($results as $result){
                        if($result->field_key=="band_genre"){
                                if($genre)
                                        $genre = $genre .", ". $result->field_value;
                                else
                                        $genre=$result->field_value;
                        }else if($result->field_key=="band_bio"){
                                $pos=strpos($result->field_value,".",200);
                                $bio=substr($result->field_value,0,$pos);
                                $bio=$bio." <a href='" . get_option('site_url')."/artist/".$row->nicename . "'>More</a>";
                        }else
                                $band_icon = get_option("gnrb_band_pics_path") . $result->field_value;        
                }
            }?>
                <div class="post">
                        <h1><a href="<?php echo get_option('site_url')."/artist/".$row->nicename?>"><?php echo $row->name?></a></h1>
                        <?php if($band_icon){ ?>
                        <div class="profile_photo"><a href="<?php echo get_option('site_url')."/artist/".$row->nicename?>"><img src="<?php echo $band_icon; ?>" alt="Artist Icon" /></a></div>
                        <?php } ?>
                        <div style="clear:both">
                        <?php if($genre){ ?>
                        <p><strong>Genre: </strong><?php echo $genre ?></p>
                        <?php } ?>  
                        <p><strong>Bio: </strong><?php echo $bio ?></p>
                        </div>
                </div>
                <p style="clear:both"></p>
                <div class="postmeta2">
                        <p>
                                <span class="listenbutton"><a href="<?php echo get_option('site_url')."/artist/".$row->nicename?>">Listen Now</a></span>
                        </p>
                </div>
        
            <?php //echo $content;
            //echo("<div style=\"position:relative;clear:both;border-bottom:1px solid #f2f1f1;margin-bottom:10px;padding-top:10px\">");
            //echo("</div>");
        }
}

function gnrb_get_bands_featured() {
          global $wpdb,$gnrb_table_artist;
        //$start=$start*10;                                                                                                                                                        

        $sql = "select id,name from wp_gnrb_artist where featured=1 limit 0,3";
        $res = $wpdb->get_results($sql);

        $featured_artists=array();
        foreach( $res as $row ) {
            //echo "<div style=\"position:relative;clear:both;padding-top:10px;\">";
            //echo("<li>");
            $content = gnrvp_get_artist_featured_info($row->id,$row->name);
            $featured_artists[]=$content;
            //    $band_name = gnrvp_get_artist_name($row->user_id);                                                                                                               
            //echo $content;
            //echo("<div style=\"position:relative;clear:both;border-bottom:1px solid #f2f1f1;margin-bottom:10px;padding-top:10px\">");
            //echo("</div>");
        }
        return $featured_artists;
}

function gnrvp_get_artist_featured_info($user_id,$band_name){
        global $wpdb, $gnrb_table_meta, $gnrb_table_fields, $gnrb_table_artist, $gnrb_table_meta, $gnrb_table_bandmembers;

        $sql = "SELECT field_key, field_value 
				FROM $gnrb_table_meta
				WHERE 
				artist_id = $user_id;";
        //echo($sql);
	$result = $wpdb->get_results($sql);
	$output = "";
	$format = stripslashes(get_option("gnrb_band_bio_format"));
	// Define variables
        $genres="";
	foreach( $result as $row ) {
                extract(array("$row->field_key"=>"$row->field_value"));
                if($row->field_key=="band_genre")
                        $genres=$genres."/ ".$row->field_value;
        }
        $genres=trim($genres,"/");
	//$band_bio = strip_tags($band_bio, get_option("gnrb_allowed_tags"));
	if( $band_image_url == "http://" or $band_image_url == "" ) {
                //if()
                $band_image = get_option("gnrb_band_pics_path") . $band_image;
        }else {
                $band_image = $band_image_url;
        }
	$band_icon = get_option("gnrb_band_pics_path") . $band_icon;
        $band_url=get_option('site_url')."artist/".sanitize_title_with_dashes($band_name);
        return array("genre"=> $genres,"image"=> $band_icon, "name"=>$band_name,"url"=>$band_url);
        //$bio="<div id=\"hpcapsule\">";
        //if($band_image == "http://test.radioverve.com/new/band_pics/")
        //        $bio = $bio. "<h3>$band_name</h3> ";
        //else{
        //        if($genres=="")
        //                $bio = $bio. "<a href=\"$user_url\"><img src=\"$band_image\"/></a><div id=\"hpcapcontent\"><a href=\"$user_url\"><h3>$band_name</h3></a></div></div>\n";
        //        else
        //                $bio = $bio. "<a href=\"$user_url\"><img  src=\"$band_image\"/></a><div id=\"hpcapcontent\"><a href=\"$user_url\"><h3>$band_name</h3></a><p>$genres</p></div></div>\n";
        //}

        //return $bio;
}

function gnrb_get_artists_icecast($current_id = null) {
	global $wpdb;
	switch_to_icecast_db();
	$sql = "SELECT id, name FROM track_artists ORDER BY name";
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
	$sql = "SELECT name FROM track_artists WHERE id = $current_id LIMIT 1";
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

function get_player_code($userid) {
        global $wpdb, $gnrb_table_artist;

        $sql = "SELECT centova_artistid from $gnrb_table_artist where id=$userid";
        $userid=$wpdb->get_var($sql);
        //      echo("What the?".$sql." ".$userid);
        /*$artist_player='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"      
			id="rvartistplayer" width="358" height="40" 
			codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab">        
                        <param name="movie" value="/player/artistplayer/RVartistplayer.swf" />               
                        <param name="quality" value="high" />     
                        <param name="bgcolor" value="#FFFFFF" />        
                        <param name="allowScriptAccess" value="sameDomain" />   
                        <param name=FlashVars values="' .$userid .'" />     
                        <embed src="/player/artistplayer/RVartistplayer.swf" FlashVars="artistid='.$userid.'" quality="high" bgcolor="#FFFFFF"   \
			width="358px" height="40px" name="RVartistplayer" align="middle" 
			play="true"     
			loop="false"    
			quality="high"  
			allowScriptAccess="sameDomain"  
			type="application/x-shockwave-flash"    
			pluginspage="http://www.adobe.com/go/getflashplayer">   
			</embed>        
                        </object>';*/
        return $userid;
}

function get_band_albums($artistid) {
        global $wpdb,  $gnrb_table_artist;
	
        $sql="select artistid from $gnrb_table_artist where user_id=$artistid";
        $artistid=$wpdb->get_var($sql);
        //echo($artistid);
        switch_to_icecast_db();
        $sql1="select id from track_artists where name=(Select name from track_artists where id='$artistid')";
        $result1 = mysql_fetch_assoc(mysql_query($sql1));
        $condition="";
        print_r($result1);
                
        foreach($result1 as $id){
                echo("Hmmm...");
                if($condition==""){
                    $condition="artistid= ".$id['id'];
                }else
                    $condition=$condition . " or " . "artistid= ".$id['id'];
        }
        echo($condition);
        $sql="select DISTINCT(name),releaseyear from tracks where ".$condition;
        $result=$wpdb->get_results($sql);
        $albumdata="<ul>\n";
        foreach($result as $albums){
                $albumdata=$albumdata."<li><b>$albumdata->name</b> $albumdata->releaseyear</li>";
        }
        $albumdata="</ul>";
        switch_to_wp_db();
        return $albumdata;
}

function gnvrp_get_band_bio($artist_id){
        global $wpdb, $gnrb_table_meta, $gnrb_table_fields, $gnrb_table_artist, $gnrb_table_meta, $gnrb_table_bandmembers;
	
	$bio = "<h2>Uh oh!</h2><p>Looks like the RV admins did not do their homework on this band.</p><p>If you have got info on this band then use the form below to alert the admins.</p>";
	
        $sql = "select user_id from wp_gnrb_artist where artistid=$artist_id";
        $userid=$wpdb->get_var($sql);
        $sql = "select field_value from wp_gnrb_meta where user_id=$userid and field_key='band_bio'";
        $results = $wpdb->get_var($sql);
        if($results){
                $results = str_replace("\n", "", $results);
                $bio="<p>".substr($results,0,180)."</p>"."<a href=\"event:\">More...</a>";
        }
        return $bio;
}

function gnrvp_get_artist_events($userid){
        global $wpdb;
        
        //$results = $wpdb->get_results("select * from wp_gigpress_shows,wp_gigpress_artistshow where wp_gigpress_shows.show_id=wp_gigpress_artistshow.show_id and wp_gigpress_artistshow.user_id=$userid");
        $results = $wpdb->get_results("select show_venue,show_title,show_date,show_time,show_locale from wp_gigpress_shows,wp_gigpress_artistshow where wp_gigpress_shows.show_id=wp_gigpress_artistshow.show_id and wp_gigpress_artistshow.artist_id=$userid and show_status='active'");
        //echo("select show_venue,show_title,show_date,show_time,show_location from wp_gigpress_shows,wp_gigpress_artistshow where wp_gigpress_shows.show_id=wp_gigpress_artistshow.show_id and wp_gigpress_artistshow.artist_id=$userid");
        return $results;
        //echo("<ul>");
        //foreach($results as $result){
        //        echo("<li><b>$result->show_title</b><div>$result->show_venue, $result->show_date, $result->show_time<div><div>$result->show_locale</div></li>");
        //}
        //echo("</ul>");
}

/* Two functions below are probably extra for no good reason*/
function gnrvp_get_band_info($user_id,$name) {
	//returns the wordpress user's info for $artist_id
	global $wpdb, $gnrb_table_meta, $gnrb_table_fields, $gnrb_table_artist, $gnrb_table_meta, $gnrb_table_bandmembers;
	
	$no_band_data = "<h2>Uh oh!</h2><p>Looks like the RV admins did not do their homework on this band.</p><p>If you have got info on this band then use the form below to alert the admins.</p>";
	
	if( $user_id ) {
                $sql = "Select field_value from $gnrb_table_meta where artist_id='$user_id' and field_key='band_bio'";
                $bio=$wpdb->get_var($sql);
                $sql = "Select field_value from $gnrb_table_meta where artist_id='$user_id' and field_key='band_image'";
                $img_results=$wpdb->get_results($sql);
                $sql = "Select field_value from $gnrb_table_meta where artist_id='$user_id' and field_key='band_video'";
                $video_results=$wpdb->get_results($sql);
                $sql = "Select member_name,member_type from $gnrb_table_bandmembers where artist_id='$user_id'";
                $band_members=$wpdb->get_results($sql);
                $sql = "Select field_value from $gnrb_table_meta where field_key='band_icon' and artist_id='$user_id' limit 0,1";
                $band_icon=$wpdb->get_var($sql);
                if($band_icon)
                        $band_icon = get_option("gnrb_band_pics_path") . $band_icon;

                return array("Bio"=>$bio,"Pictures"=>$img_results,"Videos"=>$video_results,"Band-Members"=>$band_members,"Band-Icon"=>$band_icon);
		// Generate band members list
		/*$sql = "SELECT field_key, field_value 
				FROM $gnrb_table_meta
				WHERE 
					artist_id = $user_id;";
		$result = $wpdb->get_results($sql);
		$output = "";
		$format = stripslashes(get_option("gnrb_band_bio_format"));
		// Define variables
		foreach( $result as $row ) extract(array("$row->field_key"=>"$row->field_value"));
		$band_bio = strip_tags($band_bio, get_option("gnrb_allowed_tags"));
		if( $band_image_url == "http://" or $band_image_url == "" ) { $band_image = get_option("gnrb_band_pics_path") . $band_image; }
		else { $band_image = $band_image_url; }
		$band_icon = get_option("gnrb_band_pics_path") . $band_icon;
                //$albumsdata=get_band_albums($user_id);
                //echo($albumdata);
                //$artist_player = get_player_code($user_id);
		if($band_icon == "http://radioverve.com/band_pics/")
			$bio = "$artist_player"; //Put a dummy picture here
		else
			$bio = "<div id=\"imageplayer\"><img class=\"alignleft\" src=\"$band_icon\" />
			";
                // echo $$band_members_list
		$band_bio = str_replace("\n", "<br />", $band_bio);

		if($band_bio!="") {
			$bio .= "<div id=\"contentTop\"></div><div id=\"contentMiddle\" >

			<div class=\"entry\"><div id=\"articleContainer\"><p style=\"clear:both;\">$band_bio</p>";
		}else {
			$bio .= $no_band_data;
		}

		if($band_url != "http:" and $band_url != "" and $band_email){
			if($band_email) $bio .= "<p>Email <a href=\"mailto:$band_email\" target=\"blank\">$band_email</a>";
			if($band_url != "http:" and $band_url != "") $bio .= "<br />Website <a href=\"$band_url\" target=\"blank\">$band_url</a>"; 
		}
		$bio .= "</p>";

		$bio = str_replace("'", "\'", $bio);
		return $bio;*/
	} else {
		return $no_band_data;
	}
}

function gnrvp_get_band_name($user_id) {
	//returns the wordpress user's info for $artist_id
	global $wpdb, $gnrb_table_meta, $gnrb_table_fields, $gnrb_table_artist, $gnrb_table_meta, $gnrb_table_bandmembers;

	$no_band_data = "Untitled";
	
	if( $user_id ) {
		// Generate band members list
		$sql = "SELECT * FROM $gnrb_table_bandmembers
				WHERE user_id = $user_id";
		$members = $wpdb->get_results($sql);
		$bandm = "<ul class=\"bandmembers\">";
		foreach( $members as $m ) {
			$bandm = $bandm . "<li><b>$m->member_name</b> <span>$m->member_type</span></li>";
		}
		$bandm = $bandm . "</ul>";
		$band_members_list = $bandm;
		$sql = "SELECT field_key, field_value 
				FROM $gnrb_table_meta
				WHERE 
					user_id = $user_id;";
		$result = $wpdb->get_results($sql);
		$output = "";
		$format = stripslashes(get_option("gnrb_band_bio_format"));
		// Define variables
		foreach( $result as $row ) extract(array("$row->field_key"=>"$row->field_value"));
		return $band_name;

	} else {
		return $no_band_data;
	}
}

function rv_get_artist($user_id) {
	//returns list of artists under the user
	global $wpdb, $gnrb_table_artist;
	
	$no_data = "no-data";
	
	if ($user_id) {
		//fetch the list of artists
		$sql = "SELECT id FROM $gnrb_table_artist
				WHERE user_id= $user_id";
		$artists = $wpdb->get_results($sql);
		foreach ($artists as $artist) {
			$a[] = $artist->id;
		}
		//return the list of atrist id's
		return $a;
	} else {
		return $no_data;	
	}
	
}

function rv_get_artistname($artist_id) {
	//returns list of artists under the user
	global $wpdb, $gnrb_table_artist;
	
	$no_data = "";
	
	if ($artist_id) {
		//fetch the list of artists
		$sql = "SELECT firstname FROM $gnrb_table_artist
				WHERE id= $artist_id";
		$artist_name = $wpdb->get_var($sql);
		//die($artist_name);
		//return the name of atrist
		return $artist_name;
	} else {
		return $no_data;	
	}
	
}

function rv_is_valid_owner_for_artist($user_id="", $artist_id="") {
	//returns true, if the owner & artist match
	global $wpdb, $gnrb_table_artist;
	
	if($user_id) {
		//get the artist_id
		$sql = "SELECT id FROM $gnrb_table_artist
				WHERE user_id = $user_id and id = $artist_id";
		$temp = $wpdb->get_var($sql);
		
		if ($temp === NULL) {
			//Invalid owner
			return FALSE;
		} else {
			//Valid owner
			return TRUE;
		}
	} else {
		//Invalid user
		return FALSE;
	}
}

function rv_add_artist_music($artist_id, $music_array) {
	//Add's music details to the gnrb_meta table
	global $wpdb, $gnrb_table_meta, $gnrb_table_artist;
	$field_list = "id, artist_id, field_key, field_id, field_value";
	for($i=0; $i<sizeof($music_array); $i++) {
		$insert = "INSERT INTO $gnrb_table_meta($field_list) VALUES( '', $artist_id, 'band_music', 10, '" . $music_array[$i] . "');";
		$results = $wpdb->query($insert);
	}
	
        //Update the artists table with song count
        $update = "UPDATE $gnrb_table_artist
                        SET songcount = " . rv_is_music_present($artist_id)  . " where id = $artist_id";
        $result = $wpdb->query($update);
        
}

function rv_is_music_present($artist_id) {
	//returns the number of files an artists has 
	global $wpdb, $gnrb_table_meta;
	
	if($artist_id) {
		//get the artist_id
		$sql = "SELECT count(id) FROM $gnrb_table_meta
				WHERE artist_id = $artist_id";
		$temp = $wpdb->get_var($sql);
                if ($temp > 0) {
                        return $temp;
                } else {
                        return 0;
                }
        }
}

function rv_show_artist_music($artist_id) {
	//returns the number of files an artists has 
	global $wpdb, $gnrb_table_meta;
	$show_string = '';
        
	if($artist_id) {
		//get the artist_id
		$sql = "SELECT id, field_value FROM $gnrb_table_meta
				WHERE artist_id = $artist_id and field_key = 'band_music'";
		$temp = $wpdb->get_results($sql);
                foreach ($temp as $music_array) {
                        $n = explode("/", $music_array->field_value);
                        $val = $n[sizeof($n)-1];
                        $show_string .= "<li><a href='" . get_option("siteurl") . "/" . $music_array->field_value . "'>" . $val . "</a></li>";
                }
        }
        
        return $show_string;
}

// Hook to actions
add_action("activate_{$gnrb_plugin_folder}/{$gnrb_plugin_folder}.php", "gnrb_install");
add_action("admin_menu", "gnrb_admin");
?>