<?php
/* Photopress shared include file */

load_plugin_textdomain('photopress');

$pp_options = array();

if (get_option('pp_options')) {
	$pp_options = get_option('pp_options');
}

if (empty($pp_options['photospath'])) {
	$pp_options['photospath'] = ABSPATH . 'wp-content/photos';
}

if (empty($pp_options['wpaddress'])) {
	$pp_options['wpaddress'] = get_settings('siteurl');
}

if (empty($pp_options['photosaddress'])) {
	$pp_options['photosaddress'] = $pp_options['wpaddress'] . '/wp-content/photos';
}

if ($pp_options['use_permalinks'] != '0' && $pp_options['use_permalinks'] != '1') {
	$pp_options['use_permalinks'] = '0';
}

if ($pp_options['insert_tags'] != '0' && $pp_options['insert_tags'] != '1') {
	$pp_options['insert_tags'] = '1';
}

if (empty($pp_options['album_token'])) {
	$pp_options['album_token'] = 'album';
}

if (empty($pp_options['cat_token'])) {
	$pp_options['cat_token'] = 'cat';
}

if (empty($pp_options['images_token'])) {
	$pp_options['images_token'] = 'images';
}

if ($pp_options['originals'] != '0' && $pp_options['originals'] != '1') {
	$pp_options['originals'] = '0';
}

if (empty($pp_options['origprefix'])) {
	$pp_options['origprefix'] = 'orig_';
}

if (empty($pp_options['min_level'])) {
	$pp_options['min_level'] = '2';
}

if (empty($pp_options['mass_min_level'])) {
	$pp_options['mass_min_level'] = '5';
}

if (empty($pp_options['maxk'])) {
	$pp_options['maxk'] = '512';
}

if (empty($pp_options['maxsize'])) {
	$pp_options['maxsize'] = '450';
}

if (empty($pp_options['thumbsize'])) {
	$pp_options['thumbsize'] = '130';
}

if ($pp_options['square'] != '0' && $pp_options['square'] != '1') {
	$pp_options['square'] = '0';
}

if (empty($pp_options['quality'])) {
	$pp_options['quality'] = '80';
}

if (empty($pp_options['allowedtypes'])) {
	$pp_options['allowedtypes'] = 'jpg jpeg gif png';
}

if ($pp_options['allow_post_image_delete'] != '0' && $pp_options['allow_post_image_delete'] != '1') {
	$pp_options['allow_post_image_delete'] = '0';
}

if ($pp_options['thumbs_in_mass_edit'] != '0' && $pp_options['thumbs_in_mass_edit'] != '1') {
	$pp_options['thumbs_in_mass_edit'] = '1';
}

if ($pp_options['show_posts'] != '0' && $pp_options['show_posts'] != '1') {
	$pp_options['show_posts'] = '0';
}

if ($pp_options['failsafe_buttons'] != '0' && $pp_options['failsafe_buttons'] != '1') {
	$pp_options['failsafe_buttons'] = '0';
}

if ($pp_options['insert_thumb'] != '0' && $pp_options['insert_thumb'] != '1') {
	$pp_options['insert_thumb'] = '1';
}

if ($pp_options['album'] != '0' && $pp_options['album'] != '1') {
	$pp_options['album'] = '1';
}

if ($pp_options['meta_link'] != '0' && $pp_options['meta_link'] != '1') {
	$pp_options['meta_link'] = '1';
}

if ($pp_options['meta_rand'] != '0' && $pp_options['meta_rand'] != '1') {
	$pp_options['meta_rand'] = '1';
}

if (empty($pp_options['images_per_page'])) {
	$pp_options['images_per_page'] = '30';
}

if (empty($pp_options['album_columns'])) {
	$pp_options['album_columns'] = '2';
}

if (empty($pp_options['manager_columns'])) {
	$pp_options['manager_columns'] = '4';
}

if (empty($pp_options['thumbprefix'])) {
	$pp_options['thumbprefix'] = 'thumb_';
}

if (empty($pp_options['image_class'])) {
	$pp_options['image_class'] = 'pp_empty centered alignleft alignright';
}

if (empty($pp_options['rand_class'])) {
	$pp_options['rand_class'] = 'centered';
}

if ($pp_struct = get_settings('permalink_structure')) {
	if (strstr($pp_struct, 'index.php')) {
		$pp_options['wppermalinks'] = 'index';
	} else {
		$pp_options['wppermalinks'] = 'mod';
	}
} else {
	$pp_options['wppermalinks'] = 'none';
}

