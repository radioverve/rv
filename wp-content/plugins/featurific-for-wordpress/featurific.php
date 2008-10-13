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


/*
Plugin Name: Featurific for Wordpress
Plugin URI: http://featurific.com/ffw
Description: This plugin provides an effortless interface to Featurific, the
featured story slideshow.  Unlike traditional slideshows, Featurific imitates
the behavior seen on the home pages of sites like time.com and msn.com,
displaying summaries of featured articles on the site.  Installation is
automatic and easy, while advanced users can customize every element of the
Flash slideshow presentation.
Author: Rich Christiansen
Version: 1.2.8
Author URI: http://endorkins.com/
*/

$featurific_version = '1.2.8';

//Libraries
include_once('featurific_db.php');

if(class_exists("HtmlParser")===false)
	include ("htmlparser.inc");

if(class_exists("XMLParser")===false) {
	if(version_compare(PHP_VERSION, '5.0', '<'))
		include 'parser_php4.phps';
	else
		include 'parser_php5.phps';
}


//Hooks
register_activation_hook( __FILE__, 'featurific_activate' );
register_deactivation_hook( __FILE__, 'featurific_deactivate' );


//Actions
add_action('switch_theme', 'featurific_activate');
add_action('admin_menu', 'featurific_add_pages');
add_action('admin_notices', 'featurific_show_admin_messages');
add_action('after_plugin_row', 'featurific_show_upgrade_notice');



if(!get_option('featurific_current_template_configured')) {
	function featurific_template_configuration_warning() {
		$theme_editor_url = get_option('siteurl').'/wp-admin/theme-editor.php';
		echo "
			<div id='featurific-for-wordpress-warning' class='updated fade'><p><strong>Featurific for Wordpress is almost ready</strong>.  To complete installation, Featurific for Wordpress needs access to your main template file.<br/>
				<ul>
					<li>
						<strong>Temporarily modify your theme's file permissions and reinstall</strong>
						<ol>
							<li>
								Change the file permissions on your current theme's files to be world-writable (chmod 666 should suffice)
								<ul>
									<li><a href='http://codex.wordpress.org/Changing_File_Permissions#Using_an_FTP_Client'>Using an FTP client (easy)</a></li>
									<li><a href='http://codex.wordpress.org/Changing_File_Permissions#Using_the_Command_Line'>Using the command line (harder, requires shell access)</a></li>
								</ul>
							</li>
							<li>Deactivate and re-activate Featurific for Wordpress, and installation will complete successfully if the main template file is in fact world-writable.</li>
							<li><strong>Optional</strong>: If desired, revert your permissions back to normal. (chmod 644 for files, chmod 755 for directories)</li>
						</ol>
					</li>
				</ul>
				<small>To get rid of this status message, either complete the steps listed above or disable the Featurific for Wordpress plugin.</small>
			</p></div>";

// 		echo "
// 			<div id='featurific-for-wordpress-warning' class='updated fade'><p><strong>Featurific for Wordpress is almost ready</strong>.  To complete installation, you must do <strong>one of the following</strong>:<br/>
// 				<ul>
// 					<li>
// 						<strong>Easy</strong> - Manually insert Featurific into your theme
// 						<ol>
// 							<li>Edit the index.php or home.php file of your theme in the <a href='$theme_editor_url'>Wordpress Theme Editor</a> (<a href='http://codex.wordpress.org/Editing_Files#Using_the_Theme_Editor'>More info on the Theme Editor</a>)</li>
// 							<li>
// 								Insert the Featurific for Wordpress code.<br/>
// <pre>&lt;?php
//  //Code automatically inserted by Featurific for Wordpress plugin
//  if(is_home())                             //If we're generating the home page (remove this line to make Featurific appear on all pages)...
//   if(function_exists('insert_featurific')) //If the Featurific plugin is activated...
//    insert_featurific();                    //Insert the HTML code to embed Featurific
// ?&gt;</pre>
// 								The code needs to go in a specific location.  Here's how to find where to put it:
// 								<ol type='a'>
// 									<li>Find the first occurrence of the text 'have_posts()' in the template.</li>
// 									<li>Find the first occurrence of '&lt;?' that <em>precedes</em> the text found in step 1.</li>
// 									<li>Insert the code <em>just before</em> the '&lt;?' found in step 2.</li>
// 								</ol>
// 							</li>
// 						</ol>
// 					</li>
// 					<li>
// 						<strong>Harder</strong> - Temporarily modify your theme's file permissions and reinstall
// 						<ol>
// 							<li><a href='http://www.google.com/search?q=chmod+777'>Change the file permissions</a> on your current theme files (the containing directory and all files) to be world-writable (chmod 777 should suffice)</li>
// 							<li>Disable and re-enable Featurific for Wordpress, and installation will complete successfully if the theme is in fact world-writable.</li>
// 							<li>Revert your permissions back to normal. (chmod 644)</li>
// 						</ol>
// 					</li>
// 				</ul>
// 			</p></div>";


	}
	add_action('admin_notices', 'featurific_template_configuration_warning');
	//return;
}

function featurific_show_admin_message_once($m) {
	$messages = get_option('featurific_admin_messages_to_show_once');
	
	//Only add the message $m if it's not already in $messages.
	if(array_search($m, $messages)===false) {
		if($messages==null)
			$messages = array();
		
		$messages[] = $m;
		update_option('featurific_admin_messages_to_show_once', $messages);
	}
}


function featurific_show_admin_messages() {
	$messages = get_option('featurific_admin_messages_to_show_once');
	
	if($messages==null || sizeof($messages)<1)
		return;
	
	foreach ($messages as $m) {
		echo "
			<div id='featurific-for-wordpress-warning' class='updated fade'><p><strong>Featurific for Wordpress needs your attention</strong>.<br/>\n
			<br/>\n
			$m\n
			</div>\n";
	}
	
	update_option('featurific_admin_messages_to_show_once', array());
}


function featurific_show_upgrade_notice($plugin_path) {
	//The next few lines were copied and modified from update.php's wp_plugin_update_row()
	$current = get_option('update_plugins');
	if(!isset($current->response[$plugin_path]) ||									//If an update for the plugin is not available
			$plugin_path!='featurific-for-wordpress/featurific.php') {	//or we currently processing a plugin other than the Featurific for Wordpress plugin...			//TODO: Don't hard-code this path, generate it dynamically (there's no guarantee that the files will have these names - the user might move the plugin to another directory, for example, and this test would (inappropriately) fail.)
		return;
	}
	
	echo "
	<tr>
		<td colspan='5' class='plugin-update'>
			<h3>Important Upgrade Notice</h3>
			<div align='left'>
				Auto-upgrading Featurific for Wordpress to the most recent version works flawlessly.  However, <strong>any changes
				to your template files will be lost</strong> if you have:
				<ul>
					<li>Modified existing templates</li>
					<li>Created new templates</li>
					<li>Installed new templates</li>
				</ul>
				Essentially, if you have done anything custom to your templates and you auto-upgrade, your customized templates will
				be lost.  <strong>Before upgrading, copy your	customized templates to a safe location (e.g. outside of the
				featurific-for-wordpress directory), upgrade the plugin, and then copy your custom templates back into Featurific's
				templates directory.</strong>  This will prevent your customized templates from being lost.
			</div>
		</td>
	</tr>
	";
}


/**
 * Activate featurific.
 *
 * Attempt to automatically add a call to insert_featurific() in the user's
 * home template.
 */
function featurific_activate($template)
{
	//echo('Activating Featurific<br/>');
		
	featurific_set_default_options();
	featurific_test_environment();
	
	featurific_create_tables();
	
	//$template is non-null (contains the name of the new theme) when featurific_activate() is called by the switch_theme action.
	if($template) {
		$template_path = get_home_template_of_theme(get_theme($template));
	}
	//$template is null when featurific_activate() is called by register_activation_hook.  In this case, just use the current theme's home template.
	else {
		$template_path = get_home_template();
	}
		
	//echo('$template_path:'.$template_path.'<br/>');
	
	//Force XML generation
	featurific_do_cron(true);

	if(featurific_configure_template($template_path)) {
		update_option('featurific_current_template_configured', true);
	}
	else {
		update_option('featurific_current_template_configured', false);
		return;
	}
	
	//echo('Activation of Featurific was successful.<br/>');
	//__FILE__
	//wp_redirect(get_option('siteurl') . '/wp-admin/options-general.php?page=featurificoptions');
}


/**
 * Perform actions on plugin deactivation.
 */
function featurific_deactivate()
{
	//echo('Deactivating Featurific');
	
	//featurific_delete_options(); //Only used for debugging
	
	//TODO: Delete SQL tables (e.g. featurific_image_cache)
}


