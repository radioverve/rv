<?php
	echo '<script type="text/javascript" src="'.get_settings('siteurl').'/wp-content/plugins/ajax-comment-posting/jquery.js"></script>';
        echo '<script type="text/javascript" src="'.get_settings('siteurl').'/wp-includes/js/jcarousel.js"></script>';
        echo '<link rel="stylesheet" type="text/css" href="'.get_settings('siteurl').'/wp-includes/css/skin.css" />';
?>
<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery('#mycarousel').jcarousel({
        visible: 1,
        scroll: 1
    });
});

</script>
<style type="text/css">
/*jquery.jcaousel.css*/
.jcarousel-container{position:relative;}.jcarousel-clip{z-index:2;padding:0;margin:0;overflow:hidden;position:relative;}.jcarousel-list{z-index:1;overflow:hidden;position:relative;top:0;left:0;margin:0;padding:0;}.jcarousel-list li,.jcarousel-item{float:left;list-style:none;width:20px;height:20px;}
</style>
<div id="featureThingy">
  <ul id="mycarousel" class="jcarousel-skin-tango">
        <?php c2c_get_recent_posts(5,"<li><a href=\"%post_url%\">%post_title%</a></li>",'22'); ?>
  </ul>
</div>