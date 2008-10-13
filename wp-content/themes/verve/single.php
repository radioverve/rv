<link rel="stylesheet" href="/wp-content/themes/verve/thickbox.css" type="text/css" media="screen" />
<?php 
if(!is_user_logged_in()){
	wp_enqueue_script('thickbox');
	//echo("<link rel=\"stylesheet\" href=\"/new/wp-content/themes/verve/thickbox.css\" type=\"text/css\" media=\"screen\" />");
	get_header();
}else{
	wp_enqueue_script('ui-tabs-latest');
        //podpress_post_javascript();
	get_header();
}


?>
						
	<div id="content">
		<!--	<div id="shoutbox" class="sidebarBox">-->
		<!--		xx-->
		<!--	</div>-->
		<!--<div id="sidebar" style="border: 1px solid red;margin: 95px 5px 0 5px;width: 200px;height: 200px;float: right;">-->
		<!--	<strong>You will like these as well</strong>-->
		<!--	<?php wp_related_posts(); ?>-->
		<!--</div>-->
	<div id="contentLeft">
	<div id="postarea">

	<? if(!is_user_logged_in()) {
		get_modal_login( "http://".$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
	?>


	
                    <h2> Please Log In or Register to read the full article.</h2>
		    <!-- <div class="jqmWindow" id="ex2">
                        Please wait... <img src="/images/rotatingclock-slow.gif" alt="loading" />
                    </div>-->
        <? }else{ ?>
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		

		<div class="singlepost post" id="post-<?php the_ID(); ?>">
			<h1><?php the_title(); ?></h1>
			
			<!-- Testing?!	 -->	
	<div class="date">
			
				<div class="dateleft">
					<p><span class="time"><?php the_time('l, F jS, Y') ?></span> by <?php the_author(); ?> &nbsp; <br /> Filed under <?php the_category(', ') ?></p> 
				</div>

				
				<div class="dateright">
				</div>
				
			</div>
		
			<?php the_content('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>
			
			<div class="postmeta">
				<p><?php the_tags( '<span class=\"tags\">Tags: ', ', ', '</span>'); ?></p>
			</div>			
					
		</div>

	
	<?php comments_template(); ?>

	<?php endwhile; else: ?>

		<p>Sorry, no posts matched your criteria.</p>
<?php endif; ?>

</div>

</div>

<div id="sidebar">
<!--Sidebar:Tags Block START-->

<!-- Get a copy and localize the JS files-->
<? echo '<link rel="stylesheet" type="text/css" href="'.get_settings('siteurl').'/wp-content/themes/verve/jquery_tabs.css" />'; ?>
<!--<script type="text/javascript" src="/new/wp-includes/js/jquery/jquery.js"></script>-->
<!--<script type="text/javascript" src="/new/wp-includes/js/jquery/ui.core.js"></script>
<script type="text/javascript" src="/new/wp-includes/js/jquery/ui.tabs.js"></script>-->

<script type="text/javascript">
jQuery.noConflict();

jQuery(document).ready(function(){
  jQuery("#tabbox > ul").tabs({ event: 'mouseover' });
});
</script>
<!-- Get a copy and localize the JS files-->
<div id="shoutbox" class="sidebarBox">
        <div class="sidebarBoxTop"></div>
            <div class="sidebarBoxMiddle" >
                    <div id="tabbox">
                        <ul>
                        <li><a href="#frag-recent-posts">Latest</a></li>
                        <li><a href="#frag-tag_cloud">Tags</a></li>
                        <li><a href="#frag-popular-posts">Popular</a></li>
                        <li><a href="#frag-recent-comments">Comments</a></li>
                        </ul>
                        <div id="tabboxcontent">
                            <?php
                                if(!function_exists('dynamic_sidebar')|| !dynamic_sidebar('Articles Side Bar')) {
                                    print 'Widget home is broken';
                                }
                            ?>
                        </div>
                    </div>
            </div>
        <div class="sidebarBoxBottom"></div>
        
        <div class="sidebarBoxTop" style="margin-top:10px"></div>
            <div class="sidebarBoxMiddle" >
                <h3>Archives</h3>
                <div id="tabboxcontent">
                    <?php
                        if(!function_exists('dynamic_sidebar')|| !dynamic_sidebar('ArchivesOnly')) {
                            print 'Widget home is broken';
                        }
                    ?>
                </div>
        </div>
        <div class="sidebarBoxBottom"></div>
</div>
<!--Sidebar:Tags Block END-->

<!--Sidebar:events START-->
								<?php /*get_sidebar('events');*/ ?>
                                                                <!--Sidebar:events Commented-->
<!--Sidebar:events END-->								

							</div>
<!--Sidebar END-->
<? } ?>

					
	</div>
			
			
					
	</div>
					
	</div>
	</div>

	<?php get_footer(); ?>
