<?php if(isset($_POST["gnrb_add_field"])) gnrb_message("Field create"); ?>
<?php if(isset($_POST["gnrb_edit_field"])) gnrb_message("Field edit"); ?>
<?php if(isset($_POST["gnrb_delete_field"])) gnrb_message("Field delete"); ?>
<?php if(isset($_POST["gnrb_update_options"])) gnrb_message("Update options"); ?>
<div class="wrap">
	<h2>Band Creator Options</h2>
	<form action="" method="post">
		<fieldset>
			<table class="optiontable">
				<tbody>
				<tr>
					<th scope="row" valign="top">Allow User Edit</th>
					<td>
						<input type="checkbox" name="gnrb_allow_edit" value="true" <?php echo get_option("gnrb_allow_edit") == 1 ? "checked=\"checked\"" : ""; ?> />
						Allow registered users to edit their band profiles
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top">Band Member Roles</th>
					<td>
						<textarea name="gnrb_bandmember_role" rows="5" cols="50"><?php echo get_option("gnrb_bandmember_role"); ?></textarea>
						<p>Create as many band member roles as you wish. Enter a comma separated list.<br/>
						<b>e.g.</b> Vocal, Lead Guitar, Rhythm Guitar, Guitar</p>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top">Band Bio Formatting</th>
					<td>
						<textarea name="gnrb_band_bio_format" rows="15" cols="80"><?php echo stripslashes(get_option("gnrb_band_bio_format")); ?></textarea>
						<p>
							PHP code for formtting for the band bio page. <font color="red">PHP code must be valid</font>, else it may generate an error. Use "Field Key" values as <span class="code">$field_key_value</span> below and plain HTML tags. <br/>
							<b>e.g.</b>
							<span class="code">
								<?php 
								echo htmlentities('echo "
								<h4>$band_name</h4>
								<img src=\"$band_image\" />
								$band_members_list
								<p>$band_bio</p>
								<h5>Contact</h5>
								<ul>";
								if($band_email) echo "<li>Email <b>$band_email</b></li>";
								if($band_url) echo "<li>Website <b>$band_url</b></li>";
								echo "</ul>";');
								?>
							</span>
						</p>
					</td>
				</tr>				
				<tr>
					<th scope="row" valign="top">Allowed Tags for Band Bio</th>
					<td>
						<input type="text" name="gnrb_allowed_tags" size="80" value="<?php echo get_option("gnrb_allowed_tags"); ?>" />
						<p>Tags to be allowed for formating the band bio for users</p>
					</td>
				</tr>				
				<tr>
					<td colspan="2" class="submit">
						<input type="hidden" name="page" value="<?php echo $gnrb_plugin_folder; ?>" />
						<input type="submit" name="gnrb_update_options" value="Update Options &raquo;" />
					</td>
				</tr>
			</table>
		</fieldset>
	</form>
</div>
<div class="wrap">
	<h2>Manage Band Fields</h2>
	<table class="widefat">
		<thead>
			<tr>
				<th scope="col">ID</th>
				<th scope="col">Field Key</th>
				<th scope="col">Order</th>
				<th scope="col">Name</th>
				<th scope="col">Description</th>
				<th scope="col">HTML Field Tag</th>
				<th scope="col">Tag Options</th>
				<th scope="col">Default Value</th>
				<th scope="col">Allow Edit</th>
				<th scope="col">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			// Generate fields table
			$sql = "SELECT * FROM $gnrb_table_fields";
			$result = $wpdb->get_results($sql);
			$alternate = "";
			foreach( $result as $f ) {
				$select_type = gnrb_get_fieldtype_select($f->field_type);
				$editable = $f->field_editable ? "checked=\"checked\"": "";
				echo "
					<tr$alternate>
						<form action=\"\" method=\"post\">
							<td>$f->id</td>
							<td>$f->field_key</td>
							<td><input name=\"field_order\" type=\"text\" size=\"3\" value=\"$f->field_order\" /></td>
							<td><input name=\"field_name\" size=\"10\" type=\"text\" value=\"$f->field_name\" /></td>
							<td><input name=\"field_descr\" type=\"text\" value=\"$f->field_descr\" /></td>
							<td>
								<select name=\"field_type\">
									$select_type
								</select>
							</td>
							<td><input name=\"field_type_data\" type=\"text\" size=\"10\" value=\"$f->field_type_data\" /></td>
							<td><input name=\"field_default\" type=\"text\" size=\"10\" value=\"$f->field_default\" /></td>
							<td><input name=\"field_editable\" type=\"checkbox\" value=\"on\" $editable /></td>
							<td class=\"submit\">
								<input type=\"hidden\" name=\"page\" value=\"$gnrb_plugin_folder\" />
								<input type=\"hidden\" name=\"field_id\" value=\"$f->id\" />
								<input type=\"submit\" name=\"gnrb_edit_field\" value=\"Modify\" />
								<input type=\"submit\" class=\"delete\" name=\"gnrb_delete_field\" value=\"Delete\" />
							</td>
						</form>
					</tr>";
				$alternate = ($alternate == "") ? " class=\"alternate\"" : "";
			}
			?>
			<tr<?php echo $alternate; ?>>
				<form action="" method="post">
					<td></td>
					<td><input name="field_key" type="text" size="10" value="" /></td>
					<td><input name="field_order" type="text" size="3" value="" /></td>
					<td><input name="field_name" size="10" type="text" value="" /></td>
					<td><input name="field_descr" type="text" value="" /></td>
					<td>
						<select name="field_type">
							<?php echo gnrb_get_fieldtype_select(); ?>
						</select>
					</td>
					<td><input name="field_type_data" type="text" size="10" value="" /></td>
					<td><input name="field_default" type="text" size="10" value="" /></td>
					<td><input name="field_editable" type="checkbox" /></td>
					<td class="submit">
						<input type="hidden" name="page" value="<?php echo $gnrb_plugin_folder; ?>" />
						<input type="submit" name="gnrb_add_field" value="Add" />
					</td>
				</form>
			</tr>
		</tbody>
	</table>
</div>