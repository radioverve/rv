<?php
if ( function_exists('register_sidebar') )
    register_sidebar(array(
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h2 class="widgettitle">',
        'after_title' => '</h2>',
    ));
    
/*BACKUP 
if( function_exists('register_sidebar'))
	register_sidebar(array(	
            'name' => 'Articles Side Bar',
            'before_widget' => '<li>',
            'after_widget' => '</li>',
            'before_title' => '<h2>',
            'after_title' => '</h2>',
        ));		
*/
if( function_exists('register_sidebar'))
	register_sidebar(array(	
            'name' => 'Articles Side Bar',
            'before_widget' => '<div id="frag-%s">',
            'after_widget' => '</div>',
            'before_title' => '<span style="display:none;">',
            'after_title' => '</span>',
        ));		

		
if( function_exists('register_sidebar'))
	register_sidebar(array(	
            'name' => 'Archives Search',
            'before_widget' => '<div id="frag-%s">',
            'after_widget' => '</div>',
            'before_title' => '<span style="display:none;">',
            'after_title' => '</span>',
        ));	

if( function_exists('register_sidebar'))
	register_sidebar(array(	
            'name' => 'ArchivesOnly',
            'before_widget' => '<div id="frag-%s">',
            'after_widget' => '</div>',
            'before_title' => '<span style="display:none;">',
            'after_title' => '</span>',
        ));	

if( function_exists('register_sidebar'))
	register_sidebar(array(	
            'name' => 'DisqusComments',
            'before_widget' => '<div id="frag-%s">',
            'after_widget' => '</div>',
            'before_title' => '<span style="display:none;">',
            'after_title' => '</span>',
        ));	



function kubrick_head() {
	$head = "<style type='text/css'>\n<!--";
	$output = '';
	if ( kubrick_header_image() ) {
		$url =  kubrick_header_image_url() ;
		$output .= "#header { background: url('$url') no-repeat bottom center; }\n";
	}
	if ( false !== ( $color = kubrick_header_color() ) ) {
		$output .= "#headerimg h1 a, #headerimg h1 a:visited, #headerimg .description { color: $color; }\n";
	}
	if ( false !== ( $display = kubrick_header_display() ) ) {
		$output .= "#headerimg { display: $display }\n";
	}
	$foot = "--></style>\n";
	if ( '' != $output )
		echo $head . $output . $foot;
}

add_action('wp_head', 'kubrick_head');

function kubrick_header_image() {
	return apply_filters('kubrick_header_image', get_option('kubrick_header_image'));
}

function kubrick_upper_color() {
	if (strpos($url = kubrick_header_image_url(), 'header-img.php?') !== false) {
		parse_str(substr($url, strpos($url, '?') + 1), $q);
		return $q['upper'];
	} else
		return '69aee7';
}

function kubrick_lower_color() {
	if (strpos($url = kubrick_header_image_url(), 'header-img.php?') !== false) {
		parse_str(substr($url, strpos($url, '?') + 1), $q);
		return $q['lower'];
	} else
		return '4180b6';
}

function kubrick_header_image_url() {
	if ( $image = kubrick_header_image() )
		$url = get_template_directory_uri() . '/images/' . $image;
	else
		$url = get_template_directory_uri() . '/images/kubrickheader.jpg';

	return $url;
}

function kubrick_header_color() {
	return apply_filters('kubrick_header_color', get_option('kubrick_header_color'));
}

function kubrick_header_color_string() {
	$color = kubrick_header_color();
	if ( false === $color )
		return 'white';

	return $color;
}

function kubrick_header_display() {
	return apply_filters('kubrick_header_display', get_option('kubrick_header_display'));
}

function kubrick_header_display_string() {
	$display = kubrick_header_display();
	return $display ? $display : 'inline';
}

add_action('admin_menu', 'kubrick_add_theme_page');