/**
 * Test the configuration of Wordpress/the webserver to determine to what
 * degree it is compatible with certain features required by the plugin.
 */
function featurific_test_environment() {
	featurific_test_plugin_root_write_access();
	featurific_test_image_cache_write_access();
}


/**
 *
 */
function featurific_test_plugin_root_write_access() {
	$filename = 'FeaturificTestPluginRootWriteAccess'.rand(999999999, 9999999999); //Create an (essentially) guaranteed unique filename
	$path = featurific_get_plugin_root().$filename;
	//echo "Testing $path<br/>";
	$f = @fopen($path, 'w');
	
	//Success
	if($f) {
		//echo 'success<br/>';
		fclose($f);
		unlink($path);
		update_option('featurific_root_write_access', true);
		update_option('featurific_store_data_xml_in_db', false);
	}
	//Failure
	else {
		//echo 'failure<br/>';
		update_option('featurific_root_write_access', false);
		update_option('featurific_store_data_xml_in_db', true);
	}
}


/**
 *
 */
function featurific_test_image_cache_write_access() {
	$filename = 'FeaturificTestImageCacheWriteAccess'.rand(999999999, 9999999999); //Create an (essentially) guaranteed unique filename
	$path = featurific_get_plugin_root().'image_cache/'.$filename;
	//echo "Testing $path<br/>";
	$f = @fopen($path, 'w');
	
	//Success
	if($f) {
		//echo 'success<br/>';
		fclose($f);
		unlink($path);
		update_option('featurific_image_cache_write_access', true);
		update_option('featurific_store_cached_images_in_db', false);
	}
	//Failure
	else {
		//echo 'failure<br/>';
		update_option('featurific_image_cache_write_access', false);
		update_option('featurific_store_cached_images_in_db', true);
	}
}

/**
 * Attempt to automatically insert the call to insert_featurific() in the user's active template.
 *
 * The algorithm used to do so is as follows:
 *  1. Find the first instance of 'have_posts()' in the template.
 *  2. Find the first instance of '<?' that *precedes* the text found in step 1.
 *  3. Find the first instance of "\n" (newline character) that *precedes* the text found in step 2.
 *  4. Insert our call to insert_featurific() just before the newline character found in step 3.
 *
 * Note that we do lots of error checking because we want to avoid trashing the template at all costs.
 *
 * Returns true if insertion was successful or if the template already contains a call to insert_featurific.
 */
function featurific_configure_template($template_path)
{
	$f = @fopen($template_path, 'r'); //NOTE: The '@' suppresses warning messages.  We need to be certain to check the return value.
	if(!$f) return featurific_configure_template_error("Template file ($template_path) could not be opened for reading.");

	//Read file into a buffer.  Hard on memory, easy on the programmer (string manipulation is much easier than file pointer manipulation).  Since this function is only run on activation, this isn't a problem.
	while(!feof($f))
		$fb .= fgets($f, 4096);
	fclose($f);

	if($fb==null || strlen($fb)<10) return featurific_configure_template_error("Template file ($template_path) could either not be read or is oddly short."); //If the template is less than 10 chars long, something is up.
	//echo "fb len: ".strlen($fb)."<br/>";


	//If the template already contains the call to insert_featurific(), just return (true).
	if(strpos($fb, 'insert_featurific')!==false) {
		return true;
	}


	//1. Find the first instance of 'have_posts()' in the template.
	$pos = strpos($fb, 'have_posts()');
	if($pos===false) return featurific_configure_template_error("Template file ($template_path) does not contain 'have_posts()'.");
	//echo "pos: $pos<br/>";
	//echo "\n\n\n\n<pre>".substr($fb, 0, $pos)."</pre>\n\n\n\n";


	// 2. Find the first instance of '<?' that *precedes* the text found in step 1.
	//We can't use strrpos() because PHP 4 only supports using a single character needle; we need to use a string ('<?')

	//tp: temp position, ltp: last temp position
	$needle = '<?';
	$needle_len = strlen($needle);
	$tp = 0-$needle_len; //Negative needle_len so that the first strpos() search starts at index 0.
	do {
		$ltp = $tp;
		//echo "ltp: $ltp";
		$tp = strpos($fb, $needle, $ltp+$needle_len); //Find the first '<?' after $ltp (+$needle_len because the needle is $needle_len chars long)
	} while($tp!==false && $tp<$pos); //Continue looping while '<?' can be found and precedes the instance of have_posts() found in step 1.
	$pos = $ltp;
	
	if($pos===false) return featurific_configure_template_error("Could not find the preceding '<?' in the template file ($template_path).");
	//echo "pos: $pos<br/>";	
	//echo "\n\n\n\n<pre>".substr($fb, 0, $pos)."</pre>\n\n\n\n";


	// 3. Find the first instance of "\n" (newline character) that *precedes* the text found in step 2.
	//We can use strrpos() here because we're using a needle of length 1.
	$pos = strrpos(substr($fb, 0, $pos), "\n"); //The offset param of strrpos() seems to have no effect, so we have to use substr() to truncate the buffer for searching.
	if($pos===false) return featurific_configure_template_error("Could not find the preceding \"\\n\" in the template file ($template_path).");
	//echo "pos: $pos<br/>";
	//echo "\n\n\n\n<pre>".substr($fb, 0, $pos)."</pre>\n\n\n\n";


	// 4. Insert our call to insert_featurific() just before the newline character found in step 3.
	//Save a back up copy of the template in case something unexpected happens.
	copy($template_path, $template_path.'.original');

	$f = @fopen($template_path, 'w'); //NOTE: The '@' suppresses warning messages.  We need to be certain to check the return value.
	if(!$f) return featurific_configure_template_error("Template file ($template_path) could not be opened for writing.");

	//Write the file from the buffer.  If an error occurs on any of the writes, restore the original file and return.
	if(	fwrite($f, substr($fb, 0, $pos))===false ||
			fwrite($f, featurific_get_template_html())===false ||
			fwrite($f, substr($fb, $pos, strlen($fb)-$pos))===false) {
		fclose($f);
		copy($template_path.'.original', $template_path); //Restore the file to its original state (from the backup copy) just in case our call to fwrite() somehow trashed the template file.
		return featurific_configure_template_error("Could not write to the template file ($template_path).");
	}
	
	return true;
}


/**
 * Write out an error message and return.
 */
function featurific_configure_template_error($error) {
	//echo "Featurific for Wordpress error: $error  For Featurific for Wordpress to function correctly, you need to manually add '&lt;?php insert_featurific(); ?&gt;' to your template file at the desired location.<br/>";
	return false;
}


/**
 * Return the HTML needed for calling featurific from the template.
 */
function featurific_get_template_html()
{
	return <<<HTML
<?php
 //Code automatically inserted by Featurific for Wordpress plugin
 if(is_home())                             //If we're generating the home page (remove this line to make Featurific appear on all pages)...
  if(function_exists('insert_featurific')) //If the Featurific plugin is activated...
   insert_featurific();                    //Insert the HTML code to embed Featurific
?>
HTML;
}


/**
 * Alias for insert_featurific().
 */
function featurific_insert() {
	return insert_featurific();
}


/**
 * Output the HTML needed for embedding Featurific (if we're generating the
 * home page).  Regenerate the XML file as necessary.
 *
 * Note that this function uses a somewhat backwards naming scheme.
 * insert_featurific() is more English-like and is less likely to confuse
 * non-programmers when dealing with this call in their templates, so that's
 * what we use here.
 */
