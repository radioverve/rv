<!-- begin sidebar -->
 
<div id="sidebar">
		
	<div class="video">
	
		<!--To determine what video is shown on the homepage, go to your WP dashboard and go to Presentation -> Revolution Music Options and enter your video code here.-->
	
		<h3>Featured Video</h3>
		<?php $video = get_option('revmusic_video'); echo stripslashes($video); ?>
		
	</div>
	
	<?php include(TEMPLATEPATH."/revolution_music/sidebar_left.php");?>
	
	<?php //include(TEMPLATEPATH."/sidebar_right.php");
	?>
	
</div>

<!-- end sidebar -->