<?php
/*                                                                                                                                                                                 
Template Name: Interviews                                                                                                                                                              
*/
?>
<?php
/*                                                                                                                                                                                 
Template Name: Articles                                                                                                                                                              
*/
wp_enqueue_script('ui-tabs-latest');
get_header();
?>
<!--Player START-->
					<?php //get_artist_ss(); ?>
<!--Player END-->

<!--Middle section START-->

<!--Featured thingy START-->
					<?php /*get_featured();*/ ?>
<!-- Featured thingy END -->					
					<div id="content">
						<div id="contentleft">
<!--Article container START-->							
							<div class="postarea">
<!--Post START-->								
<?php c2c_get_recent_posts(10, $format="<div class=\"post\">
<h1><a href=\"%post_url%\" rel=\"bookmark\">%post_title%</a></h1>

<div class=\"date\">			
		<div class=\"dateleft\">
			<p><span class=\"time\">%post_date%</span> by <a>%post_author%</a> &nbsp; <br /> Filed under %post_categories_URL%</p> 
		</div>
			
		<div class=\"dateright\">
			<p><span class=\"comment\"><a href=\"%comments_url%\" title=\"Comment on %post_title%\">Leave a Comment</a></span></p> 
		</div>
				
</div>

<p>%post_excerpt%</p>
<span class=\"listenbutton\"><a href=\"%post_url%\">Listen Now</a></span>
</div>
<div style=\"clear:both;\"></div>

<div class=\"postmeta2\">
				<p><span class=\"tags\">Tags: %post_tags_URL%</span></p>
			</div>

", "71"); ?>
<!--Post END-->								
							</div>	
<!--Article container END-->
							
													
						</div>
						
<!--Sidebar START-->
						<div id="sidebar">
<!--Sidebar:Tags Block START-->

<!-- Get a copy and localize the JS files-->
<? echo '<link rel="stylesheet" type="text/css" href="'.get_settings('siteurl').'/wp-content/themes/verve/jquery_tabs.css" />'; ?>
<!--<script type="text/javascript" src="/new/wp-includes/js/jquery/jquery.js"></script>-->
<!--<script type="text/javascript" src="/new/wp-includes/js/jquery/ui.core.js"></script>
<script type="text/javascript" src="/new/wp-includes/js/jquery/ui.tabs.js"></script>-->

<script>
$(document).ready(function(){
  $("#tabbox > ul").tabs({ event: 'mouseover' });
});
</script>

<!-- Get a copy and localize the JS files-->
<div id="shoutbox">
            <div class="widgetarea" >
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
        
            <div class="widgetarea" >
                <h3>Archives</h3>
                <div id="tabboxcontent">
                    <?php
                        if(!function_exists('dynamic_sidebar')|| !dynamic_sidebar('ArchivesOnly')) {
                            print 'Widget home is broken';
                        }
                    ?>
                </div>
        </div>
</div>
<!--Sidebar:Tags Block END-->

<!--Sidebar:events START-->
								<?php /*get_sidebar('events');*/ ?>
                                                                <!--Sidebar:events Commented-->
<!--Sidebar:events END-->								

							</div>
		
<!--Sidebar END-->						
					</div>	
									
				</div>
			</div>		
		</div>

<!--Middle section END-->
<?php get_footer(); ?>