function insert_featurific() {
	global $featurific_version;
	
	//Plugin is active and we're on the home page - prepare and insert the HTML.
	featurific_do_cron();
	$web_root = featurific_get_plugin_web_root();
	$width = get_option('featurific_width');
	$height = get_option('featurific_height');
	
	$data_xml_override = get_option('featurific_data_xml_override');
	//Use the data.xml override
	if($data_xml_override!=null && $data_xml_override!='')
		$data_xml_filename = $data_xml_override;
	//Don't use the data.xml override
	else {
		//Serve up the XML dynamically from the DB
		if(get_option('featurific_store_data_xml_in_db')) {
			$data_xml_filename = 'data_xml.php';
		}
		//Serve up the XML via the web server from a previously generated flat file
		else {
			$data_xml_filename = get_option('featurific_data_xml_filename');
		}
	}
	
	$html = <<<HTML
		<center><!-- Begin Featurific Flash Gallery (version {$featurific_version}) - featurific.com -->
		<script type="text/javascript" src="{$web_root}featurific.js"></script><div id="swfDiv" name="swfDiv" wmode="transparent">
		<script type="text/javascript">
		// <![CDATA[
		fo = new SWFObject("{$web_root}FeaturificFree.swf?&lzproxied=false", "lzapp", "$width", "$height", "6", "#FF6600");
		fo.addParam("swLiveConnect", "true");
		fo.addParam("name", "lzapp");
		fo.addParam("wmode", "transparent");
		fo.addParam("allowScriptAccess", "always");
		fo.addVariable("xml_location", "{$web_root}{$data_xml_filename}");
		fo.write("swfDiv");
		// ]]>
		</script></div>
		<!-- End Featurific --></center>
HTML;

// 	$html .= <<<HTML
// 		<br/>
// 		<br/>
// 		<br/>
// 		<br/>
// 		<br/>
// 		<br/>
// 		<br/>
// 		<br/>
// 		<br/>
// 		<br/>
// 		<center><!-- Begin Featurific Flash Gallery - featurific.com -->
// 		<script type="text/javascript" src="{$web_root}swfobject.js"></script><div id="swfDiv2" name="swfDiv2" wmode="transparent">
// 		<script type="text/javascript">
// 		// <![CDATA[
// 		fo = new SWFObject("{$web_root}FeaturificProDebug.swf?&lzproxied=false", "lzapp", "$width", "$height", "6", "#FF6600");
// 		fo.addParam("swLiveConnect", "true");
// 		fo.addParam("name", "lzapp");
// 		fo.addParam("wmode", "transparent");
// 		fo.addParam("allowScriptAccess", "always");
// 		fo.addVariable("xml_location", "{$web_root}{$data_xml_filename}");
// 		fo.write("swfDiv2");
// 		// ]]>
// 		</script></div>
// 		<!-- End Featurific --></center>
// HTML;

	echo $html;
}


/**
 * Get the home template of a specified theme.
 *
 * Adapted from Wordpress' get_home_template() in theme.php.
 *
 * @theme: A theme array, as returned by get_theme().
 */
function get_home_template_of_theme($theme) {
	$template = '';

	if ( file_exists(ABSPATH . $theme['Template Dir'] . "/home.php") )
		$template = ABSPATH . $theme['Template Dir'] . "/home.php";
	elseif ( file_exists(ABSPATH . $theme['Template Dir'] . "/index.php") )
		$template = ABSPATH . $theme['Template Dir'] . "/index.php";

	return apply_filters('home_template', $template);
}


/**
 * Get the root directory of the Featurific for Wordpress plugin relative to
 * the filesystem root.
 */
function featurific_get_plugin_root() {
	//return substr(__FILE__, 0, strrpos(__FILE__, '/')+1); //Works, but only on POSIX systems (not windows).  And should use DIRECTORY_SEPARATOR instead of '/' anyway...
	return dirname(__FILE__).'/'; //Should work on all systems
}


/**
 * Get the root directory of the Featurific for Wordpress plugin relative to
 * the web root.
 */
function featurific_get_plugin_web_root() {
	$site_url = get_option('siteurl');
	
	//Test URLs
	//$site_url = 'http://nacl.ir';
	//$site_url = 'http://nacl.ir/';
	//$site_url = 'http://nacl.ir/a-dir/whatever/wordpress';
	//$site_url = 'http://nacl.ir/a-dir/whatever/wordpress/';
	$pos = featurific_strpos_nth(3, $site_url, '/');
	
	$plugin_root = featurific_get_plugin_root();
	//PHP 5 only
	//$plugin_dir_name = substr($plugin_root, strrpos($plugin_root, '/', -2)+1); //-2 to skip the trailing '/' on $plugin_root
	//PHP 4 workaround
	$plugin_dir_name = substr($plugin_root, strrpos(substr($plugin_root, 0, strlen($plugin_root)-2), DIRECTORY_SEPARATOR)+1); //-2 to skip the trailing '/' on $plugin_root

	if($pos===false)
		$web_root = substr($site_url, strlen($site_url));
	else
		$web_root = '/' . substr($site_url, $pos);
	
	if($web_root[strlen($web_root)-1]!='/')
		$web_root .= '/';

	$web_root .= 'wp-content/plugins/' . $plugin_dir_name;
	
	return $web_root;
}


/**
 * Find the position of the $n-th occurence of $needle in $haystack, starting
 * at $offset.  Not fully tested.
 */
function featurific_strpos_nth($n, $haystack, $needle, $offset=0)
{
	$needle_len = strlen($needle);
	$hits = 0;
	while($hits!=$n) {
		$offset = strpos($haystack, $needle, $offset);
		
		if($offset===false)
			return false;
		
		$offset += $needle_len;
		$hits++;
	}
	
	return $offset;
}


function featurific_parse_images_from_html($html) {
	$images = array();
	$parser = new HtmlParser($html);

	while ($parser->parse()) {
		if($parser->iNodeType==NODE_TYPE_ELEMENT && strtolower($parser->iNodeName)=='img') {
			$src = $parser->iNodeAttributes['src'];
			
			if($src!=null && $src!='') {
				//echo "Found '$src'<br/>";
				$images[] = $src;
				//$thumbpath = image_resize( $file, $max_side, $max_side );
			}
		}
	}
	
	return $images;
}


/**
 * Convert a Wordpess date into a human-friendly date.
 */
function featurific_date_to_human_date($date) {
  return date('F j, Y', $date);
}


/**
 * Convert a Wordpess date into a long human-friendly date.
 */
function featurific_date_to_long_human_date($date) {
  return date('l jS \of F Y', $date);
}


/**
 * Convert a Wordpess date into a slash-separated date.
 */
function featurific_date_to_slashed_date($date) {
  return date('m/d/y', $date);
}


/**
 * Convert a Wordpess date into a period-separated date.
 */
function featurific_date_to_dotted_date($date) {
  return date('m.d.y', $date);
}


/**
 * Convert a Wordpess date into a human-friendly time.
 */
function featurific_date_to_human_time($date) {
  return date('g:i a', $date);
}


/**
 * Convert a Wordpess date into a long human-friendly time.
 */
function featurific_date_to_long_human_time($date) {
  return date('g:i:s a', $date);
}


/**
 * Convert a Wordpess date into a military time.
 */
function featurific_date_to_military_time($date) {
  return date('H:i:s', $date);
}


/**
 * Helper function for our date/time formatters.
 *
 * Adapted from Wordpress' get_gmt_from_date() in formatting.php.
 */
function featurific_parse_date($string) {
  preg_match('#([0-9]{1,4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})#', $string, $matches);
  return mktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1]);
}


/**
 * Add the Featurific options page to the Settings menu.
 */
function featurific_add_pages() {
	// Add a new submenu under Options:
	add_options_page('Featurific', 'Featurific', 8, 'featurificoptions', 'featurific_options_page');
}


/**
 * Delete Featurific Options
 */
function featurific_delete_options() {
	//User-specified options
	delete_option('featurific_screen_assignment');
	delete_option('featurific_width');
	delete_option('featurific_height');
	delete_option('featurific_type');
	delete_option('featurific_user_specified_posts');
	delete_option('featurific_generation_frequency');
	delete_option('featurific_data_xml_override');
	delete_option('featurific_template');
	delete_option('featurific_num_posts');
	delete_option('featurific_popular_days');
	delete_option('featurific_auto_excerpt_length');
	delete_option('featurific_screen_duration');
	
	//Internal options
	delete_option('featurific_last_generation_time');
	delete_option('featurific_data_xml_filename');
	delete_option('featurific_data_xml_filename_old');
	delete_option('featurific_data_xml_filename_to_delete');
}


/**
 * Set options according to their defaults, but only if the option is undefined.
 * This allows user-specified options to persist if the user disables the
 * plugin for a period of time and then re-enables it later.
 */
function featurific_set_default_options() {
	//User-specified options
	if(get_option('featurific_screen_assignment')===false)		add_option('featurific_screen_assignment', 'random');
	if(get_option('featurific_width')===false)								add_option('featurific_width', 0);
	if(get_option('featurific_height')===false)								add_option('featurific_height', 0);
	if(get_option('featurific_type')===false)									add_option('featurific_type', 'commented');
	if(get_option('featurific_user_specified_posts')===false)	add_option('featurific_user_specified_posts', '');
	if(get_option('featurific_generation_frequency')===false)	add_option('featurific_generation_frequency', 10);
	if(get_option('featurific_data_xml_override')===false)		add_option('featurific_data_xml_override', '');
	if(get_option('featurific_template')===false)							add_option('featurific_template', 'Thumber Abstract/template.xml');
	if(get_option('featurific_num_posts')===false)						add_option('featurific_num_posts', 5);
	if(get_option('featurific_popular_days')===false)					add_option('featurific_popular_days', 90);
	if(get_option('featurific_auto_excerpt_length')===false)	add_option('featurific_auto_excerpt_length', 150);
	if(get_option('featurific_screen_duration')===false)			add_option('featurific_screen_duration', 7000);

	//Internal options
	if(get_option('featurific_last_generation_time')===false)					add_option('featurific_last_generation_time', 0);
	if(get_option('featurific_data_xml_filename')===false)						add_option('featurific_data_xml_filename', '');
	if(get_option('featurific_data_xml_filename_old')===false)				add_option('featurific_data_xml_filename_old', '');
	if(get_option('featurific_data_xml_filename_to_delete')===false)	add_option('featurific_data_xml_filename_to_delete', '');
	if(get_option('featurific_admin_messages_to_show_once')===false)	add_option('featurific_admin_messages_to_show_once', array());
}


