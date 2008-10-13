<?php
/*                                                                                                                                                                                 
Template Name: UploadMusic
*/
//wp_enqueue_script('jqmodal','/new/wp-includes/js/jquery/jqModal.js',array('jquery-latest'));
if(!is_user_logged_in()){
echo("<link rel=\"stylesheet\" href=\"/wp-content/themes/verve/thickbox.css\" type=\"text/css\" media=\"screen\" />");
wp_enqueue_script('thickbox');
}

$pmp = $_SERVER["REQUEST_URI"];    //Grab the page name using this hack, run the rest of the page through a case structure.
$tmp_url = explode("/", $pmp);

$page_name = $tmp_url[1];

//echo $page_name;
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
    get_modal_login("http://".$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
}
?>
        <div id="content">
	    <div id="plainContentTop"></div>
	    <div id="plainContentMiddle">
                <? if(!is_user_logged_in()) { ?>
                    <h2> Please Log In or Register to upload your music.</h2>
		    <a href="/new/login?height=200&width=300" title="Login" class="thickbox">Login or Register now</a>  
                    <!-- <div class="jqmWindow" id="ex2">
                        Please wait... <img src="/images/rotatingclock-slow.gif" alt="loading" />
                    </div>-->
                <? }else{ ?>
                        <div id="pagecontent">
                            <?
                                switch ($page_name) {
                                    case 'upload-music':
                                            //START of condition: upload-music
                                            global $userdata;
                                             get_currentuserinfo();
                                             $errors=NULL;
                                             if(isset($_POST["artistname"])||isset($_POST["firstname"])||isset($_POST["lastname"])||isset($_POST["email"])||isset($_POST["phonenumber"])||isset($_POST["address"])){
                                                 if(!isset($_POST["artistname"])||$_POST["artistname"]==''){
                                                     $errors = new WP_Error();
                                                     $errors->add('empty_username', __('<strong>ERROR</strong>: Please enter a username.'));
                                                 }
                                                 if(!isset($_POST["firstname"])||$_POST["firstname"]==''){
                                                     if(!$errors)
                                                         $errors = new WP_Error();
                                                     $errors->add('empty_firstname', __('<strong>ERROR</strong>: Please enter a First Name.'));
                                                 }
                                                 if(!isset($_POST["lastname"])||$_POST["lastname"]==''){
                                                     if(!$errors)
                                                         $errors = new WP_Error();
                                                     $errors->add('empty_lastname', __('<strong>ERROR</strong>: Please enter a Last Name.'));
                                                 }
                                                 if(!isset($_POST["email"])||$_POST["email"]==''||!is_email($_POST["email"])){
                                                     if(!$errors)
                                                         $errors = new WP_Error();
                                                     
                                                     $errors->add('empty_email', __('<strong>ERROR</strong>: Please enter a valid email.'));
                                                 }
                                                 if(!isset($_POST["phonenumber"])||$_POST["phonenumber"]==''){
                                                     if(!$errors)
                                                         $errors = new WP_Error();
                                                     
                                                     $errors->add('empty_firstname', __('<strong>ERROR</strong>: Please enter a phone number.'));
                                                 }
                                                 if(!isset($_POST["address"])||$_POST["address"]==''){
                                                     if(!$errors)
                                                         $errors = new WP_Error();
                                                     
                                                     $errors->add('empty_firstname', __('<strong>ERROR</strong>: Please enter a phone number.'));
                                                 }
                                                 
                                                 if(is_wp_error($errors)){
                                                     echo("<div class=\"error\">" . $errors->get_error_message() . "</div>");
                                                 }else{
                                                     //cho("What the fuck is happening?");
                                                     //$wpdb->show_errors();
                                                     insert_artist($userdata->ID);
                                                     //$wpd
                                                 }
                                             }
                                             
                                             //echo($userdata->ID);
                                             if(is_not_registered_artist($userdata->ID)){
                                                 show_first_artist_reg_page($userdata->ID,TRUE);
                                                 //echo(get_option("site_url"));
                                             }else {
                                                 show_registered_artists($userdata->ID);
                                                 show_first_artist_reg_page($userdata->ID,FALSE);
                                             }
                                             //END of condition: upload-music
                                            break;
                                    case 'update-profile':
                                            //START of condition: update-profile
                                            $artist_id = $_GET['aid'];
                                            if (rv_is_valid_owner_for_artist($userdata->ID, $artist_id) === TRUE) {
                                                
                                                //Assume form is submitted
                                                if (isset($_POST['edit_button']) and $_POST['edit_button'] != "") {
                                                    //lets enter the details into the DB
                                                    $artist_array = array();
                                                    $artist_array['artist_id'] = $_POST['artist_id'];
                                                    $artist_array['artistname'] = $_POST['artistname'];
                                                    $artist_array['firstname'] = $_POST['firstname'];
                                                    $artist_array['lastname'] = $_POST['lastname'];
                                                    $artist_array['email'] = $_POST['email'];
                                                    $artist_array['phonenumber'] = $_POST['phonenumber'];
                                                    $artist_array['address'] = $_POST['address'];
                                                    
                                                    //insert values into DB
                                                    rv_edit_artist($artist_array);
                                                    $errors = new WP_Error();	//Error object
                                                    $errors->add('artist_edited_sucessfully', __('<strong>Success!</strong>: The artist details have been sucessfully updated;.'));
                                                    
                                                    //Display errors if any
                                                    if(is_wp_error($errors)){
                                                        echo("<div class=\"error\">" . $errors->get_error_message() . "</div>");
                                                    }
                                                    
                                                }                                            
                                                //Normal behaviour 
                                                if ($artist_id) {
                                                    $artist_name = rv_get_artistname($artist_id);
                                                    echo "Hi " . $artist_name . ", Wassup?";
                                                    rv_show_edit_artist_page($artist_id, $artist_name);                                                
                                                } else {
                                                    $errors = new WP_Error();	//Error object
                                                    $errors->add('artist_notfounds', __('<strong>ERROR</strong>: The artist ID you entered is not found.'));
                                                    
                                                    //Display errors if any
                                                    if(is_wp_error($errors)){
                                                        echo("<div class=\"error\">" . $errors->get_error_message() . "</div>");
                                                    }                                                    
                                                    
                                                }
                                                                                               
                                            } else {
                                                $errors = new WP_Error();	//Error object
                                                $errors->add('artist_notfound', __('<strong>ERROR</strong>: The artist ID you entered is not found.'));
                                                
                                                //Display errors if any
                                                if(is_wp_error($errors)){
                                                    echo("<div class=\"error\">" . $errors->get_error_message() . "</div>");
                                                }
                                            }
                                            

                                            //END of condition: update-profile 
                                            break;                        
                                }
                                ?>
                        </div>
<?php               } ?>                
            </div>
            <div id="plainContentBottom"></div>
	</div>	
									
    </div>		
</div>

<!--Middle section END-->
<?php get_footer(); ?>