function kubrick_add_theme_page() {
	if ( isset( $_GET['page'] ) && $_GET['page'] == basename(__FILE__) ) {
		if ( isset( $_REQUEST['action'] ) && 'save' == $_REQUEST['action'] ) {
			check_admin_referer('kubrick-header');
			if ( isset($_REQUEST['njform']) ) {
				if ( isset($_REQUEST['defaults']) ) {
					delete_option('kubrick_header_image');
					delete_option('kubrick_header_color');
					delete_option('kubrick_header_display');
				} else {
					if ( '' == $_REQUEST['njfontcolor'] )
						delete_option('kubrick_header_color');
					else {
						$fontcolor = preg_replace('/^.*(#[0-9a-fA-F]{6})?.*$/', '$1', $_REQUEST['njfontcolor']);
						update_option('kubrick_header_color', $fontcolor);
					}
					if ( preg_match('/[0-9A-F]{6}|[0-9A-F]{3}/i', $_REQUEST['njuppercolor'], $uc) && preg_match('/[0-9A-F]{6}|[0-9A-F]{3}/i', $_REQUEST['njlowercolor'], $lc) ) {
						$uc = ( strlen($uc[0]) == 3 ) ? $uc[0]{0}.$uc[0]{0}.$uc[0]{1}.$uc[0]{1}.$uc[0]{2}.$uc[0]{2} : $uc[0];
						$lc = ( strlen($lc[0]) == 3 ) ? $lc[0]{0}.$lc[0]{0}.$lc[0]{1}.$lc[0]{1}.$lc[0]{2}.$lc[0]{2} : $lc[0];
						update_option('kubrick_header_image', "header-img.php?upper=$uc&lower=$lc");
					}

					if ( isset($_REQUEST['toggledisplay']) ) {
						if ( false === get_option('kubrick_header_display') )
							update_option('kubrick_header_display', 'none');
						else
							delete_option('kubrick_header_display');
					}
				}
			} else {

				if ( isset($_REQUEST['headerimage']) ) {
					check_admin_referer('kubrick-header');
					if ( '' == $_REQUEST['headerimage'] )
						delete_option('kubrick_header_image');
					else {
						$headerimage = preg_replace('/^.*?(header-img.php\?upper=[0-9a-fA-F]{6}&lower=[0-9a-fA-F]{6})?.*$/', '$1', $_REQUEST['headerimage']);
						update_option('kubrick_header_image', $headerimage);
					}
				}

				if ( isset($_REQUEST['fontcolor']) ) {
					check_admin_referer('kubrick-header');
					if ( '' == $_REQUEST['fontcolor'] )
						delete_option('kubrick_header_color');
					else {
						$fontcolor = preg_replace('/^.*?(#[0-9a-fA-F]{6})?.*$/', '$1', $_REQUEST['fontcolor']);
						update_option('kubrick_header_color', $fontcolor);
					}
				}

				if ( isset($_REQUEST['fontdisplay']) ) {
					check_admin_referer('kubrick-header');
					if ( '' == $_REQUEST['fontdisplay'] || 'inline' == $_REQUEST['fontdisplay'] )
						delete_option('kubrick_header_display');
					else
						update_option('kubrick_header_display', 'none');
				}
			}
			//print_r($_REQUEST);
			wp_redirect("themes.php?page=functions.php&saved=true");
			die;
		}
		add_action('admin_head', 'kubrick_theme_page_head');
	}
	add_theme_page(__('Customize Header'), __('Header Image and Color'), 'edit_themes', basename(__FILE__), 'kubrick_theme_page');
}

