<?php

/*
Plugin Name: Ajax Comment Posting
Plugin URI: http://acp.regua.biz/
Description: Posts comments and validates the comment form using Ajax
Author: regua
Version: 1.2.3
Author URI: http://regua.biz/
*/ 

function initialize() {
	#echo '<script type="text/javascript" src="'.get_settings('siteurl').'/wp-content/plugins/ajax-comment-posting/jquery.js"></script>';
	echo '<script type="text/javascript" src="'.get_settings('siteurl').'/wp-content/plugins/ajax-comment-posting/jquery.form.js"></script>';
	echo '<script type="text/javascript" src="'.get_settings('siteurl').'/wp-content/plugins/ajax-comment-posting/lang.js"></script>';
	echo '<script type="text/javascript" src="'.get_settings('siteurl').'/wp-content/plugins/ajax-comment-posting/acp.js"></script>';
	echo '<link rel="stylesheet" type="text/css" href="'.get_settings('siteurl').'/wp-content/plugins/ajax-comment-posting/acp.css" />';
}

add_action('wp_head', 'initialize');

?>
