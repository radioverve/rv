<?php get_header(); ?>
<!--Player START-->
					<?php get_player(); ?>
<!--Player END-->

<!--Middle section START-->

<!--Featured thingy START-->
					<?php get_featured(); ?>
<!--Featured thingy END-->					
					<div id="content">
						<div id="contentTop"></div>
						<div id="contentMiddle">
<!--Article container START-->							
							<?php if (have_posts()) : ?>
							<div id="articleContainer">
<!--Post START-->								
								<?php while (have_posts()) : the_post(); ?>
								<div class="post" id="post-<?php the_ID(); ?>">
									<h2><span><?php the_time('F jS, Y') ?></span>  <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
									<p><?php the_excerpt('Read the rest of this entry &raquo;'); ?></p>
									<a href="#">Read more...</a>
								</div>
								<div style="padding: 5px 0 5px 0;"></div>
								<?php endwhile; ?>
<!--Post END-->								
							</div>	
							<?php else : ?>
							
							<?php endif; ?>
<!--Article container END-->
<!--Sidebar START-->
							
							<div id="sidebar">
<!--Sidebar:shoutbox START-->
								<?php get_sidebar('shoutbox'); ?>
<!--Sidebar:shoutbox END-->

<!--Sidebar:events START-->
								<?php get_sidebar('events'); ?>
<!--Sidebar:events END-->								

							</div>
<!--Sidebar END-->								
						</div>
						<div id="contentBottom"></div>
					</div>	
									
				</div>
			</div>		
		</div>

<!--Middle section END-->

<?php get_footer(); ?>
