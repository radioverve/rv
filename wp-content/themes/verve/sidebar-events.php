<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery('#mycarousely').jcarousel({
        vertical: true,
        visible: 3,
        scroll: 1
    });
});
</script>
<style type="text/css">
/*jquery.jcaousel.css*/
.jcarousel-container{position:relative;}.jcarousel-clip{z-index:2;padding:0;margin:0;overflow:hidden;position:relative;}.jcarousel-list{z-index:1;overflow:hidden;position:relative;top:0;left:0;margin:0;padding:0;}.jcarousel-list li,.jcarousel-item{float:left;list-style:none;width:75px;height:75px;}.jcarousel-next{z-index:3;display:none;}.jcarousel-prev{z-index:3;display:none;}
</style>

<div id="events">
        <div class="widgetarea">
                <h3>Events</h3>
                <!--<ul  id="mycarousely" class="jcarousel-skin-tango">
                        <li>Some text here 1 and some long events<br><small>26th June 2008</small></li>
                        <li>Some here long events<br><small>26th June 2008</small></li>
                        <li>Some text  some long events<br><small>26th June 2008</small></li>
                        <li>Here 1 and some long events<br><small>26th June 2008</small></li>
                        <li>And some long events<br><small>26th June 2008</small></li>
                        <li>Text here 1 and some long events<br><small>26th June 2008</small></li>
		</ul>-->               
                <?php
                gigpress_sidebar(3);
                ?>
        </div>
</div>