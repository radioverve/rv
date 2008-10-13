<?php
/* Photopress upload and browse popup - original hack by shockingbird.com for b2 */
require_once( dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-config.php');
require_once( ABSPATH . 'wp-content/plugins/photopress/include.php');

get_currentuserinfo();
if ($user_level == 0) //Checks to see if user has logged in
        die (__('Try logging in','photopress'));
if ($user_level < $pp_options['min_level']) //Checks to see if user's level is high enough
        die (__('Ask the administrator to promote you.'));

// print the header (there's probably a better way to do this)
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>WordPress &rsaquo; Upload Images</title>
<link rel="stylesheet" href="' . get_settings('siteurl') . '/wp-admin/wp-admin.css" type="text/css" />
<link rel="shortcut icon" href="' . get_settings('siteurl') . '/wp-images/wp-favicon.png" />
<meta http-equiv="Content-Type" content="text/html; charset=' . get_settings('blog_charset') . '" />
<style type="text/css">
.pp_insert_button {
	font-weight: bold;
}
.pp_browse_table {
	width: 100%;
	text-align: right;
}
#insertimages {
	display: block;
}
#uploadcomplete {
	display: none;
}
</style>
<script type="text/javascript">
//<![CDATA[
window.focus();
window.onload = function() {
	if (typeof window.opener.document.post == "undefined") {
		var bbutton = document.getElementById("adminmenu").getElementsByTagName("li")[1];
		bbutton.style.display = "none";
		if (document.getElementById("insert_button")) {
			var insertbutton = document.getElementById("insertbutton");
			insertbutton.style.display = "none";
		}
		if (document.getElementById("insertimages")) {
			var insertimages = document.getElementById("insertimages");
			insertimages.style.display = "none";
		}
		if (document.getElementById("uploadcomplete")) {
			var uploadcomplete = document.getElementById("uploadcomplete");
			uploadcomplete.style.display = "block";
		}
		if (document.getElementById("hideuploads")) {
			var hideuploads = document.getElementById("hideuploads");
			hideuploads.style.display = "none";
		}
	}
}
function insertcode(imgfile, imgsize, classname, imgname, width, height, twidth, theight) {
	var usealbum = "' . $pp_options['album'] . '";
	var inserttags = "' . $pp_options['insert_tags'] . '";
	var thumbprefix = "' . $pp_options['thumbprefix'] . '";
	if (imgsize == "thumb") {
		var thumbcode = thumbprefix;
		var imgwidth = twidth;
		var imgheight = theight;
	} else {
		var thumbcode = "";
		var imgwidth = width;
		var imgheight = height;
	}
	if (inserttags == "1") {
		var linkcode = "[photopress:" + imgfile + "," + imgsize + "," + classname + "]";
	} else {
		if (usealbum == "0") {
			var linkcode = "<a href=\"' . $pp_options['photosaddress'] . '/" + imgfile + "\" onclick=\"pp_image_popup(\'' . $pp_options['photosaddress'] . '/" + imgfile + "\'," + width + "," + height + "\); return false;\" title=\"" + imgname + "\"><img src=\"' . $pp_options['photosaddress'] . '/" + thumbcode + imgfile + "\" width=\"" + imgwidth + "\" height=\"" + imgheight + "\" alt=\"" + imgname + "\" class=\"" + classname + "\" /></a>";
		} else {
			var linkcode = "<a href=\"' . $pp_options['imgaddress'] . '" + imgfile + "\" title=\"" + imgname + "\"><img src=\"' . $pp_options['photosaddress'] . '/" + thumbcode + imgfile + "\" alt=\"" + imgname + "\" width=\"" + imgwidth + "\" height=\"" + imgheight + "\" class=\"" + classname + "\" /></a>";
		}
	}
	if (typeof tinyMCE != "undefined") {
		tinyMCE.execCommand("mceInsertContent", false, linkcode);
	} else if (typeof window.opener.document.post.content != "undefined") {
		window.opener.document.post.content.value += linkcode;
	}
	return;
}
if (typeof window.opener.tinyMCE != "undefined") {
';
if (function_exists('get_user_option')) {
	if (get_user_option('rich_editing') == 'true') {
		include_once(ABSPATH . 'wp-includes/js/tinymce/tiny_mce_popup.js');
	}
}
echo '
}
function setnumphotos(num) {
	window.top.location.href = "' . get_settings('siteurl') . '/wp-content/plugins/photopress/popup.php?action=upload&num_photos=" + num.options[num.selectedIndex].value;
	return false;
}
function setcategory(cat) {
	window.top.location.href = "' . get_settings('siteurl') . '/wp-content/plugins/photopress/popup.php?action=browse&cat=" + cat.options[cat.selectedIndex].value;
	return false;
}
function setbrowsesort(sort) {
	window.top.location.href = "' . get_settings('siteurl') . '/wp-content/plugins/photopress/popup.php?action=browse&sort=" + sort.options[sort.selectedIndex].value;
	return false;
}
//]]>
</script>
</head>
<body>
<ul id="adminmenu">
	<li><a '; if ($_GET['action'] == 'upload' || $_POST['submit']) { echo 'class="current" ';} echo 'href="' . get_settings('siteurl') . '/wp-content/plugins/photopress/popup.php?action=upload">' . __('Upload','photopress') . '</a></li>
	<li><a ';  if ($_GET['action'] == 'browse') { echo 'class="current" ';} echo 'href="' . get_settings('siteurl') . '/wp-content/plugins/photopress/popup.php?action=browse">' . __('Browse','photopress') . '</a></li>
	<li><a href="#" onclick="window.close()">' . __('Close window','photopress') . '</a></li>
