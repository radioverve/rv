<?php
/*
Template Name: EventInfo
*/
?>

<?php get_header(); ?>
<!-- The weird alignment is to show open divs in the header-->
            <div id="content" class="widecolumn">
                <div id="plainContentTop"></div>
                    <div id="plainContentMiddle">
                        <div id="pagecontent">
                            <h2>Show event info here</h2>
                            <?
                                global $wpdb;
                                if(isset($_GET["show_id"])){
                                    $showid=$_GET["showid"];
                                    $results=$wpdb->get_results("Select * from wp_gigpress_shows where show_id=$showid");
                                    foreach($results as $result){
                                        
                                    }
                                    //print_r($results);
                                }
                            ?>
                       </div>
                    </div>
                <div id="plainContentBottom"></div>
            </div>
	</div>
    </div>		
</div>
<? get_footer(); ?>