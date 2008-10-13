<?php
/*
Plugin Name: Customizable Post Listings
Version: 3.0.1
Plugin URI: http://coffee2code.com/wp-plugins/customizable-post-listings
Author: Scott Reilly
Author URI: http://coffee2code.com
Description: Display Recent Posts, Recently Commented Posts, Recently Modified Posts, Random Posts, and other post, page, or draft listings using the post information of your choosing in an easily customizable manner.  You can narrow post searches by specifying categories and/or authors, among other things.

More documentation is available on the plugin's admin options page, under Options -> CPL (or in WP 2.5: Settings -> CPL)

Compatible with WordPress 2.3+ and 2.5, but not with versions of WP prior to 2.3.

=>> Read the accompanying readme.txt file for more information.  Also, visit the plugin's homepage
=>> for more information and the latest updates

Installation:

1. Download the file http://coffee2code.com/wp-plugins/customizable-post-listings.zip and unzip it into your 
/wp-content/plugins/ directory.
2. Activate the plugin through the 'Plugins' admin menu in WordPress
3. Go to the new Options -> CPL (or in WP 2.5: Settings -> CPL) admin options page.
Optionally customize the settings.  You can read documentation on the template tags, arguments, and 
percent-substitution tags there as well.
4. In your sidebar.php (or other template file), insert calls to post listings function(s) provided by the plugin.

*/

