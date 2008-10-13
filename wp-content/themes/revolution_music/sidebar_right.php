<!-- begin r_sidebar -->

<div id="r_sidebar">

	<ul id="r_sidebarwidgeted">
	
	<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar(3) ) : else : ?>
		
		<!--To define the 120x600 ad, go to your WP dashboard and go to Presentation -> Revolution Music Options and enter the ad code.-->
	
		<li id="ads">
		<h3>Advertisement</h3>
			<?php $ad_120 = get_option('revmusic_ad_120'); echo stripslashes($ad_120); ?>
		</li>

	<?php endif; ?>

	</ul>
	
</div>

<!-- end r_sidebar -->