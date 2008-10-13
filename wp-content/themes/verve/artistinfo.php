<?php
/*
Template Name: ArtistInfo
*/
if(!is_user_logged_in()){
echo("<link rel=\"stylesheet\" href=\"/wp-content/themes/verve/thickbox.css\" type=\"text/css\" media=\"screen\" />");
wp_enqueue_script('thickbox');
}
wp_enqueue_script('flash-check');
?>
<?php

$params = explode("/", $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
//print_r($params);
$key = $params[2];

//$sql = "SELECT * FROM  $wpdb->users  WHERE user_nicename='$key'";
global $gnrb_table_artist;
$sql = "Select id,name from $gnrb_table_artist where nicename='$key'";
//echo($sql);
$res = $wpdb->get_results($sql);
//print_r($key);
$userid=0;
foreach( $res as $row ) {
//echo $row->id;
$content = gnrvp_get_band_info($row->id,$name);
$centova_id= get_player_code($row->id);
$events = gnrvp_get_artist_events($row->id);
}

if($key == "")
{ $content = ""; $band_name = ""; }
?>
<?php get_header(); ?>

	<script type="text/javascript">
		function show_modal_dialog(){
			tb_show("Please Login/Register","http://towel.radioverve.com/login?height=200&width=300&redirect=<?php echo "http://".$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>",false);
		}
	</script>
	<!--	<div id="shoutbox" class="sidebarBox">-->
	<!--		xx-->
	<!--	</div>-->
	<!--<div id="sidebar" style="border: 1px solid red;margin: 95px 5px 0 5px;width: 200px;height: 200px;float: right;">-->
	<!--	<strong>You will like these as well</strong>-->
	<!--	<?php wp_related_posts(); ?>-->
	<!--</div>-->
	
	
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					
	<div id="content">		
		<div id="contentleft">
			<h1 class="artistTitle"><?php the_title(); ?></h1>
			<p>
				<?php if($content["Band-Icon"]) ?>
					<div class="profile_photo"><img src="<?php echo($content["Band-Icon"]); ?>" alt="Artist Icon" /></div>
			</p>
			<p style="clear:both;padding-top:5px;width:358px;height:40px">
				<script language="JavaScript" type="text/javascript">

					// Globals
					// Major version of Flash required
					var requiredMajorVersion = 9;
					// Minor version of Flash required
					var requiredMinorVersion = 0;
					// Minor version of Flash required
					var requiredRevision = 0;
					
					// Version check for the Flash Player that has the ability to start Player Product Install (6.0r65)
					var hasProductInstall = DetectFlashVer(6, 0, 65);
					
					// Version check based upon the values defined in globals
					var hasRequestedVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);
					
					if ( hasProductInstall && !hasRequestedVersion ) {
						// DO NOT MODIFY THE FOLLOWING FOUR LINES
						// Location visited after installation is complete if installation is required
						var MMPlayerType = (isIE == true) ? "ActiveX" : "PlugIn";
						var MMredirectURL = "http://towel.radioverve.com/scripts/";
					    document.title = document.title.slice(0, 47) + " - Flash Player Installation";
					    var MMdoctitle = document.title;
					
						AC_FL_RunContent(
							"src", "/player/playerProductInstall",
							"FlashVars", "MMredirectURL="+MMredirectURL+'&MMplayerType='+MMPlayerType+'&MMdoctitle='+MMdoctitle+"&url=http://towel.radioverve.com/scripts/",
							"width", "100%",
							"height", "100%",
							"align", "middle",
							"id", "RVartistplayer",
							"quality", "high",
							"bgcolor", "${bgcolor}",
							"name", "${application}",
							"allowScriptAccess","sameDomain",
							"type", "application/x-shockwave-flash",
							"pluginspage", "http://www.adobe.com/go/getflashplayer"
						);
					} else if (hasRequestedVersion) {
						// if we've detected an acceptable version
						// embed the Flash Content SWF when all tests are passed
						AC_FL_RunContent(
								
								"src", "/player/artistplayer/RVartistplayer",
								"width", "100%",
								"height", "100%",
								"align", "middle",
								"id", "RVartistplayer",
								"quality", "high",
								"bgcolor", "#FFFFFF",
								"name", "RVartistplayer",
								"allowScriptAccess","sameDomain",
								"FlashVars","baseurl=<?php echo bloginfo('url');?>/scripts/&musicurl=<?php echo bloginfo('url'); ?>/music/&artistid=<?php echo $centova_id; ?>&loggedin=<?php if(is_user_logged_in()) echo 1; else echo 0;; ?>",
								"type", "application/x-shockwave-flash",
								"pluginspage", "http://www.adobe.com/go/getflashplayer"
						);
					  } else {  // flash is too old or we can't detect the plugin
					    var alternateContent = 'Alternate HTML content should be placed here. '
						+ 'This content requires the Adobe Flash Player. '
						+ '<a href=http://www.adobe.com/go/getflash/>Get Flash</a>';
					    document.write(alternateContent);  // insert non-flash content
					  }				
				</script>
				<noscript>
					<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"      
					id="rvartistplayer" width="358" height="40" 
					codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab">        
					<param name="movie" value="/player/artistplayer/RVartistplayer.swf" />               
					<param name="quality" value="high" />     
					<param name="bgcolor" value="#FFFFFF" />        
					<param name="allowScriptAccess" value="sameDomain" />   
					<param name=FlashVars values="' .$userid .'" />     
					<embed src="/player/artistplayer/RVartistplayer.swf" FlashVars="artistid='.$userid.'" quality="high" bgcolor="#FFFFFF"   \
					width="358px" height="40px" name="RVartistplayer" align="middle" 
					play="true"     
					loop="false"    
					quality="high"  
					allowScriptAccess="sameDomain"  
					type="application/x-shockwave-flash"    
					pluginspage="http://www.adobe.com/go/getflashplayer">   
					</embed>        
					</object>
				</noscript>
				<!--<?php echo $player_code; ?>-->
			</p>
			<div>
				<h3>Bio</h3>
				<?php echo(str_replace("\n", "<br />", $content["Bio"]));?>
			</div>
			
			<p>
				<?php	if($content["Pictures"]){ ?>
					<h3>Photos</h3>
					<?php foreach($content["Pictures"] as $picture){ ?>
						<div class="profile_photo artist_pictures"><img  src="<?php echo get_option("gnrb_band_pics_path") . $picture->field_value;?>" alt="Artist Image" /></div>
					<?php }?>
				<?php } ?>
			</p>
			<p class="postmetadata alt">
				<small>						
					<div style="margin:10px 10px 20px;">
						<?php comments_template(); ?>
					</div>							
				</small>
			</p>
			
		</div>
		
		<div id="sidebar">
			<!--Sidebar:shoutbox START-->
			<div class="shoutbox">
				<div class="widgetarea">
					<h3> Upcoming Events</h3>
					
					<?php
						if($events){
							echo("<ul>");
							foreach($events as $result){
							       echo("<li><b>$result->show_title</b><div>$result->show_venue, $result->show_date, $result->show_time<div><div>$result->show_locale</div></li>");
							}
							echo("</ul>");
						}else{
							echo("<b>No Upcoming Shows</b>");
						}
						
					?>
				</div>
				<?php if($content["Band-Members"]){ ?>
				<div class="widgetarea">
					<h3>Members</h3>
					<ul>
					<?php foreach($content["Band-Members"] as $members){?>
						<li><b><?php echo($members->member_name);?></b><div><small><?php echo $members->member_type; ?></small></div></li>
					<?php }?>
					</ul>
				</div>
				<?php } ?>
			</div>
		</div>	
	</div>
	<?php endwhile; else: ?>
		<p>Sorry, no posts matched your criteria.</p>
<?php endif; ?>
	
<?php get_footer(); ?>