function kubrick_theme_page_head() {
?>
<script type="text/javascript" src="../wp-includes/js/colorpicker.js"></script>
<script type='text/javascript'>
// <![CDATA[
	function pickColor(color) {
		ColorPicker_targetInput.value = color;
		kUpdate(ColorPicker_targetInput.id);
	}
	function PopupWindow_populate(contents) {
		contents += '<br /><p style="text-align:center;margin-top:0px;"><input type="button" class="button-secondary" value="<?php echo attribute_escape(__('Close Color Picker')); ?>" onclick="cp.hidePopup(\'prettyplease\')"></input></p>';
		this.contents = contents;
		this.populated = false;
	}
	function PopupWindow_hidePopup(magicword) {
		if ( magicword != 'prettyplease' )
			return false;
		if (this.divName != null) {
			if (this.use_gebi) {
				document.getElementById(this.divName).style.visibility = "hidden";
			}
			else if (this.use_css) {
				document.all[this.divName].style.visibility = "hidden";
			}
			else if (this.use_layers) {
				document.layers[this.divName].visibility = "hidden";
			}
		}
		else {
			if (this.popupWindow && !this.popupWindow.closed) {
				this.popupWindow.close();
				this.popupWindow = null;
			}
		}
		return false;
	}
	function colorSelect(t,p) {
		if ( cp.p == p && document.getElementById(cp.divName).style.visibility != "hidden" )
			cp.hidePopup('prettyplease');
		else {
			cp.p = p;
			cp.select(t,p);
		}
	}
	function PopupWindow_setSize(width,height) {
		this.width = 162;
		this.height = 210;
	}

	var cp = new ColorPicker();
	function advUpdate(val, obj) {
		document.getElementById(obj).value = val;
		kUpdate(obj);
	}
	function kUpdate(oid) {
		if ( 'uppercolor' == oid || 'lowercolor' == oid ) {
			uc = document.getElementById('uppercolor').value.replace('#', '');
			lc = document.getElementById('lowercolor').value.replace('#', '');
			hi = document.getElementById('headerimage');
			hi.value = 'header-img.php?upper='+uc+'&lower='+lc;
			document.getElementById('header').style.background = 'url("<?php echo get_template_directory_uri(); ?>/images/'+hi.value+'") center no-repeat';
			document.getElementById('advuppercolor').value = '#'+uc;
			document.getElementById('advlowercolor').value = '#'+lc;
		}
		if ( 'fontcolor' == oid ) {
			document.getElementById('header').style.color = document.getElementById('fontcolor').value;
			document.getElementById('advfontcolor').value = document.getElementById('fontcolor').value;
		}
		if ( 'fontdisplay' == oid ) {
			document.getElementById('headerimg').style.display = document.getElementById('fontdisplay').value;
		}
	}
	function toggleDisplay() {
		td = document.getElementById('fontdisplay');
		td.value = ( td.value == 'none' ) ? 'inline' : 'none';
		kUpdate('fontdisplay');
	}
	function toggleAdvanced() {
		a = document.getElementById('jsAdvanced');
		if ( a.style.display == 'none' )
			a.style.display = 'block';
		else
			a.style.display = 'none';
	}
	function kDefaults() {
		document.getElementById('headerimage').value = '';
		document.getElementById('advuppercolor').value = document.getElementById('uppercolor').value = '#69aee7';
		document.getElementById('advlowercolor').value = document.getElementById('lowercolor').value = '#4180b6';
		document.getElementById('header').style.background = 'url("<?php echo get_template_directory_uri(); ?>/images/kubrickheader.jpg") center no-repeat';
		document.getElementById('header').style.color = '#FFFFFF';
		document.getElementById('advfontcolor').value = document.getElementById('fontcolor').value = '';
		document.getElementById('fontdisplay').value = 'inline';
		document.getElementById('headerimg').style.display = document.getElementById('fontdisplay').value;
	}
	function kRevert() {
		document.getElementById('headerimage').value = '<?php echo js_escape(kubrick_header_image()); ?>';
		document.getElementById('advuppercolor').value = document.getElementById('uppercolor').value = '#<?php echo js_escape(kubrick_upper_color()); ?>';
		document.getElementById('advlowercolor').value = document.getElementById('lowercolor').value = '#<?php echo js_escape(kubrick_lower_color()); ?>';
		document.getElementById('header').style.background = 'url("<?php echo js_escape(kubrick_header_image_url()); ?>") center no-repeat';
		document.getElementById('header').style.color = '';
		document.getElementById('advfontcolor').value = document.getElementById('fontcolor').value = '<?php echo js_escape(kubrick_header_color_string()); ?>';
		document.getElementById('fontdisplay').value = '<?php echo js_escape(kubrick_header_display_string()); ?>';
		document.getElementById('headerimg').style.display = document.getElementById('fontdisplay').value;
	}
	function kInit() {
		document.getElementById('jsForm').style.display = 'block';
		document.getElementById('nonJsForm').style.display = 'none';
	}
	addLoadEvent(kInit);
// ]]>
</script>
<style type='text/css'>
	#headwrap {
		text-align: center;
	}
	#kubrick-header {
		font-size: 80%;
	}
	#kubrick-header .hibrowser {
		width: 780px;
		height: 260px;
		overflow: scroll;
	}
	#kubrick-header #hitarget {
		display: none;
	}
	#kubrick-header #header h1 {
		font-family: 'Trebuchet MS', 'Lucida Grande', Verdana, Arial, Sans-Serif;
		font-weight: bold;
		font-size: 4em;
		text-align: center;
		padding-top: 70px;
		margin: 0;
	}

	#kubrick-header #header .description {
		font-family: 'Lucida Grande', Verdana, Arial, Sans-Serif;
		font-size: 1.2em;
		text-align: center;
	}
	#kubrick-header #header {
		text-decoration: none;
		color: <?php echo kubrick_header_color_string(); ?>;
		padding: 0;
		margin: 0;
		height: 200px;
		text-align: center;
		background: url('<?php echo kubrick_header_image_url(); ?>') center no-repeat;
	}
	#kubrick-header #headerimg {
		margin: 0;
		height: 200px;
		width: 100%;
		display: <?php echo kubrick_header_display_string(); ?>;
	}
	#jsForm {
		display: none;
		text-align: center;
	}
	#jsForm input.submit, #jsForm input.button, #jsAdvanced input.button {
		padding: 0px;
		margin: 0px;
	}
	#advanced {
		text-align: center;
		width: 620px;
	}
	html>body #advanced {
		text-align: center;
		position: relative;
		left: 50%;
		margin-left: -380px;
	}
	#jsAdvanced {
		text-align: right;
	}
	#nonJsForm {
		position: relative;
		text-align: left;
		margin-left: -370px;
		left: 50%;
	}
	#nonJsForm label {
		padding-top: 6px;
		padding-right: 5px;
		float: left;
		width: 100px;
		text-align: right;
	}
	.defbutton {
		font-weight: bold;
	}
	.zerosize {
		width: 0px;
		height: 0px;
		overflow: hidden;
	}
	#colorPickerDiv a, #colorPickerDiv a:hover {
		padding: 1px;
		text-decoration: none;
		border-bottom: 0px;
	}
