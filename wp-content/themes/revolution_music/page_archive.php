<?php
/*
Template Name: Archive Page
*/
?>

<?php get_header(); ?>

<div id="content">

	<div id="contentleft">

		<div class="postarea">
		
		<?php include(TEMPLATEPATH."/breadcrumb.php");?>
				
				<h1>Site Archives</h1><br />
				
				<div class="archive">
		
					<b>by page:</b>
						<ul>
							<?php wp_list_pages('title_li='); ?>
						</ul>
				
					<b>by month:</b>
						<ul>
							<?php wp_get_archives('type=monthly'); ?>
						</ul>
							
					<b>by category:</b>
						<ul>
							<?php wp_list_categories('sort_column=name&title_li='); ?>
						</ul>
		
				</div>
				
				<div class="archive">
					
					<b>by post:</b>
						<ul>
							<?php get_archives('postbypost', 100); ?>
						</ul>
				</div>
			
			</div>
		
	</div>
	
<?php include(TEMPLATEPATH."/sidebar.php");?>
		
</div>

<!-- The main column ends  -->

<?php get_footer(); ?>