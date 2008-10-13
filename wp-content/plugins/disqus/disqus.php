<?php
/*
Plugin Name: Disqus Comment System
Plugin URI: http://disqus.com/
Description: <a href="edit-comments.php?page=disqus">Click Here to Manage Settings</a> (if activated).  The Disqus Comment System replaces your oridinary WordPress comments with your comments hosted and powered by Disqus.  Head over to the Comments admin page to set up your Disqus Comment System.
Author: Disqus.com <wordpress@disqus.com>
Version: 1.04
Author URI: http://disqus.com/
*/

define('DISQUS_URL',		'http://disqus.com');
define('DISQUS_DOMAIN',		'disqus.com');
define('DISQUS_RSS_PATH',	'/latest.rss');

	/* Template tags */
	
	function disqus_recent_comments($num_comments = 5, $display_message = false) {
		$fp = fopen('http://' . get_option('disqus_forum_url') . '.' . DISQUS_DOMAIN . "/~get_recent_comments/?num_comments=$num_comments", 'r');
		$buffer = '';
		
		while ( !feof($fp) ) {
			$buffer .= fgets($fp);
		}
		fclose($fp);
		
		$comments = explode("\n", $buffer);
		foreach ( $comments as $comment ) {
			if ( $comment ) {
				$comment = explode(',', $comment, 7);
				echo '<li style="padding-bottom: 5px">'
					. ($comment[1] ? "<a href='" . DISQUS_URL . "/users/$comment[1]/' title='View $comment[1]&#39;s profile.'>" : "")
					. $comment[0] . ($comment[1] ? "</a>" : "")
					. " on "
					. ($comment[4] ? "<a href='$comment[4]" . (strpos($comment[4], '?') === false ? '?' : '&') . "disqus_reply=$comment[5]'>" : '')
					. $comment[2]
					. ($comment[4] ? '</a>' : '')
					. ($display_message ? "<br /><blockquote class='disqus-recent-blockquote'>" . substr($comment[6], 0, 75) . " ...</blockquote>" : '')
					. "<br /><em>$comment[3] ago"
					. "</li>"
				;
			}
		}
	}
	
	/* Filters/Actions */
	 
	function dsqs_can_replace() {
		global $id, $post;
		$replace = get_option('disqus_replace');
		
		if ( !isset($post->comment_count) ) {
			$num_comments = 0;
		} else {
			if ( 'empty' == $replace ) {
				// Only get count of comments, not including pings.
				
				// If there are comments, make sure there are comments (that are not track/pingbacks)
				if ($post->comment_count > 0 ) {	
					// Yuck, this causes a DB query for each post.  This can be
					// replaced with a lighter query, but this is still not optimal.
					$comments = get_approved_comments($post->ID);
					foreach ( $comments as $comment ) {
						if ( $comment->comment_type != 'trackback' && $comment->comment_type != 'pingback' ) {
							$num_comments++;
						}
					}
				} else {
					$num_comments = 0;
				}
			}
			else {
				$num_comments = $post->comment_count;
			}
		}
		
		return ( 'all' == $replace || ('empty' == $replace && 0 == $num_comments)
			|| ('closed' == $replace && 'closed' == $post->comment_status) );
	}
	
	function dsqs_comments_template($value) {
		if ( ! (is_single() || is_page() || $withcomments) ) {
			return;
		}
		
		if ( dsqs_can_replace() ) {
			return dirname(__FILE__) . '/comments.php';
		} else {
            return $value;
        }
	}
	
	function dsqs_wp_footer() {
		global $site_url;
		
		if ( (is_single() || is_page() || $withcomments) ) {
			return;
		}
		
		?>
		
		<script type="text/javascript">
		(function() {
			var links = document.getElementsByTagName('a');
			var query = '?';
			for(var i = 0; i < links.length; i++) {
				if(links[i].href.indexOf('#disqus_thread') >= 0) {
					links[i].innerHTML = 'View Comments';
					query += 'url' + i + '=' + encodeURIComponent(links[i].href) + '&';
				}
			}
			document.write('<script type="text/javascript" src="<?php echo DISQUS_URL ?>/forums/<?php echo get_option('disqus_forum_url'); ?>/get_num_replies.js' + query + '"><' + '/script>');
		}());
		</script>
		
		<?php
	}

	function dsqs_add_pages() {
		global $menu, $submenu;
		
		add_submenu_page('edit-comments.php', 'Disqus', 'Disqus', 8, 'disqus', dsqs_manage);
		
		// Replace Comments top-level menu link with link to our page
		foreach ( $menu as $key => $value ) {
			if ( 'edit-comments.php' == $menu[$key][2] ) {
				$menu[$key][2] = 'edit-comments.php?page=disqus';
			}
		}
	}
	
	function dsqs_manage() {
		require_once('admin-header.php');
		
		if ( isset($_POST['disqus_forum_url']) ) {
			// TODO: Do validation on the forum URL (make sure they don't include the .disqus.com)
		    update_option('disqus_forum_url', $_POST['disqus_forum_url']);
		    echo '<div id="message" class="updated fade"><p>Your settings have been changed.</p></div>';
		}
		
		if ( isset($_POST['disqus_replace']) ) {
			update_option('disqus_replace', $_POST['disqus_replace']);
		}
		
		$disqus_replace = get_option('disqus_replace');
		if ( empty($disqus_replace) ) {
			$disqus_replace = 'all';
		}
		?>
		
		<form method="POST">
		<div class="wrap">
			<ol style="padding:0px;margin:0px">
				<h2>Disqus Comments</h2>
				<li style="margin-left: 20px">Disqus Forum URL:
					<p>http://<input type="text" name="disqus_forum_url" value="<?php echo get_option('disqus_forum_url'); ?>"/>.disqus.com</p>
				<li style="margin-left: 20px">Choose an option
					<ul style="list-style-type:none;">
						<li><label for="disqus_replace_all"><input type="radio" id="disqus_replace_all" name="disqus_replace" value="all" <?php if('all'==$disqus_replace){echo 'checked';}?>/>&nbsp;Replace comments on all posts</label>
						<li><label for="disqus_replace_empty"><input type="radio" id="disqus_replace_empty" name="disqus_replace" value="empty" <?php if('empty'==$disqus_replace){echo 'checked';}?>/>&nbsp;Replace all entries with no comments (including future posts)</label>
						<li><label for="disqus_replace_closed"><input type="radio" id="disqus_replace_closed" name="disqus_replace" value="closed" <?php if('closed'==$disqus_replace){echo 'checked';}?>/>&nbsp;Replace comments only on entries with closed comments.</label>
					</ul>
				</p>
				<input type="submit" value="Save" />
		</div>
		</form>
		<?php
	}
	
	function dsqs_get_comments_number($num_comments) {
		$replace = get_option('disqus_replace');
		
		// HACK: Don't allow $num_comments to be 0.  If we're only replacing
		// closed comments, we don't care about the value. For
		// comments_popup_link();
		if ( $replace != 'closed' && 0 == $num_comments ) {
			return -1;
		} else {
			return $num_comments;
		}
	}
	
	// Mark entries in index to replace comments link.
	function dsqs_comments_number($comment_text) {
		if ( dsqs_can_replace() ) {
			ob_start();
			the_permalink();
			$the_permalink = ob_get_contents();
			ob_end_clean();

            // TODO: Put text in the last link (it's an empty tag right now).
			return '</a><noscript><a href="http://' . get_option('disqus_forum_url') . '.' . DISQUS_DOMAIN . '/?url=' . $the_permalink .'">View comments</a></noscript><a href="' . $the_permalink . '#disqus_thread"></a>';
		} else {
			return $comment_text;
		}
	}

	// For WordPress 2.0.x
	function dsqs_loop_start() {
		global $comment_count_cache;
		
		if ( isset($comment_count_cache) ) {
			foreach ( $comment_count_cache as $key => $value ) {
				if ( 0 == $value ) {
					$comment_count_cache[$key] = -1;
				}
			}
		}
	}
	
	function dsqs_bloginfo_url($url) {
		if ( get_feed_link('comments_rss2') == $url ) {
			return 'http://' . get_option('disqus_forum_url') . '.' . DISQUS_DOMAIN . DISQUS_RSS_PATH;
		} else {
			return $url;
		}
	}

	// Always add Disqus management page to the admin menu
	add_action('admin_menu', 'dsqs_add_pages');

	if ( !get_option('disqus_forum_url') && !isset($_POST['forum_url']) ) {
		function disqus_warning() {
			echo '<div id="disqus_warning" class="updated fade-ff0000"><p><strong>You must <a href="edit-comments.php?page=disqus">add your Disqus forum URL</a> to enable the Disqus Comment System.</strong></p></div>';
			echo '<style type="text/css">#submenu{margin-bottom:5em;}#disqus_warning{position:absolute;top:9em;}</style>';
		}
		
		add_action('admin_footer', 'disqus_warning');
	} else {
		// Only replace comments if the disqus_forum_url option is set.
		add_filter('comments_number', 'dsqs_comments_number');
		add_filter('bloginfo_url', 'dsqs_bloginfo_url');
		add_filter('get_comments_number', 'dsqs_get_comments_number');
		add_filter('comments_template', 'dsqs_comments_template');
		add_action('wp_footer', 'dsqs_wp_footer');
		add_action('loop_start', 'dsqs_loop_start');
	}
?>