</ul>
<div class="wrap">
';

if ($_GET['action'] == 'browse') {
	$image_count = pp_count();
	if ($image_count > 0) {
		if ($image_count > 1) {
			echo '<p><form>' . __('Sort: ','photopress') . '<select name="sort" onChange="setbrowsesort(this)">
				<option value="imgtimeD"'; if ($_GET['sort'] == 'imgtimeD' || !isset($_GET['sort'])) { echo ' selected="selected"'; } echo '>' . __('New to Old','photopress') . '</option>
				<option value="imgtimeA"'; if ($_GET['sort'] == 'imgtimeA') { echo ' selected="selected"'; } echo '>' . __('Old to New','photopress') . '</option>
				<option value="imgfileA"'; if ($_GET['sort'] == 'imgfileA') { echo ' selected="selected"'; } echo '>' . __('A to Z','photopress') . '</option>
				<option value="imgfileD"'; if ($_GET['sort'] == 'imgfileD') { echo ' selected="selected"'; } echo '>' . __('Z to A','photopress') . '</option>
				<option value="imgcatA"'; if ($_GET['sort'] == 'imgcatA') { echo ' selected="selected"'; } echo '>' . __('A-Z by cat','photopress') . '</option>
				<option value="imgcatD"'; if ($_GET['sort'] == 'imgcatD') { echo ' selected="selected"'; } echo '>' . __('Z-A by cat','photopress') . '</option>
			</select><br />' . __('or show Category: ','photopress') . '<select name="category" onChange="setcategory(this)">
				<option value="pp_browse_all_cats"';
			$pp_slugs = pp_list_slugs();
			if ($_GET['cat'] == 'pp_browse_all_cats' || !isset($_GET['cat'])) {
				echo ' selected="selected"';
			} else {
				$current_cat = $_GET['cat'];
				$image_count = pp_count($_GET['cat']);
			}
			echo '>' . __('Show All','photopress') . '</option>
			';
			foreach ((array)$pp_slugs as $slug) {
				echo '<option value="' . $slug . '"';
				if ($_GET['cat'] == $slug) { echo ' selected="selected"'; }
				echo '>' . stripslashes(pp_get_cat($slug)) . '</option>
				';
			}
			echo '</select></form></p>';
		}
		if ($_GET['sort'] == 'imgfileD') {
			$pp_sort = 'imgfile DESC';
		} elseif ($_GET['sort'] == 'imgfileA') {
			$pp_sort = 'imgfile';
		} elseif ($_GET['sort'] == 'imgtimeA') {
			$pp_sort = 'imgtime';
		} elseif ($_GET['sort'] == 'imgcatD') {
			$pp_sort = 'imgcat DESC';
		} elseif ($_GET['sort'] == 'imgcatA') {
			$pp_sort = 'imgcat';
		} else {
			$pp_sort = 'imgtime DESC';
		}
		if ($image_count > 0) {
			$pages = (int)ceil($image_count/$pp_options['images_per_page']);
			if ($pages > 1) {
			if (isset($_GET['page'])) {
				$current_page = $_GET['page'];
			} else {
				$current_page = 1;
			}
			echo '<p>';
			if ($current_page > 1) {
				echo '<a href="' . get_settings('siteurl') . '/wp-content/plugins/photopress/popup.php?action=browse&amp;page=' . ($current_page - 1); if (isset($_GET['sort'])) { echo '&amp;sort=' . $_GET['sort']; } if (isset($_GET['cat'])) { echo '&amp;cat=' . $_GET['cat']; } echo '">';
			}
			echo __('Previous','photopress');
			if ($current_page > 1) {
				echo '</a>';
			}
			echo ' | ';
			for ($i = 1; $i <= $pages; $i++) {
				if ($i != $current_page) {
					echo '<a href="' . get_settings('siteurl') . '/wp-content/plugins/photopress/popup.php?action=browse&amp;page=' . $i; if (isset($_GET['sort'])) { echo '&amp;sort=' . $_GET['sort']; } if (isset($_GET['cat'])) { echo '&amp;cat=' . $_GET['cat']; } echo '">';
				}
				echo $i;
				if ($i != $current_page) {
					echo '</a>';
				}
				if ($i < $pages) {
					echo ' | ';
				}
			}
			echo ' | ';
			if ($current_page < $pages) {
				echo '<a href="' . get_settings('siteurl') . '/wp-content/plugins/photopress/popup.php?action=browse&amp;page=' . ($current_page + 1); if (isset($_GET['sort'])) { echo '&amp;sort=' . $_GET['sort']; } if (isset($_GET['cat'])) { echo '&amp;cat=' . $_GET['cat']; } echo '">';
			}
			echo __('Next','photopress');
			if ($current_page < $pages) {
				echo '</a>';
			}
			$list_start = ($current_page - 1) * $pp_options['images_per_page'];
			echo '</p>';
			} else { // there's only one page so we'll start with the 0th image
				$list_start = 0;
			}
			echo '</div>
			';
			if (isset($_GET['cat']) && $_GET['cat'] != 'pp_browse_all_cats') {
				$current_image_list = pp_images_with_data($_GET['cat'],$pp_sort,$list_start,$pp_options['images_per_page']);
			} else {
				$current_image_list = pp_images_with_data(FALSE,$pp_sort,$list_start,$pp_options['images_per_page']);
			}
			foreach ((array)$current_image_list as $image) {
				$thumbsize = @getimagesize($pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $image['imgfile']);
				$imgsize = @getimagesize($pp_options['photospath'] . '/' . $image['imgfile']);
				echo '<div class="wrap"><table class="pp_browse_table"><tr><td><img src="' . $pp_options['photosaddress'] . '/' . $pp_options['thumbprefix'] . $image['imgfile'] . '" ' . $thumbsize[3] . ' title="' . stripslashes($image['imgname']) . '" /></td>
				<td><p><form>
				<input type="hidden" name="file" value="' . $image['imgfile'] . '" />
				' . __('Size:','photopress') . '<select name="thumb">
					<option value="thumb"'; if ($pp_options['insert_thumb'] == '1') { echo ' selected="selected"'; } echo '>' . __('Thumbnail','photopress') . '</option>
					<option value="full"'; if ($pp_options['insert_thumb'] == '0') { echo ' selected="selected"'; } echo '>' . __('Full Image','photopress') . '</option>
				</select><br />
            ' . __('Style:','photopress') . '<select name="classes">';
				$classes = explode(' ',trim($pp_options['image_class']));
				foreach ((array)$classes as $class) {
					echo '<option value="' . $class . '"'; if ($class == $classes[0]) { echo ' selected="selected"'; } echo '>' . $class . '</option>
					';
				}
				if (strlen($image['imgfile']) > 20) {
					$image_file_short = substr($image['imgfile'],0,17) . '...';
				} else {
					$image_file_short = $image['imgfile'];
				}
				$insert_text = __('Insert','photopress') . "\n" . $image_file_short;
				$inserted_text = __('Inserted','photopress');
				echo '</select></p>
				<p class="submit"><input class="pp_insert_button" type="button" name="insert" value="' . $insert_text . '" onClick="insertcode(this.form.file.value,this.form.thumb.options[this.form.thumb.selectedIndex].value,this.form.classes.options[this.form.classes.selectedIndex].value,\'' . htmlentities2($image['imgname']) . '\',' . $imgsize[0] . ',' . $imgsize[1] . ',' . $thumbsize[0] . ',' . $thumbsize[1] . '); this.form.insert.value=\'' . $inserted_text . '\'+\'\n\'+\'' . $image_file_short . '\'; return false;" /></p>
				</form></td></tr></table></div>
				';
			}
		}
	} else {
		echo '<p>' . __('No photos have been uploaded yet.','photopress') . '</p></div>';
	}
	echo '
</body>
</html>';
die();
}