if ($pp_options['use_permalinks'] == '1' && $pp_options['wppermalinks'] != 'none') {
	if ($pp_options['wppermalinks'] == 'index') {
		$pp_options['albumaddress'] = trailingslashit($pp_options['wpaddress']) . 'index.php/' . $pp_options['album_token'];
	} else {
		$pp_options['albumaddress'] = trailingslashit($pp_options['wpaddress']) . $pp_options['album_token'];
	}
	$pp_options['cataddress'] = trailingslashit($pp_options['albumaddress']) . $pp_options['cat_token'] . '/';
	$pp_options['imgaddress'] = trailingslashit($pp_options['albumaddress']) . $pp_options['images_token'] . '/';
	$pp_options['page_token'] = '/';
} else {
	$pp_options['albumaddress'] = $pp_options['wpaddress'] . '/?pp_album=main';
	$pp_options['cataddress'] = $pp_options['albumaddress'] . '&pp_cat=';
	$pp_options['imgaddress'] = $pp_options['albumaddress'] . '&pp_image=';
	$pp_options['page_token'] = '&pp_page=';
}

// cleans up data for insert into db, but $wpdb->escape seems to work better...leaving it here for now
function pp_cleanstring($string) {
	$string = (get_magic_quotes_gpc() == 1) ? stripslashes($string) : $string;
	if (is_numeric($string)) {
		return (int)$string;
	} else {
		return mysql_real_escape_string($string);
	}
}

// Shared update function so the uploader can save data to the table. Receives an array which should include filename, short image name, image description, category, and category slug. If the file doesn't exist or if the array isn't the right size it returns FALSE.
function pp_table_update($pp_update_array) {
	global $table_prefix, $wpdb, $pp_options;
	$table_name = $table_prefix . "photopress";
	if (is_file($pp_options['photospath'] . '/' . $pp_update_array[0]) && count($pp_update_array) > 3) { // we need at least 4 records in the array - the slug may be omitted
		$imgfile = $wpdb->escape($pp_update_array[0]);
		$imgname = $wpdb->escape($pp_update_array[1]);
		$imgdesc = $wpdb->escape($pp_update_array[2]);
		$imgcat = $wpdb->escape($pp_update_array[3]);
		if (isset($pp_update_array[4])) {
			$catslug = $wpdb->escape($pp_update_array[4]);
		} else {
			$catslug = pp_slugify($pp_update_array[3]); // make a slug if there isn't one in the array
			$catslug = $wpdb->escape($catslug);
		}
		$imgtime = filemtime($pp_options['photospath'] . '/' . $pp_update_array[0]);
		// if the image is already in there we'll update the row with the new data
		if ($wpdb->get_var("SELECT imgfile FROM $table_name WHERE binary imgfile='$imgfile'")) {
			return $wpdb->query("UPDATE $table_name SET imgname='$imgname', imgdesc='$imgdesc', imgcat='$imgcat', catslug='$catslug', imgtime='$imgtime' WHERE binary imgfile='$imgfile'");
		// if the image isn't in there we'll add a new row
		} else {
			return $wpdb->query("INSERT INTO $table_name SET imgfile='$imgfile', imgname='$imgname',imgdesc='$imgdesc',imgcat='$imgcat',catslug='$catslug',imgtime='$imgtime'");
		}
	} else {
		return FALSE;
	}
}

// gets data for a photo from the database, or makes up some data based on the filename
function pp_get_data($image) {
	global $table_prefix, $wpdb, $pp_options;
	$table_name = $table_prefix . 'photopress';
	if ($results = $wpdb->get_row("SELECT * FROM $table_name WHERE binary imgfile = '$image'", ARRAY_A)) {
		return $results;
	} else {
		return FALSE;
	}
}

// gives a count of images in $category, or full count otherwise
function pp_count($slug=FALSE) {
	global $table_prefix, $wpdb;
	$table_name = $table_prefix . 'photopress';
	if (empty($slug)) {
		if ($results = $wpdb->get_var("SELECT count(*) FROM $table_name")) {
			return $results;
		} else {
			return FALSE;
		}
	} else {
		$slug_for_query = $wpdb->escape($slug);
		if ($results = $wpdb->get_var("SELECT count(*) FROM $table_name WHERE binary catslug='$slug_for_query'")) {
			return $results;
		} else {
			return FALSE;
		}
	}
}

