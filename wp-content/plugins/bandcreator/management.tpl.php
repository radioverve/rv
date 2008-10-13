<div class="wrap">
	<h2>Band Management</h2>
	<p>Click on a band/user login name to edit it</p>
	<ul class="bandslist">
		<?php $num_bands = gnrb_get_bands_list();?>
	</ul>
	<?php if(!$num_bands) echo "You have no bands. Please create users and associate them with bands"; ?>
</div>
<?php if( $user_id ) include_once($gnrb_abspath."profile.tpl.php"); ?>
<?php if(isset($_POST["gnrb_create_band_user"])) gnrb_message("Create User/Band association"); ?>
<?php if(isset($_POST["gnrb_del_dead_bands"])) gnrb_message("Dead band associations delete"); ?>
<div class="wrap">
	<h2>Unassociated Bands</h2>
	<?php
	if(isset($_POST["gnrb_show_unassoc_bands"])) {
		gnrb_unassociated_bands();
	} else {
		//$num_dead = gnrb_get_num_dead_bands();
		echo "
		<form action=\"\" method=\"post\">
			<input type=\"hidden\" name=\"page\" value=\"$gnrb_plugin_folder\" />
			<input type=\"submit\" name=\"gnrb_show_unassoc_bands\" value=\"Show Unassociated Bands &raquo;\" />";
		//if($num_dead) echo "<input type=\"submit\" onclick=\"return confirm('Are you sure you want to delete the dead associations? This cannot be undone');\" name=\"gnrb_del_dead_bands\" value=\"Delete Dead Bands (found $num_dead) &raquo;\" />";
		echo "</form>";
	}
	?>
</div>