if ($_GET['action'] == 'upload') {
	if (!is_writable($pp_options['photospath'])) {
		echo '<h3>' . __('The photos folder is not writable','photopress') . '</h3>
		<p>';
		printf(__("It doesn't look like you can use Photopress at this time because the directory you have specified (<code>%s</code>) isn't writable. Check the permissions on the directory and for typos in your configuration.","photopress"), $pp_options['photospath']);
		echo '</p>
	</div>
</body>
</html>';
		die();
	}
	if (!extension_loaded('gd')) {
		if (!dl('gd.so')) {
			echo '<h3>' . __('The GD module is not installed','photopress') . '</h3>
			<p>' . __('It appears that you do not have the GD module for PHP installed. This module is required in order to resize images.','photopress') . '</p>
	</div>
</body>
</html>';
			die();
		}
	}
	if (isset($_GET['num_photos'])) {
		$num_photos = $_GET['num_photos'];
	} else {
		$num_photos = 1;
	}
	echo '<p>' . __('Number to upload:','photopress') . '<select name="num_photos" onChange="setnumphotos(this)">';
		for ($i=1;$i<=10;$i++) {
			echo '<option value="' . $i . '" ';
				if ((int)$num_photos == $i) {
					echo 'selected="selected"';
				}
			echo '>' . $i . '</option>
			';
		}
		echo '</select></p>
		<form action="' . get_settings('siteurl') . '/wp-content/plugins/photopress/popup.php" method="post" enctype="multipart/form-data">
		<input type="hidden" name="MAX_FILE_SIZE" value="' . 1024*$pp_options['max_size'] . '" />
		<input type="hidden" name="upload" id="upload" value="yes" />';
		for ($i=0; $i<$num_photos; $i++) {
			echo '<p><input type="file" name="file_name[]" id="file_name" size="30" class="uploadform" /></p>
			';
		}
		echo '<p class="submit"><input type="submit" name="submit" value="' . __('Upload','photopress') . '" /></p>
		</form>
	</div>
</body>
</html>';
die();
}