</style>
<?php
}

function kubrick_theme_page() {
	if ( isset( $_REQUEST['saved'] ) ) echo '<div id="message" class="updated fade"><p><strong>'.__('Options saved.').'</strong></p></div>';
?>
<div class='wrap'>
	<div id="kubrick-header">
	<h2><?php _e('Header Image and Color'); ?></h2>
		<div id="headwrap">
			<div id="header">
				<div id="headerimg">
					<h1><?php bloginfo('name'); ?></h1>
					<div class="description"><?php bloginfo('description'); ?></div>
				</div>
			</div>
		</div>
		<br />
		<div id="nonJsForm">
			<form method="post" action="">
				<?php wp_nonce_field('kubrick-header'); ?>
				<div class="zerosize"><input type="submit" name="defaultsubmit" value="<?php echo attribute_escape(__('Save')); ?>" /></div>
					<label for="njfontcolor"><?php _e('Font Color:'); ?></label><input type="text" name="njfontcolor" id="njfontcolor" value="<?php echo attribute_escape(kubrick_header_color()); ?>" /> <?php printf(__('Any CSS color (%s or %s or %s)'), '<code>red</code>', '<code>#FF0000</code>', '<code>rgb(255, 0, 0)</code>'); ?><br />
					<label for="njuppercolor"><?php _e('Upper Color:'); ?></label><input type="text" name="njuppercolor" id="njuppercolor" value="#<?php echo attribute_escape(kubrick_upper_color()); ?>" /> <?php printf(__('HEX only (%s or %s)'), '<code>#FF0000</code>', '<code>#F00</code>'); ?><br />
				<label for="njlowercolor"><?php _e('Lower Color:'); ?></label><input type="text" name="njlowercolor" id="njlowercolor" value="#<?php echo attribute_escape(kubrick_lower_color()); ?>" /> <?php printf(__('HEX only (%s or %s)'), '<code>#FF0000</code>', '<code>#F00</code>'); ?><br />
				<input type="hidden" name="hi" id="hi" value="<?php echo attribute_escape(kubrick_header_image()); ?>" />
				<input type="submit" name="toggledisplay" id="toggledisplay" value="<?php echo attribute_escape(__('Toggle Text')); ?>" />
				<input type="submit" name="defaults" value="<?php echo attribute_escape(__('Use Defaults')); ?>" />
				<input type="submit" class="defbutton" name="submitform" value="&nbsp;&nbsp;<?php _e('Save'); ?>&nbsp;&nbsp;" />
				<input type="hidden" name="action" value="save" />
				<input type="hidden" name="njform" value="true" />
			</form>
		</div>
		<div id="jsForm">
			<form style="display:inline;" method="post" name="hicolor" id="hicolor" action="<?php echo attribute_escape($_SERVER['REQUEST_URI']); ?>">
				<?php wp_nonce_field('kubrick-header'); ?>
	<input type="button"  class="button-secondary" onclick="tgt=document.getElementById('fontcolor');colorSelect(tgt,'pick1');return false;" name="pick1" id="pick1" value="<?php echo attribute_escape(__('Font Color')); ?>"></input>
		<input type="button" class="button-secondary" onclick="tgt=document.getElementById('uppercolor');colorSelect(tgt,'pick2');return false;" name="pick2" id="pick2" value="<?php echo attribute_escape(__('Upper Color')); ?>"></input>
		<input type="button" class="button-secondary" onclick="tgt=document.getElementById('lowercolor');colorSelect(tgt,'pick3');return false;" name="pick3" id="pick3" value="<?php echo attribute_escape(__('Lower Color')); ?>"></input>
				<input type="button" class="button-secondary" name="revert" value="<?php echo attribute_escape(__('Revert')); ?>" onclick="kRevert()" />
				<input type="button" class="button-secondary" value="<?php echo attribute_escape(__('Advanced')); ?>" onclick="toggleAdvanced()" />
				<input type="hidden" name="action" value="save" />
				<input type="hidden" name="fontdisplay" id="fontdisplay" value="<?php echo attribute_escape(kubrick_header_display()); ?>" />
				<input type="hidden" name="fontcolor" id="fontcolor" value="<?php echo attribute_escape(kubrick_header_color()); ?>" />
				<input type="hidden" name="uppercolor" id="uppercolor" value="<?php echo attribute_escape(kubrick_upper_color()); ?>" />
				<input type="hidden" name="lowercolor" id="lowercolor" value="<?php echo attribute_escape(kubrick_lower_color()); ?>" />
				<input type="hidden" name="headerimage" id="headerimage" value="<?php echo attribute_escape(kubrick_header_image()); ?>" />
				<p class="submit"><input type="submit" name="submitform" class="defbutton" value="<?php echo attribute_escape(__('Update Header')); ?>" onclick="cp.hidePopup('prettyplease')" /></p>
			</form>
			<div id="colorPickerDiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;visibility:hidden;"> </div>
			<div id="advanced">
				<form id="jsAdvanced" style="display:none;" action="">
					<?php wp_nonce_field('kubrick-header'); ?>
					<label for="advfontcolor"><?php _e('Font Color (CSS):'); ?> </label><input type="text" id="advfontcolor" onchange="advUpdate(this.value, 'fontcolor')" value="<?php echo attribute_escape(kubrick_header_color()); ?>" /><br />
					<label for="advuppercolor"><?php _e('Upper Color (HEX):');?> </label><input type="text" id="advuppercolor" onchange="advUpdate(this.value, 'uppercolor')" value="#<?php echo attribute_escape(kubrick_upper_color()); ?>" /><br />
					<label for="advlowercolor"><?php _e('Lower Color (HEX):'); ?> </label><input type="text" id="advlowercolor" onchange="advUpdate(this.value, 'lowercolor')" value="#<?php echo attribute_escape(kubrick_lower_color()); ?>" /><br />
					<input type="button" class="button-secondary" name="default" value="<?php echo attribute_escape(__('Select Default Colors')); ?>" onclick="kDefaults()" /><br />
					<input type="button" class="button-secondary" onclick="toggleDisplay();return false;" name="pick" id="pick" value="<?php echo attribute_escape(__('Toggle Text Display')); ?>"></input><br />
				</form>
			</div>
		</div>
	</div>
</div>
<?php }

