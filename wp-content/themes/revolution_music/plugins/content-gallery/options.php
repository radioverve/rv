<?php
$location = $options_page; // Form Action URI
?>

<div class="wrap">
	<h2>Featured Content Gallery Configuration</h2>
	<p>Use the fields below to customize your gallery width, height, text overlay height, the category you want to use for gallery content as well as the number of gallery items you want to be displayed.</p>
	
    <div style="margin-left:0px;">
    <form method="post" action="options.php"><?php wp_nonce_field('update-options'); ?>
		<fieldset name="general_options" class="options">

        Gallery Width in Pixels:<br />
		<div style="margin:0;padding:0;">
        <input name="gallery-width" id="gallery-width" size="25" value="<?php echo get_option('gallery-width'); ?>"></input>
        </div><br />
        
        Gallery Height in Pixels:<br />
		<div style="margin:0;padding:0;">
        <input name="gallery-height" id="gallery-height" size="25" value="<?php echo get_option('gallery-height'); ?>"></input> 
        </div><br />
        
        Text Overlay Height in Pixels:<br />
		<div style="margin:0;padding:0;">
        <input name="gallery-info" id="gallery-info" size="25" value="<?php echo get_option('gallery-info'); ?>"></input> 
        </div><br />
        
        Category Name:<br />
		<div style="margin:0;padding:0;">
        <input name="gallery-category" id="gallery-category" size="25" value="<?php echo get_option('gallery-category'); ?>"></input>   
        </div><br />
        
        Number of Items to Display:<br />
		<div style="margin:0;padding:0;">
        <input name="gallery-items" id="gallery-items" size="25" value="<?php echo get_option('gallery-items'); ?>"></input>   
        </div><br />
                
        <input type="hidden" name="action" value="update" />

		</fieldset>
		<p class="submit"><input type="submit" name="Submit" value="<?php _e('Update Options') ?>" /></p>
	</form>      
</div>