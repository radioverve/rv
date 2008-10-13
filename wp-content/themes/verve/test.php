<?php
/*                                                                                                                                                                                 
Template Name: Test                                                                                                                                                              
*/
wp_enqueue_script('jquery-carousel');
?>
<link href="http://test.radioverve.com/new/wp-includes/css/skin.css" type="text/css" rel="stylesheet">
<?
get_header();
?>
<!-- <script type="text/javascript">
$().ready(function() {
  $('#ex2').jqm({ajax: '/new/login', trigger: 'a.ex2trigger'});
  $('#ex2').jqmShow();
});
</script>-->

<script type="text/javascript">
jQuery.noConflict();

jQuery(document).ready(function() {
    jQuery('#mycarousel').jcarousel({
        vertical: true,
        visible: 3,
        scroll: 1
    });
});
</script>
<style type="text/css">
/*jquery.jcaousel.css*/
.jcarousel-container{position:relative;}
.jcarousel-clip{z-index:2;padding:0;margin:0;overflow:hidden;position:relative;}
.jcarousel-list{z-index:1;overflow:hidden;position:relative;top:0;left:0;margin:0;padding:0;}
.jcarousel-list li,.jcarousel-item{float:left;list-style:none;width:175px;height:175px;}
.jcarousel-next{z-index:3;display:none;}
.jcarousel-prev{z-index:3;display:none;}
</style>
<ul id="mycarousel" class="jcarousel-skin-tango">
                        <li>Rock<br><small>Thermal and a Quarter - Paper Puli</small><br><small><a href="#">Listen Now</a></small></li>
                        <li>Metal<br><small>Demonic Resurrection - Destruction something</small><br><small><a href="#">Listen Now</a></small></li>
                        <li>Hindi<br><small>Unlucky Ali - Mujhe tumso something hai</small><br><small><a href="#">Listen Now</a></small></li>
                        <li>Devotional<br><small>Raakshash - God makes me blue</small><br><small><a href="#">Listen Now</a></small></li>
                        <li>Classical<br><small>Manasi Prasad - I talk way too much</small><br><small><a href="#">Listen Now</a></small></li>
                        <li>Electronic<br><small>Shaair N Func - Its probably that</small><br><small><a href="#">Listen Now</a></small></li>
</ul>
<?
  echo(sanitize_title_with_dashes("Thermal and a Quarter"));
  //print_r(gnrb_get_bands_featured());
  //print_r(gigpress_frontpage_events());
?>
  </div>
</div>