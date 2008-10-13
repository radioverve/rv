<!-- begin l_sidebar -->

<div id="l_sidebar">

	<ul id="l_sidebarwidgeted">
	
	<?php //if ( function_exists('dynamic_sidebar') && dynamic_sidebar(2) ) : else : ?>
	
		<li id="categories">
		<h3>Categories</h3>
			<ul>
				<?php wp_list_categories('sort_column=name&title_li=&depth=2'); ?>
			</ul>
		</li>
	
		<!--<li id="archives">
		<<h3>Archives</h3>
			<ul>
				<?php wp_get_archives('type=monthly'); ?>
			</ul>
		</li>
		
		<li id="links">
		<h3>Blogroll</h3>
			<ul>
				<?php wp_list_bookmarks('title_li=&categorize=0'); ?>
			</ul>
		</li>-->
		
	<?php //endif; ?>
	
	</ul>
	
</div>

<!-- end l_sidebar -->