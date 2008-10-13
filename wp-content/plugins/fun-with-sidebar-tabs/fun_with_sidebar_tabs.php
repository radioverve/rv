<?php
/*
Plugin Name: Fun with Sidebar Tabs
Plugin URI: http://www.wp-fun.co.uk/fun-with-sidebar-tabs/
Description: Adds a tabbed sidebar to your theme
Author: Andrew Rickmann
Version: 0.5
Author URI: http://www.wp-fun.co.uk
Generated At: www.wp-fun.co.uk;
*/ 

/*  Copyright 2007  Andrew Rickmann  (email : PLUGIN AUTHOR EMAIL)

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
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( isset( $_GET['deliver'] ) && $_GET['deliver'] == 'css'  ) {

	$root = dirname(__FILE__);
	while( $root && $root!= '.' && !file_exists($root.'/wp-load.php') && !file_exists($root.'/wp-config.php')){
    	$root = dirname($root);
	}
	if ( file_exists($root.'/wp-load.php') ){
		include($root.'/wp-load.php');	
	} else if (file_exists($root.'/wp-config.php')) {
		include($root.'/wp-config.php');
	}
	
	//set the base path	
	$path = ABSPATH . PLUGINDIR . '/fun-with-sidebar-tabs/css/';

	//get the options
	$number_of_sidebars = get_option( 'fw_tabbed_sidebars' );
	
	if ( !$number_of_sidebars ) { exit; }
	
	//get the default styles
	$style_template = file_get_contents( $path . 'style.css');
	
	//get the template overide css
	$override_template = file_get_contents( $path . 'override.css');
	
	//GO GO GO
	
	$override_css = array();
	
	for($i = 1; $i <= $number_of_sidebars['number']; $i++){
		if ( $override_options = get_option('tabbed_sidebar_css_options_'.$i ) ) {
			$new_css = $override_template;
			
			//calculate replacement value, if 1 then also include the class
			$num_val = ( $i == 1 ) ? '1.first' : '1';
			
			$new_css = str_replace('%num%' , $num_val , $new_css );
			$new_css = str_replace('%tabs%' , $override_options['tabs'] , $new_css );
			$new_css = str_replace('%tabs_selected%' , $override_options['tabs_selected'] , $new_css );
			$new_css = str_replace('%tabs_hover%' , $override_options['tabs_hover'] , $new_css );
			$new_css = str_replace('%tabs_position%' , $override_options['tabs_position'] , $new_css );
			$new_css = str_replace('%content_container%' , $override_options['content_container'] , $new_css );
				
			$new_style_css = str_replace('%num%' , $i , $style_template );
			$override_css[] = stripslashes($new_style_css);

			$override_css[] = stripslashes($new_css);
			
			
		}
	}
	
	//output the CSS
	header('Content-type: text/css');
	foreach( $override_css as $css ){
		echo $css;
	}
	exit;
		
} elseif ( isset( $_GET['deliver'] ) && $_GET['deliver'] == 'ie-css'  ){
	
	$root = dirname(__FILE__);
	while( $root && $root!= '.' && !file_exists($root.'/wp-load.php') && !file_exists($root.'/wp-config.php')){
    	$root = dirname($root);
	}
	if ( file_exists($root.'/wp-load.php') ){
		include($root.'/wp-load.php');	
	} else if (file_exists($root.'/wp-config.php')) {
		include($root.'/wp-config.php');
	}
	
	//get the options
	$number_of_sidebars = get_option( 'fw_tabbed_sidebars' );
	
	if ( !$number_of_sidebars ) { exit; }
	
	$override_css = array();
	
	for($i = 1; $i <= $number_of_sidebars['number']; $i++){
		if ( $override_options = get_option('tabbed_sidebar_css_options_'.$i ) ) {
			if ( !empty($override_options['ie_css']) ){
				$override_css[] = stripslashes($override_options['ie_css']);
			}
		}
	}
	
	header('Content-type: text/css');
	include_once($path . 'ie-style.css');
	foreach( $override_css as $css ){
		echo $css;
	}
	exit;
	
}


/**
* sidebar class that will deal with all the pages and options and things
*/

