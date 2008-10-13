<?php
/*
Plugin Name: Admin Drop Down Menu
Plugin URI: http://planetozh.com/blog/my-projects/wordpress-admin-menu-drop-down-css/
Description: Replaces admin menus with a CSS dropdown menu bar. Saves lots of clicks and page loads! <strong>For WordPress 2.5+</strong>
Version: 2.1
Author: Ozh
Author URI: http://planetOzh.com/
*/

/* Release History :
 * 1.0:       Initial release
 * 1.1:       Tiger Admin compatibility !
 * 1.2:       Multiple Page Plugin (ex: Akismet) compatibility and minor CSS improvements
 * 1.3:       Fix for plugins with subfolders on Windows WP installs
 * 1.3.1:     Minor CSS tweaks
 * 2.0:       Complete rewrite for WordPress 2.5
 * 2.0.1:     Fixed: bug with uploader
 * 2.0.2:     Improved: compatibility with admin custom CSS (some colors are now dynamically picked)
              Fixed: bug with submenu under plugin toplevel menus
              Fixed: WP's internal behavior or rewriting the "Manage" link according to the current "Write" page and vice-versa (makes sense?:)
			  Added: Option to display original submenu, per popular demand
 * 2.0.3:     Fixed: CSS bug with uploader, again. Grrrr.
 * 2.1:		  Added: Wordpress Mu compatibility \o/
              Fixed: CSS issues with IE7, thanks Stuart
			  Added: Ability to dynamically resize menu on two lines when too many entries.
			  Added: Option to set max number of submenu entries before switching to horizontal display
 */

/***********************************/
/***** Options: Edit if wished *****/
/***********************************/
global $wp_ozh_adminmenu;

$wp_ozh_adminmenu['display_submenu'] = false;
	// boolean: do you want to still display the sublevel menus? Some find it more convenient.

$wp_ozh_adminmenu['too_many_plugins'] = 30;
	// integer: if more than this many entries in a drop down menu, make it
	// horizontal so that it does not get longer than your screen height
	// Note: does not work with Fluency.
	
/***********************************/
/*** Do not modify anything below **/
/***********************************/