/**
 * Get all of the available Featurific templates (usually in
 * plugins/featurific/templates)
 *
 * Adapted from Wordpress' get_themes() function in theme.php.  Note that this
 * function is not fully tested.
 */
function featurific_get_templates() {
	$template_root = featurific_get_plugin_root().'templates/';
	$templates = array();
	
	$templates_dir = @ opendir($template_root);
	if ( !$templates_dir )
		return false;

	while ( ($template_dir = readdir($templates_dir)) !== false ) {
		if ( is_dir($template_root . '/' . $template_dir) && is_readable($template_root . '/' . $template_dir) ) {
			if ( $template_dir{0} == '.' || $template_dir == '..' || $template_dir == 'CVS' )
				continue;
			$stylish_dir = @ opendir($template_root . '/' . $template_dir);
			//$found_stylesheet = false;
			while ( ($template_file = readdir($stylish_dir)) !== false ) {
					if ( $template_file == 'template.xml' ) {
						$templates[$template_dir] = $template_dir . '/' . $template_file;
					break;
				}
			}
			@closedir($stylish_dir);
		}
	}
	
	return $templates;
}


/**
 * Display the page content for the Featurific admin submenu, and save
 * the values resulting from form submission of this admin page.
 *
 * Adapted from http://codex.wordpress.org/Adding_Administration_Menus
 */
function featurific_options_page() {
	$hidden_field_name = 'featurific_submit_hidden';
	
	//Set up names
	$screen_assignment_opt_name = 'featurific_screen_assignment';
	$width_opt_name = 'featurific_width';
	$height_opt_name = 'featurific_height';
	$type_opt_name = 'featurific_type';
	$user_specified_posts_opt_name = 'featurific_user_specified_posts';
	$frequency_opt_name = 'featurific_generation_frequency';
	$data_xml_override_opt_name = 'featurific_data_xml_override';
	$template_opt_name = 'featurific_template';
	$num_posts_opt_name = 'featurific_num_posts';
	$popular_days_opt_name = 'featurific_popular_days';
	$auto_excerpt_length_opt_name = 'featurific_auto_excerpt_length';
	$screen_duration_opt_name = 'featurific_screen_duration';
	//$_opt_name = 'featurific_';
	//$_opt_name = 'featurific_';

	//Read in existing option values from database
	$screen_assignment_opt_val = get_option($screen_assignment_opt_name);
	$width_opt_val = get_option($width_opt_name);
	$height_opt_val = get_option($height_opt_name);
	$type_opt_val = get_option($type_opt_name);
	$user_specified_posts_opt_val = get_option($user_specified_posts_opt_name);
	$frequency_opt_val = get_option($frequency_opt_name);
	$data_xml_override_opt_val = get_option($data_xml_override_opt_name);
	$template_opt_val = get_option($template_opt_name);
	$num_posts_opt_val = get_option($num_posts_opt_name);
	$popular_days_opt_val = get_option($popular_days_opt_name);
	$auto_excerpt_length_opt_val = get_option($auto_excerpt_length_opt_name);
	$screen_duration_opt_val = get_option($screen_duration_opt_name);
	//$_opt_val = get_option($_opt_name);
	//$_opt_val = get_option($_opt_name);

	// See if the user has posted us some information
	// If they did, this hidden field will be set to 'Y'
	if( $_POST[ $hidden_field_name ] == 'Y' ) {
		//Read the posted values
		$screen_assignment_opt_val = $_POST[$screen_assignment_opt_name];
		//$width_opt_val = $_POST[$width_opt_name];
		//$height_opt_val = $_POST[$height_opt_name];
		$type_opt_val = $_POST[$type_opt_name];
		$user_specified_posts_opt_val = $_POST[$user_specified_posts_opt_name];

		//Make sure there's a valid value in the frequency field.  If not, we just insert our own valid value.
		$frequency_opt_val = $_POST[$frequency_opt_name];
		if($_POST[$frequency_opt_name]==null || $_POST[$frequency_opt_name]=='' || $_POST[$frequency_opt_name]<1)
			$frequency_opt_val = 10;

		$data_xml_override_opt_val = $_POST[$data_xml_override_opt_name];
		$template_opt_val = $_POST[$template_opt_name];
		$num_posts_opt_val = $_POST[$num_posts_opt_name];
		$popular_days_opt_val = $_POST[$popular_days_opt_name];
		$auto_excerpt_length_opt_val = $_POST[$auto_excerpt_length_opt_name];
		$screen_duration_opt_val = $_POST[$screen_duration_opt_name];
		//$_opt_val = $_POST[$_opt_name];
		//$_opt_val = $_POST[$_opt_name];
		
		//If 'popular' post selection was chosen but the user has not installed Wordpress.com stats correctly, report an error and fall back to another post selection type.
		if($type_opt_val=='popular' && !function_exists('stats_get_csv')) {
			echo "<div class='updated' style='background-color:#f66;'><p><a href='options-general.php?page=featurificoptions'>Featurific for Wordpress</a> needs attention: please install the <a href='http://wordpress.org/extend/plugins/stats/'>Wordpress.com Stats</a> plugin to use the 'Most popular' post selection type.  Until the plugin is installed, consider using the 'Most commented' post selection type instead.</p></div>";
			$type_opt_val = 'commented'; //'commented' is the best approximation of 'popular' that we have
		}
		
		//If we're using a manually created data.xml override, get the dimensions from the data.xml file.  Otherwise, the dimensions will be set according to the template file when the data.xml file is automatically generated.
		if($data_xml_override_opt_val!=null && $data_xml_override_opt_val!='') {
			$in = file_get_contents(featurific_get_plugin_root() . $data_xml_override_opt_val);
			$xml = new XMLParser($in);
			$xml->Parse();

			$dimensions = featurific_get_dimensions_from_xml($xml);
			$width_opt_val = $dimensions['width'];
			$height_opt_val = $dimensions['height'];
		}
		
		//Save the posted values in the database
		update_option($screen_assignment_opt_name, $screen_assignment_opt_val);
		update_option($width_opt_name, $width_opt_val);
		update_option($height_opt_name, $height_opt_val);
		update_option($type_opt_name, $type_opt_val);
		update_option($user_specified_posts_opt_name, $user_specified_posts_opt_val);
		update_option($frequency_opt_name, $frequency_opt_val);
		update_option($data_xml_override_opt_name, $data_xml_override_opt_val);
		update_option($template_opt_name, $template_opt_val);
		update_option($num_posts_opt_name, $num_posts_opt_val);
		update_option($popular_days_opt_name, $popular_days_opt_val);
		update_option($auto_excerpt_length_opt_name, $auto_excerpt_length_opt_val);
		update_option($screen_duration_opt_name, $screen_duration_opt_val);
		//update_option($_opt_name, $_opt_val);
		//update_option($_opt_name, $_opt_val);
		
		// Output a status message.
		echo '<div class="updated"><p><strong>Options saved.</strong></p></div>';

	
		//Force XML generation
		featurific_do_cron(true);
	}

	//Prepare the template (or data.xml override) notes
	$template_opt_val = get_option('featurific_template');
	$in = file_get_contents(featurific_get_plugin_root() . 'templates/'. $template_opt_val);
	$xml = new XMLParser($in);
	$xml->Parse();
	$notes_html = featurific_get_notes_from_xml($xml);

	//Prepare other assorted values
	$stats_installed_str = function_exists('stats_get_csv')?'<font color="#00cc00">is installed</font>':'<font color="#ff0000">is not installed</font>';
	$plugin_directory = featurific_get_plugin_root();

	// Display the options editing screen
	?>
<div class="wrap">
<h2>Featurific for Wordpress</h2>

<form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<table class="form-table">


 <tr valign="top">
  <th scope="row">Template:</th>
  <td>
   <select name="<?php echo $template_opt_name; ?>">
    <?php
			$templates = featurific_get_templates();
			asort($templates);
			// echo '<!--';
			// print_r($templates);
			// echo '-->';
			foreach($templates as $template => $path) {
				$selected = $path==$template_opt_val?"selected='selected'":'';
				echo "<option value='$path' $selected>$template</option>\n";
				
				if($path==$template_opt_val)
					$current_template_name = $template;
			}
		?>
   </select>
		<?php
			if($notes_html!=null && $notes_html!='') {
		?>
		<div style="background-color:#f9f9ff; padding-top: 1px; padding-bottom: 10px; padding-left: 10px; padding-right: 10px; ">
		<h3>Notes for <font color="#0000cc"><?php echo $current_template_name; ?></font></h3>
		<?php
				echo $notes_html;
				echo '</div>';
			}
		?>
  </td>
 </tr>

 <!--
 <tr valign="top">
  <th scope="row">Gallery Size:</th>
  <td>
   Width <input type="text" name="<?php echo $width_opt_name; ?>" value="<?php echo $width_opt_val; ?>" size="5">
   Height <input type="text" name="<?php echo $height_opt_name; ?>" value="<?php echo $height_opt_val; ?>" size="5">
  </td>
 </tr>
 -->

 <tr valign="top">
  <th scope="row">Post Selection:</th>
  <td>
   <input type="radio" name="<?php echo $type_opt_name; ?>" value='popular' <?php if($type_opt_val=='popular') { echo 'checked'; } ?>> Most popular posts over the last <input type="text" name="<?php echo $popular_days_opt_name; ?>" value="<?php echo $popular_days_opt_val; ?>" size="2"> days (<a href='http://wordpress.org/extend/plugins/stats/'>Wordpress.com Stats Plugin</a> <?php echo $stats_installed_str; ?>)<br/>
   <input type="radio" name="<?php echo $type_opt_name; ?>" value='commented' <?php if($type_opt_val=='commented') { echo 'checked'; } ?>> Most commented posts<br/>
   <input type="radio" name="<?php echo $type_opt_name; ?>" value='recent' <?php if($type_opt_val=='recent') { echo 'checked'; } ?>> Most recent posts<br/>
   <input type="radio" name="<?php echo $type_opt_name; ?>" value='userspecified' <?php if($type_opt_val=='userspecified') { echo 'checked'; } ?>> User-specified posts: <input type="text" name="<?php echo $user_specified_posts_opt_name; ?>" value="<?php echo $user_specified_posts_opt_val; ?>" size="35"> (comma separated - e.g. "4, 1, 16, 5")<br/>
   <!--This field is only used if  '<code>User-specified posts</code>' is selected as the <code>Post Selection</code>.-->
  </td>
 </tr>

 <tr valign="top">
  <th scope="row">Number of Posts:</th>
  <td>
   <input type="text" name="<?php echo $num_posts_opt_name; ?>" value="<?php echo $num_posts_opt_val; ?>" size="2"> posts<br />
   The number of posts (i.e. screens) to include in the gallery.
  </td>
 </tr>

 <tr valign="top">
  <th scope="row">Screen Duration:</th>
  <td>
   <input type="text" name="<?php echo $screen_duration_opt_name; ?>" value="<?php echo $screen_duration_opt_val; ?>" size="5"> milliseconds<br />
   The duration for which each screen will be displayed.  Note that this value is provided in <strong>milliseconds</strong>.
  </td>
 </tr>

</table>

<h3>Advanced Options</h3>

<table class="form-table">

 <tr valign="top">
  <th scope="row">Screen Assignment:</th>
  <td>
   <input type="radio" name="<?php echo $screen_assignment_opt_name; ?>" value='ordered' <?php if($screen_assignment_opt_val=='ordered') { echo 'checked'; } ?>> Ordered (Assign first post to screen 1, second post to screen 2, etc.  Predictable, but the same screen layout is displayed first each time the page loads.)<br/>
   <input type="radio" name="<?php echo $screen_assignment_opt_name; ?>" value='random' <?php if($screen_assignment_opt_val=='random') { echo 'checked'; } ?>> Random (Assign posts to random screens.  A random screen layout is displayed first each time the page loads, which visually suggests that the page has been updated.)<br />
   Note that a new random screen is chosen every time the data.xml file is generated (every 10 minutes), not every time the page is loaded.
  </td>
 </tr>

 <tr valign="top">
  <th scope="row">Auto-Excerpt Length:</th>
  <td>
   First <input type="text" name="<?php echo $auto_excerpt_length_opt_name; ?>" value="<?php echo $auto_excerpt_length_opt_val; ?>" size="3"> characters<br />
   When an excerpt is not provided for a post, an excerpt is automatically created from the first x characters of the post.  This field specifies how many characters long the auto-excerpt should be (i.e. the value of x).
  </td>
 </tr>

 <tr valign="top">
  <th scope="row">Update Frequency:</th>
  <td>
   Every <input type="text" name="<?php echo $frequency_opt_name; ?>" value="<?php echo $frequency_opt_val; ?>" size="2"> minutes<br />
   How often the gallery will be re-generated (e.g. to include new posts).
  </td>
 </tr>

 <tr valign="top">
  <th scope="row">data.xml Override:</th>
  <td>
   <input type="text" name="<?php echo $data_xml_override_opt_name; ?>" value="<?php echo $data_xml_override_opt_val; ?>" size="20"><br />
   Specify a manually-created <code>data.xml</code> file to use instead of the auto-generated one.  This file should be in the Featurific plugin directory (<?php echo $plugin_directory; ?>) (Leave this field blank to use the auto-generated data.xml)
  </td>
 </tr>

</table>

<hr />

<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Update Options', 'featurific_trans_domain' ) ?>" />
</p>

</form>
</div>

<?php
 
}