if ($_POST['submit']) {
	if ($_POST['upload']) {
		echo '<h3>' . __('Uploading Images','photopress') . '</h3>
		<p>' . __('Your uploads have been attempted. Problems with each image are reported below. You can enter some information about your images now or just save the defaults and move on to the next screen, where you can insert images into your post.','photopress') . '</p>
		</div>
		<form action="' . get_settings('siteurl') . '/wp-content/plugins/photopress/popup.php" method="post" enctype="multipart/form-data">
		<input type="hidden" name="data_update" value="yes" />
		';
		$allowedtypes = explode(' ', trim(strtolower($pp_options['allowedtypes'])));
		$j=0;
		foreach ($_FILES['file_name']['error'] as $key=>$error) {
			$image_ext = trim(strtolower(pathinfo($_FILES['file_name']['name'][$key],PATHINFO_EXTENSION)));
			if (function_exists('exif_imagetype')) {
				$image_type = exif_imagetype($_FILES['file_name']['tmp_name'][$key]);
			} else {
				$image_type = getimagesize($_FILES['file_name']['tmp_name'][$key]);
			}
			if (in_array($image_ext,$allowedtypes) && $image_type) { // the extension is okay and the file is an image
				$clean_name = preg_replace('/[^a-z0-9_.]/i', '_', $_FILES['file_name']['name'][$key]);
				$destination = $pp_options['photospath'] . '/' . $clean_name;
				$i=1;
				while (is_file($destination)) { // if it's a dupe give it a new name
					$image_name = pathinfo($destination,PATHINFO_BASENAME);
					$realbase = substr($image_name,0,strrpos($image_name, '.'));
					$destination = $pp_options['photospath'] . '/' . $realbase . '_' . $i . '.' . pathinfo($destination,PATHINFO_EXTENSION);
					$i++;
				}
				@move_uploaded_file($_FILES['file_name']['tmp_name'][$key],$destination);
				if ($pp_options['originals'] == '1') {
					$origdest = $pp_options['photospath'] . '/' . $pp_options['origprefix'] . pathinfo($destination,PATHINFO_BASENAME);
					@copy($destination, $origdest);
					@chmod($origdest, 0664);
				}
				$resized = pp_resize($destination, $pp_options['maxsize'], 0, '', 0);
				@chmod($destination,0664);
				$thumbed = pp_resize($destination, $pp_options['thumbsize'], $pp_options['thumbsize'], $pp_options['thumbprefix'], 1);
				$imgfile = pathinfo($destination,PATHINFO_BASENAME);
				@chmod($pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $imgfile,0664);
				$imgname = strtr(substr($imgfile,0,strrpos($imgfile, '.')),'_',' ');
				$update_array = array($imgfile,$imgname,$imgname,'Default','default');
				$updated = pp_table_update($update_array);
				$thumbsize = getimagesize($pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $imgfile);
				if (strlen($imgfile) > 30) {
					$image_file_short = substr($imgfile,0,27) . '...';
				} else {
					$image_file_short = $imgfile;
				}
				$pp_categories = pp_list_cats();
				echo '<div class="wrap">
				<input type="hidden" name="row' . $j . '[imgfile]" value="' . $imgfile . '" />
				<p><img src="' . $pp_options['photosaddress'] . '/' . $pp_options['thumbprefix'] . $imgfile . '" ' . $thumbsize[3] . ' alt="' . htmlentities2($imgname) . '" title="' . htmlentities2($imgname) . '" /><br />
				' . __('File: ','photopress') . $image_file_short . '<br />
				' . __('Name: ','photopress') . '<input type="text" name="row' . $j . '[imgname]" class="uploadform" size="15" value="' . $imgname . '" /><br />
				' . __('Description: ','photopress') . '<input type="text" name="row' . $j . '[imgdesc]" class="uploadform" size="15" value="' . $imgname . '" /><br />
				' . __('Category: ','photopress') . '<input type="text" name="row' . $j . '[imgcat]" class="uploadform" size="15" value="" /><br />';
				echo '<select name="row' . $j . '[imgcatdrop]">';
				echo pp_cat_dropdown($pp_categories);
				echo '</select>
				</p></div>';
				$j++;
			} else {
				echo '<div class="wrap">';
				printf(__('Error uploading <strong>%s</strong>. The file is either too large, not one of the allowed types, or not an image. Check the file and try again.','photopress'),$_FILES['file_name']['name'][$key]);
				echo '</div>
				';
			}
		}
		echo '<div class="wrap">
		<p class="submit"><input type="submit" name="submit" value="' . __('Next','photopress') . '" /></p>
		</div>
		</form>
</body>
</html>';
die();
	} elseif (isset($_POST['data_update'])) {
		echo '<div id="insertimages"><h3>' . __('Insert Images','photopress') . '</h3>
		<p>' . __('Your data has been saved. You can now insert the images into your post by clicking the buttons below. Change how the images will appear in your post using the drop-down menus.','photopress') . '</p></div>
		<div id="uploadcomplete"><h3>' . __('Uploads Complete','photopress') . '</h3>
		<p>' . __('Your data has been saved. You can now upload more images or close this window.','photopress') . '</p></div>
		</div>
		<div id="hideuploads">';
		foreach ((array)$_POST as $key=>$data_update) {
			if (is_array($data_update)) {
				if (!empty($data_update['imgcat'])) {
					$imgcat = $data_update['imgcat'];
					$catslug = pp_slugify($imgcat);
				} else {
					$imgcat = $data_update['imgcatdrop'];
					$catslug = pp_slugify($imgcat);
				}
				$update_array = array($data_update['imgfile'],$data_update['imgname'],$data_update['imgdesc'],$imgcat,$catslug);
				pp_table_update($update_array);
				$image_link_code = '[photopress:'. $data_update['imgfile'] . ',full]';
				$thumb_link_code = '[photopress:' . $data_update['imgfile'] . ',thumb]';
				$thumbsize = getimagesize($pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $data_update['imgfile']);
				echo '<div class="wrap"><form><table class="pp_browse_table"><tr><td><img src="' . $pp_options['photosaddress'] . '/' . $pp_options['thumbprefix'] . $data_update['imgfile'] . '" ' . $thumbsize[3] . ' title="' . stripslashes($data_update['imgname']) . '" /></td>
				<td>
				<input type="hidden" name="file" value="' . $data_update['imgfile'] . '" />
				' . __('Size','photopress') . '<select name="thumb">
					<option value="thumb"'; if ($pp_options['insert_thumb'] == '1') { echo ' selected="selected"'; } echo '>' . __('Thumbnail','photopress') . '</option>
					<option value="full"'; if ($pp_options['insert_thumb'] == '0') { echo ' selected="selected"'; } echo '>' . __('Full Image','photopress') . '</option>
				</select><br />
				' . __('Style:','photopress') . '<select name="classes">';
				$classes = explode(' ',trim($pp_options['image_class']));
				foreach ((array)$classes as $class) {
					echo '<option value="' . $class . '"'; if ($class == $classes[0]) { echo ' selected="selected"'; } echo '>' . $class . '</option>
					';
				}
				if (strlen($data_update['imgfile']) > 20) {
					$image_file_short = substr($data_update['imgfile'],0,17) . '...';
				} else {
					$image_file_short = $data_update['imgfile'];
				}
				$thumbsize = @getimagesize($pp_options['photospath'] . '/' . $pp_options['thumbprefix'] . $data_update['imgfile']);
				$imgsize = @getimagesize($pp_options['photospath'] . '/' . $data_update['imgfile']);
				$insert_text = __('Insert','photopress') . "\n" . $image_file_short;
				$inserted_text = __('Inserted','photopress') . "\n" . $image_file_short;
				echo '</select></td></tr></table>
				<div><p class="submit"><input class="pp_insert_button" type="button" name="insert" value="' . $insert_text . '" onClick="insertcode(this.form.file.value,this.form.thumb.options[this.form.thumb.selectedIndex].value,this.form.classes.options[this.form.classes.selectedIndex].value,\'' . htmlentities2($image['imgname']) . '\',' . $imgsize[0] . ',' . $imgsize[1] . ',' . $thumbsize[0] . ',' . $thumbsize[1] . '); this.form.insert.value=\'' . $inserted_text . '\'; return false;" /></p></div>
				</form></div>
				';
			}
		}
		echo '
</div>
</body>
</html>';
die();
	}
}
?>
