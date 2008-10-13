<script src="<?php bloginfo('template_url'); ?>/revolution_music/javascript/tabber.js" type="text/javascript"></script>
<script type="text/javascript">
document.write('<style type="text/css">.tabber{display:none;}<\/style>');
var tabberOptions = {manualStartup:false};
function begForMoney()
{
  if (!arguments.callee.stopBegging) {
    arguments.callee.stopBegging = true;
  }
}
</script>

<script type="text/javascript"><!--//--><![CDATA[//><!--
sfHover = function() {
	if (!document.getElementsByTagName) return false;
	var sfEls = document.getElementById("nav").getElementsByTagName("li");

	// if you only have one main menu - delete the line below //
	var sfEls1 = document.getElementById("subnav").getElementsByTagName("li");
	//

	for (var i=0; i<sfEls.length; i++) {
		sfEls[i].onmouseover=function() {
			this.className+=" sfhover";
		}
		sfEls[i].onmouseout=function() {
			this.className=this.className.replace(new RegExp(" sfhover\\b"), "");
		}
	}

	// if you only have one main menu - delete the "for" loop below //
	for (var i=0; i<sfEls1.length; i++) {
		sfEls1[i].onmouseover=function() {
			this.className+=" sfhover1";
		}
		sfEls1[i].onmouseout=function() {
			this.className=this.className.replace(new RegExp(" sfhover1\\b"), "");
		}
	}
	//

}
if (window.attachEvent) window.attachEvent("onload", sfHover);
//--><!]]></script>

<?php
wp_enqueue_script('jd-gallery');
//wp_enqueue_script('jquery-carousel');
get_header();
?>
<link href="http://test.radioverve.com/new/wp-includes/css/skin.css" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/jd.gallery.css" type="text/css" media="screen" charset="utf-8"/>


<div id="content">

	<div id="homepagetop">
		
			<div class="hptabber">

				<!--This section is where the Featured Content plugin is called.
					
					Sunday, 12th October, 2008. The plugin has been edited.
				-->
				
				<?php include (ABSPATH . '/wp-content/plugins/content-gallery/gallery.php'); ?>
			
			</div>
			
			<div class="frontplayer">
				<h3 class="frontplayerh3">On Air</h3>
				<!--
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
									id="RadioVeRVePlayer" width="100%" height="100%"
									codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab">
									<param name="movie" value="/player/rvfrontpage/RVFrontPage.swf" />
									<param name="quality" value="high" />
									<param name="bgcolor" value="#FFFFFF" />
									<param name="allowScriptAccess" value="sameDomain" />
									<embed src="/player/rvfrontpage/RVFrontPage.swf" quality="high" bgcolor="#FFFFFF"
											width="345" height="279" name="RadioVeRVePlayer" align="middle"
											play="true"
											loop="false"
											quality="high"
											allowScriptAccess="sameDomain"
											type="application/x-shockwave-flash"
											pluginspage="http://www.adobe.com/go/getflashplayer">
									</embed>
					</object>
