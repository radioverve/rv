<!-- begin sidebar -->
 
<div id="sidebar">

	<div class="newsletter">
	
		<!--To enable the eNews &amp; Updates feature, go to your WP dashboard and go to Presentation -> Revolution Music Options and enter your Feedburner ID.-->

		<h3>eNews &amp; Updates</h3>
		<p>Sign up to receive breaking news <br /> as well as receive other site updates!</p><form id="subscribe" action="http://www.feedburner.com/fb/a/emailverify" method="post" target="popupwindow" onsubmit="window.open('http://www.feedburner.com', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true"><p><input type="text" value="Enter your email address..." id="subbox" onfocus="if (this.value == 'Enter your email address...') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Enter your email address...';}" name="email"/><input type="hidden" value="http://feeds.feedburner.com/~e?ffid=<?php $feedburner_id = get_option('revmusic_feedburner_id'); echo $feedburner_id; ?>" name="url"/><input type="hidden" value="eNews Subscribe" name="title"/><input type="submit" value="GO" id="subbutton" /></p></form>	
	
	</div>
		
	<div class="video">
	
		<!--To determine what video is shown on the homepage, go to your WP dashboard and go to Presentation -> Revolution Music Options and enter your video code here.-->
	
		<h3>Featured Video</h3>
		<?php $video = get_option('revmusic_video'); echo stripslashes($video); ?>
		
	</div>
	
	<?php include(TEMPLATEPATH."/sidebar_left.php");?>
	
	<?php //include(TEMPLATEPATH."/sidebar_right.php");
	?>
	
</div>

<!-- end sidebar -->