function get_modal_login($redirecturl){
        echo("<script type=\"text/javascript\">\n");
        
        echo("jQuery(function() {\n");
        
        echo("tb_show(\"Login\",\"http://towel.radioverve.com/login?height=200&width=300&redirect=$redirecturl\",false);\n");
        echo("});</script>");
}

function show_first_artist_reg_page($userid,$firsttime) {
?>
                <?php if($firsttime){ ?>
                        <h3>Hey! You are an artist or label? Welcome, We love creative people!</h3>
                <?php }else{ ?>
                        <h3>Add New Artist</h3>
                <?php } ?>
                        <p>Before you upload your music, we need you to update some private details.</p>
                <p>This is just incase we need to contact you</p>
                <div id="artistreg">
                        <form class="register step2" method="post" action="<? echo(get_option("siteurl")); ?>/upload-music">
                                <h3>Contact Info</h3>
                                <input type="hidden" name="type" value="artist">
                                <input type="hidden" name="userid" value="<? echo $userid; ?>">
                                <ol id="ContactDetails">
                                        <li>
                                                <label for="artistname">Artist Name</label>
                                                <input id="artistname" class="text" type="text" name="artistname">                                        
                                        </li>
                                        <li>
                                                <label for="firstname">First Name</label>
                                                <input id="firstname" class="text" type="text" name="firstname">                                        
                                        </li>
                                        <li>
                                                <label for="lastname">Last Name</label>
                                                <input id="lastname" class="text" type="text" name="lastname">                                          
                                        </li>
                                        <li>
                                                <label for="email">Email</label>
                                                <input id="email" class="text" type="text" name="email">                                        
                                        </li>
                                        <li>
                                                <label for="phonenumber">Phone Number</label>
                                                <input id="phonenumber" class="text" type="text" name="phonenumber">                                        
                                        </li>
                                        <li>
                                                <label for="address">Address</label>
                                                <textarea id="address" rows="5" cols="30" name="address"></textarea>
                                        </li>
                                </ol>
                                <h3> Terms and Conditions</h3>
                                <div class="scrollable">
                                        <p>I / We, hereby grant RadioVeRVe, a sole proprietary concern represented by Mr. Gaurav Vaz ("Licensee") a worldwide, non exclusive, royalty free license to communicate and broadcast our sound recordings via RadioVeRVe's Internet radio station on the following terms and conditions.</p>
                                        <ol>
                                                <li>We warrant that we are the authors and owners of the sound recordings, and that we have the full authority to enter into this license</li>
                                                <li>The license shall be for a period for three years, and may be renewed on mu- tually agreed terms</li>
                                                <li>This license is non-exclusive in nature and the Licensor shall be free to license its music in a manner to any other party in a manner that does not conflict with the terms of the present agreement</li>
                                                <li>Radio Verve may communicate the work adapting it to any technological for- mat reasonably necessary.</li>
                                                <li>The licensor represents that he/it understands that regulation of content on RadioVeRVe's online radio station shall purely be Radio Verve's prerogative and that RadioVeRVe is not in any manner bound to play the licensor's music.</li>
                                                <li>The licensor further understands that if the music provided by it/he to RadioVeRVe as the subject matter of this agreement, is not original, then RadioVeRVe is at liberty and enjoys the prerogative to conclude/terminate the agreement forthwith irrespective and independent of the manner provided for by this agreement for termination of this agreement, with no liability whatsoever accruing to it due to this event.</li>
                                                <li>RadioVeRVe shall clearly indicate the source of the music whensoever it plays the sound recording and the licensor's name shall be indicated as the source of the music.</li>
                                                <li>RadioVeRVe represents that every song displayed on its website shall have a link advertising the Licensor. This advertisement shall be in the nature of a write- up on the Licensor IN WITNESS WHEREOF, the Parties have put their hand and have caused this Agreement to be executed under seal as of the day month and year first above written.</li>
                                                <li>The parties to this agreement shall be entitled to exercise the option of terminating this agreement with a written notice of three months in a manner as provided by the terms of this agreement.</li>
                                                <li> The licensor hereby agrees to indemnify and shall at all times keep indemnified against and hold harmless from all losses, proceedings, claims, actions, suits, notices, judicial pronouncements, orders or decrees made against RadioVeRVe, at any time, by any person, in relation to and/or arising out of a breach by the licensor of their warranties, undertakings, representations and obligations as provided for by the terms of this license. </li>
                                        </ol>
                                        
                                </div>
                                <div class="agreeTerms">
                                        <label for="agreeTerms">
                                                <input id="agreeTerms" class="checkbox" type="checkbox" value="1" name="agreeTerms" onclick="clicked_checkbox()" />
                                                By ticking this box you confirm that you have read, understood and accept these Terms and Conditions and the Privacy Policy.
                                        </label>
                                </div>
                                <input id="regsubbutton" class="submit" type="submit" value="Lets go!" disabled="true"/>
                        </form>
                </div>
<?
}

