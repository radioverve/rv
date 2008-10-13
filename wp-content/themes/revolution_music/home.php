<?php get_header(); ?>

<div id="content">

	<div id="homepage">
	
		<div id="homepagetop">
		
			<div class="hptabber">

				<!--This section is where the Featured Content plugin is called.-->
				
				<?php include (ABSPATH . '/wp-content/plugins/content-gallery/gallery.php'); ?>
			
			</div>
			
		</div>
					
		<div id="homepageleft">
		
			<!--This section is currently pulling category ID #1, and can be switched by changing the cat=1 to show whatever category ID you would like in this area.-->
				
			<div class="featured">
			<h3>Featured Category</h3>
			
				<!--This is where the thumbnails are found for the homepage bottom section - note the custom field name for this image is "thumbnail". Recommended image size is 70x70, as the stylesheet is written for this size.-->
			
				<?php $recent = new WP_Query("cat=1&showposts=3"); while($recent->have_posts()) : $recent->the_post();?>
				<?php if( get_post_meta($post->ID, "thumbnail", true) ): ?>
				    <a href="<?php the_permalink() ?>" rel="bookmark"><img style="float:left;margin:0px 10px 0px 0px;" src="<?php echo get_post_meta($post->ID, "thumbnail", true); ?>" alt="<?php the_title(); ?>" /></a>
				<?php else: ?>
				   	<a href="<?php the_permalink() ?>" rel="bookmark"><img style="float:left;margin:0px 10px 0px 0px;"  src="<?php bloginfo('template_url'); ?>/images/thumbnail.png" alt="<?php the_title(); ?>" /></a>
				<?php endif; ?>				
				<b><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></b>
				<?php the_content_limit(80, ""); ?>
				
				<div style="border-bottom:1px dotted #94B1DF; margin-bottom:10px; padding:0px 0px 10px 0px; clear:both;"></div>
				
				<?php endwhile; ?>

				<!--This is where you can specify the archive link for each section. Replace the # with the appropriate URL-->
				
				<b><a href="#" rel="bookmark">Read More Posts From This Category</a></b>
				
			</div>			
				
		</div>
		
		<div id="homepageright">
		
			<!--This section is currently pulling category ID #1, and can be switched by changing the cat=1 to show whatever category ID you would like in this area.-->
		
			<div class="featured">
			<h3>Featured Category</h3>
			
				<!--This is where the thumbnails are found for the homepage bottom section - note the custom field name for this image is "thumbnail". Recommended image size is 70x70, as the stylesheet is written for this size.-->
			
				<?php $recent = new WP_Query("cat=1&showposts=3"); while($recent->have_posts()) : $recent->the_post();?>
				<?php if( get_post_meta($post->ID, "thumbnail", true) ): ?>
				    <a href="<?php the_permalink() ?>" rel="bookmark"><img style="float:left;margin:0px 10px 0px 0px;" src="<?php echo get_post_meta($post->ID, "thumbnail", true); ?>" alt="<?php the_title(); ?>" /></a>
				<?php else: ?>
				   	<a href="<?php the_permalink() ?>" rel="bookmark"><img style="float:left;margin:0px 10px 0px 0px;"  src="<?php bloginfo('template_url'); ?>/images/thumbnail.png" alt="<?php the_title(); ?>" /></a>
				<?php endif; ?>				
				<b><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></b>				
				<?php the_content_limit(80, ""); ?>
								
				<div style="border-bottom:1px dotted #94B1DF; margin-bottom:10px; padding:0px 0px 10px 0px; clear:both;"></div>
				
				<?php endwhile; ?>
				
				<!--This is where you can specify the archive link for each section. Replace the # with the appropriate URL-->
				
				<b><a href="#" rel="bookmark">Read More Posts From This Category</a></b>
				
			</div>		
			
		</div>
		
		<div id="homepagebottom">
		
			<div class="hpbottom">
			
				<h3>Featured Category</h3>
	
				<!--This is where the thumbnails are found for the homepage bottom section - note the custom field name for this image is "hpbottom". Recommended image size is 70x70, as the stylesheet is written for this size.-->
				
				<?php $recent = new WP_Query("cat=1&showposts=3"); while($recent->have_posts()) : $recent->the_post();?>
				<?php if( get_post_meta($post->ID, "hpbottom", true) ): ?>
				    <a href="<?php the_permalink() ?>" rel="bookmark"><img style="float:left;margin:0px 10px 0px 0px;" src="<?php echo get_post_meta($post->ID, "hpbottom", true); ?>" alt="<?php the_title(); ?>" /></a>
				<?php else: ?>
				   	<a href="<?php the_permalink() ?>" rel="bookmark"><img style="float:left;margin:0px 10px 0px 0px;"  src="<?php bloginfo('template_url'); ?>/images/thumbnail.png" alt="<?php the_title(); ?>" /></a>
				<?php endif; ?>				
				<b><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></b>
				<?php the_content_limit(350, "[Read more of this review]"); ?>
					
				<div style="border-bottom:1px dotted #2255AA; margin-bottom:10px; padding:0px 0px 10px 0px; clear:both;"></div>
					
				<?php endwhile; ?>
	
				<!--This is where you can specify the archive link for each section. Replace the # with the appropriate URL-->
					
				<b><a href="#" rel="bookmark">Read More Posts From This Category</a></b>
			
			</div>
		
		</div>
		
	</div>
	
<?php include(TEMPLATEPATH."/sidebar_home.php");?>

<div style="clear:both;"></div>
		
</div>

<!-- The main column ends  -->

<?php get_footer(); ?>