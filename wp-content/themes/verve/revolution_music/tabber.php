<div class="tabber">
	
	<!--This section is where the custom images are found for the homepage tabber - note the custom field name for this image is "homepage". Recommended image size is 600x200, as the stylesheet is written for this size.-->

	<div class="tabbertab">
	
		<!--This section is currently pulling category ID #1, and can be switched by changing the cat=1 to show whatever category ID you would like in this area.-->

		<h2>Featured #1</h2>
		<?php $recent = new WP_Query("cat=1&showposts=1"); while($recent->have_posts()) : $recent->the_post();?>
		<?php if( get_post_meta($post->ID, "homepage", true) ): ?>
			<img src="<?php echo get_post_meta($post->ID, "homepage", true); ?>" alt="<?php the_title(); ?>" />
		<?php else: ?>
			<img src="<?php bloginfo('template_url'); ?>/images/tabber.png" alt="<?php the_title(); ?>" />
		<?php endif; ?>				
		<h1><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h1>
		<?php the_content_limit(200, ""); ?><a href="<?php the_permalink() ?>" rel="bookmark">[Read more...]</a>
		<?php endwhile; ?>
		
	</div>

	<div class="tabbertab">
	
		<!--This section is currently pulling category ID #1, and can be switched by changing the cat=1 to show whatever category ID you would like in this area.-->

		<h2>Featured #2</h2>
		<?php $recent = new WP_Query("cat=1&showposts=1&offset=1"); while($recent->have_posts()) : $recent->the_post();?>
		<?php if( get_post_meta($post->ID, "homepage", true) ): ?>
			<img src="<?php echo get_post_meta($post->ID, "homepage", true); ?>" alt="<?php the_title(); ?>" />
		<?php else: ?>
			<img src="<?php bloginfo('template_url'); ?>/images/tabber.png" alt="<?php the_title(); ?>" />
		<?php endif; ?>				
		<h1><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h1>
		<?php the_content_limit(200, ""); ?><a href="<?php the_permalink() ?>" rel="bookmark">[Read more...]</a>
		<?php endwhile; ?>
		
	</div>
	
	<div class="tabbertab">
	
		<!--This section is currently pulling category ID #1, and can be switched by changing the cat=1 to show whatever category ID you would like in this area.-->

		<h2>Featured #3</h2>
		<?php $recent = new WP_Query("cat=1&showposts=1&offset=2"); while($recent->have_posts()) : $recent->the_post();?>
		<?php if( get_post_meta($post->ID, "homepage", true) ): ?>
			<img src="<?php echo get_post_meta($post->ID, "homepage", true); ?>" alt="<?php the_title(); ?>" />
		<?php else: ?>
			<img src="<?php bloginfo('template_url'); ?>/images/tabber.png" alt="<?php the_title(); ?>" />
		<?php endif; ?>				
		<h1><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h1>
		<?php the_content_limit(200, ""); ?><a href="<?php the_permalink() ?>" rel="bookmark">[Read more...]</a>
		<?php endwhile; ?>
		
	</div>
	
	<div class="tabbertab">
	
		<!--This section is currently pulling category ID #1, and can be switched by changing the cat=1 to show whatever category ID you would like in this area.-->

		<h2>Featured #4</h2>
		<?php $recent = new WP_Query("cat=1&showposts=1&offset=3"); while($recent->have_posts()) : $recent->the_post();?>
		<?php if( get_post_meta($post->ID, "homepage", true) ): ?>
			<img src="<?php echo get_post_meta($post->ID, "homepage", true); ?>" alt="<?php the_title(); ?>" />
		<?php else: ?>
			<img src="<?php bloginfo('template_url'); ?>/images/tabber.png" alt="<?php the_title(); ?>" />
		<?php endif; ?>				
		<h1><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h1>
		<?php the_content_limit(200, ""); ?><a href="<?php the_permalink() ?>" rel="bookmark">[Read more...]</a>
		<?php endwhile; ?>
		
	</div>
	
	<div class="tabbertab">
	
		<!--This section is currently pulling category ID #1, and can be switched by changing the cat=1 to show whatever category ID you would like in this area.-->

		<h2>Featured #5</h2>
		<?php $recent = new WP_Query("cat=1&showposts=1&offset=4"); while($recent->have_posts()) : $recent->the_post();?>
		<?php if( get_post_meta($post->ID, "homepage", true) ): ?>
			<img src="<?php echo get_post_meta($post->ID, "homepage", true); ?>" alt="<?php the_title(); ?>" />
		<?php else: ?>
			<img src="<?php bloginfo('template_url'); ?>/images/tabber.png" alt="<?php the_title(); ?>" />
		<?php endif; ?>				
		<h1><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h1>
		<?php the_content_limit(200, ""); ?><a href="<?php the_permalink() ?>" rel="bookmark">[Read more...]</a>
		<?php endwhile; ?>
		
	</div>
	
	<div class="tabbertab">
	
		<!--This section is currently pulling category ID #1, and can be switched by changing the cat=1 to show whatever category ID you would like in this area.-->

		<h2>Featured #6</h2>
		<?php $recent = new WP_Query("cat=1&showposts=1&offset=5"); while($recent->have_posts()) : $recent->the_post();?>
		<?php if( get_post_meta($post->ID, "homepage", true) ): ?>
			<img src="<?php echo get_post_meta($post->ID, "homepage", true); ?>" alt="<?php the_title(); ?>" />
		<?php else: ?>
			<img src="<?php bloginfo('template_url'); ?>/images/tabber.png" alt="<?php the_title(); ?>" />
		<?php endif; ?>				
		<h1><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h1>
		<?php the_content_limit(200, ""); ?><a href="<?php the_permalink() ?>" rel="bookmark">[Read more...]</a>
		<?php endwhile; ?>
		
	</div>

</div>