<?php
/*                                                                                                                                                                                 
Template Name: MusicManager
*/
//wp_enqueue_script('jqmodal','/new/wp-includes/js/jquery/jqModal.js',array('jquery-latest'));
if(!is_user_logged_in()){
wp_enqueue_script('thickbox');
}
?>

<script>
function clicked_checkbox(){
    //alert("What is happening?");
    document.getElementById("regsubbutton").disabled=!document.getElementById("regsubbutton").disabled;
}
</script>

<?
get_header();
?>

<?
if(!is_user_logged_in()){
    echo("<link rel=\"stylesheet\" href=\"/new/wp-content/themes/verve/thickbox.css\" type=\"text/css\" media=\"screen\" />");
    get_modal_login();
}
//Variables
global $wpdb;
$errors=NULL;
$artist_id = $_GET['aid'];
$artist_name = rv_get_artistname($artist_id);
$checker = 0;
$insert_array = array();
/*echo "Unstripped: " . $artist_id;
echo "Stripped: " . $wpdb->escape("$artist_id")*/;
?>
        <div id="content">
	    <div id="plainContentTop"></div>
	    <div id="plainContentMiddle">
                <div id="pagecontent">		    
                <? if(!is_user_logged_in()) { ?>
			<h2> Please Log In or Register to upload your music.</h2>
			<a href="/new/login?height=200&width=300" title="Login" class="thickbox">Login or Register now</a>
                <? } else {
			//rv_get_artist($userdata->ID);
			if(rv_is_valid_owner_for_artist($userdata->ID, $artist_id) === TRUE) {
			    echo "<a href='" . get_option("siteurl") . "/upload-music'> << Back</a> <br /><br />Artist: " . $artist_name; //<-- This needs formatting & change of location";
                            
                            //Show the list of music files added
                            if (rv_is_music_present($artist_id) > 0) {
                                echo rv_show_artist_music($artist_id);
                            } else {
                                echo "<br/> No files found! Please upload some.";
                            }
                            
			    if (isset($_POST["counter"])) {
				$c = $_POST["counter"];		//Apply ome filtering here
				for($i=0; $i<$c; $i++) {
				    if ($_FILES["music_file"]["size"][$i] > 0) {
					$up = new file_upload();
					$up->the_file = $_FILES["music_file"]["name"][$i];
					$up->the_temp_file = $_FILES["music_file"]["tmp_name"][$i];
					$tmp_dir = 'wp-content/uploads/band_music/artists/';
					$up->create_directory = FALSE;
					$up->extensions = array('.jpg','.JPG', '.pdf', '.mp3', '.txt');
					//Get dir name for atrist
					$up->create_directory = true;
					$dir_path = $tmp_dir . $artist_id;	//Path where the dir will be created
					$up->create_dir($dir_path);		//created a new dir for the artist
					$up->upload_dir = $dir_path . "/";
					$final_f_name = $up->upload();
					$final_f_name = $dir_path . "/" . $final_f_name;
					array_push($insert_array, $final_f_name);
					//var_dump($up);
				    } else {
					$checker ++;
				    }
				    $errors = new WP_Error();	//Error object
				    $errors->add('music_added_sucessfully', __('<strong>SUCCESS:</strong>: ' . ($c - $checker) . ' files uploaded sucessfully!.'));
				}
				
				if (sizeof($insert_array) > 0) {
				    //=> There is one or more file uploaded, please insert it into Db
				    rv_add_artist_music($artist_id, $insert_array);
				}
				
				if ($checker == $c) {
				    $errors = new WP_Error();	//Error object
				    $errors->add('music_notadded', __('<strong>ERROR</strong>: Please select atleast one file to upload!.'));
				}	
			    }
			    
			    //Error display for IF loop
			    if(is_wp_error($errors)){
				echo("<div class=\"error\">" . $errors->get_error_message() . "</div>");
				unset($errors);
			    }
			    
			    //Show Add music form
			    add_artist_music_form();
			} else {
			    $errors = new WP_Error();	//Error object
			    $errors->add('artist_notfound', __('<strong>ERROR</strong>: The artist ID you entered is not found.'));
			}
            
			//Error display for ELSE loop
			if(is_wp_error($errors)){
			    echo("<div class=\"error\">" . $errors->get_error_message() . "</div>");
			}
			//$wpdb->show_errors();
		    }
		?>
                </div>
            </div>
            <div id="plainContentBottom"></div>
	</div>	
    </div>		
</div>

<!--Middle section END-->
<?php get_footer(); ?>