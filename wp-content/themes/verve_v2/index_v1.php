<?php get_header(); ?>
<!--Player START-->
					<?php get_player(); ?>
<!--Player END-->

<!--Middle section START-->

<!--Featured thingy START-->
					<?php get_featured(); ?>
<!--Featured thingy END-->					
					<div id="content">
						<div id="contentTop"></div>
						<div id="contentMiddle">
<!--Article container START-->							
							<div id="articleContainer">
<!--Post START-->								
<?php c2c_get_recent_posts(5, $format="<div class=\"post\"><h2><span>%post_date%</span><a href=\"%post_url%\" rel=\"bookmark\">%post_title%</a></h2><p>%post_content%</p><a href=\"#\">Read more...</a></div><div style=\"padding: 5px 0 5px 0;\"></div>", "22 6"); ?>
<!--Post END-->								
							</div>	
<!--Article container END-->
<!--Sidebar START-->
							
							<div id="sidebar">
<!--Sidebar:shoutbox START-->
								<?php get_sidebar('shoutbox'); ?>
<!--Sidebar:shoutbox END-->

<!--Sidebar:events START-->
								<?php get_sidebar('events'); ?>
<!--Sidebar:events END-->								

							</div>
<!--Sidebar END-->								
						</div>
						<div id="contentBottom"></div>
					</div>	
									
				</div>
			</div>		
		</div>

<!--Middle section END-->

<?php get_footer(); ?>