if (!class_exists('fw_tabbed_sidebars')) {

class fw_tabbed_sidebar{

		/**
		* The internal ID, used to differentiate options in WordPress
		*/
		var $id = 0;

		/**
		* PHP 4 Compatible Constructor
		*/
		function fw_tabbed_sidebar( $id ){$this->__construct( $id );}
		
		/**
		* PHP 5 Constructor
		*/		
		function __construct( $id ){
		
		$this->id = $id;
		$this->register_me();
		$this->register_my_widget();
				
		}
		
		/**
		* Registers the sidebar with WordPress
		*/
		function register_me(){
		$details = array(
			'name' => 'Tabbed Sidebar '.$this->id,
			'before_widget' => '<li class="tab">',
			'after_widget' => '</div></li>',
			'before_title' => '<h3 class="fwTabTitle">',
			'after_title' => '</h3><div class="tab-content">',
				);
			
		register_sidebar($details);
			
		}
		
		/**
		* Registers the widget
		*/		
		function register_my_widget(){
			$name = 'Tabbed Sidebar '.$this->id;
			
			register_sidebar_widget($name, array(&$this,"my_widget"));
			register_widget_control($name, array(&$this,"my_widget_control") , 500 , 600 );
		}
		
		/**
		* The widget output
		*/		
		function my_widget( $args ){
		extract($args);
				
		$firstclass = ( $this->id === 1 ) ? ' first' : '';
		$id_class = ' ts'.$this->id;
		echo '<div class="fw_tabs_tabs_surround" id="fw_tabs_tabs_surround_'.$this->id.'"><div id="fw_tabs_tabdisplay_x_'.$this->id.'" class="fw_tabs_tabdisplay'.$firstclass.'"><ul class="tabbed_sidebar">';
		if ( function_exists('dynamic_sidebar') ) { 
			dynamic_sidebar('Tabbed Sidebar '.$this->id );
		} 
		echo '</ul>';
		//an extra div to act as a replaceable element
		echo '<div>&nbsp;</div>';
		echo '</div></div>';
		
		}
		
		/**
		* The settings page for the widgets
		*/
		function my_widget_control(){
			
			//get the saved options
			$css_options = get_option('tabbed_sidebar_css_options_'.$this->id );
			if ( !is_array( $css_options )){ $css_options = array(); }
			
			if ( isset ( $_POST['my_widget_control_' . $this->id ]) ){
				
				//do the processing		
				$css_options['tabs'] = $this->css_filter($_POST['tabbed_sidebar_'.$this->id.'_tabs']);
				$css_options['tabs_selected'] = $this->css_filter($_POST['tabbed_sidebar_'.$this->id.'_tabs_selected']);
				$css_options['tabs_hover'] = $this->css_filter($_POST['tabbed_sidebar_'.$this->id.'_tabs_hover']);
				$css_options['tabs_position'] = $this->css_filter($_POST['tabbed_sidebar_'.$this->id.'_tabs_position']);
				$css_options['content_container'] = $this->css_filter($_POST['tabbed_sidebar_'.$this->id.'_content_container']);
				$css_options['ie_css'] = $this->css_filter($_POST['tabbed_sidebar_'.$this->id.'_ie_css']);
				
				update_option('tabbed_sidebar_css_options_'.$this->id , $css_options );

			}
			
			?>
			<h2>Style This Tabbed Sidebar</h2>
			<p>This allows you to overide the default settings for each sidebar. The default settings are included for you to start with.</p>
			<p>
			<label for="tabbed_sidebar_<?php echo $this->id; ?>_tabs_position">Amend the position of the tabs</label><br />
			<textarea id="tabbed_sidebar_<?php echo $this->id; ?>_tabs_position" name="tabbed_sidebar_<?php echo $this->id; ?>_tabs_position" style="width:400px;"><?php 
				
				if ( isset ( $css_options['tabs_position'] ) ){
					echo stripslashes($css_options['tabs_position']);
				} else {
					//echo a default
					?>margin-top:-32px;<?php					
				}
				
				 ?></textarea>
			</p>
			<p>
			<label for="tabbed_sidebar_<?php echo $this->id; ?>_tabs">Overide the Tabs CSS</label><br />
			<textarea id="tabbed_sidebar_<?php echo $this->id; ?>_tabs" name="tabbed_sidebar_<?php echo $this->id; ?>_tabs" style="width:400px;"><?php 
				
				if ( isset ( $css_options['tabs'] ) ){
					echo stripslashes($css_options['tabs']);
				} else {
					//echo a default
					?>font-size:10px;
line-height:1;
font-family:Arial, Helvetica, sans-serif;
color:#000;
padding:5px;
border:1px solid #ccc;
background-color:#eee;<?php					
				}
				
				 ?></textarea>
			</p>
						<p>
			<label for="tabbed_sidebar_<?php echo $this->id; ?>_tabs_selected">The Selected Tab</label><br />
			<textarea id="tabbed_sidebar_<?php echo $this->id; ?>_tabs_selected" name="tabbed_sidebar_<?php echo $this->id; ?>_tabs_selected" style="width:400px;"><?php 
				
				if ( isset ( $css_options['tabs_selected'] ) ){
					echo stripslashes($css_options['tabs_selected']);
				} else {
					//echo a default
					?>border-bottom:1px solid #fff;
background-color:#fff;<?php					
				}
				
				 ?></textarea>
			</p>
						<p>
			<label for="tabbed_sidebar_<?php echo $this->id; ?>_tabs_hover">Tabs Hovered Over</label><br />
			<textarea id="tabbed_sidebar_<?php echo $this->id; ?>_tabs_hover" name="tabbed_sidebar_<?php echo $this->id; ?>_tabs_hover" style="width:400px;"><?php 
				
				if ( isset ( $css_options['tabs_hover'] ) ){
					echo stripslashes($css_options['tabs_hover']);
				} else {
					//echo a default
					?>background-color:#dfe4ec;<?php					
				}
				
				 ?></textarea>
			</p>
			<p>
			<label for="tabbed_sidebar_<?php echo $this->id; ?>_content_container">Tab Content Container</label><br />
			<textarea id="tabbed_sidebar_<?php echo $this->id; ?>_content_container" name="tabbed_sidebar_<?php echo $this->id; ?>_content_container" style="width:400px;"><?php 
				
				if ( isset ( $css_options['content_container'] ) ){
					echo stripslashes($css_options['content_container']);
				} else {
					//echo a default
					?>border:1px solid #ccc;
padding:10px 0px;
/* to add more space above each sidebar increase this (the minimum it can be is 32px) */
margin-top:32px;<?php					
				}
				
				 ?></textarea>
			</p>
		
			<input type="hidden" name="my_widget_control_<?php echo $this->id; ?>" value="set" />
			<?php
			
		}
		
		function css_filter($css){
			
			//remove backticks
			$filtered_css = str_replace( '`' , '' , $css );
			//remove script tags, and php tags
			$filtered_css = str_replace( '<?php' , '' , $filtered_css );
			$filtered_css = str_replace( '?>' , '' , $filtered_css );
			$filtered_css = str_replace( '<script' , '' , $filtered_css );
			$filtered_css = str_replace( '</script>' , '' , $filtered_css );
			
			return $filtered_css;
		}
}


    class fw_tabbed_sidebars	{
	
	var $collection = array();

		
		/**
		* PHP 4 Compatible Constructor
		*/
		function fw_tabbed_sidebars(){$this->__construct();}
		
		/**
		* PHP 5 Constructor
		*/		
		function __construct(){
		register_activation_hook( __FILE__ , array(&$this,"on_activation") );
		add_action("init", array(&$this,"add_scripts") , 1);
		add_action("init", array(&$this,"register_tabbed_sidebars"));
		//this next line needs priority to work on FF3 to ensure the css is loaded when the script fires. 
		add_action("wp_head", array(&$this,"add_css") , 1);
		add_action('sidebar_admin_page', array(&$this,"fw_tabbed_sidebars_page"));
		add_Action('sidebar_admin_setup', array(&$this,'fw_tabbed_sidebars_setup'));
		}
		
		/**
		* Makes sure the appropriate options are set
		*/
		function on_activation(){
		$options['number'] = 1;
		update_option( 'fw_tabbed_sidebars', $options );
		}
		
			/**
		* Registers the sidebars
		*/
		function register_tabbed_sidebars( $ignorepost = false ){
		$options = get_option( 'fw_tabbed_sidebars' );
		//if post is set then don't do it, it will be called later on by fw_tabbed_sidebars_setup
		if ( !isset( $_POST['wp_sidebars-number-submit'] ) || $ignorepost == true ) {
			for ( $i = 0; $i < $options['number']; $i++ ) {
				$this->collection[] = new fw_tabbed_sidebar( $i+1 );
			}
		}
		}
		
		/**
		* Allows the number of sidebars to be determined
		*/
		function fw_tabbed_sidebars_page() {
		$options = get_option( 'fw_tabbed_sidebars' );
		?>		
		<div class="wrap">
		<form method="post">
		<h2><?php _e( 'Tabbed Sidebars' ); ?></h2>
		<p style="line-height: 30px;"><?php _e( 'How many sidebars would you like?' ); ?>
		<select id="wp-sidebars-number" name="wp-sidebars-number" value="<?php echo attribute_escape( $options['number'] ); ?>">
		<?php
		for ( $i = 1; $i < 10; $i++ ) {
			echo '<option value="' . $i . '"' . ( $i == $options['number'] ? ' selected="selected"' : '' ) . '>' . $i . "</option>\n";
		}
		?>
		</select>
		<span class="submit">
		<input type="submit" value="<?php echo attribute_escape( __( 'Save' ) ); ?>" id="wp_sidebars-number-submit" name="wp_sidebars-number-submit" />
		</span>
		</p>
		</form>
		</div>
		<?php
		}
		
		
		function fw_tabbed_sidebars_setup() {
		$options = $newoptions = get_option( 'fw_tabbed_sidebars' );
	
		if ( isset( $_POST['wp_sidebars-number-submit'] ) ) {
			$number = (int) $_POST['wp-sidebars-number'];
	
			if ( $number > 9 ) {
				$number = 9;
			} elseif ( $number < 1 ) {
				$number = 1;
			}
	
			$newoptions['number'] = $number;
		}
	
		if ( $newoptions != $options ) {
			$options = $newoptions;
			update_option( 'fw_tabbed_sidebars', $options );
		}
	
		if ( isset( $_POST['wp_sidebars-number-submit'] ) ) {
			//if the post isset then we must call and register the sidebars
			$this->register_tabbed_sidebars(true);
		}
		}

		/**
		* Tells WordPress to load the scripts
		*/
		function add_scripts(){
			//none of these are needed if we are in admin
			if ( is_admin() ){ return; }
			global $wp_version;
		
		//although the minimujm required version of WordPress is 2.5 I have left the alternative
		//script in because people don't read instructions.
		$versions = array('2.3', '2.3.1', '2.3.2', '2.3.3');
		$script_name = ( in_array( $wp_version, $versions  ) ) ? 'script-114.js' : 'script.js';
		
		//the Jquery version
		wp_enqueue_script('fun_with_sidebar_tabs_script', '/wp-content/plugins/fun-with-sidebar-tabs/js/'.$script_name, array("jquery") , 0.1); 
		}
		
		
		/**
		* Adds a link to the stylesheet to the header
		*/
		function add_css(){
		?>
<link rel="stylesheet" href="<?php bloginfo('url') ?>/wp-content/plugins/fun-with-sidebar-tabs/fun_with_sidebar_tabs.php?deliver=css" type="text/css" media="screen"  />
<!--[if IE 6]>
<link rel="stylesheet" href="<?php bloginfo('url') ?>/wp-content/plugins/fun-with-sidebar-tabs/css/ie-style.css" type="text/css" media="screen"  />
<![endif]--> 
		<?php
		}
		

    }


//instantiate the class
if (class_exists('fw_tabbed_sidebars')) {
	$fw_tabbed_sidebars = new fw_tabbed_sidebars();
	
	/**
	* Outputs the sidebar
	*/
	function the_tabbed_sidebar( $num = 1) {

	if ( function_exists('dynamic_sidebar') ) { 
		$id_class = ' ts'.$num;
		$firstclass = ( $num === 1 ) ? ' first' : '';
		echo '<div class="fw_tabs_tabs_surround"><div class="fw_tabs_tabdisplay_x'.$firstclass.$id_class.'"><ul class="tabbed_sidebar">';
		dynamic_sidebar('Tabbed Sidebar '.$num);
	 } 
	echo '</ul>';
	//an extra div to act as a replaceable element
	echo '<div></div>';
	echo '</div></div>';
		
	}
	
}

} 




?>