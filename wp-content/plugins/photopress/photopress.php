<?php
/*
Plugin Name: Photopress
Plugin URI: http://familypress.net/photopress/
Description: Photopress adds user-friendly image handling tools to Wordpress, including a popup upload and browse tool, a random image template function, and a simple album. Installs and uses a database table.
Version: 0.9
Author: Isaac Wedin
Author URI: http://familypress.net/
*/

/* INSTALLATION
Extract the archive in your WP plugins folder. Create a 'photos' folder in your wp-content folder and make it writable. (You can also try to CHMOD the folder at Maintain:Photopress by clicking Maintain then CHMOD Folder.) Activate the plugin (or de-activate and re-activate if you're upgrading), which will install a new database table and import any old data. Configure the options at Options:Photopress. If you're using permalinks make sure to update your permalink structure at Options:Permalinks. Use the new Photos button on the Quicktags toolbar to upload photos. Enter information about your photos during upload or at Manage:Photopress. Edit pp_album.php and pp_album_css.php to work with your theme if necessary (they're designed to work well with the default theme).
*/

/*  Copyright 2004, 2005  Isaac Wedin (email : isaac@familypress.net)

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

// Include shared functions and defaults
require_once(ABSPATH . 'wp-content/plugins/photopress/include.php');

// Inserts Javascript to launch the Photos button
function pp_popup_js() {
	echo '
<script type="text/javascript">
//<![CDATA[
function pp_popup() {
	var mylink = "' . get_settings('siteurl') . '/wp-content/plugins/photopress/popup.php?action=upload";
	var windowname = "photopress";
	if (! window.focus) return true;
		var href;
	if (typeof(mylink) == "string")
		href = mylink;
	else
		href = mylink.href;
	ppopupwin = window.open(href, windowname, "width=400,height=600,scrollbars=yes,toolbar=no,menubar=no,resizable=yes");
	ppopupwin.focus();
	return false;
}
function setbrowsesort(sort) {
	window.top.location.href = "' . get_settings('siteurl') . '/wp-admin/edit.php?page=photopress.php&" + sort.options[sort.selectedIndex].value;
	return false;
}
//]]>
</script>
	';
}

add_action('admin_head','pp_popup_js');

// Adds the Photos button to the Quicktags menu. Borrowed from ContactForm, which I think borrowed from Alex King's JS Quicktags. In WP 2.x it suppresses the built-in uploader and adds a button to the top of the right column.
function pp_insert_button() {
	echo '
<script type="text/javascript">
//<![CDATA[
	if (document.getElementById("quicktags") && !document.getElementById("postdivrich")) {
		document.getElementById("quicktags").innerHTML += "<input type=\"button\" class=\"ed_button\" id=\"ed_upload\" value=\"Photos\" onclick=\"return pp_popup();\" />";
	}
//	if (document.getElementById("uploading")) {
//		document.getElementById("uploading").style.display = "none";
//	}
	if (document.getElementById("postdivrich")) {
		var pp_div = document.createElement("p");
		pp_div.setAttribute("class","submit");
		pp_div.innerHTML = "<input type=\"button\" class=\"ed_button\" id=\"ed_upload\" value=\"Photos\" onclick=\"return pp_popup();\" />";
		var parent_div = document.getElementById("moremeta");
		var targ_div = document.getElementById("grabit");
		parent_div.insertBefore(pp_div,targ_div);
	}
//]]>
</script>
	';
}

add_action('admin_footer', 'pp_insert_button');

// random image function. given $category_slug and $type it returns an array of random images, formatted by $type: 1 = linked thumb, 2 = unlinked thumb, 3 = file name; CSS class can be set by passing a third argument (did this to get proper class on the main album page)
function pp_random_image($category_slug='', $type = 1, $style = 'random', $number = 1) {
	global $pp_options, $table_prefix, $wpdb;
	$table_name = $table_prefix . 'photopress';
	if (empty($category_slug) && $count = pp_count()) {
		if ($count <= $number) { // if too many are requested just return all of them
			$number = $count;
			$rand = 0;
		} else {
			$rand = rand(0,$count - $number); // spot in list to start getting images
		}
		$random_image_array = array();
		for ($i=0;$i<$number;$i++) { // do this $number times
			$random_image_array[] = $wpdb->get_row("SELECT * FROM $table_name",ARRAY_A, $rand+$i);
		}
	} elseif (!empty($category_slug) && $catcount = pp_count($category_slug)) {
		if ($catcount <= $number) {
			$number = $catcount;
			$rand = 0;
		} else {
			$rand = rand(0,($catcount - $number));
		}
		$random_image_array = array();
		$category_slug = $wpdb->escape($category_slug);
		for ($i=0;$i<$number;$i++) { // do this $number times
			$random_image_array[] = $wpdb->get_row("SELECT * FROM $table_name WHERE binary catslug='$category_slug'",ARRAY_A, $rand+$i);
		}
	} else {
		return FALSE;
	}
	$random_thumb = array();
	$random_alt_text = array();
	$j = 0;
	foreach ((array)$random_image_array as $random_image) {
		if (!empty($random_image['imgname'])) {
			$random_alt_text[] = $random_image['imgname'];
		} else {
			$random_alt_text[] = $random_image['imgfile'];
		}
		$random_image_thumb_size = @getimagesize( $pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $random_image['imgfile']);
		$random_thumb_temp = '<img class="';
		if ($style == 'album') {
			$random_thumb_temp .= 'pp_centered';
		} elseif ($style == 'random') {
			$random_thumb_temp .= $pp_options['rand_class'];
		} else {
			$random_thumb_temp .= $style;
		}
		$random_thumb_temp .= '" src="' . $pp_options['photosaddress'] . '/' . $pp_options['thumbprefix'] . $random_image['imgfile'] . '" ' . $random_image_thumb_size[3] . ' alt="' . htmlentities2($random_alt_text[$j]) . '" title="' . htmlentities2($random_alt_text[$j]) . '" />';
		$random_thumb[] = $random_thumb_temp;
		$j++;
	}
	$random_linked_thumb = array();
	if ($pp_options['album'] == '1') {
		$k = 0;
		foreach((array)$random_image_array as $random_image) {
			$random_linked_thumb[] =  '<a href="' . $pp_options['imgaddress'] . $random_image['imgfile'] . '" title="' . htmlentities2($random_alt_text[$k]) . '">' . $random_thumb[$k] . '</a>';
			$k++;
		}
	} else {
		$k = 0;
		foreach((array)$random_image_array as $random_image) {
			$random_image_address = $pp_options['photosaddress'] . '/' . $random_image['imgfile'];
			$random_image_size = @getimagesize($pp_options['photospath'] . '/' . $random_image['imgfile']);
			$random_linked_thumb[] =  '<a href="' . $random_image_address . '" onclick="pp_image_popup(\'' . htmlentities2($random_image_address) . '\',' . $random_image_size[0] . ',' . $random_image_size[1] . '); return false;" title="' . htmlentities2($random_alt_text[$k]) . '">' . $random_thumb[$k] . '</a>';
			$k++;
		}
	}
	if ($type == 3) {
		return $random_image;
	} elseif ($type == 2) {
		return $random_thumb;
	} else {
		return $random_linked_thumb;
	}
}

// template function to make a plain random linked thumb, sorta silly
function pp_random_image_bare($number=1,$before='',$after='<br />',$style='random',$category='',$type=1) {
	$randimages = array();
	$randimages = pp_random_image($category,$type,$style,$number);
	foreach ((array) $randimages as $randimage) {
		echo $before . $randimage . $after . "\n";
	}
}

// put a random link to the photo album and/or a random thumbnail in the meta section of the sidebar, depending on the options, does nothing if there aren't any images yet
function pp_stuff_in_meta() {
	global $pp_options;
	$stuff_for_meta = '';
	if ($pp_options['meta_link'] == 1) {
		$stuff_for_meta .= '<li><a href="' . $pp_options['albumaddress'] . '">' . __('Photo Album','photopress') . '</a></li>' . "\n";
	}
	if ($pp_options['meta_rand'] == 1 && pp_count()) {
		$rand_image = pp_random_image();
		$stuff_for_meta .= '<li>' . $rand_image[0] . "</li>\n";
	}
	echo $stuff_for_meta;
}

add_action('wp_meta', 'pp_stuff_in_meta');

// wrapper function to display the album
function pp_album() {
	if (get_query_var('pp_image') != '') {
		pp_display_image(stripslashes(get_query_var('pp_image')));
	} elseif (!empty($_GET['pp_image'])) {
		pp_display_image(stripslashes($_GET['pp_image']));
	} elseif (!empty($_GET['pp_cat'])) {
		pp_display_cat(stripslashes($_GET['pp_cat']));
	} elseif (get_query_var('pp_cat') != '') {
		pp_display_cat(stripslashes(get_query_var('pp_cat')));
	} else {
		pp_display_main();
	}
}

// displays images from $category_slug in an html table
function pp_display_cat($category_slug) {
	global $pp_options;
	$category_slug = stripslashes($category_slug);
	$category = pp_get_cat($category_slug);
	echo '<h3>' . htmlentities2($category) . '</h3>
	';
	if (isset($_GET['pp_page'])) {
		$current_page = $_GET['pp_page'];
	} elseif (get_query_var('pp_page') != '') {
		$current_page = get_query_var('pp_page');
	} else {
		$current_page = 1;
	}
	if (isset($_GET['sort'])) {
		$sort = $_GET['sort'];
	} else {
		$sort = 'imgfile';
	}
	$column = 0;
	$image_count = pp_count($category_slug);
	$pages = (int)ceil($image_count/$pp_options['images_per_page']);
	if ($pages > 1) {
		echo '<p>
		';
		for ($i=1; $i<=$pages; $i++) {
			if ($i != $current_page) {
				echo '<a href="' . $pp_options['cataddress'] . $category_slug . $pp_options['page_token'] . $i . '">';
			}
			echo __('Page','photopress') . ' ' . $i;
			if ($i != $current_page) {
				echo '</a>';
			}
			if ($i < $pages) {
				echo ' | ';
			}
		}
		echo '</p>
		';
		$list_start = ($current_page - 1) * $pp_options['images_per_page'];
	} else {
		$list_start = 0;
	}
	$cat_images = pp_images_with_data($category_slug,$sort,$list_start,$pp_options['images_per_page']);
	echo '<table id="pp_gallery">
	';
	foreach((array)$cat_images as $key=>$array_img) {
		$column++;
		if($column == 1) {
			echo '<tr>';
		}
		$thumbsize = @getimagesize($pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $array_img['imgfile']);
		if (!empty($array_img['imgname'])) {
			$cleanedname = stripslashes($array_img['imgname']);
		} else {
			$cleanedname = $array_img['imgfile'];
		}
		echo '<td class="pp_cell"><a href="' . $pp_options['imgaddress'] . $array_img['imgfile'] . '" title="' . htmlentities2($cleanedname) . '"><img src="' . $pp_options['photosaddress'] . '/' . $pp_options['thumbprefix'] . $array_img['imgfile'] . '" ' . $thumbsize[3] . ' class="pp_centered" alt="' . htmlentities2($cleanedname) . '" />'; if (!empty($array_img['imgname'])) { echo '<br />' . htmlentities2($cleanedname); } echo '</a></td>';
		if($column == $pp_options['album_columns']) {
			echo '</tr>
			';
			$column = 0;
		}
	}
	if ($column < $pp_options['album_columns'] && $column > 0) {
		echo '</tr>
		';
	}
	echo '</table>
	';
}

function pp_display_main() {
	global $pp_options;
	$pp_count = pp_count();
	if ($pp_count > 0) {
	$cat_slugs = array();
	$cat_slugs = pp_list_slugs('album');
	$column = 0;
	echo '<table id="pp_gallery">
	';
	if (count($cat_slugs) > 1) {
		foreach((array)$cat_slugs as $slug) {
			$column++;
			if ($column == 1) {
				echo '<tr>';
			}
			$image_count = pp_count($slug);
			$cat = pp_get_cat($slug);
			$randimage = pp_random_image($slug,2,'album');
			echo '<td class="pp_cell"><a href="' . $pp_options['cataddress'] . $slug . '" title="' . htmlentities2($cat) . '">' . $randimage[0] . '<br />' . htmlentities2($cat) . ' (' . $image_count . ')</a></td>';
			if ($column == $pp_options['album_columns']) {
				echo '</tr>
				';
				$column = 0;
			}
		}
		if ($column < $pp_options['album_columns'] && $column > 0) {
			echo '</tr>
			';
		}
	} elseif (count($cat_slugs) == 1) {
		$cat = pp_get_cat($cat_slugs[0]);
		echo '<tr>';
		$cat_images = pp_cat_images($cat_slugs[0]);
		$image_count = count($cat_images);
		$randimage = pp_random_image($cat_slugs[0],2,'album');
		echo '<td class="pp_cell"><a href="' . $pp_options['cataddress'] . $cat_slugs[0] . '" title="' . htmlentities2($cat) . '">' . $randimage[0] . '<br />' . htmlentities2($cat) . ' (' . $image_count . ')</a></td>';
		echo '</tr>
		';
	} else {
		echo '<tr><td><strong>' . __('No photos uploaded yet.','photopress') . '</strong></td></tr>
		';
	}
	echo '</table>
	';
	} else {
		echo '<p>' . __('No photos uploaded yet.','photopress') . '</p>';
	}
}

// displays an image, linked to a larger version or to the original if it's there
function pp_display_image($image) {
	global $pp_options;
	if (file_exists($pp_options['photospath'] . '/' . $image)) {
		if ($image_data = pp_get_data($image)) {
			$image_cat = stripslashes($image_data['imgcat']);
			if (!empty($image_data['imgname'])) {
				$cleanedname = stripslashes($image_data['imgname']);
			} else {
				$cleanedname = $image_data['imgfile'];
			}
			$imgdesc = stripslashes($image_data['imgdesc']);
		}
		echo '<div id="pp_meta"><strong><a href="' . $pp_options['cataddress'] . $image_data['catslug'] . '">' . htmlentities2($image_cat) . '</a> : ' . htmlentities2($cleanedname) . '</strong></div>' . "\n";
		$cat_array = pp_cat_images($image_data['catslug']);
		$image_size = @getimagesize($pp_options['photospath'] . '/' . $image);
		$itemcount = sizeof($cat_array);
		if ($itemcount > 1) {
			$prevnext = '<div id="pp_prevnext">' . "\n";
			for ($j = 0; $j < $itemcount; $j++) {
				$item = $cat_array[$j];
				if ($image == $item) {
					if ($cat_array[$j-1]) {
						$prevnext .= '<a class="pp_prev" href="' . $pp_options['imgaddress'] . $cat_array[$j-1] . '" title="' . $cat_array[$j-1] . '">' . __('Previous','photopress') . '</a>' . "\n";
					} else {
						$prevnext .= '<div class="pp_prev"></div>' . "\n";
					}
					if ($cat_array[$j+1]) {
						$prevnext .= '<a class="pp_next" href="' . $pp_options['imgaddress'] . $cat_array[$j+1] . '" title="' . $cat_array[$j+1] . '">' . __('Next','photopress') . '</a>' . "\n";
					} else {
						$prevnext .= '<div class="pp_next"></div>' . "\n";
					}
				}
			}
			$prevnext .= "</div>\n";
			echo $prevnext;
		}
		echo '<div id="pp_gallery">';
		if (is_file($pp_options['photospath'] . '/' . $pp_options['origprefix'] . $image)) {
			$orig_size = @getimagesize($pp_options['photospath'] . '/' . $pp_options['origprefix'] . $image);
			echo '<a href="' . $pp_options['photosaddress'] . '/' . $pp_options['origprefix'] . $image . '" onclick="pp_image_popup(\'' . htmlentities2($pp_options['photosaddress'] . '/' . $pp_options['origprefix'] . $image) . '\',' . $orig_size[0] . ',' . $orig_size[1] . '); return false;" title="' . htmlentities2($cleanedname) . '"><img class="pp_centered" src="' . $pp_options['photosaddress'] . '/' . $image . '" ' . $image_size[3] . ' alt="' . htmlentities2($cleanedname) . '" /></a>';
		} else {
			echo '<a href="' . $pp_options['photosaddress'] . '/' . $image . '" onclick="pp_image_popup(\'' . htmlentities2($pp_options['photosaddress'] . '/' . $image) . '\',' . $image_size[0] . ',' . $image_size[1] . '); return false;" title="' . htmlentities2($cleanedname) . '"><img class="pp_centered" src="' . $pp_options['photosaddress'] . '/' . $image . '" ' . $image_size[3] . ' alt="' . htmlentities2($cleanedname) . '" /></a>';
		}
		if (!empty($imgdesc)) {
			echo '<p>' . htmlentities2($imgdesc) . '</p>';
		}
		if ( $pp_options['show_posts'] == '1' ) {
			query_posts("s=$image");
			if ( have_posts() ) :
				echo '<p><strong>' . __('Posts with this image','photopress') . ':</strong></p>
			<ul>';
			while ( have_posts() ) : the_post();
				echo '<li><a href="';
				the_permalink();
				echo '">';
				the_title();
				echo '</a></li>';
			endwhile;
			echo '</ul>';
		else: endif;
		}
		echo "</div>\n";
	} else {
		echo "<p>" . __("Image not found.","photopress") . "</p>\n";
	}
}

function pp_is_album() {
	if (isset($_GET['pp_album']) || get_query_var('pp_album') != '') {
		return TRUE;
	} else {
		return FALSE;
	}
}

function pp_album_template() {
	if (pp_is_album()) {
		$stylesheet_dir = ABSPATH . 'wp-content/themes/' . get_settings('stylesheet');
		$template_dir = ABSPATH . 'wp-content/themes/' . get_settings('template');
		if (file_exists($template_dir . '/pp_album.php') ) {
			include($template_dir . '/pp_album.php');
			exit;
		} elseif (file_exists($stylesheet_dir . '/pp_album.php') ) {
			include($stylesheet_dir . '/pp_album.php');
			exit;
		} else {
			include(ABSPATH . 'wp-content/plugins/photopress/pp_album.php');
			exit;
		}
	}
	return;
}

add_action('template_redirect','pp_album_template');

// insert the css for the album and the javascript to do image popups
function pp_album_css() {
	if (isset($_GET['pp_album']) || get_query_var('pp_album') != '') {
		$stylesheet_dir = ABSPATH . 'wp-content/themes/' . get_settings('stylesheet');
		$template_dir = ABSPATH . 'wp-content/themes/' . get_settings('template');
		if (file_exists($template_dir . '/pp_album_css.php')) {
			include($template_dir . '/pp_album_css.php');
		} elseif (file_exists($stylesheet_dir . '/pp_album_css.php')) {
			include($stylesheet_dir . '/pp_album_css.php');
		} else {
			include(ABSPATH . 'wp-content/plugins/photopress/pp_album_css.php');
		}
	}
	echo '
<style type="text/css" media="screen">
	img.pp_empty {
}
</style>
<script type="text/javascript">
//<![CDATA[
function pp_image_popup(image,width,height) {
	height += 24;
	width += 24;
	image_popup = window.open(image,\'image\',\'height=\' + height + \',width=\' + width + \',toolbar=no,menubar=no,scrollbars=yes,resizable=yes\');
	image_popup.resizeTo(width+2,height+30);
	image_popup.focus();
	return false;
}
//]]>
</script>';
}

add_action('wp_head','pp_album_css');
add_action('admin_head','pp_album_css');

// do the Options page, setting the min level to use it at 8
function pp_add_options_page() {
	add_options_page('Photopress', 'Photopress', 8, basename(__FILE__), 'pp_options_subpanel');
}

function pp_options_subpanel() {
	global $pp_options;
	if (isset($_POST['pp_options_update'])) {
	   $pp_updated_options = array();
		$pp_updated_options = $_POST;
		add_option('pp_options');
		update_option('pp_options', $pp_updated_options);
		$pp_options = get_option('pp_options');
		echo '<div class="updated">' . __('Photopress options updated.','photopress') . '</div>';
	}
	echo '
	<div class="wrap">
	<h2>' . __('Photopress Options','photopress') . '</h2>
	<form name="pp_options" method="post">
	<input type="hidden" name="pp_options_update" value="update" />
	<fieldset class="options">
	<table width="100%" cellspacing="2" cellpadding="5" class="editform">
		<tr>
			<th scope="row">' . __('Path to your photos folder:','photopress') . '</th>
			<td><input name="photospath" type="text" id="photospath" value="' . $pp_options['photospath'] . '" size="40" /><br />' . __('Best guess:','photopress') . ' "' . ABSPATH . 'wp-content/photos" ' . __('Make sure this path is correct and that the folder at the end exists and is writable.','photopress') . '</td>
			</td>
		</tr>
		<tr>
			<th scope = "row">' . __('Wordpress address:','photopress') . '</th>
			<td><input name="wpaddress" type="text" id="wpaddress" value="' . $pp_options['wpaddress'] . '" size="40" /><br />' . __('Best guess:','photopress') . ' "' . get_settings('siteurl') . '"</td>
		</tr>
		<tr>
			<th scope = "row">' . __('Address of your photos folder:','photopress') . '</th>
			<td><input name="photosaddress" type="text" id="photosaddress" value="' . $pp_options['photosaddress'] . '" size="40" /><br />' . __('Best guess:','photopress') . ' "' . get_settings('siteurl') . '/wp-content/photos"</td>
		</tr>
		<tr>
			<th scope="row">' . __('Use permalinks:','photopress') . '</th>
			<td>
			<label><input name="use_permalinks" type="radio" value="1" ';
			if ($pp_options['use_permalinks'] == '1') {echo 'checked="checked" ';}
			echo '/> ' . __('Yes','photopress') . '</label><br />
			<label><input name="use_permalinks" type="radio" value="0" ';
			if ($pp_options['use_permalinks'] == '0') {echo 'checked="checked" ';}
			echo ' /> ' . __('No','photopress') . '</label><br />
			' . __('This will add some rules for Photopress to your Wordpress permalink setup.','photopress') . '</td>
		</tr>
		<tr>
			<th scope = "row">' . __('Album token for permalinks:','photopress') . '</th>
			<td><input name="album_token" type="text" id="album_token" value="' . $pp_options['album_token'] . '" size="10" /><br />' . __('This will appear just after your blog address; use something like "album". (Ignored if not using permalinks.)','photopress') . '</td>
		</tr>
		<tr>
			<th scope = "row">' . __('Category token for permalinks:','photopress') . '</th>
			<td><input name="cat_token" type="text" id="cat_token" value="' . $pp_options['cat_token'] . '" size="10" /><br />' . __('This will appear just after the album token; use something like "cat". (Ignored if not using permalinks.)','photopress') . '</td>
		</tr>
		<tr>
			<th scope = "row">' . __('Images token for permalinks:','photopress') . '</th>
			<td><input name="images_token" type="text" id="images_token" value="' . $pp_options['images_token'] . '" size="10" /><br />' . __('This will appear just after the album token; use something like "images". (Ignored if not using permalinks.)','photopress') . '</td>
		</tr>
		<tr>
			<th scope="row">' . __('Keep original images:','photopress') . '</th>
			<td>
			<label><input name="originals" type="radio" value="1" ';
			if ($pp_options['originals'] == '1') {echo 'checked="checked" ';}
			echo '/> ' . __('Yes','photopress') . '</label><br />
			<label><input name="originals" type="radio" value="0" ';
			if ($pp_options['originals'] == '0') {echo 'checked="checked" ';}
			echo ' /> ' . __('No','photopress') . '</label><br />
			' . __('Originals are normally deleted to save disk space, but if that is not an issue you may wish to keep them.','photopress') . '</td>
		</tr>
		<tr>
			<th scope="row">' . __('Originals prefix:','photopress') . '</th>
			<td><input name="origprefix" type="text" id="origprefix" value="' . $pp_options['origprefix'] . '" size="10" /><br />' . __('String to add to the original image name, if they are being retained.','photopress') . '</td>
		</tr>
		<tr>
			<th scope="row">' . __('Minimum level to upload and browse:','photopress') . '</th>
			<td><select name="min_level" id="min_level">';
			for ($i = 1; $i <= 10; $i++) {
				echo '
				<option value="' . $i . '"';
				if ($pp_options['min_level'] == $i) { echo ' selected="selected"'; }            echo '>' . $i . '</option>';
			}
			echo '
			</select></td>
		</tr>
		<tr>
			<th scope="row">' . __('Minimum level to use mass edit and maintain tools:','photopress') . '</th>
			<td><select name="mass_min_level" id="mass_min_level">';
			for ($i = 1; $i <= 10; $i++) {
				echo '
				<option value="' . $i . '"';
				if ($pp_options['mass_min_level'] == $i) { echo ' selected="selected"'; }            echo '>' . $i . '</option>';
			}
			echo '
			</select></td>
		</tr>
		<tr>
			<th scope="row">' . __('Maximum allowed size for uploads:','photopress') . '</th>
			<td><input name="maxk" type="text" id="maxk" value="' . $pp_options['maxk'] . '" size="4" /> 
			' . __('Kilobytes (KB)','photopress') . '<br />
			' . __('This setting does not always work. PHP also has its own maximum allowed upload size.','photopress') . '</td>
		</tr>
		<tr>
			<th scope="row">' . __('Maximum resized image dimensions (width or height):','photopress') . '</th>
			<td><input name="maxsize" type="text" id="maxsize" value="' . $pp_options['maxsize'] . '" size="4" />
			' . __('pixels','photopress') . '</td>
		</tr>
		<tr>
			<th scope="row">' . __('Maximum thumbnail dimensions:','photopress') . '</th>
			<td><input name="thumbsize" type="text" id="thumbsize" value="' . $pp_options['thumbsize'] . '" size="4" />' . __('pixels','photopress') . '</td>
		</tr>
		<tr>
			<th scope="row">' . __('Make square thumbnails:','photopress') . '</th>
			<td>
			<label><input name="square" type="radio" value="1" ';
			if ($pp_options['square'] == '1') {echo 'checked="checked" ';}
			echo '/> ' . __('Yes','photopress') . '</label><br />
			<label><input name="square" type="radio" value="0" ';
			if ($pp_options['square'] == '0') {echo 'checked="checked" ';}
			echo ' /> ' . __('No','photopress') . '</label><br />
			' . __('This crops your thumbnails to make them square.','photopress') . '</td>
		</tr>
		<tr>
			<th scope="row">' . __('Quality for resized JPEGs:','photopress') . '</th>
			<td><input name="quality" type="text" id="quality" value="' . $pp_options['quality'] . '" size="3" /><br />' . __('Recommended: between 70 and 95.','photopress') . '</td>
		</tr>
		<tr>
			<th valign="top" scope="row">' . __('Allowed file extensions (and MIME types):','photopress') . '</th>
			<td><input name="allowedtypes" type="text" id="allowedtypes" value="' . $pp_options['allowedtypes'] . '" size="40" /><br />' . __('Recommended: <code>jpg jpeg png gif</code> (The resizing function only supports these types.','photopress') . '</td>
		</tr>
		<tr>
			<th scope="row">' . __('Insert thumbnail or full image by default:','photopress') . '</th>
			<td><label><input name="insert_thumbs" type="radio" value="1" ';
			if ($pp_options['insert_thumb'] == '1') {echo 'checked="checked" ';}
			echo '/> ' . __('Thumb','photopress') . '</label> 
			<label><input name="insert_thumbs" type="radio" value="0" ';
			if ($pp_options['insert_thumb'] == '0') {echo 'checked="checked" ';}
			echo ' /> ' . __('Full','photopress') . '</label><br />' . __('Both options will be in a dropdown, this selects the default.','photopress') . '</td>
		</tr>
		<tr>
			<th scope="row">' . __('Thumbnails prefix:','photopress') . '</th>
			<td><input name="thumbprefix" type="text" id="thumbprefix" value="' . $pp_options['thumbprefix'] . '" size="10" /><br />' . __('String to add to the thumbnails Photopress creates.','photopress') . '</td>
		</tr>
		<tr>
			<th scope="row">' . __('Link to album:','photopress') . '</th>
			<td>
			<label><input name="album" type="radio" value="1" ';
			if ($pp_options['album'] == '1') {echo 'checked="checked" ';}
			echo '/> ' . __('Yes','photopress') . '</label> 
			<label><input name="album" type="radio" value="0" ';
			if ($pp_options['album'] == '0') {echo 'checked="checked" ';}
			echo ' /> ' . __('No','photopress') . '</label><br />' . __('The code for inserted images can point to the image in the album or straight to a popup containing the image.','photopress') . '
        </td>
		</tr>
		<tr>
			<th scope="row">' . __('Insert tags or HTML:','photopress') . '</th>
			<td>
			<label><input name="insert_tags" type="radio" value="1" ';
			if ($pp_options['insert_tags'] == '1') {echo 'checked="checked" ';}
			echo '/> ' . __('Tags','photopress') . '</label>
			<label><input name="insert_tags" type="radio" value="0" ';
			if ($pp_options['insert_tags'] == '0') {echo 'checked="checked" ';}
			echo ' /> ' . __('HTML','photopress') . '</label><br />' . __('The popup tool can insert tags (like [photopress:...]) or regular HTML. HTML gives you inline images in the rich text editor, which can be nice. However, since tags are rendered into HTML when posts are displayed they can respond to changes to Options and image sizes, so you should be less likely to end up with broken or weird images in old posts.','photopress') . '
			</td>
		</tr>
		<tr>
			<th scope="row">' . __('Insert link in Meta:','photopress') . '</th>
			<td>
			<label><input name="meta_link" type="radio" value="1" ';
			if ($pp_options['meta_link'] == '1') {echo 'checked="checked" ';}
			echo '/> ' . __('Yes','photopress') . '</label> 
			<label><input name="meta_link" type="radio" value="0" ';
			if ($pp_options['meta_link'] == '0') {echo 'checked="checked" ';}
			echo ' /> ' . __('No','photopress') . '</label><br />' . __('Adds a link to the photo album in the Meta sidebar list.','photopress') . '
			</td>
		</tr>
		<tr>
			<th scope="row">' . __('Insert random image in Meta:','photopress') . '</th>
			<td>
			<label><input name="meta_rand" type="radio" value="1" ';
			if ($pp_options['meta_rand'] == '1') {echo 'checked="checked" ';}
			echo '/> ' . __('Yes','photopress') . '</label> 
			<label><input name="meta_rand" type="radio" value="0" ';
			if ($pp_options['meta_rand'] == '0') {echo 'checked="checked" ';}
			echo ' /> ' . __('No','photopress') . '</label><br />' . __('Adds a random linked thumb to the Meta sidebar list.','photopress') . '
			</td>
		</tr>
		<tr>
			<th scope="row">' . __('Add failsafe button:','photopress') . '</th>
			<td>
			<label><input name="failsafe_buttons" type="radio" value="1" ';
			if ($pp_options['failsafe_buttons'] == '1') {echo 'checked="checked" ';}
			echo '/> ' . __('Yes','photopress') . '</label> 
			<label><input name="failsafe_buttons" type="radio" value="0" ';
			if ($pp_options['failsafe_buttons'] == '0') {echo 'checked="checked" ';}
			echo ' /> ' . __('No','photopress') . '</label><br />' . __('Enables a Photos button that may work more reliably than the default button on the Quicktags toolbar.','photopress') . '
			</td>
		</tr>
		<tr>
			<th scope="row">' . __('Show posts with image in album:','photopress') . '</th>
			<td>
			<label><input name="show_posts" type="radio" value="1" ';
			if ($pp_options['show_posts'] == '1') {echo 'checked="checked" ';}
			echo '/> ' . __('Yes','photopress') . '</label> 
			<label><input name="show_posts" type="radio" value="0" ';
			if ($pp_options['show_posts'] == '0') {echo 'checked="checked" ';}
			echo ' /> ' . __('No','photopress') . '</label><br />' . __('Shows a list of posts containing the image in the album. Notes on making this work better with your theme can be found at: ','photopress') . '<a href="http://familypress.net/archives/fixing-the-sidebar/">familypress.net</a>
			</td>
		</tr>
		<tr>
			<th scope="row">' . __('Show thumbs in mass edit in the album manager:','photopress') . '</th>
			<td>
			<label><input name="thumbs_in_mass_edit" type="radio" value="1" ';
			if ($pp_options['thumbs_in_mass_edit'] == '1') {echo 'checked="checked" ';}
			echo '/> ' . __('Yes','photopress') . '</label> 
			<label><input name="thumbs_in_mass_edit" type="radio" value="0" ';
			if ($pp_options['thumbs_in_mass_edit'] == '0') {echo 'checked="checked" ';}
			echo ' /> ' . __('No','photopress') . '</label><br />' . __('Toggles thumbnail display in the Mass Editor part of the album manager, speeding up display a bit.','photopress') . '
			</td>
		</tr>
		<tr>
			<th scope="row">' . __('Allow images used in posts to be deleted:','photopress') . '</th>
			<td>
			<label><input name="allow_post_image_delete" type="radio" value="1" ';
			if ($pp_options['allow_post_image_delete'] == '1') {echo 'checked="checked" ';}
			echo '/> ' . __('Yes','photopress') . '</label>
			<label><input name="allow_post_image_delete" type="radio" value="0" ';
			if ($pp_options['allow_post_image_delete'] == '0') {echo 'checked="checked" ';}
			echo ' /> ' . __('No','photopress') . '</label><br />' . __('Makes the delete button appear in the album manager for images that are used in posts. The mass editor will still have check boxes to delete.','photopress') . '
			</td>
		</tr>
		<tr>
			<th scope="row">' . __('Images per page:','photopress') . '</th>
			<td><input name="images_per_page" type="text" id="images_per_page" value="' . $pp_options['images_per_page'] . '" size="3" /><br />' . __('Number of images to show on pages with multiple images. (The album, the browse list, and in the manager.)','photopress') . '</td>
		</tr>
		<tr>
			<th scope="row">' . __('Album columns:','photopress') . '</th>
			<td><input name="album_columns" type="text" id="album_columns" value="' . $pp_options['album_columns'] . '" size="3" /><br />' . __('Number of columns to use in the album: probably 2 if you have a sidebar, maybe 3 otherwise.','photopress') . '</td>
		</tr>
		<tr>
			<th scope="row">' . __('Manager columns:','photopress') . '</th>
			<td><input name="manager_columns" type="text" id="manager_columns" value="' . $pp_options['manager_columns'] . '" size="3" /><br />' . __('Number of columns to use in the album manager, probably 4 or 5.','photopress') . '</td>
		</tr>
		<tr>
			<th scope="row">' . __('CSS classes for inserted images:','photopress') . '</th>
			<td><input name="image_class" type="text" id="image_class" value="' . $pp_options['image_class'] . '" size="10" /><br />' . __('alignleft, alignright, and centered are available in the default theme, or you can add new classes to your theme style file and enter the names here. Separate the classes with spaces. These will appear in a dropdown list in the uploader and browser, and the first one will be selected by default.','photopress') . '</td>
		</tr>
		<tr>
			<th scope="row">' . __('CSS class for random thumbs:','photopress') . '</th>
			<td><input name="rand_class" type="text" id="rand_class" value="' . $pp_options['rand_class'] . '" size="10" /><br />' . __('alignleft, alignright, and centered are available in the default theme, or you can add a new class to your theme and enter the name here.','photopress') . '</td>
		</tr>
	</table> 
	</fieldset>
	<p class="submit"><input type="submit" name="Submit" value="' . __('Update Options &raquo;','photopress') . '" /></p>
</form> 
</div>
';
}

add_action('admin_menu', 'pp_add_options_page');

// makes the Manage:Photopress Album pages
function pp_album_management() {
	global $wpdb,$pp_options, $user_level;
	if (isset($POST['pp_fix_cats'])) {
		if (pp_fix_cats()) {
			echo '<div class="updated">' . __('Repaired category names.','photopress') . '</div>';
		} else {
			echo '<div class="updated">' . __('Category name repair failed.','photopress') . '</div>';
		}
	}
	if (isset($_POST['pp_chmod_folder'])) {
		if (is_dir($pp_options['photospath'])) {
			if (chmod($pp_options['photospath'],0777)) {
				echo '<div class="updated">' . __('Changed permissions successfully.','photopress') . '</div>';
			} else {
				echo '<div class="updated">' . __('Failed to change permissions.','photopress') . '</div>';
			}
		} else {
			echo '<div class="updated">' . __('Folder not found, make sure it exists and your paths are correct.','photopress') . '</div>';
		}
	}
	if (isset($_POST['pp_import_orphans'])) {
		$imported = pp_import_orphans();
		if ($imported == 1) {
			echo '<div class="updated">' . __('1 image imported.','photopress') . '</div>';
		} else {
			echo '<div class="updated">' . $imported . __(' images imported.','photopress') . '</div>';
		}
	}
	if (isset($_POST['pp_reinstall'])) {
		$install = pp_table_install();
		echo '<div class="updated">' . __('Photopress table updated.','photopress') . '</div>';
	}
	if (isset($_POST['pp_change_cat'])) {
		$catrec = 0;
		foreach ((array)$_POST as $key=>$val) {
			if (is_array($val)) {
				if (!empty($val['newcat'])) {
					$update_cat = $val['newcat'];
				} else {
					$update_cat = $val['dropcat'];
				}
				if ($update_cat != $val['oldcat'] || $val['hide_cat'] != 'keep') {
					if (pp_change_cat($val['oldslug'],$val['newslug'],$val['oldcat'],$update_cat,$val['hide_cat'])) {
						$catrec++;
					}
				}
			}
		}
		$wpdb->flush();
		echo '<div class="updated">' . $catrec . __(' album categories changed.','photopress') . '</div>';
	}
	if (isset($_POST['pp_album_update'])) {
		if(!empty($_POST['newimgcat'])) {
			$pp_updated_cat = $_POST['newimgcat'];
		} else {
			$pp_updated_cat = $_POST['oldimgcat'];
		}
		$pp_updated_array = array($_POST['imgfile'],$_POST['imgname'],$_POST['imgdesc'],$pp_updated_cat);
		if (pp_table_update($pp_updated_array)) {
			echo '<div class="updated">' . __('Photopress album data updated.','photopress') . '</div>';
		} else {
			echo '<div class="updated">' . __('Photopress album data update failed for some reason.','photopress') . '</div>';
		}
	}
	if (isset($_POST['pp_mass_update'])) {
		$dels = 0;
		$updates = 0;
		foreach ((array)$_POST as $key=>$val) {
			if (is_array($val)) {
				if ($val['imgdelete'] == '1') {
					if (pp_delete_photo($val['imgfile'])) {
						$dels++;
					}
				} else {
					if (!empty($val['newimgcat'])) {
						$pp_updated_cat = $val['newimgcat'];
					} else {
						$pp_updated_cat = $val['oldimgcat'];
					}
					$pp_updated_array = array($val['imgfile'],$val['imgname'],$val['imgdesc'],$pp_updated_cat);
					if (pp_table_update($pp_updated_array)) {
						$updates++;
					}
				}
				$wpdb->flush();
			}
		}
		echo '<div class="updated">' . $updates . __(' records updated. ','photopress') . $dels . __(' images deleted.','photopress') . '</div>';
	}
	if (isset($_POST['pp_rotate'])) {
		$rotated = pp_rotate($_POST['imgfile'],$_POST['pp_rotate'],$_POST['pp_copy']);
		if ($rotated == 1) {
			echo '<div class="updated">' . $_POST['imgfile'] . __(' was rotated successfully.','photopress') . '</div>';
		} else {
			echo '<div class="updated">' . $rotated . '</div>';
		}
	}
	if (isset($_GET['pp_delete'])) {
		if (pp_delete_photo($_GET['pp_delete'])) {
			echo '<div class="updated">' . $_GET['pp_delete'] . __(' was deleted.','photopress') . '</div>';
		} else {
			echo '<div class="updated">' . $_GET['pp_delete'] . __(' was not deleted for some reason.','photopress') . '</div>';
		}
	}
	if (isset($_POST['pp_filename_repair'])) {
		if ($repaired = pp_filename_repair()) {
			echo '<div class="updated">' . $repaired . __(' images had bad filenames and were repaired.','photopress') . '</div>';
		} else {
			echo '<div class="updated">' . __('No filename repair necessary.','photopress') . '</div>';
		}
	}
	if (isset($_POST['pp_db_cleanup'])) {
		if ($cleaned = pp_db_cleanup()) {
			echo '<div class="updated">' . $cleaned . __(' records had no matching images and were removed.','photopress') . '</div>';
		} else {
			echo '<div class="updated">' . __('No cleanup necessary.','photopress') . '</div>';
		}
	}
	if (isset($_POST['pp_mass_resize'])) {
		$resized = pp_mass_resize();
		echo '<div class="updated">';
		printf(__('Resized %s images, %s thumbs.','photopress'),$resized[0], $resized[1]);
		echo '</div>';
	}
	echo '
	<div class="wrap">
	<h2>' . __('Photopress Album Management','photopress') . '</h2>
	<p>
	';
	if (pp_count() > 0) {
		echo '<a href="edit.php?page=photopress.php" title="' . __('Category View','photopress') . '">' . __('Category View','photopress') . '</a> | ';
		if ($user_level >= $pp_options['mass_min_level']) {
			echo '<a href="edit.php?page=photopress.php&amp;pp_manage_mass=yes" title="' . __('Mass Edit','photopress') . '">' . __('Mass Edit','photopress') . '</a> | ';
		}
		echo '<a href="edit.php?page=photopress.php&amp;pp_change_cat=yes" title="' . __('Edit Categories','photopress') . '">' . __('Edit Categories','photopress') . '</a> | ';
	}
	echo '<a href="#" onclick="return pp_popup();" title="' . __('Upload','photopress') . '">' . __('Upload','photopress') . '</a>';
	if ($user_level >= $pp_options['mass_min_level']) {
		echo ' | 
		<a href="edit.php?page=photopress.php&amp;pp_maintain=yes" title="' . __('Maintain','photopress') . '">' . __('Maintain','photopress') . '</a>';
	}
	echo '</p>
	</div>
	';
	if (isset($_GET['pp_maintain'])) {
		echo '
		<div class="wrap">
		<h2>' . __('Maintain','photopress') . '</h2>
		<p>' . __('Various possibly-useful maintenance tools. <strong>Import Photos</strong> imports any images in the photos folder that are not in the database. <strong>Install DB</strong> nondestructively updates the database, adding new columns if necessary. <strong>Cleanup DB</strong> checks for images in the DB that are not on disk and removes the records. <strong>CHMOD Folder</strong> attempts to change the permissions of the photos folder to 0777. This will frequently fail. <strong>Fix Categories</strong> re-saves your category names, escaping any characters which may be causing trouble. <strong>Mass Resize</strong> checks your image and thumb sizes and down-sizes to your current setting if necessary. Also makes your thumbs square if necessary. <strong>Filename Repair</strong> fixes bad filenames which earlier versions may have caused. You should only use these if something is not working correctly.','photopress') . '</p>
		<form name="pp_album_cats" method="post">
		<input type="hidden" name="pp_import_orphans" value="update" />
		<p class="submit"><input type="submit" name="Submit" value="' . __('Import Photos','photopress') . ' &raquo;" /></p>
		</form>
		<form name="pp_album_cats" method="post">
		<input type="hidden" name="pp_reinstall" value="update" />
		<p class="submit"><input type="submit" name="Submit" value="' . __('Install DB','photopress') . ' &raquo;" /></p>
		</form>
		<form name="pp_album_cats" method="post">
		<input type="hidden" name="pp_db_cleanup" value="update" />
		<p class="submit"><input type="submit" name="Submit" value="' . __('Cleanup DB','photopress') . ' &raquo;" /></p>
		</form>
		<form name="pp_album_cats" method="post">
		<input type="hidden" name="pp_chmod_folder" value="update" />
		<p class="submit"><input type="submit" name="Submit" value="' . __('CHMOD Folder','photopress') . ' &raquo;" /></p>
		</form>
      <form name="pp_album_cats" method="post">
		<input type="hidden" name="pp_fix_cats" value="update" />
		<p class="submit"><input type="submit" name="Submit" value="' . __('Fix Categories','photopress') . ' &raquo;" /></p>
		</form>
		<form name="pp_album_cats" method="post">
		<input type="hidden" name="pp_mass_resize" value="update" />
		<p class="submit"><input type="submit" name="Submit" value="' . __('Mass Resize','photopress') . ' &raquo;" /></p>
		</form>
		<form name="pp_album_cats" method="post">
		<input type="hidden" name="pp_filename_repair" value="update" />
		<p class="submit"><input type="submit" name="Submit" value="' . __('Filename Repair','photopress') . ' &raquo;" /></p>
		</form>
		</div>
		';
	} elseif (isset($_GET['pp_manage_image'])) {
		$pp_manage_image = $_GET['pp_manage_image'];
		$pp_image_data = pp_get_data($pp_manage_image);
		$pp_imgfile = $pp_image_data['imgfile'];
		$pp_imgdesc = stripslashes($pp_image_data['imgdesc']);
		$pp_imgname = stripslashes($pp_image_data['imgname']);
		$pp_imgcat = stripslashes($pp_image_data['imgcat']);
		$pp_imgtime = $pp_image_data['imgtime'];
		$pp_slug = $pp_image_data['catslug'];
		$pp_cats = pp_list_cats();
		echo '
		<div class="wrap">
		<h3>' . __('Editing','photopress') . ' <em>' . $pp_imgfile . '</em> ' . __('from the','photopress') . ' <em><a href="edit.php?page=photopress.php&amp;pp_manage_cat=' . $pp_slug . '">' . $pp_imgcat . '</a></em> ' . __('category','photopress') . '</h3>
		<table width="100%" cellpadding="5">
		<tr><td>
		<form name="pp_album_cats" method="post">
		<input type="hidden" name="pp_album_update" value="update" />
		<input type="hidden" name="imgfile" value="' . $pp_imgfile . '" />
		<input type="hidden" name="catslug" value="' . $pp_slug . '" />
		<img src="' . $pp_options['photosaddress'] . '/' . $pp_manage_image . '" alt="' . $pp_imgname . '" title="' . $pp_imgname . '" />
		</td><td>
		<p><strong>' . __('Category:','photopress') . '</strong><br /><select name="oldimgcat" id="pp_oldimgcat">';
		echo pp_cat_dropdown($pp_cats,$pp_imgcat); 
		echo '</select><br />
		 ' . __('or enter new:','photopress') . ' <input type="text" name="newimgcat" value="" /></p>
		<p><strong>' . __('Name:','photopress') . '</strong><br /><input type="text" name="imgname" value="' . $pp_imgname . '" /></p>
		<p><strong>' . __('Description:','photopress') . '</strong><br /><textarea rows="2" cols ="30" name="imgdesc" id="imgdesc" class="uploadform">' . $pp_imgdesc . '</textarea></p>
		<p><strong>' . __('Date uploaded:','photopress') . '</strong><br />' . date("j M Y, H:i", $pp_imgtime) . '</p>
		<p class="submit">
		<input type="submit" name="Submit" value="' . __('Update','photopress') . ' &raquo;" />
		</p>';
		query_posts("s=$pp_manage_image");
		if ( have_posts() ) :
		echo '<p>' . __('Posts with this image:','photopress') . '</p>
			<ul>';
		while ( have_posts() ) : the_post();
		echo '<li><a href="';
		the_permalink();
		echo '">';
		the_title();
		echo '</a></li>';
		endwhile;
		echo '</ul>';
		else:
		if ($pp_options['allow_post_image_delete'] == '0') {
			echo "<p><a href=\"edit.php?page=photopress.php&amp;pp_manage_cat=" . $pp_slug . "&pp_delete=" . $pp_manage_image . "\" class=\"delete\" onclick=\"return confirm('";
			printf(__("OK to delete %s?","photopress"),$pp_manage_image);
			echo "')\">";
			printf(__("Delete %s","photopress"),$pp_manage_image);
			echo '</a></p>';
		}
		endif;
		if ($pp_options['allow_post_image_delete'] != '0') {
			echo "<p><a href=\"edit.php?page=photopress.php&amp;pp_manage_cat=" . $pp_slug . "&amp;pp_delete=" . $pp_manage_image . "\" class=\"delete\" onclick=\"return confirm('";
			printf(__("OK to delete %s?","photopress"),$pp_manage_image);
			echo "')\">";
			printf(__("Delete %s","photopress"),$pp_manage_image);
			echo '</a></p>';
		}
		echo '
		</form>
		<form name="pp_album_cats" method="post">
		<input type="hidden" name="pp_rotate" value="update" />
		<input type="hidden" name="imgfile" value="' . $pp_imgfile . '" />
		<p class="submit">
		<strong>' . __('Rotate: ','photopress') . '</strong>
		<select name="pp_copy" id="pp_copy">
			<option value="copy">' . __('copy','photopress') . '</option>
			<option value="replace">' . __('replace','photopress') . '</option>
		</select> 
		<select name="pp_rotate" id="pp_rotate">
			<option value="270">' . __('90 CW','photopress') . '</option>
			<option value="90">' . __('90 CCW','photopress') . '</option>
			<option value="180">' . __('180','photopress') . '</option>
		</select>
		<input type="submit" name="Submit" value="' . __('Rotate','photopress') . ' &raquo;" />
		</p>
		</form>
		</td></tr></table>
		</div>
		';
	} elseif (isset($_GET['pp_manage_mass'])) {
		$pp_cats = pp_list_cats();
		if (isset($_GET['pp_page'])) {
			$current_page = $_GET['pp_page'];
		} else {
			$current_page = 1;
		}
		echo '
		<div class="wrap">
		<h2>' . __('Mass Edit','photopress') . '</h2>
		<p><form>' . __('Sort: ','photopress') . '<select name="sort" onChange="setbrowsesort(this)">
			<option value="pp_manage_mass=yes&sort=imgtimeD"'; if ($_GET['sort'] == 'imgtimeD' || !isset($_GET['sort'])) { echo ' selected="selected"'; } echo '>' . __('New to Old','photopress') . '</option>
			<option value="pp_manage_mass=yes&sort=imgtimeA"'; if ($_GET['sort'] == 'imgtimeA') { echo ' selected="selected"'; } echo '>' . __('Old to New','photopress') . '</option>
			<option value="pp_manage_mass=yes&sort=imgfileA"'; if ($_GET['sort'] == 'imgfileA') { echo ' selected="selected"'; } echo '>' . __('A to Z','photopress') . '</option>
			<option value="pp_manage_mass=yes&sort=imgfileD"'; if ($_GET['sort'] == 'imgfileD') { echo ' selected="selected"'; } echo '>' . __('Z to A','photopress') . '</option>
			<option value="pp_manage_mass=yes&sort=imgcatA"'; if ($_GET['sort'] == 'imgcatA') { echo ' selected="selected"'; } echo '>' . __('A-Z by cat','photopress') . '</option>
			<option value="pp_manage_mass=yes&sort=imgcatD"'; if ($_GET['sort'] == 'imgcatD') { echo ' selected="selected"'; } echo '>' . __('Z-A by cat','photopress') . '</option>
		</select></form></p>';
		if ($_GET['sort'] == 'imgfileD') {
			$pp_sort = 'imgfile DESC';
		} elseif ($_GET['sort'] == 'imgfileA') {
			$pp_sort = 'imgfile';
		} elseif ($_GET['sort'] == 'imgtimeA') {
			$pp_sort = 'imgtime';
		} elseif ($_GET['sort'] == 'imgcatA') {
			$pp_sort = 'imgcat';
		} elseif ($_GET['sort'] == 'imgcatD') {
			$pp_sort = 'imgcat DESC';
		} else {
			$pp_sort = 'imgtime DESC';
		}
		$image_count = pp_count();
		$pages = (int)ceil(($image_count/$pp_options['images_per_page']));
		echo '<p>';
		if ($pages > 1) {
			for ($i = 1; $i <= $pages; $i++) {
				if ($i != $current_page) {
					echo '<a href="edit.php?page=photopress.php&amp;pp_manage_mass=yes&amp;pp_page=' . $i; if (isset($_GET['sort'])) { echo '&amp;sort=' . $_GET['sort']; } echo '">';
				}
				echo __('Page','photopress') . ' ' . $i;
				if ($i != $current_page) {
					echo '</a>';
				}
				if ($i < $pages) {
					echo ' | ';
				}
			}
			$list_start = ($current_page - 1) * $pp_options['images_per_page'];
		} else {
			$list_start = 0;
		}
		echo '</p>';
		echo '<form name="pp_album_cats" method="post">
		<input type="hidden" name="pp_mass_update" value="update" />
		<table>
		<tr>
		<th scope="col">' . __('File','photopress') . '</th>
		<th colspan="2">' . __('Category','photopress') . '</th>
		<th scope="col">' . __('Name','photopress') . '</th>
		<th scope="col">' . __('Description','photopress') . '</th>
		<th scope="col">' . __('Delete','photopress') . '</th>
		</tr>
		';
		$current_image_list = pp_images_with_data('',$pp_sort,$list_start,$pp_options['images_per_page']);
		$i = 1;
		foreach ((array)$current_image_list as $image_data) {
			if (strlen($image_data['imgfile']) > 20) {
				$image_file_short = substr($image_data['imgfile'],0,17) . '...';
			} else {
				$image_file_short = $image_data['imgfile'];
			}
			echo '<tr>
			<td><input type="hidden" name="row' . $i . '[imgfile]" value="' . $image_data['imgfile'] . '" /><input type="hidden" name="row' . $i . '[catslug]" value="' . $image_data['catslug'] . '" />';
			$image_size = @getimagesize($pp_options['photospath'] . '/' . $image_data['imgfile']);
			echo '<a href="' . $pp_options['photosaddress'] . '/' . $image_data['imgfile'] . '" onclick="pp_image_popup(\'' . $pp_options['photosaddress'] . '/' . $image_data['imgfile'] . '\',' . $image_size[0] . ',' . $image_size[1] . '); return false;" title="' . $image_data['imgname'] . '">';
			if ($pp_options['thumbs_in_mass_edit'] == '1') {
				$thumb_size = @getimagesize($pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $image_data['imgfile']);
				echo '<img src="' . $pp_options['photosaddress'] . '/' . $pp_options['thumbprefix'] . $image_data['imgfile'] . '" ' . $thumb_size[3] . ' /><br />';
			}
			echo $image_file_short . '</a></td>
			<td><p><select name="row' . $i . '[oldimgcat]">' . pp_cat_dropdown($pp_cats,$image_data['imgcat']) . '</select><p/><p>' . __('Slug:','photopress') . '</p></td>
			<td><p><input type="text" size="12" name="row' . $i . '[newimgcat]" value="" /><p/><p>' . $image_data['catslug'] . '</p></td>
			<td><input type="text" size="12" name="row' . $i . '[imgname]" value="' . stripslashes($image_data['imgname']) . '" /></td>
			<td><input type="text" size="27" name="row' . $i . '[imgdesc]" value="' . stripslashes($image_data['imgdesc']) . '" /></td>
			<td><input type="checkbox" name="row' . $i . '[imgdelete]" value="1" /></td>
			</tr>
			';
			$i++;
		}
		echo '
		</table>
		<p class="submit"><input type="submit" name="Submit" value="' . __('Update','photopress') . ' &raquo;" /></p>
		</form>
		</div>
		';
	} elseif (isset($_GET['pp_manage_cat'])) {
		$pp_manage_slug = urldecode($_GET['pp_manage_cat']);
		$stripped_cat = pp_get_cat($pp_manage_slug);
		echo '
		<div class="wrap">
		<h2>' . __('Images in the','photopress') . ' <em>' . $stripped_cat . '</em> ' . __('category','photopress') . '</h2>
   	<p><form>' . __('Sort: ','photopress') . '<select name="sort" onChange="setbrowsesort(this)">
			<option value="pp_manage_cat=' . $pp_manage_slug . '&sort=imgtimeD"'; if ($_GET['sort'] == 'imgtimeD' || !isset($_GET['sort'])) { echo ' selected="selected"'; } echo '>' . __('New to Old','photopress') . '</option>
			<option value="pp_manage_cat=' . $pp_manage_slug . '&sort=imgtimeA"'; if ($_GET['sort'] == 'imgtimeA') { echo ' selected="selected"'; } echo '>' . __('Old to New','photopress') . '</option>
			<option value="pp_manage_cat=' . $pp_manage_slug . '&sort=imgfileA"'; if ($_GET['sort'] == 'imgfileA') { echo ' selected="selected"'; } echo '>' . __('A to Z','photopress') . '</option>
			<option value="pp_manage_cat=' . $pp_manage_slug . '&sort=imgfileD"'; if ($_GET['sort'] == 'imgfileD') { echo ' selected="selected"'; } echo '>' . __('Z to A','photopress') . '</option>
			</select></form></p>';
		if ($_GET['sort'] == 'imgfileD') {
			$pp_sort = 'imgfile DESC';
		} elseif ($_GET['sort'] == 'imgfileA') {
			$pp_sort = 'imgfile';
		} elseif ($_GET['sort'] == 'imgtimeA') {
			$pp_sort = 'imgtime';
		} else {
			$pp_sort = 'imgtime DESC';
		}
		if (isset($_GET['pp_page'])) {
			$current_page = $_GET['pp_page'];
		} else {
			$current_page = 1;
		}
		$image_count = pp_count($pp_manage_slug);
		$pages = (int)ceil($image_count/$pp_options['images_per_page']);
		if ($pages > 1) {
			echo '<p>';
			for ($i=1; $i<=$pages; $i++) {
				if ($i != $current_page) {
					echo '<a href="' . get_settings('siteurl') . '/wp-admin/edit.php?page=photopress.php&amp;pp_manage_cat=' . $pp_manage_slug . '&amp;pp_page=' . $i; if (isset($_GET['sort'])) { echo '&amp;sort=' . $_GET['sort']; } echo '">';
				}
				echo __('Page','photopress') . ' ' . $i;
				if ($i != $current_page) {
					echo '</a>';
				}
				if ($i < $pages) {
					echo ' | ';
				}
			}
			echo '</p>
			';
			$list_start = ($current_page - 1) * $pp_options['images_per_page'];
		} else { // there's only one page so it's the whole list
				$list_start = 0;
		}
		echo '<table>
		';
		$current_image_list = pp_images_with_data($pp_manage_slug,$pp_sort,$list_start,$pp_options['images_per_page']);
		$column = 0;
		foreach((array)$current_image_list as $pp_image) {
			$column++;
			if ($column == 1) {
				echo '<tr>
				';
			}
			echo '<td><a href="edit.php?page=photopress.php&amp;pp_manage_image=' . $pp_image['imgfile'] . '" title="' . stripslashes($pp_image['imgname']). '"><img src="' . $pp_options['photosaddress'] . '/' . $pp_options['thumbprefix'] . $pp_image['imgfile'] . '" title="' . stripslashes($pp_image['imgname']) . '" alt="' . stripslashes($pp_image['imgname']) . '" /><br />';
			if (strlen(stripslashes($pp_image['imgname'])) > 15) {
				echo substr(stripslashes($pp_image['imgname']),0,12) . '...';
			} else {
				echo stripslashes($pp_image['imgname']);
			}
			echo '</a></td>
			';
			if ($column == $pp_options['manager_columns']) {
				echo '</tr>
				';
				$column = 0;
			}
		}
		if ($column < $pp_options['manager_columns'] && $column > 0) {
			echo '</tr>
			';
		}
		echo '
		</table>
		</div>';
	} elseif (isset($_GET['pp_change_cat'])) {
		echo '<div class="wrap">
		<h2>' . __('Edit Categories','photopress') . '</h2>
		<form name="pp_album_cats" method="post">
		<input type="hidden" name="pp_change_cat" value="update" />
		<table>
		<tr>
			<th scope="col">' . __('Current','photopress') . '</th>
			<th scope="col">' . __('Choose Existing','photopress') . '</th>
			<th scope="col">' . __('Enter New','photopress') . '</th>
			<th scope="col">' . __('Hide in Album','photopress') . '</th>
			<th scope="col">' . __('Slug','photopress') . '</th>
		</tr>
		';
		$cat_slugs = pp_list_slugs();
		$pp_cats = pp_list_cats();
		$i = 0;
		foreach ((array)$cat_slugs as $slug) {
			$pp_cat = pp_get_cat($slug);
			echo '<tr><td><input type="hidden" name="row' . $i . '[oldcat]" value="' . $pp_cat . '" />' . $pp_cat . '</td>
			<td><select name="row' . $i . '[dropcat]" id="dropcat">';
			echo pp_cat_dropdown($pp_cats,$pp_cat); 
			echo '</select></td>
			<td><input type="text" value="' . $pp_cat . '" name="row' . $i . '[newcat]" /></td>
			<td><label><input type="radio" name="row' . $i . '[hide_cat]" value="keep" checked="checked" /> ' . __('Leave unchanged','photopress') . '</label><br />
			<label><input type="radio" name="row' . $i . '[hide_cat]" value="hide" /> ' . __('Hide all','photopress') . '</label><br />
			<label><input type="radio" name="row' . $i . '[hide_cat]" value="show" /> ' . __('Show all','photopress') . '</label>
			</td>
			<td><input type="hidden" name="row' . $i . '[oldslug]" value="' . $slug . '" /><input type="text" value="' . $slug . '" name="row' . $i . '[newslug]" /></td>
			</tr>
			';
			$i++;
		}
		echo '</table>
			<p class="submit"><input type="submit" name="Submit" value="' . __('Update Categories &raquo;','photopress') . '" /></p>
		</div>
		';
	} elseif (pp_count() > 0) {
		$cat_slugs = pp_list_slugs();
		echo '<div class="wrap">
		<h2>' . __('Categories','photopress') . '</h2>
		<table>
		';
		$column = 0;
		foreach ((array)$cat_slugs as $slug) {
			$pp_cat = pp_get_cat($slug);
			$column++;
			if ($column == 1) {
				echo '<tr>';
			}
			$randimage = pp_random_image($slug,2);
			echo '
			<td><a href="edit.php?page=photopress.php&amp;pp_manage_cat=' . $slug . '">' . $randimage[0] . '<br />
			' . $pp_cat . ' (' . pp_count($slug) . ')</a></td>
			';
			if ($column == $pp_options['manager_columns']) {
				echo '</tr>
				';
				$column = 0;
			}
		}
		if ($column > 0) {
			echo '</tr>
			';
		}
		echo '</table>
		</div>
		';
	}
}

function pp_add_management_page() {
	global $pp_options;
	add_management_page('Photopress', 'Photopress', $pp_options['min_level'], basename(__FILE__), 'pp_album_management');
}

add_action('admin_menu', 'pp_add_management_page');

// delete function, not sure how secure it is...
function pp_delete_photo($photo) {
	global $pp_options, $wpdb, $table_prefix;
	$pp_phototodelete = $pp_options['photospath'] . '/' . $photo;
	$pp_thumbtodelete = $pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $photo;
	$pp_origtodelete = $pp_options['photospath'] . '/' . $pp_options['origprefix'] . $photo;
	$table_name = $table_prefix . "photopress";
	if (!is_file($pp_thumbtodelete) || !is_file($pp_phototodelete)) {
		$wpdb->query("DELETE FROM $table_name WHERE binary imgfile = '$photo'");
		return FALSE;
	} else {
		@unlink($pp_phototodelete);
		@unlink($pp_thumbtodelete);
		if (is_file($pp_origtodelete)) {
			@unlink($pp_origtodelete);
		}
		$wpdb->query("DELETE FROM $table_name WHERE binary imgfile = '$photo'");
		return TRUE;
	}
	
}

// if the failsafe button is turned on in Options:Photopress, let's insert
if ($pp_options['failsafe_buttons'] == '1') {
	function pp_failsafe_buttons() {
		echo '
			<input type="button" value="Photos" onclick="return pp_popup()" />';
	}
	add_action('simple_edit_form', 'pp_failsafe_buttons');
	add_action('edit_form_advanced', 'pp_failsafe_buttons');
}

// rotate image function, rotates $image counterclockwise by $angle, returns TRUE or an error message
function pp_rotate($image, $angle, $copy) {
	global $pp_options;
	$origdest = $pp_options['photospath'] . '/' . $image; // this should be the path to the image
	if (is_file($origdest)) { // if the file isn't there we shouldn't do anything
		$newdest = $origdest;
		if ($copy == 'copy') { // find a new name if making a copy
			$i = 1;
			while (is_file($newdest)) {
				$realbase = substr($image,0,strrpos($image, '.'));
				$newname = $realbase . '_' . $i . '.' . pathinfo($origdest,PATHINFO_EXTENSION);
				$newdest = $pp_options['photospath'] . '/' . $newname;
				$i++;
			}
		}
		$type = @getimagesize($origdest);
		// if the associated function doesn't exist - then it's not
		// handle. duh. i hope.
		if (!function_exists('imagegif') && $type[2] == 1) {
			$error = __('Filetype not supported.','photopress');
		} elseif(!function_exists('imagejpeg') && $type[2] == 2) {
			$error = __('Filetype not supported.','photopress');
		} elseif(!function_exists('imagepng') && $type[2] == 3) {
			$error = __('Filetype not supported.','photopress');
		} else {
		// create the copy from the original file, rotate, and write to disk
			if($type[2] == 1) {
				$newimage = imagecreatefromgif($origdest);
				$rotated = imagerotate($newimage,$angle,0);
				if(!imagegif($rotated, $newdest))
					$error = __('Failed to write rotated image to disk at ','photopress') . $newdest;
			} elseif($type[2] == 2) {
				$newimage = imagecreatefromjpeg($origdest);
				$rotated = imagerotate($newimage,$angle,0);
				if(!imagejpeg($rotated, $newdest, $pp_options['quality']))
					$error = __('Failed to write rotated image to disk at ','photopress') . $newdest;
			} elseif($type[2] == 3) {
				$newimage = imagecreatefrompng($origdest);
				$rotated = imagerotate($newimage,$angle,0);
				if(!imagepng($rotated, $newdest))
					$error = __('Failed to write rotated image to disk at ','photopress') . $newdest;
			}
			if (is_file($newdest)) {
				@chmod($newdest,0664);
				$thumbed = pp_resize($newdest, $pp_options['thumbsize'], $pp_options['thumbsize'], $pp_options['thumbprefix'], 1);
				@chmod($pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $newname,0664);
				$data = pp_get_data($image);
				pp_table_update(array($newname,$data['imgname'],$data['imgdesc'],$data['imgcat'],$data['catslug']));
			} else {
				$error = __('Failed to write rotated image to disk at ','photopress') . $newdest;
			}
			@imagedestroy($image);
		}
	} else {
		$error = $image . __(' does not exist.','photopress');
	}
if (!empty($error)) {
	return $error;
} else {
	return 1;
}
}

// install a table in the db for PP image data
function pp_table_install() {
	global $table_prefix, $wpdb;
	$table_name = $table_prefix . "photopress";
	$sql = "CREATE TABLE ".$table_name." (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		imgfile varchar(100) NOT NULL,
		imgname varchar(55) NULL,
		imgdesc text NULL,
		imgcat varchar(55) NULL,
		catslug varchar(55) NULL,
		imghide varchar(55) NULL,
		imgtime varchar(55) NULL,
		UNIQUE KEY id (id)
	);";
	require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
	dbDelta($sql);
// import image info stored in the WP Options by previous version of PP
	if (get_option('pp_album_cats') && !is_array($wpdb->get_col("SELECT imgfile FROM $table_name LIMIT 5"))) { // only import if there's data to import and the table is empty
		$images = array();
		$images = pp_folder_contents();
		$cat_array = array();
		$cat_array = get_option('pp_album_cats');
		foreach((array)$images as $img) {
			$img_key = substr_replace($img, '_', -4, 1);
			if (!empty($cat_array[$img_key])) {
				$category = $cat_array[$img_key];
			} else {
				$category = 'Default';
			}
			$name = strtr(substr($img, 0, -4), "_", " ");
			$catslug = pp_slugify($category);
			$import_array = array($img, $name, $name, $category, $catslug);
			pp_table_update($import_array);
		}
	} else {// if there's already data there then then we should see if the slug field needs to be filled
		pp_slug_import();
	}
}

// makes populates the slug field with slugified cat names
function pp_slug_import() {
	global $table_prefix, $wpdb;
	$table_name = $table_prefix . "photopress";
	if ($wpdb->get_var("SELECT count(*) FROM $table_name where catslug is null") > 0) {
		$cats = array_unique($wpdb->get_col("SELECT imgcat FROM $table_name"));
		foreach ($cats as $cat) {
			$catslug = pp_slugify($cat);
			$catforquery = $wpdb->escape($cat);
			$wpdb->query("UPDATE $table_name SET catslug='$catslug' WHERE binary imgcat='$catforquery'");
		}
	}
}

// run the table installer when the plugin is activated
if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
	add_action('init', 'pp_table_install');
}

// re-saves category names, escaping characters which might not have been escaped by a previous version of Photopress
function pp_fix_cats() {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix . 'photopress';
	$oldcats = pp_list_cats();
	$newcats = $oldcats;
	$failcheck = 0;
	foreach((array)$newcats as $key=>$cat) {
		$cat = $wpdb->escape($cat);
		$wpdb->query("UPDATE $table_name SET imgcat='$cat' WHERE binary imgcat='$oldcats[$key]'");
	}
	return true;
}

// imports images in the photos folder that aren't in the database
function pp_import_orphans() {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix . 'photopress';
	$full_array = pp_folder_contents();
	$in_db = $wpdb->get_col("SELECT imgfile FROM $table_name");
	$not_in_db = array_diff((array)$full_array, (array)$in_db);
	$imported = 0;
	foreach((array)$not_in_db as $imgfile) {
		$imgname = strtr(substr($imgfile, 0, strrpos($imgfile, '.')), "_", " ");
		$imgcat = 'Default';
		$import_array = array($imgfile, $imgname, $imgname, $imgcat);
		pp_table_update($import_array);
		$imported++;
	}
	return $imported;
}

function pp_change_cat($oldslug,$newslug,$oldcat,$newcat,$hide) {
	global $wpdb, $table_prefix, $pp_options;
	$table_name = $table_prefix . 'photopress';
	$oldcat = $wpdb->escape($oldcat);
	$oldslug = $wpdb->escape($oldslug);
	$newcat = $wpdb->escape($newcat);
	$newnewslug = $wpdb->escape($newslug);
	if ($hide == 'keep') { // do nothing to imghide column
		if ($wpdb->query("UPDATE $table_name SET imgcat='$newcat',catslug='$newslug' WHERE binary catslug='$oldslug'")) {
			return TRUE;
		} else {
			return FALSE;
		}
	} else {
		if ($hide == 'show') {
			$hide = '';
		}
		if ($wpdb->query("UPDATE $table_name SET imgcat='$newcat',catslug='$newslug',imghide='$hide' WHERE binary catslug='$oldslug'")) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}

function pp_filename_repair() {
	global $wpdb, $table_prefix, $pp_options;
	$table_name = $table_prefix . 'photopress';
	$images = pp_folder_contents();
	$repaired = 0;
	foreach ((array)$images as $image) {
		if (preg_match('/[^a-z0-9_.]/i',$image)) {
			$newname = preg_replace('/[^a-z0-9_.]/i', '_', $image);
			$destination = $pp_options['photospath'] . '/' . $newname;
			$i = 1;
			while (is_file($destination)) { // in case the new name is already taken
				$image_name = pathinfo($destination,PATHINFO_BASENAME);
				$realbase = substr($image_name,0,strrpos($image_name, '.'));
				$destination = $pp_options['photospath'] . '/' . $realbase . '_' . $i . '.' . pathinfo($destination,PATHINFO_EXTENSION);
				$i++;
			}
			if (is_file($pp_options['photospath'] . '/' . $image)) {
				@rename($pp_options['photospath'] . '/' . $image , $destination);
			}
			if (is_file($pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $image)) {
				@rename($pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $image , $pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . pathinfo($destination,PATHINFO_BASENAME));
			}
			if (is_file($pp_options['photospath'] . '/' . $pp_options['origprefix'] . $image)) {
				@rename($pp_options['photospath'] . '/' . $pp_options['origprefix'] . $image , $pp_options['photospath'] . '/' . $pp_options['origprefix'] . pathinfo($destination,PATHINFO_BASENAME));
			}
			if (is_file($destination)) {
				$repaired++;
			}
			$escaped_oldimage = $wpdb->escape($image);
			$escaped_newimage = $wpdb->escape(pathinfo($destination,PATHINFO_BASENAME));
			$wpdb->query("UPDATE $table_name SET imgfile='$escaped_newimage' WHERE binary imgfile='$escaped_oldimage'");
		}
	}
	if ($fixed > 0) {
		return $fixed;
	} else {
		return FALSE;
	}
}

function pp_mass_resize() {
	global $pp_options;
	$images = pp_folder_contents();
	$thumbsdone = 0;
	$imagesdone = 0;
	foreach ((array)$images as $image) {
		$pathtoimage = $pp_options['photospath'] . '/' . $image;
		$pathtothumb = $pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $image;
		if ($imginfo = getimagesize($pathtoimage)) {
			if (($imginfo[0] > $pp_options['maxsize']) || ($imginfo[1] > $pp_options['maxsize'])) {
				if (pp_resize($pathtoimage, $pp_options['maxsize'], 0, '', 0)) {
					$imagesdone++;
				}
			}
		}
		if ($thumbinfo = getimagesize($pathtothumb)) {
			if (($thumbinfo[0] > $pp_options['thumbsize']) || ($thumbinfo[1] > $pp_options['thumbsize'])) {
				if (pp_resize($pathtoimage, $pp_options['thumbsize'], $pp_options['thumbsize'], $pp_options['thumbprefix'], 1)) {
					$thumbsdone++;
				}
			}
		}
	}
return array($imagesdone,$thumbsdone);
}

function pp_db_cleanup() {
	global $wpdb, $table_prefix, $pp_options;
	$table_name = $table_prefix . 'photopress';
	$in_db = $wpdb->get_col("SELECT imgfile FROM $table_name");
	if ($full_array = pp_folder_contents()) {
		$not_on_disk = array_diff($in_db, $full_array);
	} else {
		$not_on_disk = $in_db;
	}
	$records = 0;
	foreach ($not_on_disk as $photo) {
		if (!is_file($pp_options['photospath'] . '/' . $photo)) {
			if ($wpdb->query("DELETE FROM $table_name WHERE binary imgfile = '$photo'")) {
				$records++;
			}
		}
	}
	if ($records > 0) {
		return $records;
	} else {
		return FALSE;
	}
}

// turn PP tags in posts into linked image tags
function photopress_make_link($stuff) {
	global $pp_options;
	$file = explode(',',$stuff);
	if (!empty($file[2])) { // support earlier tags that didn't have own class
		$image_class = $file[2];
	} else {
		$image_class = 'centered';
	}
	if ($file[1] == 'thumb') {
		if ($thumbsize = @getimagesize($pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $file[0])) {
			if ($pp_options['album'] == '1') {
				$image_data = pp_get_data($file[0]);
				return '<a href="' . $pp_options['imgaddress'] . $file[0] . '" title="' . htmlentities2($image_data['imgname']) . '"><img src="' . $pp_options['photosaddress'] . '/' . $pp_options['thumbprefix'] . $file[0] . '" class="' . $image_class . '" alt="' . htmlentities2($image_data['imgname']) . '" ' . $thumbsize[3] . ' /></a>';
			} else {
				$image_size = @getimagesize($pp_options['photospath'] . '/' . $file[0]);
				return '<a href="' . $pp_options['photosaddress'] . '/' . $file[0] . '" title="' . htmlentities2($image_data['imgname']) . '" onclick="pp_image_popup(\'' . htmlentities2($pp_options['photosaddress'] . '/' . $file[0]) . '\',' . $image_size[0] . ',' . $image_size[1] . '); return false;"><img src="' . $pp_options['photosaddress'] . '/' . $pp_options['thumbprefix'] . $file[0] . '" class="' . $image_class . '" alt="' . htmlentities2($image_data['imgname']) . '" ' . $thumbsize[3] . ' /></a>';
			}
		} else {
			return ''; // if the image isn't there return nothing
		}
	} elseif ($file[1] == 'full') {
		if ($fullsize = @getimagesize($pp_options['photospath'] . '/' . $file[0])){
			if ($pp_options['album'] == '1') {
				$image_data = pp_get_data($file[0]);
				return '<a href="' . $pp_options['imgaddress'] . $file[0] . '" title="' . htmlentities2($image_data['imgname']) . '"><img src="' . $pp_options['photosaddress'] . '/' . $file[0] . '" class="' . $image_class . '" alt="' . htmlentities2($image_data['imgname']) . '" ' . $fullsize[3] . ' /></a>';
			} else {
				return '<a href="' . $pp_options['photosaddress'] . '/' . $file[0] . '" title="' . htmlentities2($image_data['imgname']) . '" onclick="pp_image_popup(\'' . htmlentities2($pp_options['photosaddress'] . '/' . $file[0]) . '\',' . $fullsize[0] . ',' . $fullsize[1] . '); return false;"><img src="' . $pp_options['photosaddress'] . '/' . $file[0] . '" class="' . $image_class . '" alt="' . htmlentities2($image_data['imgname']) . '" ' . $fullsize[3] . ' /></a>';
			}
		} else {
			return ''; // if the image isn't there return nothing
		}
	} else {
		return ''; // if there's something wrong with the tag return nothing
	}
}

// process WP content for PP tags
function photopress_tag_process($content = '') {
	return preg_replace( "/\[photopress\:(.*?)\]/e","photopress_make_link('\\1')", $content);
}

add_filter('the_content', 'photopress_tag_process');
add_filter('the_excerpt', 'photopress_tag_process');

class photopress_actions {

function &photopress_album_rewrite(&$rules) {
	global $wp_version, $pp_options;
	if ($pp_options['wppermalinks'] == 'index') {
		$root_token = 'index.php/';
	} else {
		$root_token = '';
	}
	$rules[$root_token . $pp_options['album_token'] . '/' . $pp_options['images_token'] . '/(.+)/?$'] = "index.php?pp_album=main&pp_image=\$matches[1]";
	$rules[$root_token . $pp_options['album_token'] . '/' . $pp_options['cat_token'] . '/(.+)/(.*)/?$'] = "index.php?pp_album=main&pp_cat=\$matches[1]&pp_page=\$matches[2]";
	$rules[$root_token . $pp_options['album_token'] . '/' . $pp_options['cat_token'] . '/(.+)/?$'] = "index.php?pp_album=main&pp_cat=\$matches[1]";
	$rules[$root_token . '(' . $pp_options['album_token'] . ')$'] = "index.php?pp_album=main";
	return $rules;
}

function &pp_add_query_var(&$wpvar_array) {
	$wpvar_array[] = 'pp_album';
	$wpvar_array[] = 'pp_page';
	$wpvar_array[] = 'pp_cat';
	$wpvar_array[] = 'pp_image';
	return $wpvar_array;
}

function &photopress_album_rewrite_old(&$rewrite) {
	global $wp_rewrite, $pp_options;
	$wp_rewrite->add_rewrite_tag('%pp_image%', '(.+)', 'pp_album=main&pp_image=');
	$wp_rewrite->add_rewrite_tag('%pp_cat%', '(.+)', 'pp_album=main&pp_cat=');
	$wp_rewrite->add_rewrite_tag('%pp_page%', '(.*)', 'pp_page=');
	$wp_rewrite->add_rewrite_tag('%pp_album%', '(' . $pp_options['album_token'] . ')', 'pp_album=main');
	$structure = $wp_rewrite->root . $pp_options['album_token'] . '/' . $pp_options['images_token'] . '/%pp_image%';
	$rewrite += $wp_rewrite->generate_rewrite_rule($structure);
	$structure = $wp_rewrite->root . $pp_options['album_token'] . '/' . $pp_options['cat_token'] . '/%pp_cat%/%pp_page%';
	$rewrite += $wp_rewrite->generate_rewrite_rule($structure);
	$structure = $wp_rewrite->root . $pp_options['album_token'] . '/' . $pp_options['cat_token'] . '/%pp_cat%';
	$rewrite += $wp_rewrite->generate_rewrite_rule($structure);
	$structure = $wp_rewrite->root . '%pp_album%';
	$rewrite += $wp_rewrite->generate_rewrite_rule($structure);
	return ($rewrite);
}

}

if ($wp_version{0} == '1') {
	add_filter('root_rewrite_rules', array('photopress_actions','photopress_album_rewrite_old'));
} else {
	add_filter('rewrite_rules_array', array('photopress_actions','photopress_album_rewrite'));
}
add_filter('query_vars',array('photopress_actions','pp_add_query_var'));

?>