// function to get an array of categories from the db, uses pp_list_slugs
function pp_list_cats($album = '') {
	if ($album == 'album') {
		$slugs = pp_list_slugs('album');
	} else {
		$slugs = pp_list_slugs();
	}
	if (count($slugs) > 0) {
		$cats = array();
		foreach ((array)$slugs as $slug) {
			$cats[] = pp_get_cat($slug);
		}
			return $cats;
	} else {
		return FALSE;
	}
}

function pp_list_slugs($album = '') {
	global $table_prefix, $wpdb;
	$table_name = $table_prefix . "photopress";
	if ($results = $wpdb->get_col("SELECT catslug FROM $table_name")) {
		if (is_array($results)) {
			sort($results);
		}
		$slugs = array();
		foreach ($results as $slug) {
			if ($album == 'album') { // if the slug list is for the album remove hidden cats
				if (!pp_is_cat_hidden($slug)) {
					$slugs[] = $slug;
				}
			} else {
				$slugs[] = $slug;
			}
		}
		return array_unique($slugs);
	} else {
		return FALSE;
	}
}

// get $category for $slug (shouldn't matter which so we grab the first matching row)
function pp_get_cat($slug) {
	global $table_prefix, $wpdb;
	$table_name = $table_prefix . "photopress";
	$slugforquery = $wpdb->escape($slug);
	if ($results = $wpdb->get_row("SELECT * FROM $table_name WHERE binary catslug = '$slug'", ARRAY_A)) {
		$category = stripslashes($results['imgcat']);
		return $category;
	} else {
		return FALSE;
	}
}

// receives a category, returns a slugified category
function pp_slugify($cat) {
	$slug = sanitize_title_with_dashes($cat);
	return $slug;
}

// returns TRUE if all of the images in a category are hidden, FALSE otherwise
function pp_is_cat_hidden($slug) {
	global $table_prefix, $wpdb;
	$table_name = $table_prefix . "photopress";
	$slugforquery = $wpdb->escape($slug);
	$slug_count = $wpdb->get_var("SELECT count(*) FROM $table_name WHERE binary catslug='$slugforquery'");
	$slug_hidden_count = $wpdb->get_var("SELECT count(*) FROM $table_name WHERE binary catslug='$slugforquery' AND imghide='hide'");
	if ($slug_hidden_count == $slug_count) {
		return TRUE;
	} else {
		return FALSE;
	}
}

// gets an array of $rows image arrays in $slug (if given, from whole table otherwise), sorted by $sort, starting with $offset
function pp_images_with_data($slug=FALSE,$sort='imgname',$offset=0,$rows=50) {
	global $table_prefix, $wpdb;
	$table_name = $table_prefix . "photopress";
	if (!empty($slug)) {
		$slugforquery = $wpdb->escape($slug);
		if ($results = $wpdb->get_results("SELECT * FROM $table_name WHERE binary catslug='$slugforquery' ORDER BY $sort LIMIT $offset,$rows",ARRAY_A)) {
			return $results;
		} else {
			return FALSE;
		}
	} else {
		if ($results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY $sort LIMIT $offset,$rows", ARRAY_A)) {
			return $results;
		} else {
			return FALSE;
		}
	}
}

// function to get an array of the images for a category slug
function pp_cat_images($slug) {
	global $table_prefix, $wpdb;
	$table_name = $table_prefix . "photopress";
	$slugforquery = $wpdb->escape($slug);
	$results = array();
	if ($results = $wpdb->get_col("SELECT imgfile FROM $table_name WHERE binary catslug='$slugforquery'")) {
		if (is_array($results)) {
			sort($results);
		}
		return $results;
	} else {
		return FALSE;
	}
}

// Returns an array of image names within photospath with matching thumbs (ignores images without thumbs). Doesn't check whether the images have data in the db, so we can use this to import photos.
function pp_folder_contents() {
	global $pp_options;
	$allowedtypes = trim(strtolower($pp_options['allowedtypes']));
	$allowedtypes = preg_replace("/ /","|",$allowedtypes);
	$fileglob = '@\.(' . $allowedtypes . ')$@i';
	$handle = opendir($pp_options['photospath']);
	$list_array = array();
	while (false !== ($folder_contents = readdir($handle))) {
		$image_name = substr($folder_contents, strlen($pp_options['thumbprefix']));
		if ((preg_match($fileglob, $folder_contents)) && (strstr($folder_contents, $pp_options['thumbprefix'])) && is_file($pp_options['photospath'] . '/' . $image_name)) { // make sure both thumb and regular image are there
			$list_array[] = $image_name;
		}
	}
	@closedir($pp_options['photospath']);
	if (count($list_array) > 1) {
		sort($list_array);
	}
	if (count($list_array) > 0) {
		return $list_array;
	} else {
		return FALSE;
	}
}