function the_content_limit($max_char, $more_link_text = '(more...)', $stripteaser = 0, $more_file = '') {
    $content = get_the_content($more_link_text, $stripteaser, $more_file);
    $content = apply_filters('the_content', $content);
    $content = str_replace(']]>', ']]&gt;', $content);
    $content = strip_tags($content);

   if (strlen($_GET['p']) > 0) {
      echo "<p>";
      echo $content;
      echo "&nbsp;<a href='";
      the_permalink();
      echo "'>"."Read More &rarr;</a>";
      echo "</p>";
   }
   else if ((strlen($content)>$max_char) && ($espacio = strpos($content, " ", $max_char ))) {
        $content = substr($content, 0, $espacio);
        $content = $content;
        echo "<p>";
        echo $content;
        echo "...";
        echo "&nbsp;<a href='";
        the_permalink();
        echo "'>".$more_link_text."</a>";
        echo "</p>";
   }
   else {
      echo "<p>";
      echo $content;
      echo "&nbsp;<a href='";
      the_permalink();
      echo "'>"."Read More &rarr;</a>";
      echo "</p>";
   }
}

function add_artist_music_form() {
        //This function generates the HTML requird for ading artist music
?>
<div id="artistreg">
        <form enctype='multipart/form-data' action='' method='POST'>
        <h3>Add: Music Details</h3>
        <!--<input type="hidden" name="type" value="artist">-->
        <!--<input type="hidden" name="userid" value="<? echo $userid; ?>">-->
        <ol id="add_music">
                <?php
                        for($i=1; $i<=5; $i++) {
                                echo '<li>';
                                echo '<label style="padding: 0 8px 0 0" for="music_file' . $i . '">File ' .  $i . '</label>';
                                echo '<input id="filenumber' . $i .'" name="music_file[]" type="file" class="text"/>';
                                //echo 'Tags: <input name="tags_' . $i .'" type="text" class="text"/>';
                                echo '</li>';
                        }
                ?>
                <input class="text" type="hidden" name="counter" value="<?php echo ($i-1); ?>">                                        
        </ol>
        <input type="submit" class="submit" value="Upload" />
        </form>
</div>
<?php
        
}

