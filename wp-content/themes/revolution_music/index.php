<?php get_header(); ?>

<div id="content">

	<div id="contentleft">
	
		<div class="postarea">
		
		<?php include(TEMPLATEPATH."/breadcrumb.php");?>
			
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<h1><?php the_title(); ?></h1>
			
			<div class="date">
			
				<div class="dateleft">
					<p><span class="time"><?php the_time('F j, Y'); ?></span> by <?php the_author_posts_link(); ?> &nbsp;<?php edit_post_link('(Edit)', '', ''); ?> <br /> Filed under <?php the_category(', ') ?></p> 
				</div>
				
				<div class="dateright">
					<p><span class="comment"><a href="<?php the_permalink() ?>#respond">Leave a comment</a></span></p> 
				</div>
				
			</div>

			<?php the_content(__('Read more'));?><div style="clear:both;"></div>
			
			<div class="postmeta">
				<p><span class="tags">Tags: <?php the_tags('') ?></span></p>
			</div>
		 			
			<!--
			<?php trackback_rdf(); ?>
			-->
			
			<?php endwhile; else: ?>
			
			<p><?php _e('Sorry, no posts matched your criteria.'); ?></p><?php endif; ?>
			
		</div>
		
		<div class="adsense-post">
		
			<!--To activate your Google AdSense ad, go to your WP dashboard and go to Presentation -> Revolution Music Options and enter your Google Adsense Code.-->
			
			<?php $adsense_468 = get_option('revmusic_adsense_468'); echo stripslashes($adsense_468); ?>
						
		</div>
			
		<div class="comments">
	
			<h4>Comments</h4>
			<?php comments_template(); // Get wp-comments.php template ?>
			
		</div>
		
	</div>
	
<?php include(TEMPLATEPATH."/sidebar.php");?>
		
</div>

<!-- The main column ends  -->

<?php get_footer(); ?>