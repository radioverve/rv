<?php
/*
Template Name: ArtistShortdata
*/
?>
<?php
if(isset($_GET["artistname"])){
        //$content = gnvrp_get_band_bio($_GET["artistid"]);
        //$content = gnvrp_get_band_bio(2);
        global $wpdb,$gnrb_table_artist;
        $name=$_GET["artistname"];
        $artistnicename=sanitize_title_with_dashes($_GET['artistname']);
        $sql = "Select id from $gnrb_table_artist where nicename='$artistnicename'";
        $id = $wpdb->get_var($sql);
        if($id)
                $content=gnrvp_get_band_info($id,"Test");
        else
                $content=NULL;
}else{
    echo "Something not set".$_GET["artistid"];
}
?>
<?php if($content){ ?>
<h1 class="artistTitle"><a href="<?php bloginfo("url");?>/artist/<?php echo(sanitize_title_with_dashes($name)); ?>"><?php echo $name; ?></a></h1>
<?}else{?>
<h1 class="artistTitle"><?php echo $name; ?></h1>
<?php }?>

        <?php if($content["Band-Icon"]){ ?>
                <p>
                        <div class="profile_photo"><img src="<?php echo($content["Band-Icon"]); ?>" alt="Artist Icon" /></div>
                </p>
        <?php }?>

        <?php if($content){ ?>
                <p style="clear:both;padding-top:5px;">
                <a id="showLink" href="<?php bloginfo("url");?>/artist/<?php echo(sanitize_title_with_dashes($name)); ?>">Artist Radio</a>
                </p>
        <?php } ?>

        <div>              
        <?php if($content["Bio"]){ ?>
                        <h3>Bio</h3>
                        <?php echo(str_replace("\n", "<br />", $content["Bio"]));?>
                <?php }else
                        echo("Oops, Doesn't look like we have detailed info about the artist. Too bad!");
                ?>
        </div>
