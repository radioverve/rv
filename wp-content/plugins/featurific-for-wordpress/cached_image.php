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
 * This file is used to serve up the XML for Featurific **IF AND ONLY IF** the
 * Featurific image_cache directory can't be written to.  (That's where we normally
 * put the cached images.  Rather than require the user to mess with directory
 * ownership and permissions, we just serve up the cached images dynamically.
 * It's not as fast or easy on the server, but at least it works with zero
 * required configuration! (And that's our goal with this plugin)
 */

//Load up the Wordpress environment
include_once('../../../wp-config.php');
include_once('featurific_db.php');

//Spit out the image
$snum = $_GET['snum'];
$inum = $_GET['inum'];

//Check for invalid input (non-integers for either snum or inum, or an snum/inum tuple that corresponds to a non-existent option)
if(($row = featurific_image_cache_get_image($snum, $inum))===FALSE) {
	echo "Featurific for Wordpress error: Invalid input.";
	return;
}

//Set the mime type and spit out the image data.
header('Content-type: '.$row->mimetype);
echo $row->image;

?>