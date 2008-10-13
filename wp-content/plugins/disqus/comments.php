<?php
	define('ALLOWED_HTML', '<b><u><i><h1><h2><h3><code><blockquote><br><hr>');
	
	ob_start();
	the_permalink();
	$the_permalink = ob_get_contents();
	ob_end_clean();
	$the_permalink = str_replace("'", "%27", $the_permalink);
	$the_permalink = str_replace("\r", "%0D", $the_permalink);
	$the_permalink = str_replace("\n", "%0A", $the_permalink);

	ob_start();
	the_title();
	$the_title = ob_get_contents();
	ob_end_clean();
	$the_title = str_replace("'", "%27", $the_title);
	$the_title = str_replace("\r", "%0D", $the_title);
	$the_title = str_replace("\n", "%0A", $the_title);
	$the_title = strip_tags($the_title, ALLOWED_HTML);

	ob_start();
	the_excerpt();
	$the_excerpt = ob_get_contents();
	ob_end_clean();
	$the_excerpt = str_replace("'", "%27", $the_excerpt);
	$the_excerpt = str_replace("\r", "%0D", $the_excerpt);
	$the_excerpt = str_replace("\n", "%0A", $the_excerpt);
	$the_excerpt = strip_tags($the_excerpt, ALLOWED_HTML);
?>
<div id="disqus_thread"></div>
<script type="text/javascript">
	var disqus_url = '<?php echo $the_permalink; ?> ';
	var disqus_title = '<?php echo $the_title; ?>';
	var disqus_message = '<?php echo $the_excerpt; ?>';
</script>
<script type="text/javascript" src="<?php echo DISQUS_URL; ?>/forums/<?php echo get_option('disqus_forum_url'); ?>/embed.js"></script>
<noscript><a href="<?php echo 'http://' . get_option('disqus_forum_url') . '.' . DISQUS_DOMAIN . '/?url=' . $the_permalink; ?>">View the entire comment thread.</a></noscript>