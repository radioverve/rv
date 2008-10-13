<?php
/*
Plugin Name: Featured Content Gallery
Plugin URI: http://www.wpelements.com
Description: Used to create a fully automated featured content gallery anywhere within your wordpress theme.
Version: 2.0
Author: Jason Schuller
Author URI: http://www.wpelements.com
*/

/* options page */
$options_page = get_option('siteurl') . '/wp-admin/admin.php?page=content-gallery/options.php';
/* Adds our admin options under "Options" */
function gallery_options_page() {
	add_options_page('Featured Content Gallery Options', 'Featured Content Gallery', 10, 'content-gallery/options.php');
}

function gallery_styles() {
    /* The next lines figures out where the javascripts and images and CSS are installed,
    relative to your wordpress server's root: */
    $gallery_path =  get_bloginfo('wpurl')."/wp-content/plugins/content-gallery/";

    /* The xhtml header code needed for gallery to work: */
	$galleryscript = "
	<!-- begin gallery scripts -->
    <link rel=\"stylesheet\" href=\"".$gallery_path."css/jd.gallery.css\" type=\"text/css\" media=\"screen\" charset=\"utf-8\"/>
	<script type=\"text/javascript\" src=\"".$gallery_path."scripts/mootools.v1.11.js\"></script>
	<script type=\"text/javascript\" src=\"".$gallery_path."scripts/jd.gallery.js\"></script>
	<!-- end gallery scripts -->\n";
	/* Output $galleryscript as text for our web pages: */
	echo($galleryscript);
}

/* we want to add the above xhtml to the header of our pages: */
add_action('wp_head', 'gallery_styles');
add_action('admin_menu', 'gallery_options_page');
?>