/*
Copyright (c) 2004-2008 by Scott Reilly (aka coffee2code)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation 
files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, 
modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the 
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

if ( !class_exists('CustomizablePostListings') ) :

class CustomizablePostListings {
	var $admin_options_name = 'c2c_customizable_post_listings';
	var $nonce_field = 'update-customizable_post_listings';
	var $show_admin = true;	// Change this to false if you don't want the plugin's admin page shown.
	var $config = array();
	var $options = array(); // Don't use this directly

	function CustomizablePostListings() {
		$this->config = array(
			// input can be 'checkbox', 'text', 'textarea', 'inline_textarea', 'hidden', or 'none'
			// datatype can be 'array' or 'hash'
			// can also specify input_attributes
			'excerpt_words' => array('input' => 'text', 'default' => 6,
					'label' => 'Excerpt words',
					'help' => 'Number of words to use for %post_excerpt_short%'),
			'excerpt_length' => array('input' => 'text', 'default' => 50,
					'label' => 'Excerpt length',
					'help' => 'Number of characters to use for %post_excerpt_short%, only used if <code>Excerpt words</code> is 0'),
			'comment_excerpt_words' => array('input' => 'text', 'default' => 6,
					'label' => 'Comment excerpt words',
					'help' => 'Number or words to use for %last_comment_excerpt% and %last_comment_excerpt_URL%'),
			'comment_excerpt_length' => array('input' => 'text', 'default' => 15,
					'label' => 'Comment excerpt length',
					'help' => 'Number of characters to use for %last_comment_excerpt% and %last_comment_excerpt_URL%, only used if <code>Comment excerpt words</code> is 0'),
			'post_URL_short_words' => array('input' => 'text', 'default' => 0,
					'label' => 'Post URL short words',
					'help' => 'Number of words to use for %post_URL_short%'),
			'post_URL_short_length' => array('input' => 'text', 'default' => 25,
					'label' => 'Post URL short length',
					'help' => 'Number of characters to use for %post_URL_short%, only used if <code>Post URL short words</code> is 0'),
			'between_categories' => array('input' => 'text', 'default' => ', ',
					'label' => 'Between categories',
					'help' => 'Text to appear between categories when categories are listed'),
			'between_tags' => array('input' => 'text', 'default' => ', ',
					'label' => 'Between tags',
					'help' => 'Text to appear between tags when tags are listed'),
			'time_format' => array('input' => 'text', 'default' => '',
					'label' => 'Time format',
					'help' => 'Default time format string (uses WordPress default if not specified). See <a href="http://us2.php.net/date" target="_blank">here</a> for more info.'),
			'comment_fancy' => array('input' => 'inline_textarea',
					'default' => array('No comments', '1 Comment', '%comments_count% Comments'),
					'datatype' => 'array',
					'label' => 'Comments fancy',
					'help' => 'Format strings used for <code>%comment_fancy%</code>.  The first line is what gets displayed when no comments are present, the second line is used when one comment is present, and the third is used with 2+ comments.  <code>%comments_count%</code> is available for use here.',
					'input_attributes' => 'style="font-family: \"Courier New\", Courier, mono;" rows="3" cols="50"'),
			'pingback_fancy' => array('input' => 'inline_textarea',
			 		'default' => array('No pingbacks', '1 Pingback', '%pingbacks_count% Pingbacks'),
					'datatype' => 'array',
					'label' => 'Pingbacks fancy',
					'help' => 'Format strings used for <code>%pingback_fancy%</code>.  The first line is what gets displayed when no pingbacks are present, the second line is used when one pingback is present, and the third is used with 2+ pingbacks.  <code>%pingbacks_count%</code> is available for use here.',
					'input_attributes' => 'style="font-family: \"Courier New\", Courier, mono;" rows="3" cols="50"'),
			'trackback_fancy' => array('input' => 'inline_textarea', 
					'default' => array('No trackbacks', '1 Trackback', '%trackbacks_count% Trackbacks'),
					'datatype' => 'array',
					'label' => 'Trackbacks fancy',
					'help' => 'Format strings used for <code>%trackback_fancy%</code>.  The first line is what gets displayed when no trackbacks are present, the second line is used when one trackback is present, and the third is used with 2+ trackbacks.  <code>%trackbacks_count%</code> is available for use here.',
					'input_attributes' => 'style="font-family: \"Courier New\", Courier, mono;" rows="3" cols="50"')
		);

		add_action('admin_menu', array(&$this, 'admin_menu'));		
	}

	function install() {
		$this->options = $this->get_options();
		update_option($this->admin_options_name, $this->options);
	}

	function admin_menu() {
		if ( $this->show_admin )
			add_options_page('Customizable Post Listings', 'CPL', 9, basename(__FILE__), array(&$this, 'options_page'));
	}

	function get_options() {
		if ( !empty($this->options)) return $this->options;
		// Derive options from the config
		$options = array();
		foreach (array_keys($this->config) as $opt) {
			$options[$opt] = $this->config[$opt]['default'];
		}
        $existing_options = get_option($this->admin_options_name);
        if (!empty($existing_options)) {
            foreach ($existing_options as $key => $value)
                $options[$key] = $value;
        }            
		$this->options = $options;
        return $options;
	}

	function options_page() {
		$options = $this->get_options();
		// See if user has submitted form
		if ( isset($_POST['submitted']) ) {
			check_admin_referer($this->nonce_field);

			foreach (array_keys($options) AS $opt) {
				$options[$opt] = htmlspecialchars(stripslashes($_POST[$opt]));
				$input = $this->config[$opt]['input'];
				if (($input == 'checkbox') && !$options[$opt])
					$options[$opt] = 0;
				if ($this->config[$opt]['datatype'] == 'array') {
					if ($input == 'text')
						$options[$opt] = explode(',', str_replace(array(', ', ' ', ','), ',', $options[$opt]));
					else
						$options[$opt] = array_map('trim', explode("\n", trim($options[$opt])));
				}
				elseif ($this->config[$opt]['datatype'] == 'hash') {
					if ( !empty($options[$opt]) ) {
						$new_values = array();
						foreach (explode("\n", $options[$opt]) AS $line) {
							list($shortcut, $text) = array_map('trim', explode("=>", $line, 2));
							if (!empty($shortcut)) $new_values[str_replace('\\', '', $shortcut)] = str_replace('\\', '', $text);
						}
						$options[$opt] = $new_values;
					}
				}
			}
			// Remember to put all the other options into the array or they'll get lost!
			update_option($this->admin_options_name, $options);

			echo "<div class='updated'><p>Plugin settings saved.</p></div>";
		}

		$action_url = $_SERVER[PHP_SELF] . '?page=' . basename(__FILE__);

		echo <<<END
		<div class='wrap'>
			<h2>Customizable Post Listings Plugin Options</h2>
			<p>Display Recent Posts, Recently Commented Posts, Recently Modified Posts, Random Posts, and other post, page, or draft listings using the post information of your choosing in an easily customizable manner.  You can narrow post searches by specifying categories and/or authors, among other things.</p>
			
			<p>For help, see the <a href="#percenttags">Percent-substitution tags</a> section for a listing and explanation of all the percent-substitution format tags or the <a href="#templatefuncs">Template functions</a> section for an explanation of the template functions and their arguments.</p>
			
			<form name="customizable_post_listings" action="$action_url" method="post">	
END;
				wp_nonce_field($this->nonce_field);
		echo '<table width="100%" cellspacing="2" cellpadding="5" class="optiontable editform form-table">';
				foreach (array_keys($options) as $opt) {
					$input = $this->config[$opt]['input'];
					if ($input == 'none') continue;
					$label = $this->config[$opt]['label'];
					$value = $options[$opt];
					if ($input == 'checkbox') {
						$checked = ($value == 1) ? 'checked=checked ' : '';
						$value = 1;
					} else {
						$checked = '';
					};
					if ($this->config[$opt]['datatype'] == 'array') {
						if ($input == 'textarea' || $input == 'inline_textarea')
							$value = implode("\n", $value);
						else
							$value = implode(', ', $value);
					} elseif ($this->config[$opt]['datatype'] == 'hash') {
						$new_value = '';
						foreach ($value AS $shortcut => $replacement) {
							$new_value .= "$shortcut => $replacement\n";
						}
						$value = $new_value;
					}
					echo "<tr valign='top'>";
					if ($input == 'textarea') {
						echo "<td colspan='2'>";
						if ($label) echo "<strong>$label</strong><br />";
						echo "<textarea name='$opt' id='$opt' {$this->config[$opt]['input_attributes']}>" . $value . '</textarea>';
					} else {
						echo "<th scope='row'>$label</th><td>";
						if ($input == "inline_textarea")
							echo "<textarea name='$opt' id='$opt' {$this->config[$opt]['input_attributes']}>" . $value . '</textarea>';
						else
							echo "<input name='$opt' type='$input' id='$opt' value='$value' $checked {$this->config[$opt]['input_attributes']} />";
					}
					if ($this->config[$opt]['help']) {
						echo "<br /><span style='color:#777; font-size:x-small;'>";
						echo $this->config[$opt]['help'];
						echo "</span>";
					}
					echo "</td></tr>";
				}
		echo <<<END
			</table>
			<input type="hidden" name="submitted" value="1" />
			<div class="submit"><input type="submit" name="Submit" value="Save Changes" /></div>
		</form>
			</div>
END;
		$logo = get_option('siteurl') . '/wp-content/plugins/' . basename($_GET['page'], '.php') . '/c2c_minilogo.png';
		echo <<<END
		<style type="text/css">
			#c2c {
				text-align:center;
				color:#888;
				background-color:#ffffef;
				padding:5px 0 0;
				margin-top:12px;
				border-style:solid;
				border-color:#dadada;
				border-width:1px 0;
			}
			#c2c div {
				margin:0 auto;
				padding:5px 40px 0 0;
				width:45%;
				min-height:40px;
				background:url('$logo') no-repeat top right;
			}
			#c2c span {
				display:block;
				font-size:x-small;
			}
		</style>
		<div id='c2c' class='wrap'>
			<div>
			This plugin brought to you by <a href="http://coffee2code.com" title="coffee2code.com">Scott Reilly, aka coffee2code</a>.
			<span><a href="http://coffee2code.com/donate" title="Please consider a donation">Did you find this plugin useful?</a></span>
			</div>
		</div>
END;
		echo <<<END
		<div class='wrap'><a name='percenttags'></a>
			<h2>Percent-substitution tags</h2>
			<p>These are the various percent-substitution tags available to you for use in the <code>\$format</code> argument of the template functions.</p>
			<style type="text/css">
			.percenttags, .percentfuncs dd {
				color:#666;
			}
			.percenttags li {
				margin:0.1em 0.1em 0.1em 0.4em;
			}
			.percenttags li strong {
				color:#111;
			}
			.percentfuncs {
				list-style:bullet;
			}
			.percentfuncs dd {
				margin-left:2em;
			}
			.percentfuncs dt {
				font-weight:bold;
			}
			.percentfuncs ul, .percentfuncs ul li {
				padding:0;
				margin:0;
			}
			.percentfuncs ul li {
				margin-left:25px;
			}
			.percentfuncs ul li code {
				font-family:"Courier New",Courier,mono;
				font-weight:normal;
				padding:0pt;
			}
			.percentfuncs ul li code pre {
				width:100%;
				overflow-x:scroll;
			}
			
			</style>
			<ul class="percenttags">
			<li><strong>%allcomments_count%</strong> : Number of comments + pingbacks + trackbacks for post</li>
			<li><strong>%allcomments_fancy%</strong> : Fancy reporting of allcomments</li>
			<li><strong>%comments_count%</strong> : Number of comments for post</li>
			<li><strong>%comments_count_URL%</strong> : Count of number of comments linked to the top of the comments section</li>
			<li><strong>%comments_fancy%</strong> : Fancy reporting of comments</li>
			<li><strong>%comments_fancy_URL%</strong> : Fancy reporting of comments linked to comments section</li>
			<li><strong>%comments_url%</strong> : URL to top of comments section for post</li>
			<li><strong>%comments_URL%</strong> : Post title linked to the top of the comments section on post's permalink page</li>
			<li><strong>%last_comment_date%</strong> : Date of last comment for post</li>
			<li><strong>%last_comment_excerpt%</strong> : Excerpt of contents for last comment to post</li>
			<li><strong>%last_comment_excerpt_URL%</strong> : Excerpt of contents for last comment to post linked to that comment</li>
			<li><strong>%last_comment_id%</strong> : ID for last comment for post</li>
			<li><strong>%last_comment_time%</strong> : Time of last comment for post</li>
			<li><strong>%last_comment_url%</strong> : URL to most recent comment for postv
			<li><strong>%last_commenter%</strong> : Author of last comment for post</li>
			<li><strong>%last_commenter_URL%</strong> : Linked (if author URL provided) of author of last comment for post</li>
			<li><strong>%pingbacks_count%</strong> : Number of pingbacks for post</li>
			<li><strong>%pingbacks_fancy%</strong> : Fancy report of trackbacks</li>
			<li><strong>%post_author%</strong> : Author for post (preferred display name)</li>
			<li><strong>%post_author_count%</strong> : Number of posts made by post author</li>
			<li><strong>%post_author_description%</strong> : Post author's description</li>
			<li><strong>%post_author_email%</strong> : Post author's email address</li>
			<li><strong>%post_author_firstname%</strong> : Post author's first name</li>
			<li><strong>%post_author_id%</strong> : ID of post author</li>
			<li><strong>%post_author_lastname%</strong> : Post author's last name</li>
			<li><strong>%post_author_login%</strong> : Post author's login name</li>
			<li><strong>%post_author_nickname%</strong> : Post author's nickname</li>
			<li><strong>%post_author_posts%</strong> : Link to page of all of post author's posts</li>
			<li><strong>%post_author_url%</strong> : Linked (if URL provided) name of post author</li>
			<li><strong>%post_categories%</strong> : Name of each of post's categories</li>
			<li><strong>%post_categories_URL%</strong> : Name of each of post's categories linked to respective category archive</li>
			<li><strong>%post_content%</strong> : Full content of the post (&lt;p> and &lt;br> tags stripped)</li>
			<li><strong>%post_content_full%</strong> : Full content of the post (&lt;p> and &lt;br> tags intact)</li>
			<li><strong>%post_content_upto_more%</strong> : Content of the post up to the &lt;!--more--> separator; nothing displayed if 'more' isn't present</li>
			<li><strong>%post_date%</strong> : Date for post</li>
			<li><strong>%post_excerpt%</strong> : Excerpt for post (&lt;p> and &lt;br> tags stripped)</li>
			<li><strong>%post_excerpt_full%</strong> : Excerpt for post (&lt;p> and &lt;br> tags intact)</li>
			<li><strong>%post_excerpt_short%</strong> : Customizably shorter excerpt, suitable for sidebar usage</li>
			<li><strong>%post_guid%</strong> : Post GUID</li>
			<li><strong>%post_id%</strong> : ID for post</li>
			<li><strong>%post_lat%</strong> : Post latitude</li>
			<li><strong>%post_lon%</strong> : Post longitude</li>
			<li><strong>%post_modified%</strong> : Last modified date for post</li>
			<li><strong>%post_name%</strong> : Post name (aka slug)</li>
			<li><strong>%post_status%</strong> : Post status for post</li>
			<li><strong>%post_tags%</strong> : Name of each of post's tags</li>
			<li><strong>%post_tags_URL%</strong> : Name of each of post's tags linked to respective tag archive</li>
			<li><strong>%post_time%</strong> : Time for post</li>
			<li><strong>%post_title%</strong> : Title for post</li>
			<li><strong>%post_type%</strong> : Post type of post</li>
			<li><strong>%post_url%</strong> : URL for post</li>
			<li><strong>%post_URL%</strong> : Post title linked to post's permalink page</li>
			<li><strong>%post_URL_short%</strong> : Customizably shorter post title linked to post's permalink page</li>
			<li><strong>%trackbacks_count%</strong> : Number of trackbacks for post</li>
			<li><strong>%trackbacks_fancy%</strong> : Fancy reporting of trackbacks</li>
			</ul>
			
			<p>These are the pseudo-function percent-substitution tags.  They act like functions, so you can send them data to work with.  You
			can even send percent-substutition tags which will get evaluated before the function gets them.</p>
			
			<dl class="percentfuncs">
			<dt>%post_custom(field,format,none)%</dt> 
		    <dd>
			A lite version of the Get Custom Field Values plugin to display custom fields.
			<ul>Arguments:
				<li>field = a custom field key value</li>
				<li>format =  an optional percent-substitution format string for the output.  In additional to all the
				existing percent-substitution tags, two additional tags are also available for use here:
					<ul>
						<li>%field% : the value you provided as "field"</li>
						<li>%value% : the value of the post's custom field; if the post has more than one custom 
				        field matching "field" then all their values are joined with a comma</li>
					</ul>
				If not specified, then the format defaults to simply <code>%value%</code>, displaying the custom field's value.
				</li>
				<li>none = text to display if the post does not have the custom field
				(leave blank to show nothing).</li>
			</ul>
			<ul>Examples:
				<li><code>%post_custom(quantity_in_stock)%</code></li>
				<li><code>%post_custom(mood, Today I am feeling %value%)%</code></li>
				<li><code>%post_custom(Books Read, Today I read: %value%, nothing)%</code></li>
			</ul>
			</dd>
			
			<dt>%post_date(date_format)%</dt>
			<dd>
			Displays the post's publication date using a custom date format, overriding the configured default format.
			<ul>Argument:
				<li>date_format = a PHP date-format string.  e.g. <code>%post_date(F d, Y)%</code><br />
				See <a href="http://us2.php.net/date" title="">http://us2.php.net/date</a> for more info on date format strings.</li>
			</ul>
			<ul>Examples:
				<li><code>%post_date(Y-m-d)%</code></li>
				<li><code>%post_date(M n, Y)%</code></li>
			</ul>
			</dd>
			
		    <dt>%post_other(other_field)%</dt>
			<dd>
			Retrieve the value of any post table field.  This is useful for accessing a field that does not already have a
			percent-substitution tag counterpart (generally for fields added to the posts table by another plugin or for 
			when WordPress updates the posts table to include something this plugin hasn't been updated to handle yet), or if
			you need to programmatically determine what field you want during runtime.
			<ul>Argument:
				<li>other_field = the name of a field in the posts table</li>
			</ul>
			<ul>Examples:
				<li><code>%post_other(post_rating)%</code></li>
				<li><code>%post_other(menu_order)%</code></li>
			</ul>
			</dd>

			<dt>%last_commenters(limit,more,between)%</dt>
			<dd>
			Lists the most recent commenters to the post.
			<ul>Arguments:
				<li>limit = number of latest commenters to list by name</li>
				<li>more = text to show after listed commenter <em>if</em> there are more commenters to the post; default is [...]</li>
				<li>between = text to show between listed commenters; default is ", "</li>
			</ul>
			<ul>Examples:
				<li><code>%last_commenters(5, and others!)%</code></li>
				<li><code>%last_commenters(1,,)%</code></li>
			</ul>
			</dd>
			
			<dt>%function(function_name,arg1,arg2,...)%</dt>
			<dd>
			Calls an arbitrary function.
			<ul>Arguments:
				<li>function_name = name of the function to be called</li>
				<li>Additional arguments are optional and are passed to the function specified.  Arguments <em>can</em>
					include percent-substitution tags, e.g. <code>%function(my_excerpt_maker,%post_content%)%</code>
				</li>
			</ul>
			<ul>Examples:
				<li><code>%function(strtoupper,%post_title%)%</code></li>
				<li><code>%function(my_excerpt_maker,%post_content%)%</code></li>
			</ul>
			<p><strong>NOTE:</strong> Currently you <em>cannot</em> embed a pseudo-function percent-substitution within another.  This is invalid:
				<code>%function(mood_handler,%post_custom(mood)%)%</code></p>
		    </dd>
			</dl>
		</div>
END;
		$default_date = get_settings('date_format');
		echo <<<END
		<div class='wrap'><a name='templatefuncs'></a>
			<h2>Template functions</h2>
			<p>These are the template functions made available by this plugins.  <code>c2c_get_recent_posts()</code> provides the core
			functionality.  The other template functions are added as a convenience to provide different default argument values for their
			different purposes.</p>
			<ul>
			<li>Get recent posts: 
<code><pre>function c2c_get_recent_posts( \$num_posts = 5,
  \$format = "&lt;li>%post_date%: %post_URL%&lt;/li>",
  \$categories = '',
  \$orderby = 'date',
  \$order = 'DESC',
  \$offset = 0,
  \$date_format = 'M d',
  \$authors = '',
  \$post_type = 'post',
  \$post_status = 'publish',
  \$include_passworded_posts = false,
  \$extra_sql_where_clause = '' )
</pre></code>
			</li>
			<li>Get Random Posts
<code><pre>
function c2c_get_random_posts( \$num_posts = 5,
  \$format = "&lt;li>%post_date%: %post_URL%&lt;/li>",
  \$categories = '',
  \$order = 'DESC',
  \$offset = 0,
  \$date_format = 'm/d/Y',
  \$authors = '',
  \$post_type = 'post',
  \$post_status = 'publish',
  \$include_passworded_posts = false )
</pre></code>
			</li>
			<li>Get Recently Commented Posts
<code><pre>
function c2c_get_recently_commented( \$num_posts = 5, 
  \$format = "&lt;li>%comments_URL%&lt;br />%last_comment_date%&lt;br />%comments_fancy%&lt;/li>",
  \$categories = '',
  \$order = 'DESC',
  \$offset = 0,
  \$date_format = 'm/d/Y h:i a',
  \$authors = '',
  \$post_type = 'post',
  \$post_status = 'publish',
  \$include_passworded_posts = false )
</pre></code>
			</li>
			<li>Get Recently Modified Posts
<code><pre>
function c2c_get_recently_modified( \$num_posts = 5,
  \$format = "&lt;li>%post_URL%&lt;br />Updated: %post_modified%&lt;/li>",
  \$categories = '',
  \$order = 'DESC',
  \$offset = 0,
  \$date_format = 'm/d/Y',
  \$authors = '',
  \$post_type = 'post',
  \$post_status = 'publish',
  \$include_passworded_posts = false )
</pre></code>
</li>
			</ul>
			
			<h4>Arguments:</h4>
			
			<dl class="percentfuncs">
			<dt>\$num_posts</dt>
			<dd>The number of posts to list.</dd>
			<dd>Default: <code>5</code></dd>

			<dt>\$format</dt>
			<dd>String containing <a href="#percenttags">percent-substitution tags</a> and text to be displayed for each post retrieved.</dd>
			
			<dt>\$categories</dt>
			<dd><em>Optional.</em> Space-separated list of category IDs -- leave empty to search for posts regardless of category.</dd>
			<dd>Default value: <code>''</code></dd>
			
			<dt>\$orderby</dt>
			<dd><em>Optional.</em> The post field to be used as the sort field.  Except for a few exceptions, the value provided will assume 
			that <code>post_</code> will be prepended to it. So a value of <code>'title'</code> will sort the posts by the 
			<code>'post_title'</code> field.</dd>
			<dd>Possible values: any post table field that begins with <code>post_</code>. In practice, it'll likely be one of the following: <code>'ID', 'date', 'modified', 'name', 'title'</code>.  The exceptions to this are: 
			<ul>
			  <li><code>rand()</code> : to randomize the order of posts</li>
			  <li><code>max_comment_date</code> : to order by the date of the most recent comment for each post</li>
			  <li><code>SQL:\$sql_here</code> : if you're feeling advanced, you can directly specify the SQL for the ORDER BY clause by
				prepending <code>SQL:</code> to the \$orderby value</li>
			</ul></dd>
			<dd>Default value: <code>'date'</code></dd>
			
			<dt>\$order</dt>
			<dd><em>Optional.</em> Indicates whether the posts should be listed in ascending or descending order.</dd>
			<dd>Possible values: <code>'ASC'</code> or <code>'DESC'</code></dd>
			<dd>Default value: <code>'DESC'</code></dd>
	
			<dt>\$offset</dt>
			<dd><em>Optional.</em> Number of posts to skip from the beginning of the list of posts found.</dd>
			<dd>Default value: <code>0</code></dd>
			
			<dt>\$date_format</dt>
			<dd><em>Optional.</em> Date format, PHP-style, to use to format any dates.  If set to <code>''</code>, then the blog default date format of
			<code>'$default_date'</code> will be used.  Keep in mind that the template functions provide their own date format.</dd>
			<dd>Possible values: see <a href="http://us2.php.net/date" title="">http://us2.php.net/date</a> for more info on date format strings.</dd>
			<dd>Default value: <code>'m/d/Y'</code></dd>
			
			<dt>\$authors</dt>
			<dd><em>Optional.</em> Space-separated list of author IDs -- leave empty to search for posts regardless of author.</dd>
			<dd>Default value: <code>''</code></dd>
			
			<dt>\$post_type</dt>
			<dd><em>Optional.</em> Space-separated list of post_type values to consider in the search.</dd>
			<dd>Possible values: <code>post, page, attachment</code></dd>
			<dd>Default value: <code>'post'</code></dd>
			
			<dt>\$post_status
			<dd><em>Optional.</em> Space-separated list of post_status values to consider in the search.</dd>
			<dd>Possible values: <code>publish, draft, private, pending, and/or future</code></dd>
			<dd>Default value: <code>'publish'</code></dd>
			
			<dt>\$include_passworded_posts</dt>
			<dd><em>Optional.</em> Should passworded posts be included in the serach?</dd>
			<dd>Possible values: <code>true</code> or <code>false</code></dd>
			<dd>Default value: <code>false</code></dd>
			
			<dt>\$extra_sql_where_clause</dt>
			<dd><em>Optional.</em> Additional SQL to be added to the query's WHERE clause, to facilitate refining the post search in
			a manner not achievable with existing arguments.</dd>
			<dd>Default value: <code>''</code></dd>
			
			</dl>
		</div>
END;
	}

} // end CustomizablePostListings

endif; // end if !class_exists()
if ( class_exists('CustomizablePostListings') ) :
	// Get the ball rolling
	$customizable_post_listings = new CustomizablePostListings();
	// Actions and filters
	if (isset($customizable_post_listings)) {
		register_activation_hook( __FILE__, array(&$customizable_post_listings, 'install') );
	}
endif;

//
// ************************ START TEMPLATE TAGS ******************************************************************
//

function c2c_get_recent_posts( $num_posts = 5,
	$format = "<li>%post_date%: %post_URL%</li>",
	$categories = '',		// space separated list of category IDs -- leave empty to get all
	$orderby = 'date',
	$order = 'DESC',		// either 'ASC' (ascending) or 'DESC' (descending)
	$offset = 0,			// number of posts to skip
	$date_format = 'M d',		// Date format, php-style, if different from blog's date-format setting
	$authors = '',			// space separated list of author IDs -- leave empty to get all
	$post_type = 'post',		// space separated list of post_types to consider (possible values: post, page, attachment)
	$post_status = 'publish',	// space separated list of post_statuses to consider (possible values: publish, draft, private, pending, and/or future)
	$include_passworded_posts = false,
	$extra_sql_where_clause = '' ) 
{
	global $wpdb;
	if ($order != 'ASC') $order = 'DESC';
	if ('max_comment_date' == $orderby) { $add_recent_comment_to_sql = 1; }
	else {
		if (strpos($orderby, 'SQL:') === 0) $orderby = substr($orderby, 4);
		elseif ($orderby != 'rand()') $orderby = "posts.post_$orderby";
		$add_recent_comment_to_sql = 0;
	}
	if (empty($post_type)) $post_type = 'post';
	if (empty($post_status)) $post_status = 'publish';
	
	if ($add_recent_comment_to_sql)
		$sql = "SELECT posts.*, MAX(comment_date) AS max_comment_date FROM $wpdb->comments AS comments, $wpdb->posts AS posts ";
	else 
		$sql = "SELECT DISTINCT * FROM $wpdb->posts AS posts ";
	if ($categories) {
		$sql .= "LEFT JOIN $wpdb->term_relationships ON (posts.ID = $wpdb->term_relationships.object_id) ";
		$sql .= "LEFT JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) ";
	}
	$sql .= "WHERE 1=1 ";
	if ($categories) {
		$cat_array = preg_split('/[,\s]+/', $categories);
		$cats = array();
		$cats_exclude = false;
		foreach ($cat_array as $cat) {
			// Note: If any one category is defined as being excluded (i.e. "-10") then ALL other listed categories will also be
			//	excluded, regardless of whether a "-" preceded them or not
			// TODO: Support mixture of multiple category inclusion(s) and exclusion(s)
			if ($cat{0} == '-') {
				$cats_exclude = true;
				$cat = substr($cat, 1);
			}
			$cats[] = $cat;
			$subcats = (array) get_term_children($cat, 'category');
			foreach ($subcats as $subcat) { $cats[] = $subcat->term_id; }
		}
		$sql .= "AND $wpdb->term_taxonomy.taxonomy = 'category' ";
		$cats = "'" . implode("', '", $cats) . "'";
		$sql .= "AND $wpdb->term_taxonomy.term_id " . ($cats_exclude ? 'NOT ' : '') . "IN ($cats) ";
	}
	$sql .= "AND ( posts.post_status = '" . str_replace(" ", "' OR posts.post_status = '", $post_status) . "' ) ";
	$sql .= "AND ( posts.post_type = '" . str_replace(" ", "' OR posts.post_type = '", $post_type) . "' ) ";
	if (!$include_passworded_posts)
		$sql .= "AND posts.post_password = '' ";
	if ($add_recent_comment_to_sql)
		$sql .= "AND posts.ID = comments.comment_post_ID AND comments.comment_approved = '1' ";
	if ($authors) {
		$authors = addslashes_gpc($authors);
		if (stristr($authors,'-')) {
			// Note: If any one author is defined as being excluded (i.e. "-10") then ALL other listed authors
			//	will also be excluded, regardless of whether a "-" preceded them or not
			// TODO: Support mixture of multiple author inclusion(s) and exclusion(s)
			$eq = '!=';
			$andor = 'AND';
		} else {
			$eq = '=';
			$andor = 'OR';
		}
		$author_array = preg_split('/[,\s]+/', $authors);
		$sql .= " AND ( posts.post_author $eq '" . abs(intval($author_array[0])) . "' ";
		for ($i = 1; $i < (count($author_array)); $i = $i + 1) {
			$sql .= "OR posts.post_author $eq '" . abs(intval($author_array[$i])) . "' ";
		}
		$sql .= ') ';
	}
	if ('modified' == $orderby) {
		$now = current_time('mysql');
		$sql .= "AND posts.post_modified_gmt <= '$now' ";
	}
	if ($extra_sql_where_clause) {
		$sql .= "AND ($extra_sql_where_clause) ";
	}
	$sql .= "GROUP BY posts.ID ORDER BY $orderby $order";
	if ($num_posts) $sql .= " LIMIT $offset, $num_posts";
	$posts = array();
	$posts = $wpdb->get_results($sql);
//echo "<p><b>SQL:</b> $sql \n which yielded " . count($posts) . " answers.</p>\n";
	if (empty($posts)) return;
	return c2c_get_recent_handler($posts, $format, $date_format);
} //end function c2c_get_recent_posts()

function c2c_get_random_posts( $num_posts = 5,
	$format = "<li>%post_date%: %post_URL%</li>",
	$categories = '',               // space separated list of category IDs -- leave empty to get all
	$order = 'DESC',                // either 'ASC' (ascending) or 'DESC' (descending)
	$offset = 0,                    // number of posts to skip
	$date_format = 'm/d/Y',         // Date format, php-style, if different from blog's date-format setting
	$authors = '',                  // space separated list of author IDs -- leave empty to get all
	$post_type = 'post',		// space separated list of post_types to consider (possible values: post, page, attachment)
	$post_status = 'publish',	// space separated list of post_statuses to consider (possible values: publish, draft, private, pending, and/or future)
	$include_passworded_posts = false )
{
        return c2c_get_recent_posts($num_posts, $format, $categories, 'rand()', $order, $offset, $date_format, $authors, $post_type, $post_status, $include_passworded_posts);
} //end function get_random_post()

function c2c_get_recently_commented( $num_posts = 5, 
	$format = "<li>%comments_URL%<br />%last_comment_date%<br />%comments_fancy%</li>",
	$categories = '',		// space separated list of category IDs -- leave empty to get all
	$order = 'DESC',		// either 'ASC' (ascending) or 'DESC' (descending)
	$offset = 0,			// number of posts to skip
	$date_format = 'm/d/Y h:i a',	// Date format, php-style, if different from blog's date-format setting
	$authors = '',			// space separated list of author IDs -- leave empty to get all
	$post_type = 'post',		// space separated list of post_types to consider (possible values: post, page, attachment)
	$post_status = 'publish',	// space separated list of post_statuses to consider (possible values: publish, draft, private, pending, and/or future)
	$include_passworded_posts = false )
{
	return c2c_get_recent_posts($num_posts, $format, $categories, 'max_comment_date', $order, $offset, $date_format, $authors, $post_type, $post_status, $include_passworded_posts);
} //end function get_recently_commented()

function c2c_get_recently_modified( $num_posts = 5,
	$format = "<li>%post_URL%<br />Updated: %post_modified%</li>",
	$categories = '',		// space separated list of category IDs -- leave empty to get all
	$order = 'DESC',		// either 'ASC' (ascending) or 'DESC' (descending)
	$offset = 0,			// number of posts to skip
	$date_format = 'm/d/Y',		// Date format, php-style, if different from blog's date-format setting
	$authors = '',			// space separated list of author IDs -- leave empty to get all
	$post_type = 'post',	// space separated list of post_types to consider (possible values: post, page, attachment)
	$post_status = 'publish',	// space separated list of post_statuses to consider (possible values: publish, draft, private, pending, and/or future)
	$include_passworded_posts = false )
{
	return c2c_get_recent_posts($num_posts, $format, $categories, 'modified', $order, $offset, $date_format, $authors, $post_type, $post_status, $include_passworded_posts);
} //end function c2c_get_recently_modified()

//
// ************************ END TEMPLATE TAGS ********************************************************************
//

if (! function_exists('c2c_comment_count') ) {
	// Leave $comment_types blank to count all comment types (comment, trackback, and pingback).  Otherwise, specify $comment_types
	//	as a space-separated list of any combination of those three comment types (only valid for WP 1.5+)
	function c2c_comment_count( $post_id, $comment_types='' ) {
		global $wpdb;
		$sql = "SELECT COUNT(*) FROM $wpdb->comments WHERE comment_post_ID = '$post_id' AND comment_approved = '1'";
		if (!empty($comment_types)) {
			$sql .= " AND ( comment_type = '" . str_replace(" ", "' OR comment_type = '", $comment_types) . "' ";
			if (strpos($comment_types,'comment') !== false)
				$sql .= "OR comment_type = '' ";		//WP allows a comment_type of '' to be == 'comment'
			$sql .= ")";
		}
		return $wpdb->get_var($sql);
	} //end function c2c_comment_count()
}

if (! function_exists('c2c_get_get_custom') ) {
	function c2c_get_get_custom( $post_id, $field, $none = '' ) {
		global $wpdb, $post_meta_cache;
		if (! empty($post_meta_cache[$id][$field]) ) {
			$result = $post_meta_cache[$id][$field];
		} else {
			$sql  = "SELECT DISTINCT meta_value FROM $wpdb->postmeta ";
			$sql .= "WHERE post_id = '$post_id' AND meta_key = '$field' ";
			$sql .= "LIMIT 1";
			$result = $wpdb->get_var($sql);
		}
		if ( empty($result) && !empty($none) ) $result = $none;
		return stripslashes($result);
	} //end function c2c_get_get_custom()
}

function c2c_get_recent_tagmap( $posts, $format, $tags, $ctags, $date_format, $echo = true ) {
	if (!$tags) return $format;
	global $authordata, $comment, $post, $customizable_post_listings;
	
	$options = $customizable_post_listings->get_options();
	$excerpt_words = $options['excerpt_words'];
	$excerpt_length = $options['excerpt_length'];
	$comment_excerpt_words = $options['comment_excerpt_words'];
	$comment_excerpt_length = $options['comment_excerpt_length'];
	$post_URL_short_words = $options['post_URL_short_words'];
	$post_URL_short_length = $options['post_URL_short_length'];
	$between_categories = $options['between_categories'];
	$between_tags = $options['between_tags'];
	$time_format = $options['time_format'];
	$comment_fancy = $options['comment_fancy'];
	$pingback_fancy = $options['pingback_fancy'];
	$trackback_fancy = $options['trackback_fancy'];

	if (!$date_format) $date_format = get_settings('date_format');
	
	// Now process the posts
	$orig_post = $post; $orig_authordata = $authordata; $orig_comment = $comment;
	foreach ($posts as $post) {
		$text = $format;
		$comment_count = ''; $pingback_count = ''; $trackback_count = ''; $allcomment_count = '';
		$authordata = '';
		$title = '';

		// If want last_comment information, then need to make a special db request
		$using_last_comment = 0;
		foreach ($tags as $tag) {
			if (strpos($tag, 'last_comment') !== false) { $using_last_comment = 1; break; }
		}
		if ($using_last_comment) {
			global $wpdb;
			$comment = $wpdb->get_row("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$post->ID' AND comment_approved = '1' AND ( comment_type = '' OR comment_type = 'comment' ) ORDER BY comment_date DESC LIMIT 1");
		}

		// Perform percent substitutions
		foreach ($ctags as $tag) {
			$new = '';
			if (strpos($tag, '%last_commenters(') !== false) {
				global $wpdb;
				preg_match("/^%last_commenters\((.+)\)%$/U", $tag, $matches);
				// This pseudo-function looks like this: %last_commenters(limit,type,more)%
				// Where:
				//	limit = number of latest commenters to list by name
				//	more = text to show after listed commenter *if* there are more commenters to the post; default is [...]
				//	between = text to show between listed commenters; default is ", "
				list($limit,$more,$between) = explode(',',$matches[1]);
				if (empty($more)) $more = '[...]';
				if (empty($between)) $between = ', ';

				$nlimit = $limit + 1;

				$comments = $wpdb->get_results("SELECT comment_author, comment_ID FROM $wpdb->comments WHERE comment_post_ID = '$post->ID' and comment_approved = '1' AND ( comment_type = '' OR comment_type = 'comment' ) ORDER BY comment_date DESC LIMIT $nlimit");
				$count = 1;
				if ($comments) :
				foreach ($comments as $cmnt) {
					if ($count > 1) $new .= $between;
					if ($count > $limit) {
						$new .= '<a href="'.get_permalink().'#comments" title="View all comments">' . $more . "</a>\n";
						break;
					}
					$new .= '<a href="'.get_permalink().'#comment-'.$cmnt->comment_ID.'" title="View comment by '.$cmnt->comment_author.'">'.$cmnt->comment_author.'</a>';
					$count++;
				}
				endif;
			} elseif (strpos($tag, '%post_custom(') !== false) {
				preg_match("/^%post_custom\((.+)\)%$/U", $tag, $matches);
				// This pseudo-function looks like this: %post_custom(field,format,none)%
				list($field,$cformat,$none) = explode(',',$matches[1]);
				$custom = c2c_get_get_custom($post->ID,$field, $none);
				if ( empty($custom) ) {
					//Do nothing
				}
				elseif ( empty($cformat) ) {
					$new = $custom;
				} else {
					$cformat = str_replace('%field%', $field, $cformat);
					$cformat = str_replace('%value%', $custom, $cformat);
					// Only call the display format handler if there's the possibility we have percent substitution tags
					if (strpos($cformat, '%') !== false)
						$cformat = c2c_get_recent_handler (array($post), $cformat, $date_format, false);
					$new = $cformat;
				}
			} elseif (strpos($tag, '%post_date(') !== false) {
				preg_match("/^%post_date\((.+)\)%$/U", $tag, $matches);
				// This pseudo-function looks like this: %post_date(F d, Y)%
				$date_format = $matches[1];
				$new = apply_filters('the_date', mysql2date($date_format, $post->post_date));
			} elseif (strpos($tag, '%post_other(') !== false) {
				preg_match("/^%post_other\((.+)\)%$/U", $tag, $matches);
				// This pseudo-function looks like this: %post_other(post_view_count)%
				$field = $matches[1];
				if (isset($post->$field)) $new = $post->$field;
			} elseif (strpos($tag, '%function(') !== false) {
				preg_match("/^%function\((.+)\)%$/U", $tag, $matches);
				// This pseudo-function looks like this: %function(function_name,arg1,arg2,...)%
				list($function, $args1) = explode(',', $matches[1], 2);
				// Have to process each argument individually
				$args1 = explode(',', $args1);
				$args = array();
				foreach ($args1 as $arg) {
					// Only call the display format handler if there's the possibility we have percent substitution tags
					if (strpos($arg, '%') !== false)
						$args[] = c2c_get_recent_handler (array($post), $arg, $date_format, false);
					else
						$args[] = $arg;
				}
				if (function_exists($function))
					$new = call_user_func_array($function, $args);
			}
			$text = str_replace($tag, $new, $text);
		}

		// Perform percent substitutions
		foreach ($tags as $tag) {
			switch ($tag) {
				case '%allcomments_count%':
					if (!$allcomment_count) { $allcomment_count = c2c_comment_count($post->ID); }
					$new = $allcomment_count;
					break;
				case '%allcomments_fancy%':
					if (!$allcomment_count) { $allcomment_count = c2c_comment_count($post->ID); }
					if ($allcomment_count < 2) $new = $comment_fancy[$allcomment_count];
					else $new = str_replace('%comments_count%', $allcomment_count, $comment_fancy[2]);
					break;
				case '%comments_count%':
					if (!$comment_count) { $comment_count = c2c_comment_count($post->ID, 'comment'); }
					$new = $comment_count;
					break;
				case '%comments_count_URL%':
					if (!$title) { $title = the_title('', '', false); }
					if (!$comment_count) { $comment_count = c2c_comment_count($post->ID, 'comment'); }
					$new = '<a href="'.get_permalink().'#comments" title="View all comments for '.wp_specialchars(strip_tags($title), 1).'">'.$comment_count.'</a>';
					break;
				case '%comments_fancy%':
				case '%comments_fancy_URL%':
					if (!$comment_count) { $comment_count = c2c_comment_count($post->ID, 'comment'); }
					if ($comment_count < 2) $new = $comment_fancy[$comment_count];
					else $new = str_replace('%comments_count%', $comment_count, $comment_fancy[2]);
					if ( '%comments_fancy_URL%' == $tag )
						$new = '<a href="'.get_permalink().'#comments" title="View all comments for '.wp_specialchars(strip_tags($title), 1).'">'.$new.'</a>';
					break;
				case '%comments_url%':
					$new = get_permalink() . "#postcomment";
					break;
				case '%comments_URL%':
					if (!$title) { $title = the_title('', '', false); }
					$new = '<a href="'.get_permalink().'#comments" title="View all comments for '.wp_specialchars(strip_tags($title), 1).'">'.$title.'</a>';
					break;
				case '%last_comment_date%':
					$new = get_comment_date($date_format);
					break;
				case '%last_comment_excerpt%':
				case '%last_comment_excerpt_URL%':
					$new = ltrim(strip_tags(apply_filters('get_comment_excerpt', $comment->comment_content)));
					if ($comment_excerpt_words) {
						$words = explode(' ', $new);
						$new = join(' ', array_slice($words, 0, $comment_excerpt_words));
						if (count($words) > $comment_excerpt_words) $new .= "...";
					} elseif ($comment_excerpt_length) {
						if (strlen($new) > $comment_excerpt_length) $new = substr($new,0,$comment_excerpt_length) . "...";
					}
					if ( '%last_comment_excerpt_URL%' == $tag )
						$new = '<a href="'.get_permalink().'#comment-'.$comment->comment_ID.'">'.$new.'</a>';
					break;
				case '%last_comment_id%':
					$new = get_comment_ID();
					break;
				case '%last_comment_time%':
					$new = get_comment_time($time_format);
					break;
				case '%last_comment_url%':
					$new = get_permalink().'#comment-'.$comment->comment_ID;
					break;
				case '%last_commenter%':
					$new = apply_filters('comment_author', get_comment_author());
					break;
				case '%last_commenter_URL%':
					$new = get_comment_author_link();
					break;
				case '%pingbacks_count%':
					if (!$pingback_count) { $pingback_count = c2c_comment_count($post->ID, 'pingback'); }
					$new = $pingback_count;
					break;
				case '%pingbacks_fancy%':
					if (!$pingback_count) { $pingback_count = c2c_comment_count($post->ID, 'pingback'); }
					if ($pingback_count < 2) $new = $pingback_fancy[$pingback_count];
					else $new = str_replace('%pingbacks_count%', $pingback_count, $pingback_fancy[2]);
					break;
				case '%post_author%':
					if (!$authordata) { $authordata = get_userdata($post->post_author); }
					$new = get_the_author();
					break;
				case '%post_author_count%':
					$new = get_the_author_posts();
					break;
				case '%post_author_description%':
					if (!$authordata) { $authordata = get_userdata($post->post_author); }
					$new = get_the_author_description();
					break;
				case '%post_author_email%':
					if (!$authordata) { $authordata = get_userdata($post->post_author); }
					$new = apply_filters('the_author_email', get_the_author_email());
					break;
				case '%post_author_firstname%':
					if (!$authordata) { $authordata = get_userdata($post->post_author); }
					$new = get_the_author_firstname();
					break;
				case '%post_author_id%':
					$new = $post->post_author;
					break;
				case '%post_author_lastname%':
					if (!$authordata) { $authordata = get_userdata($post->post_author); }
					$new = get_the_author_lastname();
					break;
				case '%post_author_login%':
					if (!$authordata) { $authordata = get_userdata($post->post_author); }
					$new = get_the_author_login();
					break;
				case '%post_author_nickname%':
					if (!$authordata) { $authordata = get_userdata($post->post_author); }
					$new = get_the_author_nickname();
					break;
				case '%post_author_posts%':
					if (!$authordata) { $authordata = get_userdata($post->post_author); }
					$new = '<a href="'.get_author_link(0, $authordata->ID, $authordata->user_nicename).'" title="';
					$new .= sprintf(__("Posts by %s"), wp_specialchars(get_the_author(), 1)).'">'.stripslashes(get_the_author()).'</a>';
					break;
				case '%post_author_url%':
					if (!$authordata) { $authordata = get_userdata($post->post_author); }
					if (get_the_author_url())
						$new = '<a href="'.get_the_author_url().'" title="Visit '.get_the_author().'\'s site" rel="external">'.get_the_author().'</a>';
					else
						$new = get_the_author();
					break;
				case '%post_categories%':
				case '%post_categories_URL%':
					$cats = get_the_category($post->ID);
					$new = '';
					if ($cats) {
						if ('%post_categories_URL%' == $tag)
							$new .= '<a href="' . get_category_link($cats[0]->cat_ID) . '" title="View archive for category">';
						$new .= $cats[0]->cat_name;
						if ('%post_categories_URL%' == $tag) $new .= '</a>';
						for ($i = 1; $i < (count($cats)); $i = $i + 1) {
							$new .= $between_categories;
							if ('%post_categories_URL%' == $tag)
								$new .= '<a href="' . get_category_link($cats[$i]->cat_ID) . '" title="View archive for category">';
							$new .= $cats[$i]->cat_name;
							if ('%post_categories_URL%' == $tag) $new .= '</a>';
						}
					}
					break;
				case '%post_content%':
				case '%post_content_full%':
					$new = apply_filters('the_content', $post->post_content);
					if ( '%post_content_full%' != $tag ) $new = str_replace(array('<p>','</p>','<br />'), '', $new);
					break;
				case '%post_content_upto_more%' :
					$content = apply_filters('the_content', $post->post_content);
					$content = explode('<!--more-->', $content, 2);
					$new = $content[0];
					break;
				case '%post_date%':
					$new = apply_filters('the_date', mysql2date($date_format, $post->post_date));
					break;
				case '%post_excerpt%':
				case '%post_excerpt_full%':
					$new = apply_filters('the_excerpt', get_the_excerpt());
					if ( '%post_excerpt_full%' != $tag ) $new = str_replace(array('<p>','</p>','<br />'), '', $new);
					break;
				case '%post_excerpt_short%':
					$new = apply_filters('the_excerpt', get_the_excerpt());
					if ($excerpt_words) {
  						$words = explode(' ', $new);
  						$new = join(' ', array_slice($words, 0, $excerpt_words));
  						if (count($words) > $excerpt_words) $new .= "...";
					} elseif ($excerpt_length) {
   						if (strlen($new) > $excerpt_length) $new = substr($new,0,$excerpt_length) . "...";
					}
					break;
				case '%post_guid%':
					$new = apply_filters('post_guid', $post->guid);
					break;
				case '%post_id%':
					$new = $post->ID;
					break;
				case '%post_lat%':
					$new = apply_filters('post_lat', $post->post_lat);
					break;
				case '%post_lon%':
					$new = apply_filters('post_lon', $post->post_lon);
					break;
				case '%post_modified%':
					$new = mysql2date($date_format, $post->post_modified);
					break;
				case '%post_name%':
					$new = apply_filters('post_name', $post->post_name);
					break;
				case '%post_status%':
					$new = apply_filters('post_status', $post->post_status);
					break;
				case '%post_tags%':
				case '%post_tags_URL%':
					$post_tags = get_the_tags($post->ID);
					$new = '';
					if ($post_tags) {
						$do_link = ('%post_tags_URL%' == $tag) ? true : false;
						$ptags = array();
						foreach ($post_tags as $ptag) {
							$tmp = '';
							if ($do_link) $tmp .= '<a href="' . get_tag_link($ptag->term_id) . '" title="View archive for tag">';
							$tmp .= $ptag->name;
							if ($do_link) $tmp .= '</a>';
							$ptags[] = $tmp;
						}
						$new = implode($between_tags, $ptags);
					}
					break;
				case '%post_time%':
					$new = apply_filters('get_the_time', get_post_time($time_format));
					break;
				case '%post_title%':
					if (!$title) { $title = the_title('', '', false); }
					$new = $title;
					break;
				case '%post_type%':
					$new = apply_filters('post_type', $post->post_type);
					break;
				case '%post_url%':
					$new = get_permalink();
					break;
				case '%post_URL%':
				case '%post_URL_short%':
					if (!$title) { $title = the_title('', '', false); }
					if ('%post_URL_short%' != $tag)
						$ntitle = $title;
					else {
						$ntitle = strip_tags($title);
						if ($post_URL_short_words) {
							$words = explode(' ', $ntitle);
							$ntitle = join(' ', array_slice($words, 0, $post_URL_short_words));
							if (count($words) > $post_URL_short_words) $ntitle .= "...";
						} elseif ($post_URL_short_length) {
							if (strlen($ntitle) > $post_URL_short_length) $ntitle = substr($ntitle,0,$post_URL_short_length) . "...";
						}
					}
					$new = '<a href="'.get_permalink().'" title="View post '.wp_specialchars(strip_tags($title), 1).'">'.$ntitle.'</a>';
					break;
				case '%trackbacks_count%':
					if (!$trackback_count) { $trackback_count = c2c_comment_count($post->ID, 'trackback'); }
					$new = $trackback_count;
					break;
				case '%trackbacks_fancy%':
					if (!$trackback_count) { $trackback_count = c2c_comment_count($post->ID, 'trackback'); }
					if ($trackback_count < 2) $new = $trackback_fancy[$trackback_count];
					else $new = str_replace('%trackbacks_count%', $trackback_count, $trackback_fancy[2]);
					break;
			}
			$text = str_replace($tag, $new, $text);
		}
		if ($echo) echo $text . "\n";
	}
	$post = $orig_post; $authordata = $orig_authordata; $comment = $orig_comment;
	return ($echo ? count($posts) : $text);
} // end function c2c_get_recent_tagmap()

function c2c_get_recent_handler( $posts, $format = '', $date_format = '', $echo = true ) {
	if (!$format) { return $posts; }
	if (!is_array($posts)) $posts = array($posts);

	// Determine the format of the listing
	$percent_tags = array(
		"%allcomments_count%",	// Number of comments + pingbacks + trackbacks for post
		"%allcomments_fancy%",	// Fancy reporting of allcomments
		"%comments_count%",	// Number of comments for post
		"%comments_count_URL%",	// Count of number of comments linked to the top of the comments section
		"%comments_fancy%",	// Fancy reporting of comments: (see get_recent_tagmap())
		"%comments_fancy_URL%",	// Fancy reporting of comments linked to comments section
		"%comments_url%", 	// URL to top of comments section for post
		"%comments_URL%",	// Post title linked to the top of the comments section on post's permalink page
		"%last_comment_date%",  // Date of last comment for post
		"%last_comment_excerpt%",	// Excerpt of contents for last comment to post
		"%last_comment_excerpt_URL%",	// Excerpt of contents for last comment to post linked to that comment
		"%last_comment_id%",	// ID for last comment for post
		"%last_comment_time%",	// Time of last comment for post
		"%last_comment_url%",	// URL to most recent comment for post
		"%last_commenter%",	// Author of last comment for post
		"%last_commenter_URL%", // Linked (if author URL provided) of author of last comment for post
		"%pingbacks_count%",	// Number of pingbacks for post
		"%pingbacks_fancy%",	// Fancy report of trackbacks
		"%post_author%",	// Author for post (preferred display name)
		"%post_author_count%",  // Number of posts made by post author
		"%post_author_description%", // Post author's description
		"%post_author_email%", // Post author's email address
		"%post_author_firstname%", // Post author's first name
		"%post_author_id%",		// ID of post author
		"%post_author_lastname%", // Post author's last name
		"%post_author_login%", // Post author's login name
		"%post_author_nickname%", // Post author's nickname
		"%post_author_posts%",  // Link to page of all of post author's posts
		"%post_author_url%",    // Linked (if URL provided) name of post author
		"%post_categories%",	// Name of each of post's categories
		"%post_categories_URL%",// Name of each of post's categories linked to respective category archive
		"%post_content%",	// Full content of the post (<p> and <br> tags stripped)
		"%post_content_full%",	// Full content of the post (<p> and <br> tags intact)
		"%post_content_upto_more%", // Content of the post up to the <!--more--> separator; nothing displayed if 'more' isn't present
		"%post_date%",		// Date for post
		"%post_excerpt%",	// Excerpt for post (<p> and <br> tags stripped)
		"%post_excerpt_full%",	// Excerpt for post (<p> and <br> tags intact)
		"%post_excerpt_short%",	// Customizably shorter excerpt, suitable for sidebar usage
		"%post_guid%",		// Post GUID
		"%post_id%",		// ID for post
		"%post_lat%",		// Post latitude
		"%post_lon%",		// Post longitude
		"%post_modified%",	// Last modified date for post
		"%post_name%",		// Post name (aka slug)
		"%post_status%",	// Post status for post
		"%post_tags%",		// Name of each of post's tags
		"%post_tags_URL%",	// Name of each of post's tags linked to respective tag archive
		"%post_time%",		// Time for post
		"%post_title%",		// Title for post
		"%post_type%",		// Post type of post
		"%post_url%",		// URL for post
		"%post_URL%",		// Post title linked to post's permalink page
		"%post_URL_short%",	// Customizably shorter post title linked to post's permalink page
		"%trackbacks_count%",	// Number of trackbacks for post
		"%trackbacks_fancy%",	// Fancy reporting of trackbacks
	);
	$ptags = array();
	foreach ($percent_tags as $tag) { if (strpos($format, $tag) !== false) $ptags[] = $tag; }
	$cptags = array();
	$custom_percent_tags = array(
		"%function\(.+\)%",		// Invocation of a function, used like this: %function(function_name,arg1,arg2,...)% where args can contain %-tags
		"%last_commenters\(.+\)%",	// List of last commenters by name, linked to their comment, used like this: %last_commenters(limit,more,between)%
		"%post_custom\(.+\)%",	// Custom field for post, used like this: %post_custom(field,format,none)%, where format can contain %field% and/or %value%
		"%post_date\(.+\)%", // The post date formatted in a specified manner, used like this: %post_date(F d, Y)%
		"%post_other\(.+\)%",	// Other unaddressed post table field, used like this: %post_other(post_view_count)%
	);
	foreach ($custom_percent_tags as $tag) { if (preg_match_all("/$tag/imsU", $format, $matches)) { $cptags = array_merge($cptags, $matches[0]); } }
	return c2c_get_recent_tagmap($posts, $format, $ptags, $cptags, $date_format, $echo);
} //end function c2c_get_recent_handler()

?>