function wp_ozh_adminmenu() {
	$menu = wp_ozh_adminmenu_build();
		
	$ozh_menu = '</ul><ul id="ozhmenu">'; // close original <ul id="dashmenu"> and add ours
	
	foreach ($menu as $k=>$v) {
		$url 	= $v['url'];
		$name 	= $k;
		$anchor = $v['name'];
		$class	= $v['class'];

		$ozh_menu .= "\t<li class='ozhmenu_toplevel'><a href='$url'$class><span>$anchor</span></a>";
		if (is_array($v['sub'])) {
			
			$ulclass='';
			if ($class) $ulclass = " class='ulcurrent'";
			$ozh_menu .= "\n\t\t<ul$ulclass>\n";

			foreach ($v['sub'] as $subk=>$subv) {
				$suburl = $subv['url'];
				$subanchor = $subv['name'];
				$subclass='';
				if (array_key_exists('class',$subv)) $subclass=$subv['class'];
				$ozh_menu .= "\t\t\t<li class='ozhmenu_sublevel'><a href='$suburl'$subclass>$subanchor</a></li>\n";
			}
			$ozh_menu .= "\t</ul>\n";
		}
		$ozh_menu .="\t</li>\n";
	}
	
	echo $ozh_menu;
	
}
 
 
/* Core stuff : builds an array populated with all the infos needed for menu and submenu */
function wp_ozh_adminmenu_build () {
	global $menu, $submenu, $plugin_page, $pagenow;
	
	/* Most of the following garbage are bits from admin-header.php,
	 * modified to populate an array of all links to display in the menu
	 */
	 
	$self = preg_replace('|^.*/wp-admin/|i', '', $_SERVER['PHP_SELF']);
	$self = preg_replace('|^.*/plugins/|i', '', $self);
	
	/* Make sure that "Manage" always stays the same. Stolen from Andy @ YellowSwordFish */
	$menu[5][0] = __("Write");
	$menu[5][1] = "edit_posts";
	$menu[5][2] = "post-new.php";
	$menu[10][0] = __("Manage");
	$menu[10][1] = "edit_posts";
	$menu[10][2] = "edit.php";	
	
	//get_admin_page_parent();
	
	$altmenu = array();
	
	/* Step 1 : populate first level menu as per user rights */
	foreach ($menu as $item) {
		// 0 = name, 1 = capability, 2 = file
		if ( current_user_can($item[1]) ) {
			if ( file_exists(ABSPATH . "wp-content/plugins/{$item[2]}") )
				$altmenu[$item[2]]['url'] = get_settings('siteurl') . "/wp-admin/admin.php?page={$item[2]}";			
			else
				$altmenu[$item[2]]['url'] = get_settings('siteurl') . "/wp-admin/{$item[2]}";

			if (( strcmp($self, $item[2]) == 0 && empty($parent_file)) || ($parent_file && ($item[2] == $parent_file)))
			$altmenu[$item[2]]['class'] = " class='current'";
			
			$altmenu[$item[2]]['name'] = $item[0];

			/* Windows installs may have backslashes instead of slashes in some paths, fix this */
			$altmenu[$item[2]]['name'] = str_replace(chr(92),chr(92).chr(92),$altmenu[$item[2]]['name']);
		}
	}
	
	/* Step 2 : populate second level menu */
	foreach ($submenu as $k=>$v) {
		foreach ($v as $item) {
			if (array_key_exists($k,$altmenu) and current_user_can($item[1])) {
				
				// What's the link ?
				$menu_hook = get_plugin_page_hook($item[2], $k);

				if (file_exists(ABSPATH . "wp-content/plugins/{$item[2]}") || ! empty($menu_hook)) {
					list($_plugin_page,$temp) = explode('?',$altmenu[$k]['url']);
					$link = $_plugin_page.'?page='.$item[2];
				} else {
					$link =  $item[2];
				}
				
				/* Windows installs may put backslashes instead of slashes in paths, fix this */
				$link = str_replace(chr(92),chr(92).chr(92),$link);
				
				$altmenu[$k]['sub'][$item[2]]['url'] = $link;
				
				// Is it current page ?
				$class = '';
				if ( (isset($plugin_page) && $plugin_page == $item[2] && $pagenow == $k) || (!isset($plugin_page) && $self == $item[2] ) ) $class=" class='current'";
				if ($class) {
					$altmenu[$k]['sub'][$item[2]]['class'] = $class;
					$altmenu[$k]['class'] = $class;
				}
				
				// What's its name again ?
				$altmenu[$k]['sub'][$item[2]]['name'] = $item[0];
			}
		}
	}
	
	// Dirty debugging: break page and dies
	/**
	echo "</ul><pre style='font-size:9px'>";
	echo '__MENU ';print_r($menu);
	echo 'SUBMENU ';print_r($submenu);
	echo 'ALTMENU ';print_r($altmenu);
	die();
	/**/
	
	// Clean debugging: prints after footer
	/**
	global $wpdb;
	$wpdb->wp_ozh_adminmenu_neat_array = "<pre style='font-size:80%'>Our Oh-So-Beautiful-4-Levels-".htmlentities(print_r($altmenu,true))."</pre>";
	add_action('admin_footer', create_function('', 'global $wpdb; echo $wpdb->wp_ozh_adminmenu_neat_array;')); 
	/**/

	return ($altmenu);
}