/**
 * Perform cron-ish functions, such as generating the data.xml file every x
 * minutes.
 *
 * Note that this isn't a true cron because it runs at *most* every x minutes,
 * not exactly every x minutes.  (Unless $ignore_times is set to true, in which case
 * cron runs immediately, regardless of when it was last run)  If there are no
 * calls to insert_featurific() for longer than x minutes, the data.xml is not
 * regenerated.  However, when the first visitor hits the main page after the
 * respite, the data.xml file will be regenerated.
 *
 * Note that even if $ignore_times is true, if we're using a data_xml_override,
 * the XML file will NOT be regenerated.
 */
function featurific_do_cron($ignore_times=false) {
	//Return if the user has specified a manually created XML file
	$data_xml_override = get_option('featurific_data_xml_override');
	if($data_xml_override!=null && $data_xml_override!='')
		return;

	//If ignore_times is false, determine if we can just return (and avoid the cost of regenerating the XML file)
	if(!$ignore_times) {
		$diff = time() - get_option('featurific_last_generation_time');
		$freq_in_secs = get_option('featurific_generation_frequency')*60; //TODO: Optimization candidate
	
		//echo "diff: $diff<br/>";
		//echo "freq_in_secs: $freq_in_secs<br/>";

		//Return if the necessary rest period has not passed
		if($diff<$freq_in_secs)
			return;
	}

	//Either ignore_times was true, or we determined that we need to regenerate the XML file.  So, regenerate the file.
	update_option('featurific_last_generation_time', time());
	featurific_data_xml_housekeeping();
}


/**
 * Perform data.xml-related housekeeping functions.
 *
 * Specifically, create a new data.xml filename and generate the file.  Set
 * the _old_filename and _filename_to_delete options appropriately.  Note that
 * we change the filename every time we generate a new data.xml file so that
 * browsers and proxies don't cache the XML file.  This is the simplest way
 * to ensure caching does not occur - other (more complicated and suboptimal)
 * possibilities include cache-related HTTP headers, generating and serving
 * the XML file dynamically, etc.
 */
