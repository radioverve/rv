<?php
/*
Template Name: Blog Page
*/
?>

<?php get_header(); ?>

<div id="content">

	<div id="contentleft">
	
		<div class="postarea">
	
		<?php include(TEMPLATEPATH."/breadcrumb.php");?>
			
			<!--The blog page template is currently set to show 5 posts.  Change showposts=5 to whatever number of posts you want to display.-->
			<!--Replace cat=1 with the Category ID you want to display in this section. See the README file to learn how to locate the Category ID-->
				
			<?php $page = (get_query_var('paged')) ? get_query_var('paged') : 1; query_posts("cat=1&showposts=5&paged=$page"); while ( have_posts() ) : the_post() ?>
			<h1><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h1>
				
			<div class="date">
			
				<div class="dateleft">
					<p><span class="time"><?php the_time('F j, Y'); ?></span> by <?php the_author_posts_link(); ?> &nbsp;<?php edit_post_link('(Edit)', '', ''); ?> <br /> Filed under <?php the_category(', ') ?></p> 
				</div>
				
				<div class="dateright">
					<p><span class="comment"><?php comments_popup_link('Leave a Comment', '1 Comment', '% Comments'); ?></span></p> 
				</div>
				
			</div>
				
			<?php the_content(__('[Read more]'));?><div style="clear:both;"></div>
				
			<div class="postmeta2">
				Filed Under: <?php the_category(', ') ?><br />Tagged: <?php the_tags('') ?>
			</div>
							
			<?php endwhile; ?>
			
			<p><?php posts_nav_link(); ?></p>
		
		</div>
		
	</div>
	
<?php include(TEMPLATEPATH."/sidebar.php");?>
		
</div>

<!-- The main column ends  -->

<?php get_footer(); ?>