function wp_ozh_adminmenu_js($menu = '') {
	global $wp_ozh_adminmenu;
	
	$submenu = $wp_ozh_adminmenu['display_submenu'] ? '': "jQuery('#wpwrap #submenu').html('')";
	$toomanyplugins = $wp_ozh_adminmenu['too_many_plugins'];
	if (!function_exists('wp_admin_fluency_css')) {
	$resize = <<<JS
		ozhmenu_resize();
		// Bind resize event		
		jQuery(window).resize(function(){
			ozhmenu_resize();
		});
JS;
	} else {
		$resize = '';
	}
	
	echo <<<JS
<script type="text/javascript"><!--//--><![CDATA[//><!--
// Resize menu to make sure it doesnt overlap with #user_info or blog title
function ozhmenu_resize() {
	// Reinit positions
	jQuery('#ozhmenu').css('width','');
	jQuery('#wphead').css('border-top-width', '30px');
	// Resize
	var ozh_w = parseInt(jQuery('#ozhmenu').css('width').replace(/px/,''));
	var info_w = parseInt(jQuery('#user_info').css('width').replace(/px/,'')) || 130; // the " or 130" part is for when width = 'auto' (on MSIE..) to get 130 instead of NaN
	jQuery('#ozhmenu').css('width', (ozh_w - info_w - 1)+'px' );
	var ozh_h = parseInt(jQuery('#ozhmenu').css('height').replace(/px/,''));
	// Compare positions of first & last top level lis
	var num_li=jQuery('#ozhmenu li.ozhmenu_toplevel').length;
	var first_li = jQuery('#ozhmenu li.ozhmenu_toplevel').eq(0).offset();
	var last_li = jQuery('#ozhmenu li.ozhmenu_toplevel').eq(num_li-1).offset(); // Dunno why, but jQuery('#ozhmenu li.ozhmenu_toplevel :last') doesn't work...
	if (!ozh_h) {ozh_h = last_li.top + 25 }
	if ( first_li.top < last_li.top ) {
		jQuery('#wphead').css('border-top-width', (ozh_h+4)+'px'); 
	}
}
jQuery(document).ready(function() {
	// Remove unnecessary links in the top right corner
	var ozhmenu_uselesslinks = jQuery('#user_info p').html();
	if (ozhmenu_uselesslinks) {
		ozhmenu_uselesslinks = ozhmenu_uselesslinks.replace(/ \| <a href="http:\/\/codex.wordpress.org.*$/i, '');
		jQuery('#user_info p').html(ozhmenu_uselesslinks);
		jQuery('#user_info').css('z-index','81');
		// Get and apply current menu colors
		var ozhmenu_bgcolor = jQuery("#wphead").css('background-color');
		var ozhmenu_color = jQuery('#dashmenu li a').css('color');
		jQuery('#ozhmenu li.ozhmenu_over').css('background-color', ozhmenu_bgcolor).css('color', ozhmenu_color);
		jQuery('#ozhmenu li .current').css('background-color', ozhmenu_bgcolor).css('color', ozhmenu_color);
		// Remove original menus (this is, actually, not needed, since the CSS should have taken care of this)
		jQuery('#sidemenu').hide();
		jQuery('#adminmenu').hide();
		$submenu
		jQuery('#dashmenu').hide();
		jQuery('#user_info').css('right','1em');
		// Make title header smaller (same comment as above)
		jQuery('#wphead #viewsite a').css('font-size','10px');
		jQuery('#wphead h1').css('font-size','25px');
		// jQueryfication of the Son of Suckerfish Drop Down Menu
		// Original at: http://www.htmldog.com/articles/suckerfish/dropdowns/
		jQuery('#ozhmenu li.ozhmenu_toplevel').each(function() {
			jQuery(this).mouseover(function(){
				jQuery(this).addClass('ozhmenu_over');
				if (jQuery.browser.msie) {ozhmenu_hide_selects(true);}
			}).mouseout(function(){
				jQuery(this).removeClass('ozhmenu_over');
				if (jQuery.browser.msie) {ozhmenu_hide_selects(false);}
			});
		});
		// Dynamically float submenu elements if there are too many
		jQuery('.ozhmenu_toplevel span').mouseover(
			function(){
				var menulength = jQuery(this).parent().parent().find('ul li').length;
				if (menulength >= $toomanyplugins) {
					jQuery(this).parent().parent().find('ul li').each(function(){
						jQuery(this).css('float', 'left');
					});
				}
			}
		);
		// Function to hide <select> elements (display bug with MSIE)
		function ozhmenu_hide_selects(hide) {
			var hidden = (hide) ? 'hidden' : 'visible';
			jQuery('select').css('visibility',hidden);
		}
		// Show our new menu
		jQuery('#ozhmenu').show();
		$resize
		// WPMU : behavior for the "All my blogs" link
		jQuery( function($) {
			var form = $( '#all-my-blogs' ).submit( function() { document.location = form.find( 'select' ).val(); return false;} );
			var tab = $('#all-my-blogs-tab a');
			var head = $('#wphead');
			$('.blog-picker-toggle').click( function() {
				form.toggle();
				tab.toggleClass( 'current' );
				return false;
			});
		} );
	}
})

//--><!]]></script>
JS;

}

function wp_ozh_adminmenu_css() {
	global $wp_ozh_adminmenu, $pagenow;
	
	$submenu = ($wp_ozh_adminmenu['display_submenu'] or ($pagenow == "media-upload.php") ) ? '' : '#wpwrap #submenu li';
	
	echo <<<CSS
<style type="text/css">
/* Restyle or hide original items */
#sidemenu, #adminmenu, #dashmenu, $submenu {
	display:none;
}
#media-upload-header #sidemenu li {
	display:auto;
}
#wphead h1 {
	font-size:25px;
}
#wphead #viewsite {
	margin-top: 6px;
}
#wphead #viewsite a {
	font-size:10px;
}
/* Styles for our new menu */
#ozhmenu { /* our new ul */
	font-size:12px;
	left:0px;
	list-style-image:none;
	list-style-position:outside;
	list-style-type:none;
	margin:0pt;
	padding-left:8px;
	position:absolute;
	top:4px;
	width:95%; /* width required for -wtf?- dropping li elements to be 100% wide in their containing ul */
	overflow:show;
	z-index:80;
}
#ozhmenu li { /* all list items */
	display:inline;
	line-height:200%;
	list-style-image:none;
	list-style-position:outside;
	list-style-type:none;
	margin:0 3px;
	padding:0;
	white-space:nowrap;
	float: left;
	width: 1*; /* maybe needed for some Opera ? */
}
#ozhmenu a { /* all links */
	text-decoration:none;
	color:#bbb;
	line-height:220%;
	padding:0px 10px;
	display:block;
	width:1*;  /* maybe needed for some Opera ? */
}
#ozhmenu li:hover,
#ozhmenu li.ozhmenu_over,
#ozhmenu li .current {
	background: #14568A;
	-moz-border-radius-topleft: 3px;
	-moz-border-radius-topright: 3px;	
	color: #ddd;
}
#ozhmenu .ozhmenu_sublevel a:hover,
#ozhmenu .ozhmenu_sublevel a.current,
#ozhmenu .ozhmenu_sublevel a.current:hover {
	background: #e4f2fd;
	-moz-border-radius-topleft: 0px;
	-moz-border-radius-topright: 0px;
	color: #555;
}
#ozhmenu li ul { /* drop down lists */
	padding: 0;
	margin: 0;
	padding-bottom:5px;
	list-style: none;
	position: absolute;
	background: white;
	opacity:0.95;
	filter:alpha(opacity=95);
	border-left:1px solid #c6d9e9 ;
	border-right:1px solid #c6d9e9 ;
	border-bottom:1px solid #c6d9e9 ;
	-moz-border-radius-bottomleft:5px;
	-moz-border-radius-bottomright:5px;
	width: 1*;  /* maybe needed for some Opera ? */
	min-width:6em;
	left: -999em; /* using left instead of display to hide menus because display: none isn't read by screen readers */
	list-style-position:auto;
	list-style-type:auto;
}
#ozhmenu li ul li { /* dropped down lists item */
	background:transparent !important;
	float:none;
	text-align:left;
}
#ozhmenu li ul li a { /* links in dropped down list items*/
	margin:0px;
	color:#666;
}
#ozhmenu li:hover ul, #ozhmenu li.ozhmenu_over ul { /* lists dropped down under hovered list items */
	left: auto;
	z-index:999999;
}
#ozhmenu li a #awaiting-mod {
	position: absolute;
	margin-left: 0.1em;
	font-size: 0.8em;
	background-image: url(images/comment-stalk-fresh.gif);
	background-repeat: no-repeat;
	background-position: -160px bottom;
	height: 1.7em;
	width: 1em;
}
#ozhmenu li.ozhmenu_over a #awaiting-mod, #ozhmenu li a:hover #awaiting-mod {
	background-position: -2px bottom;
}
#ozhmenu li a #awaiting-mod span {
	color: #fff;
	top: -0.3em;
	right: -0.5em;
	position: absolute;
	display: block;
	height: 1.3em;
	line-height: 1.3em;
	padding: 0 0.8em;
	background-color: #2583AD;
	-moz-border-radius: 4px;
	-khtml-border-radius: 4px;
	-webkit-border-radius: 4px;
	border-radius: 4px;
}
#ozhmenu li.ozhmenu_over a #awaiting-mod span, #ozhmenu li a:hover #awaiting-mod span {
	background-color:#D54E21;
}
#ozhmenu .current {
	border:0px; /* MSIE insists on having this */
}
#ozhmenu li ul li a.current:before {
	content: "\\00BB \\0020";
	color:#d54e21;
}
/* Mu Specific */
#ozhmumenu_head {
	color:#bbb;
	font-weight:bolder;
}
#ozhmumenu_head #all-my-blogs {
	position:relative;
	top:0px;
	background:#ffa;
	color:#000;
}
/* Just for IE7 */
#wphead {
	#border-top-width: 31px;
}
#media-upload-header #sidemenu { display: block; }
</style>
CSS;
}