function featurific_data_xml_housekeeping() {
	//echo 'Performing data.xml housekeeping...<br/>';

	//Get the current options.
	$f_new = 'data_' . time() . '.xml';
	$f_current = get_option('featurific_data_xml_filename');
	$f_old = get_option('featurific_data_xml_filename_old');
	$f_to_delete = get_option('featurific_data_xml_filename_to_delete');
	
	// echo "f_new: $f_new<br/>";
	// echo "f_current: $f_current<br/>";
	// echo "f_old: $f_old<br/>";
	// echo "f_to_delete: $f_to_delete<br/>";

	//Generate the new XML file.
	$success = featurific_generate_data_xml($f_new);
	
	if(!$success) {
		featurific_show_admin_message_once('Featurific for Wordpress needs your attention: There was a minor error while generating your Featurific data.xml file.  Don\'t worry - everything is fine with your site and Featurific.  However, we recommend that you <a href="http://featurific.com/content/contact-us">contact us</a> so we can help you resolve this minor issue.');
		return;
	}

	//Delete the oldest XML file.
	if($f_to_delete!='')
		@unlink(featurific_get_plugin_root() . $f_to_delete); //NOTE: The '@' suppresses warnings (e.g. if the file could not be deleted, no error is reported.)

	//Update the options.  Note that we keep two old files around to avoid attempts at deleting the file while the web server is still sending it to clients.  It's unprobable, although possible, that if we immediately deleted the old XML file, that a client could still be requesting the old file.  So, keeping around the *two* old XML files (instead of just one) essentially eliminates this possibility.
	update_option('featurific_data_xml_filename', $f_new);
	update_option('featurific_data_xml_filename_old', $f_current);
	update_option('featurific_data_xml_filename_to_delete', $f_old);
}


/**
 * Generate the data.xml file according to the current template and post
 * selection type.
 */
function featurific_generate_data_xml($output_filename) {
	$template = get_option('featurific_template');
	
	//Parse the template XML
	$in = file_get_contents(featurific_get_plugin_root() . 'templates/'. $template);
	$template_xml = new XMLParser($in);
	$template_xml->Parse();
	
	//Set options for the dimensions of the gallery
	$dimensions = featurific_get_dimensions_from_xml($template_xml);
	update_option('featurific_width', $dimensions['width']);
	update_option('featurific_height', $dimensions['height']);
	
	//Generate the gallery XML
	$out = "<data>\n";
	$out .= featurific_generate_non_screen_elements($template_xml);
	
	$posts = featurific_get_posts(
		get_option('featurific_type'),
		get_option('featurific_num_posts'),
		get_option('featurific_user_specified_posts')
	);

	$out .= featurific_generate_screen_elements($template_xml, $posts);

	$out .= "\n</data>\n";
	
	$out = featurific_strip_tags($out);
	
	//echo str_replace("\n", "<br/>", htmlentities($out));

	//Write the gallery XML to the DB
	if(get_option('featurific_store_data_xml_in_db')) {
		update_option('featurific_data_xml', $out);
		//echo get_option('featurific_data_xml');
	}
	//Write the gallery XML to disk
	else {
		$fout_filename = featurific_get_plugin_root() . $output_filename;

		$fout = @fopen($fout_filename, 'w'); //NOTE: The '@' suppresses warning messages.  We need to be certain to check the return value.
		if(!$fout) {
			echo "Could not open $fout_filename for writing.";
			return false;
		}

		if(fwrite($fout, $out)===false) {
			echo "Could not write the XML to $fout_filename.";
			fclose($fout);
			return false;
		}

		fclose($fout);
	}
	
	return true;
}


/**
 * Return the height and width specified in the template or data.xml file.
 *
 * Since template XML and data.xml files have a similar structure, this
 * function works on both file types.
 */
function featurific_get_dimensions_from_xml($xml) {
	$dimensions = array();
	
	$dimensions['width'] = $xml->document->size[0]->tagAttrs['width'];
	$dimensions['height'] = $xml->document->size[0]->tagAttrs['height'];
	
	return $dimensions;
}


/**
 * Return the contents of the <notes>...</notes> tag.
 *
 * Since template XML and data.xml files have a similar structure, this
 * function works on both file types.
 */
function featurific_get_notes_from_xml($xml) {
	return
		str_replace('[', '<',
			str_replace(']', '>',
				$xml->document->notes[0]->tagData
			)
		);
}


/**
 * Generate the non-screen XML elements (size, automute, timeout, etc.)
 */
function featurific_generate_non_screen_elements($template_xml) {
	$out = '';
	
	foreach($template_xml->document->tagChildren as $k => $v) {
		if(strtolower($v->tagName)!='screen')
			$out .= $v->GetXML()."\n";
	}

	return $out;
}


/**
 * Generate the screen XML elements according to the specified $template_xml
 * and $posts.
 */
function featurific_generate_screen_elements($template_xml, $posts) {
	$templates = featurific_get_template_screen_elements($template_xml);
	$screen_assignment_type = get_option('featurific_screen_assignment');
	$last_template_id = -1; //-1 so that the first time into featurific_choose_screen_template(), the template at index 0 is chosen (if we're using the 'ordered' type)
	$out = '';
	
	//echo str_replace("\n", "<br/>", htmlentities($templates[0]->GetXML()));
	
	//Generate the screen elements
	foreach ($posts as $post_id => $post) {
		//TODO: Note that $last_template_id is passed by reference - it will be incremented and managed within featurific_choose_screen_template().  I don't like this solution, it should be re-coded.
		$screen_template_xml = featurific_choose_screen_template($templates, $screen_assignment_type, $last_template_id);
		
		//If something went wrong with getting the screen template xml, just continue to the next post.
		if($screen_template_xml==null)
			continue;
		
		//Create the translation array from the post
		$tr_arr = featurific_generate_translation_array($post);

		//Parse and set tag defaults
		foreach($screen_template_xml->tagChildren as $c) {
			if(strtolower($c->tagName)!='default')
				continue;
			
			$key = '%'.$c->tagAttrs['name'].'%';
			$value = $c->tagAttrs['value'];
			// echo "default: $key = $value<br/>";
			
			//Only set the tag to the default value if the post did not specify a valid value
			if($tr_arr[$key]==null || $tr_arr[$key]=='')
				$tr_arr[$key] = $value;
		}

		//Actually perform the translation
		$out .= strtr($screen_template_xml->GetXML(), $tr_arr);
		$out .= "\n";
	}
	
	return $out;
}


/**
 * Generate the translation array used for converting tags (e.g. %tagname%)
 * to their respective values from $post.
 */
function featurific_generate_translation_array($post) {
	$tr_arr = array();
	
	//Wrap all keys in '%', leave values untouched.
	foreach ($post as $k => $v) {
		$v = featurific_flash_escape($v);
		$tr_arr["%$k%"] = $v;
	}
	
	return $tr_arr;
}


/**
 * Escape a string suitable for use in Flash-manipulated XML.
 *
 * We could use htmlspecialchars(), but that escapes too much (&, ', ", <, >).
 * We only need to escape ' and ", so that's all we do here.  In addition to
 * escaping special HTML chars, we also want to escape '%' characters since
 * those have special meaning in our XML attributes.  We also need to
 * transform some HTML sequences that are not valid in Flash (e.g.
 * Some users copy over their posts from Word/OpenOffice which may use, for
 * example, &ldquo; &rdquo; &lsquo; &rsquo; for opening and closing quotes.
 * Flash (or at least the font we're using?) doesn't support these sequences,
 * so we need to transform them to their normal (' and ") equivalents.)
 */
function featurific_flash_escape($s) {
	//OPT: Optimization candidate

	return
	$string =
		str_replace('\'', '&#039;',
			str_replace('"', '&quot;',
				str_replace('%', '&#37;',
					str_replace('&ldquo;', '&quot;',
						str_replace('&rdquo;', '&quot;',
							str_replace('&lsquo;', '&#039;',
								str_replace('&rsquo;', '&#039;', $s)
							)
						)
					)
				)		
			)
		);
}


/**
 * Strip all Featurific for Wordpress tags (e.g. %tagname%) from $out.
 */
function featurific_strip_tags($out) {
	return ereg_replace("%[a-zA-Z0-9_]+%", '', $out);
}


/**
 * Convert $html to plain text.  Newlines, <div>s, <table>s (etc) become
 * ' - '.  Unlike many html to plain text converters, this function makes no
 * attempt to emulate HTML formatting in the plaintext (beyond replacing
 * certain elements with ' - '.)
 *
 * Adapted from a (slightly flawed) function found at
 * http://sb2.info/php-script-html-plain-text-convert/
 */
