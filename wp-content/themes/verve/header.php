<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta name="distribution" content="global" />
<meta name="robots" content="follow, all" />
<meta name="language" content="en, sv" />

<title><?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' :'; } ?> <?php bloginfo('name'); ?></title>
<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" />
<!-- leave this for stats please -->

<link rel="Shortcut Icon" href="<?php echo get_settings('home'); ?>/wp-content/themes/revolution_music-10/images/favicon.ico" type="image/x-icon" />
<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php wp_get_archives('type=monthly&format=link'); ?>
<?php wp_head(); ?>

<link type="text/css" media="screen" rel="stylesheet" href="<?php bloginfo('url'); ?>/wp-content/themes/verve/revolution_music/revolution-style.css" />
<link type="text/css" media="screen" rel="stylesheet" href="<?php bloginfo('template_url'); ?>/revolution_music/tab.css"  />

</head>

<body>

<div id="wrap">

<div id="header">

	<div class="wrapper">
		
	<div class="headerleft">
		<a href="<?php echo get_settings('home'); ?>/"><img src="<?php bloginfo('template_url'); ?>/images/logoRadioVerve.png" alt="<?php bloginfo('description'); ?>" /></a>
	</div>
		
	<div class="headerright">
		<?php 
			if (is_user_logged_in()){
				echo "		
					<ul>
						<li>Hi, user</li>
					</ul>
				";
			}
		?>
		
		<?php $ad_468 = get_option('revmusic_ad_468'); echo stripslashes($ad_468); ?>
	</div>
	
	</div>


</div>

<div id="navbar">

	<div id="navbarleft">
		<ul id="nav">
			<li><a href="<?php echo get_settings('home'); ?>">Home</a></li>
                        <li><a href="<?php bloginfo('url'); ?>/radio">Radio</a></li>
			<li><a href="<?php bloginfo('url'); ?>/artists">Artists</a></li>
			<li><a href="<?php bloginfo('url'); ?>/articles">Articles</a></li>
			<li><a href="<?php bloginfo('url'); ?>/events">Events</a></li>
			<li><a href="<?php bloginfo('url'); ?>/interviews">Interviews</a></li>
		</ul>
	</div>
	
	<div id="navbarright">
		<form id="searchform" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<input type="text" value="Search this website..." name="s" id="s" onfocus="if (this.value == 'Search this website...') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Search this website...';}" />
		<input type="submit" id="sbutt" value="GO" /></form>
	</div>
	
</div>

<div style="clear:both;"></div>

<div style="clear:both;"></div>