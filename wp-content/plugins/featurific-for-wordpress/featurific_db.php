<?php
/*
  This file is part of Featurific For Wordpress.

  Copyright 2008  Rich Christiansen  (rich at <please don't spam me> byu period net)

  Featurific For Wordpress is free software: you can redistribute it and/or modify
  it under the terms of the GNU Lesser General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  Featurific For Wordpress is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License
  along with Featurific For Wordpress.  If not, see <http://www.gnu.org/licenses/>.

	Featurific (Free, Pro, etc) is not released under the GNU Lesser General Public
	License.  It is released under the license contained in license.txt.  For details
	on licensing of Featurific (Free, Pro, etc), please contact Breeze Computer
	Consulting at support@featurific.com.
*/


/**
 * Return the table name for the image cache table.
 *
 * We used to store the table name in a global variable, but found that some
 * LAMP installations simply couldn't 'see' the global variable.  (The variable
 * was empty)  Weird.  So, this is an easy way around the problem.
 */
function featurific_get_image_cache_table_name() {
	global $wpdb;

	return $wpdb->prefix . "featurific_image_cache";
}


/**
 * Create tables necessary for the plugin.
 */
function featurific_create_tables() {
	featurific_create_image_cache_table();
}



/**
 * Create the image cache table.
 */
function featurific_create_image_cache_table() {
	global $wpdb;
	$featurific_image_cache_table_name = featurific_get_image_cache_table_name();
	
	if($wpdb->get_var("show tables like '$featurific_image_cache_table_name'") != $featurific_image_cache_table_name) {
		$sql = "CREATE TABLE " . $featurific_image_cache_table_name . " (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			screen_num tinyint(4) NOT NULL,
			image_num tinyint(4) NOT NULL,
			image longblob NOT NULL,
			mimetype varchar(50),
			UNIQUE KEY id (id)
		);";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		print_r(dbDelta($sql));
	}	
}


/**
 * Check to see if a row exists for the given $snum (screen_number) and
 * $inum (image_number)
 */
function featurific_image_cache_row_exists($snum, $inum) {
	global $wpdb;
	$featurific_image_cache_table_name = featurific_get_image_cache_table_name();
	
	if(!is_numeric($snum) || floatval($snum) != intval(floatval($snum)) ||
	   !is_numeric($inum) || floatval($inum) != intval(floatval($inum)))
		return false;

	return $wpdb->get_var("SELECT COUNT(*) FROM $featurific_image_cache_table_name WHERE screen_num=$snum AND image_num=$inum;")==0?false:true;
}


/**
 * Store an image in the database.
 */
function featurific_image_cache_put_image($snum, $inum, $image, $mimetype) {
	global $wpdb;
	$featurific_image_cache_table_name = featurific_get_image_cache_table_name();

	if(!is_numeric($snum) || floatval($snum) != intval(floatval($snum)) ||
	   !is_numeric($inum) || floatval($inum) != intval(floatval($inum)))
		return false;
	
	$image = addslashes($image); //Escape the binary image data

	//Update the row if one already exists for this $snum and $inum.  Otherwise, insert.
	if(featurific_image_cache_row_exists($snum, $inum)) {
		$num_rows = $wpdb->query("UPDATE $featurific_image_cache_table_name SET image='$image', mimetype='$mimetype' WHERE screen_num=$snum AND image_num=$inum");
	}
	else {
		$num_rows = $wpdb->query("INSERT INTO $featurific_image_cache_table_name SET screen_num=$snum, image_num=$inum, image='$image', mimetype='$mimetype'");
	}
	
	if($num_rows===false) //query() returns false on error, 0 if no result is found
		return false;
	
	return true;
}


/**
 * Get an image from the database for the given $snum (screen_number) and
 * $inum (image_number).
 */
function featurific_image_cache_get_image($snum, $inum) {
	global $wpdb;
	$featurific_image_cache_table_name = featurific_get_image_cache_table_name();
	
	if(!is_numeric($snum) || floatval($snum) != intval(floatval($snum)) ||
	   !is_numeric($inum) || floatval($inum) != intval(floatval($inum)))
		return false;
	
	$row = $wpdb->get_row("SELECT * FROM $featurific_image_cache_table_name WHERE screen_num=$snum AND image_num=$inum;");
	
	if($row==null) //get_row() returns null if no result is found
		return false;
	
	return $row;
}

?>