function featurific_html_to_text($html) {
	//Remove everything inside of <style> tags.
	$html = preg_replace('/<style[^>]*>.*?<\/style[^>]*>/si','',$html);

	//Remove everything inside of <script> tags.
	$html = preg_replace('/<script[^>]*>.*?<\/script[^>]*>/si','',$html);

	//Replace certain elements (that typically result in line breaks) with a newline character.
  $tags = array (
	  0 => '/<(\/)?h[123][^>]*>/si',
	  1 => '/<(\/)?h[456][^>]*>/si',
	  2 => '/<(\/)?table[^>]*>/si',
	  3 => '/<(\/)?tr[^>]*>/si',
	  4 => '/<(\/)?li[^>]*>/si',
	  5 => '/<(\/)?br[^>]*>/si',
	  6 => '/<(\/)?p[^>]*>/si',
	  7 => '/<(\/)?div[^>]*>/si',
  );
  $html = preg_replace($tags, "\n", $html);

	//Remove tags
	$html = preg_replace('/<[^>]+>/s', '', $html);

	//Replace non-breaking spaces with actual spaces.
	$html = preg_replace('/\&nbsp;/', ' ', $html);

	//Reduce spaces
	$html = preg_replace('/ +/s', ' ', $html);
	$html = preg_replace('/^\s+/m', '', $html);
	$html = preg_replace('/\s+$/m', '', $html);

	//Replace newlines with spaces
	$html = preg_replace('/\n+/s', '-!Line Break123!-', $html); //-!Line Break123!- is just a string that is highly unlikely to occur in the original string.

	//Reduce line break chars.
	$html = preg_replace('/(-!Line Break123!-)+/s', ' - ', $html);

	//Reduce spaces
	$html = preg_replace('/ +/s', ' ', $html);
	$html = preg_replace('/^\s+/m', '', $html);
	$html = preg_replace('/\s+$/m', '', $html);

	return $html;
}


/**
 * Choose a particular screen template in $templates according to the
 * $screen_assignment_type, and (if 'ordered' is chosen), the
 * $last_template_id.
 */
function featurific_choose_screen_template($templates, $screen_assignment_type, &$last_template_id) {
	$templates_size = sizeof($templates);
		
	//Ordered
	if($screen_assignment_type=='ordered' && $last_template_id!==null) {
		if($last_template_id==$templates_size-1) {
			$last_template_id = 0;
			return $templates[$last_template_id];
		}
			
		$last_template_id++;
		return $templates[$last_template_id];
	}
	//Random
	else {
		return $templates[rand(0, $templates_size-1)];
	}
}


/**
 * Get the screen elements from $template_xml.
 */
function featurific_get_template_screen_elements($template_xml) {
	$s = array();
	
	foreach($template_xml->document->tagChildren as $k => $v) {
		if(strtolower($v->tagName)=='screen')
			$s[] = $v;
	}

	return $s;
}


/**
 * Get an array of $n posts according to the post selection type ($type) and
 * (if 'userspecified' is chosen) $post_list.
 */
function featurific_get_posts($type, $n, $post_list=null)
{
	switch($type) {
		case 'popular':
			$days = get_option('featurific_popular_days');
			$popular_posts = stats_get_csv('postviews', "days=$days&limit=$n");
			
			$post_list = '';
			foreach ($popular_posts as $post) {
				if($post_list!='')
					$post_list .= ', ';
					
				$post_list .= $post['post_id'];
			}
			
			return featurific_get_posts('userspecified', $n, $post_list);
			break;

			
		case 'recent':
			// $posts = get_posts(
			// 	array(
			// 		'numberposts' => 5, 'offset' => 0,
			// 		'category' => 0, 'orderby' => 'post_date',
			// 		'order' => 'DESC', 'include' => '',
			// 		'exclude' => '', 'meta_key' => '',
			// 		'meta_value' =>'', 'post_type' => 'post',
			// 		'post_status' => 'publish', 'post_parent' => 0
			// 	)
			// );

			$posts = get_posts(
				array(
					'numberposts' => $n
				)
			);

			break;

			
		case 'commented':
			$posts = get_posts(
				array(
					'numberposts' => $n,
					'orderby' => 'comment_count'
				)
			);
			break;


		case 'userspecified':
			$posts_tmp = get_posts(
				array(
					'numberposts' => $n,
					'include' => $post_list
				)
			);
			
			//Order posts according to their order in $post_list
			$posts = array();
			$post_list_arr = preg_split('/[\s,]+/', $post_list); //From WP's post.php
			
			//For all post id's in the $post_list
			foreach($post_list_arr as $post_id) {
				//Find the post with the corresponding post id
				foreach($posts_tmp as $post) {
					if($post->ID==$post_id) {
						$posts[] = $post;
						break; //Break out of the inner-most loop
					}
				}
			}
			break;

		
		//TODO: combinations of types

			
		default:
			$posts = null;
			break;
	}
	
	//Convert get_posts()'s returned array of objects to an array of arrays to make the data easier to work with.
	//Also, re-index the posts so they can be accessed by their post id.  Note that given PHP's arrays, this still retains the order of the posts in the new $posts_fixed array - they will be in the same order as we insert them, not in order according to their numeric keys (post id)
	$posts_fixed = array();
	if($posts!=null && sizeof($posts)>0 && is_object($posts[0])) {
		foreach ($posts as $k => $v)
			$posts_fixed[$v->ID] = (array) $v;
	}
	
	featurific_get_posts_categories($posts_fixed);	//Add categories
	featurific_get_posts_tags($posts_fixed);				//Add tags
	featurific_get_posts_meta($posts_fixed);				//Add custom fields
	featurific_get_posts_tweak($posts_fixed);
	
	return $posts_fixed;
}


/**
 * Get the text version of categories for all $posts.  Since this is pass by
 * reference, we modify $posts in place and return nothing.
 */
function featurific_get_posts_categories(&$posts) {
	//For each post, get the categories
	foreach ($posts as $post_id => $post) {
		$cats = wp_get_post_categories($post_id);
		
		//For each category, get the name
		$categories = '';
		$cat_num = 1;
		foreach ($cats as $cat_id) {
			$cat = get_category($cat_id);
			
			//Comma-separated list of categories
			if($categories!='')
				$categories .= ', ';
			$categories .= $cat->name;
			
			//New entry for every category
			$posts[$post_id]["category_$cat_num"] = $cat->name;
			$cat_num++;
		}
		
		$posts[$post_id]['categories'] = $categories;
	}
}


/**
 * Get the text version of tags for all $posts.  Since this is pass by
 * reference, we modify $posts in place and return nothing.
 */
function featurific_get_posts_tags(&$posts) {
	//For each post, get the categories
	foreach ($posts as $post_id => $post) {
		$tags = get_the_tags($post_id);
		
		$tags_str = '';
		if($tags!=null && sizeof($tags)>0) {
			//For each tag, get the name
			$tag_num = 1;
			foreach ($tags as $tag) {
				//Comma-separated list of tags
				if($tags_str!='')
					$tags_str .= ', ';
				$tags_str .= $tag->name;

				//New entry for every tag
				$posts[$post_id]["tag_$tag_num"] = $tag->name;
				$tag_num++;
			}
		}
		
		$posts[$post_id]['tags'] = $tags_str;
	}
}


/**
 * Get the custom fields for all $posts.  Since this is pass by reference, we
 * modify $posts in place and return nothing.
 */
function featurific_get_posts_meta(&$posts) {
	//For each post, get the custom fields
	foreach ($posts as $post_id => $post) {
		$custom_fields = get_post_custom($post_id);
		
		//print_r($custom_fields);
		
		//For each field, get the value
		foreach ($custom_fields as $k => $v) {
			$posts[$post_id][$k] = $v[0];
		}
	}
}


/**
 * Tweak certain values for all $posts.  Since this is pass by reference, we
 * modify $posts in place and return nothing.
 *
 * Due to the fact that this function tweaks values created by other
 * featurific_get_posts_xxx() functions, it should be called last of all of
 * the featurific_get_posts_xxx() functions.
 */
