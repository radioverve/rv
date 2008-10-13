<?php
/*
Template Name: Artists
*/
?>
<?php
wp_enqueue_script('ui-tabs-latest');
get_header();
?>
<script>
$(document).ready(function(){
  $("#tabbox > ul").tabs({ event: 'mouseover' });
});
</script>

<!-- This is so badly aligned because the header has 3 open divs -->
       <div id="content">
		<div id="contentleft">
                        <!--Article container START-->							
			<div id="postarea">
			
                            <!-- Navigator and rest comes here-->
                            <div id="artistnavigator">
                                <?
                                    if(isset($_GET["start"]))
                                        $start=$_GET["start"];
                                    else
                                        $start='A';
                                        
                                    gnrb_get_band_navigator($start);
                                ?>
                            </div>
                            <div class="artistlist">
                            	<? gnrb_get_bands_shortdata($start);?>
                            </div>
                            <div id="artistnavigator">
                                <? gnrb_get_band_navigator($start);?>
                            </div>
			</div>	
                        <!--Article container END-->
            								
     	</div>
     	
     	            <!--Sidebar START-->							
			<div id="sidebar">
                            <!--Sidebar:Tags Block START-->
                            <!-- Get a copy and localize the JS files-->
                            <? echo '<link rel="stylesheet" type="text/css" href="'.get_settings('siteurl').'/wp-content/themes/verve/jquery_tabs.css" />'; ?>
                            <!-- Get a copy and localize the JS files-->
                            <div id="shoutbox">
                                <div class="widgetarea">
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
							
								<div class="widgetarea">
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
                            
							<!--Sidebar:events END-->								

						</div>
			<!--Sidebar END-->
                
	    </div>	
									
	</div>
    </div>		
</div>

<?php get_footer(); ?>