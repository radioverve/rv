<?php
/*                                                                                                                                                                                 
Template Name: Listen                                                                                                                                                              
*/
wp_enqueue_script('flash-check');
wp_enqueue_script('jquery-carousel');
jal_add_to_head();?>

<script type="text/JavaScript">
	function load_url(artistid,artistname){
		//alert(artistname);
		jQuery("#postarea").load("<?php bloginfo('url'); ?>/artistdata?artistid="+artistid+"&artistname="+escape(artistname));
	}
</script>

<?php
get_header();
?>
<!--Player START-->


					<?php get_player(); ?>
<!--Player END-->

<!--Middle section START-->

<!--Featured thingy START-->
					<?php get_featured(); ?>
<!--Featured thingy END-->					
					<div id="content">
						<div id="contentleft">
<!--Article container START-->				<h3>Artist Info</h3>			
							<div id="postarea">
<!--Post START-->								
							
<!--Post END-->								
							</div>	
<!--Article container END-->
								
						</div>
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
									
				
			</div>	
	</div>

<!--Middle section END-->

<?php get_footer(); ?>
