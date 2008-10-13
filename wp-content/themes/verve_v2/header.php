<!--Page START-->
<!--Header START-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?> <?php wp_title(); ?></title>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />


<!--Doubt:Dont know if our page will need the block below: START-->
<style type="text/css" media="screen">

<?php
// Checks to see whether it needs a sidebar or not
if ( !empty($withcomments) && !is_single() ) {
?>
	#page { background: url("<?php bloginfo('stylesheet_directory'); ?>/images/kubrickbg-<?php bloginfo('text_direction'); ?>.jpg") repeat-y top; border: none; }
<?php } else { // No sidebar ?>
	#page { background: url("<?php bloginfo('stylesheet_directory'); ?>/images/kubrickbgwide.jpg") repeat-y top; border: none; }
<?php } ?>
</style>
<!--Doubt END-->

<?php wp_head(); ?>
</head>
<body id="home">

	<div id="container">
		<div id="wrapper">
			<div id="header">
				<a href="<?php echo get_option('home'); ?>/" id="logo"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/logoRadioVerve.png" width="282" height="101" alt="<?php bloginfo('name'); ?>" /></a>
				<div id="nav">
					<ul>
						<li id="navHome"><a href="#">Home</a></li>
						<li id="navArtists"><a href="#">Artists</a></li>
						<li id="navArticles"><a href="#">Articles</a></li>
						<li id="navEvents"><a href="#">Events</a></li>
						<li id="navPodcasts"><a href="#">Podcasts</a></li>
					</ul>
				</div>
<!--Header END-->
