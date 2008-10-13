// Ajax Comment Posting
// WordPress plugin
// version 1.2.3
// author: regua
// http://regua.biz

jQuery(document).ready(function(){
						   
	jQuery.noConflict();
						   
	jQuery('#commentform').after('<div id="error"></div>');
	jQuery('#submit').after('<img src="wp-content/plugins/ajax-comment-posting/loading.gif" id="loading" alt="'+loading+'" />');
	jQuery('#loading').hide();
	var form = jQuery('#commentform');
	var err = jQuery('#error');
	
	// WP Ajax Edit Comments hook
	if (window.AjaxEditComments) {
   	AjaxEditComments.init();
	} // end if
	
    form.submit(function() { 
    
  if(form.find('#author')[0]) {
      if(form.find('#author').val() == '') {
		   err.html('<span class="error">'+enter_name+'</span>');
		   return false;
	   } // end if
		if(form.find('#email').val() == '') {
			err.html('<span class="error">'+enter_email+'</span>');
			return false;
		} // end if
		var filter  = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		if(!filter.test(form.find('#email').val())) {
			err.html('<span class="error">'+enter_valid+'</span>');
			return false;
		} // end if
	} // end if
	
	if(form.find('#comment').val() == '') {
		err.html('<span class="error">'+enter_comment+'</span>');
		return false;
	} // end if
	
    jQuery(this).ajaxSubmit({
						   
		beforeSubmit: function() {
			jQuery('#loading').show();
			jQuery('#submit').attr('disabled','disabled');
		}, // end beforeSubmit

		error: function(request){
				err.empty();
				if (request.responseText.search(/<title>WordPress &rsaquo; Error<\/title>/) != -1) {
					var data = request.responseText.match(/<p>(.*)<\/p>/);
					err.html('<span class="error">'+ data[1] +'</span>');
				} else {
					var data = request.responseText;
					err.html('<span class="error">'+ data[1] +'</span>');
				}
				jQuery('#loading').hide();
				jQuery('#submit').removeAttr("disabled");
				return false;
		}, // end error()

        success: function(data) {
            try {
                var response = jQuery("<ol>").html(data);
					 if (jQuery(document).find('.commentlist')[0]) {
							jQuery('.commentlist').append(response.find('.commentlist li:last'));
					 } else {
							jQuery('#respond').before(response.find('.commentlist'));
					 } // end if
					 if (jQuery(document).find('#comments')[0]) {
					 		jQuery('#comments').html(response.find('#comments'));
					 } else {
					 		jQuery('.commentlist').before(response.find('#comments'));
					 } // end if
					 err.empty();
					 form.remove(); // REMOVE THIS IF YOU DON'T WANT THE FORM TO DISAPPEAR
					 jQuery('#respond').hide();
					 err.html('<span class="success">Your comment has been added.</span>');
					 jQuery('#submit').removeAttr("disabled");
                jQuery('#loading').hide();	
                
            } catch (e) {
                jQuery('#loading').hide();
				    jQuery('#submit').removeAttr("disabled");
                 alert(error+'\n\n'+e);
            } // end try
            
            // WP Ajax Edit Comments hook
				if (window.AjaxEditComments) {
   				AjaxEditComments.init();
				} // end if
						   
			} // end success()
			
		}); // end ajaxSubmit()
		
        return false; 
		
	}); // end form.submit()
}); // end document.ready()
										