function wp_ozh_adminmenu_head() {
	wp_ozh_adminmenu_css();
	wp_ozh_adminmenu_js();
}

/***** Mu specific ****/

function wp_ozh_adminmenu_remove_blogswitch_init() {
	remove_action( '_admin_menu', 'blogswitch_init' );
	add_action( '_admin_menu', 'wp_ozh_adminmenu_blogswitch_init' );
}

function wp_ozh_adminmenu_blogswitch_init() {
	global $current_user, $current_blog;
	$blogs = get_blogs_of_user( $current_user->ID );
	if ( !$blogs )
		return;
	add_action( 'admin_menu', 'wp_ozh_adminmenu_blogswitch_ob_start' );
	add_action( 'dashmenu', 'blogswitch_markup' );
}


function wp_ozh_adminmenu_blogswitch_ob_start() {
	ob_start( 'wp_ozh_adminmenu_blogswitch_ob_content' );
}

function wp_ozh_adminmenu_blogswitch_ob_content( $content ) {
	// Menu with blog list
	$mumenu = preg_replace( '#.*%%REAL_DASH_MENU%%(.*?)%%END_REAL_DASH_MENU%%.*#s', '\\1', $content );
	$mumenu = str_replace ('<li>', '<li class="ozhmenu_sublevel">', $mumenu);
	$mumenu = preg_replace( '#</ul>.*?<form id="all-my-blogs"#s', '<li><form id="all-my-blogs"', $mumenu);
	$mumenu = str_replace ('</form>', '</form></li></ul>', $mumenu);
	
	
	$content = preg_replace( '#%%REAL_DASH_MENU%%(.*?)%%END_REAL_DASH_MENU%%#s', '', $content );
	$content = str_replace( '<ul id="ozhmenu">', '<ul id="ozhmenu"><li class="ozhmenu_toplevel" id="ozhmumenu_head"><a href="">My blogs</a><ul id="ozhmumenu">'.$mumenu.'</li>', $content );
	
	return $content;
}


/***** Hook things in ****/

global $wpmu_version;
if ($wpmu_version)
	add_action( '_admin_menu', 'wp_ozh_adminmenu_remove_blogswitch_init', -100 );

if (is_admin()) {
	add_action('init', create_function('', 'wp_enqueue_script("jquery");')); 
}
add_action('dashmenu', 'wp_ozh_adminmenu');
add_action('admin_head', 'wp_ozh_adminmenu_head');


?>