// make a list of html option tags for a category select list (the part between the select tags), adding Default and setting it as selected if no selected cat is provided (or if the one provided has no images)
function pp_cat_dropdown($pp_cats,$selected='Default') {
	$pp_cats[] = 'Default';
	$pp_cats = array_unique($pp_cats);
	$code = '';
	foreach ($pp_cats as $category) {
		$code .= '<option value="' . stripslashes($category) . '"';
		if ($selected == stripslashes($category)) { $code .= ' selected="selected"'; }
		$code .= '>' . stripslashes($category) . '</option>';
	}
	return $code;
}

// Resizing function, blatently stolen from the built-in wordpress uploader.
function pp_resize($file, $maxsize, $minsize, $prefix = '', $isthumb) {
	global $pp_options;
	// 1 = GIF, 2 = JPEG, 3 = PNG
	if(file_exists($file)) {
		$type = getimagesize($file);
		// if the associated function doesn't exist - then it's not
		// handle. duh. i hope.
		if(!function_exists('imagegif') && $type[2] == 1) {
			$error = __('Filetype not supported. Image not resized.','photopress');
		}elseif(!function_exists('imagejpeg') && $type[2] == 2) {
			$error = __('Filetype not supported. Image not resized.','photopress');
		}elseif(!function_exists('imagepng') && $type[2] == 3) {
			$error = __('Filetype not supported. Image not resized.','photopress');
		} else {
			// create the initial copy from the original file
			if($type[2] == 1) {
				$image = imagecreatefromgif($file);
			} elseif($type[2] == 2) {
				$image = imagecreatefromjpeg($file);
			} elseif($type[2] == 3) {
				$image = imagecreatefrompng($file);
			}
			if (function_exists('imageantialias'))
				imageantialias($image, TRUE);
			$image_attr = getimagesize($file);
			// anti-upsize fix contributed by Jono (jono@redcliffs.net)
			// set the current image width and heights
			$image_width = $image_attr[0];
			$image_height = $image_attr[1];
			// if image is larger than defined max size
			if($image_width >= $maxsize || $image_height >= $maxsize || $image_width <= $minsize || $image_height <= $minsize) {
				// figure out the longest side            
				if($image_width > $image_height) {
					$off_w = (int)($image_width - $image_height) / 2;
					$off_h = 0;
					$sq_sz = $image_height;
					$image_new_width = $maxsize;
					$image_ratio = $image_width/$image_new_width;
					$image_new_height = $image_height/$image_ratio;
				} elseif ($image_height > $image_width) {
					$off_w = 0;
					$off_h = (int)($image_height - $image_width) / 2;
					$sq_sz = $image_width;
					$image_new_height = $maxsize;
					$image_ratio = $image_height/$image_new_height;
					$image_new_width = $image_width/$image_ratio;
				} else { // square image
					$image_new_height = $maxsize;
					$image_new_width = $maxsize;
					$off_w = 0;
					$off_h = 0;
					$sq_sz = $image_width;
				}
				if ($isthumb == 1 && $pp_options['square'] == '1') {
					$resized = imagecreatetruecolor($maxsize, $maxsize);
					@imagecopyresampled($resized, $image, 0, 0, $off_w, $off_h, $maxsize, $maxsize, $sq_sz, $sq_sz);
				} else {
					$resized = imagecreatetruecolor($image_new_width, $image_new_height);
					@imagecopyresampled($resized, $image, 0, 0, 0, 0, $image_new_width, $image_new_height, $image_attr[0], $image_attr[1]);
				}
				// move the thumbnail to it's final destination
				$path = explode('/', $file);
				$resizedpath = substr($file, 0, strrpos($file, '/')) . '/' . $prefix . $path[count($path)-1];
				if($type[2] == 1) {
					if(!imagegif($resized, $resizedpath)) {
						$error = __('Photo path invalid','photopress');
					}
				} elseif($type[2] == 2) {
					if(!imagejpeg($resized, $resizedpath, $pp_options['quality'])) {
						$error = __('Photo path invalid','photopress');
					}
				} elseif($type[2] == 3) {
					if(!imagepng($resized, $resizedpath)) {
						$error = __('Photo path invalid','photopress');
					}
				}
			} else {
				$error = __('Image not resized, is smaller than ' . $maxsize . 'x' . $maxsize);
			}
		}
	}
	@imagedestroy($image);
	@imagedestroy($resized);
	if(!empty($error)) {
		return $error;
	} else {
		return 1;
	}
}
?>
