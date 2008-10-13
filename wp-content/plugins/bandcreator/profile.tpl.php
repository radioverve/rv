<?php if(isset($_POST["gnrb_edit_band"])) gnrb_message("Profile update"); ?>
<?php if(isset($_POST["gnrb_member_edit"])) gnrb_message("Member edited"); ?>
<?php if(isset($_POST["gnrb_member_add"])) gnrb_message("Member added"); ?>
<div class="wrap">
	<h2>Band Profile &raquo; <?php echo $user_login; ?></h2>
	<form action="" method="post" enctype="multipart/form-data">
		<table class="optiontable">
			<tbody>
				<?php 
				// Get current user
				global $userdata;
    			get_currentuserinfo();

			    echo('Username: ' . $user_id . "\n");
		      	//echo('User level: ' . $userdata->user_level . "\n");
			    //echo('User ID: ' . $userdata->ID . "\n");

				$sql = "SELECT field_id, field_value FROM $gnrb_table_meta WHERE artist_id = $user_id;";
				//echo "User id is".$user_id;
				$user_data = $wpdb->get_results($sql);
				$featured=false;
				//print_r($user_data);
				function gnrb_get_field_value($result, $field_id){
					//print_r($field_id);
					//echo("<BR />");
					//print_r($result);
					$genre="";
					foreach($result as $r ) {
						if( $r->field_id == $field_id ) {
							if($field_id!=9)
								return $r->field_value;
							else
								$genre=$genre.",".$r->field_value;
						}
					}
					if($genre!="")
						return trim($genre,",");
					return false;
				}
				
				
				if($is_admin) {
					// Show artistid
					$sql = "SELECT centova_artistid,featured FROM $gnrb_table_artist WHERE id = $user_id LIMIT 1;";
					$artists = $wpdb->get_results($sql);
					foreach($artists as $artistid){
						$field = $artistid->artistid . " &raquo; " . gnrb_get_artistname_icecast($artistid->centova_artistid);
						//echo("Huh huh".$artistid->featured);
						if($artistid->featured==1)
							$featured=true;
					}
					
					echo "<tr>
							<th scope=\"row\" valign=\"top\"><label>Artistid</label></th>
							<td><b>$field</b> - This links the current user with your icecast database.</td>
						</tr>";
				}
				// Generate band profile options
				$sql = "SELECT * FROM $gnrb_table_fields";
				if(!$is_admin)$sql = $sql . " WHERE field_editable = 1";
				$sql = $sql . " ORDER BY field_order;";
				$results = $wpdb->get_results($sql);
				foreach( $results as $f ) {
					$label = $f->field_name;
					$field = "";
					$value = gnrb_get_field_value($user_data, $f->id);
					$value = $value ? $value : $f->field_default;
					$data = $f->field_type_data;
					$descr = $f->field_descr;
					$name = "name=\"field$f->id\"";
					switch( $f->field_type ) {
						case "text":
							if( $f->field_name == "band_image_url" or $f->field_name == "band_icon_url") echo "<img src=\"$value\" /><br /><br />";
							$field = "<input type=\"text\" $name size=\"$data\" value=\"$value\" />";
							break;
						case "checkbox":
							$checked = $value ? 'checked="checked"' : '';
							$field = "<input type=\"checkbox\" $name $checked />";
							break;
						case "textarea":
							$data = explode(",", $data);
							$data[0] = trim($data[0]);
							$data[1] = trim($data[1]);
							$field = "<textarea $name cols=\"$data[0]\" rows=\"$data[1]\">$value</textarea>";
							break;
						case "select":
							$options = gnrb_get_fieldtype_select($value, $data);
							$field = "<select $name>$options</select>";
							break;
						case "file":
							$field = "";
							srand(time());
							$random = (rand()%1000);
							if( $value != "" ) $field = "<img class=\"$f->field_key\" src=\"{$gnrb_upload_path_virtual}{$value}?rand=$random\" /><br /><br />";
							$field = $field . "<input type=\"file\" size=\"50\" $name />
										<input type=\"hidden\" name=\"field{$f->id}options\" value=\"$data\" />";
							break;
					}
				
					echo "
						<tr>
							<th scope=\"row\" valign=\"top\"><label for=\"field$f->id\">$label</label></th>
							<td>$field<br />$descr</td>
						</tr>";
				}
				?>
				<tr>
					<th scope=\"row\" valign=\"top\"><label for=\"featured\">Featured</label></th>
					
					<td><input name="featured" type="checkbox" <? if ($featured==true) {echo('checked="true"');} ?> ><br />Is this band featured on Main Page?</td>
				</tr>
				<tr>
					<td colspan="2" class="submit">
						<select name="import_band_fromdb">
							<option value='-1'></option> 
						<?
							function gnrb_import_data(){
								global $wpdb;
								//switch_to_import_db();
								$sql="Select user_id,field_value from old_wp_gnrb_meta where field_key='band_name' order by field_value";
								$result = $wpdb->get_results($sql);
								foreach($result as $obj) {
									echo("<option value='$obj->user_id'>$obj->field_value</option>");
								}
								//print_r($wpdb);
								//gnrb_message($wpdb);
								//switch_to_wp_db();
							}
							gnrb_import_data();
						?>
						</select>
						
						<input type="hidden" name="user_login" value="<?php echo $user_login; ?>" />
						<input type="hidden" name="artistid" value="<?php echo $user_id; ?>" />
						<input type="hidden" name="user_id" value="<?php echo $userdata->ID; ?>"/>
						<input type="hidden" name="page" value="<?php echo $gnrb_plugin_folder; ?>" />
						<input type="submit" name="gnrb_edit_band" value="Update Profile &raquo;" />
					</td>
				</tr>
			</tbody>
		</table>
	</form>
	<h2>Band Members</h2>
	<p>To select multiple roles for the member, hold down the "Ctrl" key and click on the roles</p>
	<table class="widefat">
		<thead>
			<th scope="col">ID</th>
			<th scope="col">Name</th>
			<th scope="col">Roles</th>
			<th scope="col">Artist Bio</th>
			<th scope="col">Actions</th>
		</thead>
		<tbody>
			<?php 
			// Generate members table
			$sql = "SELECT * FROM $gnrb_table_bandmembers WHERE artist_id = $user_id";
			$members = $wpdb->get_results($sql);
			$alternate = "";
			function gnrb_member_role_options($current_role = null){
				$options = "";
				$roles = explode(", ", get_option("gnrb_bandmember_role"));
				$current_role = explode(", ", $current_role);
				$index = 0;
				foreach( $roles as $role ) {
					$found = false;
					if( $index < strlen($current_role) ) {
						$found = ($current_role[$index] == $role) ? 1 : 0;
						$index += $found;
					}
					$selected = $found ? "selected=\"selected\"" : "";
					$options = $options . "<option value=\"$role\" $selected>$role</option>";
				}
				return $options;
			}
			foreach( $members as $m ) {
				$options = gnrb_member_role_options($m->member_type);
				$member_descr = stripslashes($m->member_descr);
				echo "
				<tr$alternate>
					<form action=\"\" method=\"post\">
						<td>$m->id</td>
						<td><input type=\"text\" name=\"member_name\" value=\"$m->member_name\" /></td>
						<td>
							<select name=\"member_type[]\" multiple=\"multiple\" size=\"5\">
								$options
							</select>
						</td>
						<td><textarea name=\"member_descr\" rows=\"5\" cols=\"40\">$member_descr</textarea></td>
						<td class=\"submit\">
							<input type=\"hidden\" name=\"page\" value=\"$gnrb_plugin_folder\" />
							<input type=\"hidden\" name=\"user_id\" value=\"$user_id\" />
							<input type=\"hidden\" name=\"member_id\" value=\"$m->id\" />
							<input type=\"hidden\" name=\"user_login\" value=\"$user_login\" />
							<input type=\"submit\" name=\"gnrb_member_edit\" value=\"Modify &raquo;\" />
							<input type=\"submit\" class=\"delete\" name=\"gnrb_member_delete\" value=\"Delete\" />
						</td>
					</form>
				</tr>";
				$alternate = ($alternate == "") ? " class=\"alternate\"" : "";
			}
			$options = gnrb_member_role_options();
			echo "
				<tr$alternate>
					<form action=\"\" method=\"post\">
						<td>&nbsp;</td>
						<td><input type=\"text\" name=\"member_name\" /></td>
						<td>
							<select name=\"member_type[]\" multiple=\"multiple\" size=\"6\">
								$options
							</select>
						</td>
						<td><textarea name=\"member_descr\" rows=\"5\" cols=\"40\"></textarea></td>
						<td class=\"submit\">
							<input type=\"hidden\" name=\"page\" value=\"$gnrb_plugin_folder\" />
							<input type=\"hidden\" name=\"user_id\" value=\"$user_id\" />
							<input type=\"hidden\" name=\"user_login\" value=\"$user_login\" />
							<input type=\"submit\" name=\"gnrb_member_add\" value=\"Add\" />
						</td>
					</form>
				</tr>";
			?>
		</tbody>
	</table>
</div>