function featurific_get_posts_tweak(&$posts) {
	//NOTE: Since this method is called AFTER featurific_get_posts_meta(), we can't use custom fields to override any tags defined in this method (e.g. post_human_date) unless we've explicitly checked to ensure that the value is empty before we write to it (like we're doing with 'post_excerpt' and 'image_x').  As for the other tags, (e.g. the 'post_xxx_date' fields, writing a method to only perform the assignment if the original value is null would be trivial.
	
	$date_chars = array('d', 'D', 'j', 'l', 'N', 'S', 'w', 'z', 'W', 'F', 'm', 'M', 'n', 't', 'L', 'o', 'Y', 'y', 'a', 'A', 'B', 'g', 'G', 'h', 'H', 'i', 's', 'u', 'e', 'I', 'O', 'P', 'T', 'Z', 'c', 'r', 'U');

	//For each post...
	$screen_number = 1;
	foreach ($posts as $post_id => $post) {
		//Post Date/Time
		$date_str = $post['post_date'];
		$date = featurific_parse_date($date_str);
		$posts[$post_id]['post_human_date'] = featurific_date_to_human_date($date);
		$posts[$post_id]['post_long_human_date'] = featurific_date_to_long_human_date($date);
		$posts[$post_id]['post_slashed_date'] = featurific_date_to_slashed_date($date);
		$posts[$post_id]['post_dotted_date'] = featurific_date_to_dotted_date($date);
		$posts[$post_id]['post_human_time'] = featurific_date_to_human_time($date);
		$posts[$post_id]['post_long_human_time'] = featurific_date_to_long_human_time($date);
		$posts[$post_id]['post_military_time'] = featurific_date_to_military_time($date);
		
		foreach($date_chars as $dc)
			$posts[$post_id]["post_date_$dc"] = date($dc, $date);


		//Modified Date/Time
		$date_str = $post['post_modified'];
		$date = featurific_parse_date($date_str);
		$posts[$post_id]['post_modified_human_date'] = featurific_date_to_human_date($date);
		$posts[$post_id]['post_modified_long_human_date'] = featurific_date_to_long_human_date($date);
		$posts[$post_id]['post_modified_slashed_date'] = featurific_date_to_slashed_date($date);
		$posts[$post_id]['post_modified_dotted_date'] = featurific_date_to_dotted_date($date);
		$posts[$post_id]['post_modified_human_time'] = featurific_date_to_human_time($date);
		$posts[$post_id]['post_modified_long_human_time'] = featurific_date_to_long_human_time($date);
		$posts[$post_id]['post_modified_military_time'] = featurific_date_to_military_time($date);

		foreach($date_chars as $dc)
			$posts[$post_id]["post_modified_date_$dc"] = date($dc, $date);		


		//Find images in the post content's HTML and prepare the images and $posts[$post_id] so the images can be accessed in the template.
		$web_root = get_option('siteurl'); //e.g. 'http://mysite.com/wordpress' (provided wordpress was installed at public_html/wordpress)
		$images = featurific_parse_images_from_html($posts[$post_id]['post_content']);


		//Before we process the images and cache them if necessary, load any image_x custom fields.  These custom fields specify images that should be used *instead of* OR *in addition to* (depending on whether or not the corresponding image (e.g. image_1) was found in the post) the existing images as parsed from the post.
		//We already have the custom fields in $posts[$post_id] since featurific_get_posts_tweak() is called after featurific_get_posts_meta().
		foreach($posts[$post_id] as $k => $v) {
			
			//If this is an image_x custom field...
			if(strpos($k, 'image_')===0) {
				
				//Get the image number and add it to the $images working variable
				$image_number = intval(substr($k, 6)); //6 because strlen('image_') is 6
				$images[$image_number] = $v;
			}
		}
		
		
		$image_number = 1;
		foreach($images as $image) {
			//echo "pos: ".strpos($image, $web_root)."<br/>";
			if($posts[$post_id]['image_'.$image_number]!=null)
				$image = $posts[$post_id]['image_'.$image_number];
			
			//If the image is on the same domain, we can access it from Flash 9 directly.
			if(strpos($image, $web_root)===0)
				$image_url = $image;
			//If the image is on a different domain, we can't access it from Flash 9 directly, so we've got to load it by proxy (save locally via PHP).
			else {
				$image_data = file_get_contents($image);
				
				//On error, just continue to the next image
				if($image_data===false)
					continue;
				
				//Store the cached images in the DB
				if(get_option('featurific_store_cached_images_in_db')) {
					$image_info = getimagesize($image); //Yes, this requires fetching the image via HTTP twice (we fetch it once to get the image (above), and again to get the mime type); but since getimagesize() will only work with a filename/uri, I can't just feed it the raw data.  The best workaround I found for this was writing a temporary file (which we can't because we might not have write access to the FS), and using stream_wrapper_register() (http://fi.php.net/manual/en/function.stream-wrapper-register.php) to 'fake' the file

					$success = featurific_image_cache_put_image($screen_number, $image_number, $image_data, $image_info['mime']);
					//echo $success?'put successful!':'put failed!';
					
					$image_url = featurific_get_plugin_web_root()."cached_image.php?snum=$screen_number&inum=$image_number";
					//echo "Stored featurific_cached_image_screen_{$screen_number}_image_{$image_number}.<br/>";

					// echo "Its value was: $image_data";
					// echo "Try it: " . get_option("featurific_cached_image_screen_{$screen_number}_image_{$image_number}");
				}
				//Store the cached images as files on the local filesystem
				else {
					$relative_path = 'image_cache/screen_'.$screen_number.'_image_'.$image_number;
					$image_path = featurific_get_plugin_root().$relative_path;
					$bytes_written = @file_put_contents($image_path, $image_data); //NOTE: The '@' suppresses warning messages.  We need to be certain to check the return value.
				
					//On error, just continue to the next image
					if($bytes_written===false) {
						echo "Error writing proxied image to file ($image_path)";
						continue;
					}

					$image_url = featurific_get_plugin_web_root().$relative_path;
				}
			}

			//Actually add the image url to the post.  Images can be accessed in template.xml in this manner: %image_1%, %image_2%, etc.
			$posts[$post_id]["image_$image_number"] = $image_url;
			
			$image_number++;
		}

		
		//Fix up the post content for plaintext display.
		$posts[$post_id]['post_content'] =
			featurific_html_to_text(					//Convert the HTML to text
				str_replace("\xC2\xA0", '',			//The wordpress editor seems to put the chars "\xC2\xA0" into our content (perhaps at newlines?).  Remove these extra characters before converting to plain text.
					$post['post_content']
				)
			);
					
		
		//If the post doesn't have a post_excerpt, then create one.
		if($posts[$post_id]['post_excerpt']==null || $posts[$post_id]['post_excerpt']=='') {
			$auto_excerpt_chars = get_option('featurific_auto_excerpt_length');
			$s = $posts[$post_id]['post_content'];
			$s = substr($s, 0, $auto_excerpt_chars);
			$s = substr($s, 0, strrpos($s, ' '));
			
			$posts[$post_id]['post_excerpt'] = $s;
		}
		//If the post does already have an excerpt, convert the HTMl to text.
		else {
			$posts[$post_id]['post_excerpt'] = featurific_html_to_text($posts[$post_id]['post_excerpt']);
		}

		//Etc
		$posts[$post_id]['nickname'] = get_usermeta($post['post_author'], 'nickname');
		$posts[$post_id]['url'] = $post['guid'];
		$posts[$post_id]['screen_duration'] = get_option('featurific_screen_duration');
		$posts[$post_id]['screen_number'] = $screen_number;
		
		$screen_number++;
	}
}


/**
 * file_get_contents for PHP 4
 *
 * (from http://www.phpbuilder.com/board/showthread.php?t=10292234)
 */
// Check to see if functin exists 
if (!function_exists('file_get_contents')) { 

    // Define function and arguments 
    function file_get_contents($file, $include=false) 
    { 
        // Varify arguments are correct types 
        if (!is_string($file))  return(false); 
        if (!is_bool($include)) return(false); 
         
        // Open the file with givin options 
        if (!$handle = @fopen($file, 'rb', $include)) return(false); 
        // Read data from file 
        $contents = fread($handle, filesize($file)); 
        // Close file 
        fclose($handle); 
         
        // Return contents of file 
        return($contents); 
    } 
}


/**
 * file_put_contents for PHP 4
 *
 * (from http://www.phpbuilder.com/board/showthread.php?t=10292234)
 */
if (!function_exists('file_put_contents')) { 
    // Define flags related to file_put_contents(), if necessary 
    if (!defined('FILE_USE_INCLUDE_PATH')) { 
        define('FILE_USE_INCLUDE_PATH', 1); 
    } 
    if (!defined('FILE_APPEND')) { 
        define('FILE_APPEND', 8); 
    } 

    function file_put_contents($filename, $data, $flags = 0) { 
        // Handle single dimensional array data 
        if (is_array($data)) { 
            // Join the array elements 
            $data = implode('', $data); 
        } 

        // Flags should be an integral value 
        $flags = (int)$flags; 
        // Set the mode for fopen(), defaulting to 'wb' 
        $mode = ($flags & FILE_APPEND) ? 'ab' : 'wb'; 
        $use_include_path = (bool)($flags & FILE_USE_INCLUDE_PATH); 

        // Open file with filename as a string 
        if ($fp = fopen("$filename", $mode, $use_include_path)) { 
            // Acquire exclusive lock if requested 
            if ($flags & LOCK_EX) { 
                if (!flock($fp, LOCK_EX)) { 
                    fclose($fp); 
                    return false; 
                } 
            } 

            // Write the data as a string 
            $bytes = fwrite($fp, "$data"); 

            // Release exclusive lock if it was acquired 
            if ($flags & LOCK_EX) { 
                flock($fp, LOCK_UN); 
            } 

            fclose($fp); 
            return $bytes; // number of bytes written 
        } else { 
            return false; 
        } 
    } 
}



?>
