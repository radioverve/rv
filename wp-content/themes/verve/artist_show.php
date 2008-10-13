<div id="player" style="background-image: none;">
<!--<h2>Artists Slideshow here!</h2>-->
<style>
#tree1 { background-color: #545454; width: 580px; height: 195px; padding-right: 4px; clear: both}
#img_container { border: 4px solid #DD2A4E; float: left; width: 350px; height: 180px; margin: 4px 0 0 4px;}
#list_main { border: 0px solid #fff; margin-left: 354px; padding-left:0px; height: 182px; margin-top: 0px; margin-top: 4px;}
#list_main li { list-style-type: none; height: 62px; border: 0px solid transparent; border-bottom: 1px solid #fff; cursor: pointer; }
#list_main #three { border-bottom: none;}
#car_img { height: 179px; width: 350px;}
.over  { background-color: #DD2A4E; }
.default  { background-color: #545454; }
.out  { background-color: #545454; }
#list_main li p { color: white; margin-top: 0px; padding-top: 10px; padding-left: 5px; font-weight: bold; }

</style>


<?php
$f_data = get_slideshow_data(3);
?>
<script type="text/javascript">
switch_image = function (x) {
    image_array = new Array();
    image_array['one'] = "<?php echo $f_data['image'][0]->meta_value; ?>";
    image_array['two'] = "<?php echo $f_data['image'][1]->meta_value; ?>";
    image_array['three'] = "<?php echo $f_data['image'][2]->meta_value; ?>";
    
    document.getElementById("car_img").src = image_array[x];	//Change the image
    document.getElementById('one').className = 'out';	//Change the BG
    document.getElementById('two').className = 'out';	//Change the BG
    document.getElementById('three').className = 'out';	//Change the BG
    document.getElementById(x).className = 'over';	//Change the BG
    
};
</script>
<div id="tree1">
    <div id="img_container">
        <img src="<?php echo $f_data['image'][0]->meta_value; ?>" id="car_img"/>
	<img src="<?php echo $f_data['image'][1]->meta_value; ?>" id="car_img" style="display: none;"/>
	<img src="<?php echo $f_data['image'][2]->meta_value; ?>" id="car_img" style="display: none;"/>
    </div>    
        <ul id="list_main">
            <li id="one" class="over" onmouseover="switch_image('one');"><p><?php echo substr($f_data['title'][0]->post_title,0,50); ?></p></li>
            <li id="two" class="default" onmouseover="switch_image('two');"><p><?php echo substr($f_data['title'][1]->post_title,0,50); ?></p></li>
	    <li id="three" class="default" onmouseover="switch_image('three');"><p><?php echo substr($f_data['title'][2]->post_title,0,50); ?></p></li>
        </ul>
</div>
<br clear="all"/>
</div>