function rv_show_edit_artist_page($artist_id, $artist_name) {
?>
                        <h3>Edit Artist: <?php echo $artist_name; ?></h3>
                <?php
                       global $wpdb, $gnrb_table_artist;
                       //INSERT INTO wp_gnrb_artist (user_id, name, centova_artistid,featured,firstname,lastname,email,phonenumber,address) VALUES (24, 'James', 0,0,'Praveen','james_last_name','praveenpn4u@gmail.com','9741100669','sdlsd;fhg asdgasdg asdga sdgasdga'); 
                       $results = $wpdb->get_results("select name, firstname, lastname, email, phonenumber, address from $gnrb_table_artist where id=$artist_id");
                       
                ?>
                Pleae edit the artist details & press "Edit" after you are done.
                <div id="artistreg">
                        <form class="register step2" method="post" action="<? echo(get_option("siteurl")); ?>/update-profile/?aid=<?php echo $artist_id; ?>">
                                <h3>Contact Info</h3>
                                <input type="hidden" name="type" value="artist">
                                <input type="hidden" name="artist_id" value="<? echo $artist_id ?>">
                                <ol id="ContactDetails">
                                        <li>
                                                <label for="artistname">Artist Name</label>
                                                <input id="artistname" class="text" type="text" name="artistname" value="<?php echo $results[0]->name; ?>">                                        
                                        </li>
                                        <li>
                                                <label for="firstname">First Name</label>
                                                <input id="firstname" class="text" type="text" name="firstname" value="<?php echo $results[0]->firstname; ?>">                                        
                                        </li>
                                        <li>
                                                <label for="lastname">Last Name</label>
                                                <input id="lastname" class="text" type="text" name="lastname" value="<?php echo $results[0]->lastname; ?>">                                          
                                        </li>
                                        <li>
                                                <label for="email">Email</label>
                                                <input id="email" class="text" type="text" name="email" value="<?php echo $results[0]->email; ?>">                                        
                                        </li>
                                        <li>
                                                <label for="phonenumber">Phone Number</label>
                                                <input id="phonenumber" class="text" type="text" name="phonenumber" value="<?php echo $results[0]->phonenumber; ?>">                                        
                                        </li>
                                        <li>
                                                <label for="address">Address</label>
                                                <textarea id="address" rows="5" cols="30" name="address"><?php echo $results[0]->address; ?></textarea>
                                        </li>
                                </ol>
                                <input id="back_button" class="submit" type="button" value="Back" onclick="window.location='<? echo(get_option("siteurl")); ?>/upload-music';"/>
                                <input id="edit_button" name="edit_button" class="submit" type="submit" value="Edit"/>
                        </form>
                </div>
<?
}

function rv_edit_artist($artist_array) {
        global $wpdb, $gnrb_table_artist;
        //Update the artists table
        $update = "UPDATE $gnrb_table_artist
                        SET name = '" . $artist_array['artistname'] . "',
                        firstname = '" . $artist_array['firstname'] . "',
                        lastname = '" . $artist_array['lastname'] . "',
                        email = '" . $artist_array['email'] . "',
                        phonenumber = '" . $artist_array['phonenumber'] . "',
                        address = '" . $artist_array['address']  . "'
                   where id = " . $artist_array['artist_id'];
        //echo $update;           
        $result = $wpdb->query($update);
        //$wpdb->print_error();
        
}

?>