-->	

					<ul id="nowPlaying">

						<li>						
							<div class="channelClip">
								<a href="#"><img src="/scripts/images/rock.gif" width="65" height="65" border="0" alt="" /></a>
							</div>
	
							<div class="channelText">
								<div class="channelName"><a href="#">Rock</a></div>
								<div class="channelTrack">
									<a href="#">Artist</a> - <a href="#">Title</a>
								</div>
							</div>
	
						</li>	
						<li>						
							<div class="channelClip">
								<a href="#"><img src="/scripts/images/metal.gif" width="65" height="65" border="0" alt="" /></a>
							</div>
	
							<div class="channelText">
								<div class="channelName"><a href="#">Rock</a></div>
								<div class="channelTrack">
									<a href="#">Artist</a> - <a href="#">Title</a>
								</div>
							</div>
	
						</li>
						<li>						
							<div class="channelClip">
								<a href="#"><img src="/scripts/images/konkani.gif" width="65" height="65" border="0" alt="" /></a>
							</div>
	
							<div class="channelText">
								<div class="channelName"><a href="#">Rock</a></div>
								<div class="channelTrack">
									<a href="#">Artist</a> - <a href="#">Title</a>
								</div>
							</div>
	
						</li>	
					
					</ul>
			</div>
			
			
		</div>

	<div id="homepage">
					
		<div id="homepageleft">
		
			<!--This section is currently pulling category ID #1, and can be switched by changing the cat=1 to show whatever category ID you would like in this area.-->
				
			<div class="featured">
			<h3>Featured Artists</h3>
			
				<!--This is where the thumbnails are found for the homepage bottom section - note the custom field name for this image is "thumbnail". Recommended image size is 70x70, as the stylesheet is written for this size.-->
				<?php
					$featured_artists = gnrb_get_bands_featured();
					foreach($featured_artists as $featured_artist){
						if($featured_artist["image"]!=""||$featured_artist["image"]=="http://test.radioverve.com/new/band_pics/"){?>
							<a href="<?php echo $featured_artist["url"]; ?>" rel="bookmark"><img width="100px" style="float:left;margin:0px 10px 0px 0px;" src="<?php echo $featured_artist["image"]; ?>" alt="<?php echo $featured_artist["name"]; ?>" /></a>
						<?php }else{?>
							<a href="<?php echo $featured_artist["url"]; ?>" rel="bookmark"><img width="100px" style="float:left;margin:0px 10px 0px 0px;" src="<?php bloginfo('template_url'); ?>/images/thumbnail.png" alt="<?php echo $featured_artist["name"]; ?>" /></a>							
						<?php }?>
						<div><b><a href="<?php echo $featured_artist["url"]; ?>" rel="bookmark"><?php echo $featured_artist["name"]; ?></a></b></div>
						<?php echo $featured_artist["genre"]; ?>
						<div style="border-bottom:1px dotted #94B1DF; margin-bottom:10px; padding:0px 0px 10px 0px; clear:both;"></div>
					<?php } ?>
				
				<!--This is where you can specify the archive link for each section. Replace the # with the appropriate URL-->
				
				<b><a href="<?php bloginfo('url'); ?>/artists" rel="bookmark">Discover new artists</a></b>
				
			</div>			
				
		</div>
		
		<div id="homepageright">
		
			<!--This section is currently pulling category ID #1, and can be switched by changing the cat=1 to show whatever category ID you would like in this area.-->
		
			<div class="featured">
			<h3>Upcoming Events</h3>
			
				<!--This is where the thumbnails are found for the homepage bottom section - note the custom field name for this image is "thumbnail". Recommended image size is 70x70, as the stylesheet is written for this size.-->
				<?php $featured_events = gigpress_frontpage_events(); ?>
				<?php foreach($featured_events as $featured_event){ ?>
				<?php if($featured_event->show_image!=""){?>
							<a href="<?php echo $featured_event->show_id; ?>" rel="bookmark"><img width="100px" style="float:left;margin:0px 10px 0px 0px;" src="<?php echo get_option("gnrb_band_pics_path") .$featured_event->show_image; ?>" alt="<?php echo $featured_event->show_title; ?>" /></a>
						<?php }else{?>
							<a href="<?php echo $featured_event->show_id; ?>" rel="bookmark"><img width="100px" style="float:left;margin:0px 10px 0px 0px;" src="<?php bloginfo('template_url'); ?>/images/thumbnail.png" alt="<?php echo $featured_event->show_title; ?>" /></a>							
						<?php }?>
						<div><b><a href="<?php echo $featured_event->show_id; ?>" rel="bookmark"><?php echo $featured_event->show_title; ?></a></b></div>
						<div><?php echo $featured_event->show_venue; ?></div>
						<?php echo $featured_event->show_locale; ?>
						<div style="border-bottom:1px dotted #94B1DF; margin-bottom:10px; padding:0px 0px 10px 0px; clear:both;"></div>
				<?php } ?>
			
				<!--<?php $recent = new WP_Query("cat=1&showposts=3"); while($recent->have_posts()) : $recent->the_post();?>
				<?php if( get_post_meta($post->ID, "thumbnail", true) ): ?>
				    <a href="<?php the_permalink() ?>" rel="bookmark"><img style="float:left;margin:0px 10px 0px 0px;" src="<?php echo get_post_meta($post->ID, "thumbnail", true); ?>" alt="<?php the_title(); ?>" /></a>
				<?php else: ?>
				   	<a href="<?php the_permalink() ?>" rel="bookmark"><img style="float:left;margin:0px 10px 0px 0px;"  src="<?php bloginfo('template_url'); ?>/images/thumbnail.png" alt="<?php the_title(); ?>" /></a>
				<?php endif; ?>				
				<b><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></b>				
				<?php the_content_limit(80, ""); ?>
								
				<div style="border-bottom:1px dotted #94B1DF; margin-bottom:10px; padding:0px 0px 10px 0px; clear:both;"></div>
				
				<?php endwhile; ?>-->
				
				<!--This is where you can specify the archive link for each section. Replace the # with the appropriate URL-->
				<b><a href="<?php bloginfo('url'); ?>/artists" rel="bookmark">More Events Dude!</a></b>				
			</div>		
			
		</div>
		
		<div id="homepagebottom">
		
			<div class="hpbottom">
			
				<h3>Articles</h3>
	
				<!--This is where the thumbnails are found for the homepage bottom section - note the custom field name for this image is "hpbottom". Recommended image size is 70x70, as the stylesheet is written for this size.-->
				
				<?php $recent = new WP_Query("showposts=5"); while($recent->have_posts()) : $recent->the_post();?>
				<?php if( get_post_meta($post->ID, "hpbottom", true) ): ?>
				    <a href="<?php the_permalink() ?>" rel="bookmark"><img style="float:left;margin:0px 10px 0px 0px;" src="<?php echo get_post_meta($post->ID, "hpbottom", true); ?>" alt="<?php the_title(); ?>" /></a>
				<?php else: ?>
				   	<a href="<?php the_permalink() ?>" rel="bookmark"><img style="float:left;margin:0px 10px 0px 0px;"  src="<?php bloginfo('template_url'); ?>/images/thumbnail-articles.jpg" alt="<?php the_title(); ?>" /></a>
				<?php endif; ?>				
				<b><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></b>
				<?php the_content_limit(350, "[Read more of this review]"); ?>
					
				<div style="border-bottom:1px dotted #2255AA; margin-bottom:10px; padding:0px 0px 10px 0px; clear:both;"></div>
					
				<?php endwhile; ?>
	
				<!--This is where you can specify the archive link for each section. Replace the # with the appropriate URL-->
				
				<b><a href="<?php bloginfo('url'); ?>/articles" rel="bookmark">More Articles!</a></b>				
	
			</div>
		
		</div>
		
	</div>
	
<?php include(TEMPLATEPATH."/revolution_music/sidebar_home.php");?>

<div style="clear:both;"></div>
		
</div>

<!-- The main column ends  -->

<?php get_footer(); ?>