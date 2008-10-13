// JavaScript Document - Jquery version


//use in no conflict mode
//the timeout is to solve a Safari problem.
setTimeout(function() {
					
jQuery(document).ready(function($){
       // Do jQuery stuff using $
      
		//get all sidebars
		$("div.fw_tabs_tabdisplay").each(function(i){
			
			$(this).attr("id" , $(this).attr("id").replace('_x',''));
			
			//get the width of the container
			totalWidth = $(this).width();
						
			/************************************/
			//this is the extra added to compensate for the plus or minus sign
			//you can modify this if you need to, to compensate.
			control_margin = 20;			
			/************************************/			
						
			//the width of tabs
			cumulativeTabWidth = 0;
			
			//previous value rounded up
			previousRoundedUp = 1;
			
			//current tab level
			currentTabLevel = 1;
			
			//get the tabs
			$("li.tab", $(this)).each(function(ti){
				//add clickyness to them
				$(this).find("h3.fwTabTitle").click( fwst_display_tab )
				
				//calculate tab levels
				cumulativeTabWidth += $(this).width(); 
				
				widthRatio = ( cumulativeTabWidth + control_margin ) / totalWidth;
				
				//if this is the first tab of a new level then we need to start the
				//cumulative width as the container width not carry on where we left off
				//so recalculate the width ratio using the dual control margin option
				if ( widthRatio > previousRoundedUp ) {
					cumulativeTabWidth = ( totalWidth * previousRoundedUp )  + $(this).width();
				}
												
				//recalculate to account for 2nd control box if level 2 or higher
				if ( widthRatio > 1 ) { widthRatio = ( cumulativeTabWidth + (control_margin * 2) ) / totalWidth; } 
						
				//add tab level class
				$(this).addClass('fwttl'+Math.ceil( widthRatio ));
				
				if ( widthRatio > 1 ){ 
					//if non-primary layer then hide it	
					$(this).css("display","none"); 
				}
				
				if ( widthRatio > previousRoundedUp ){ 
					//if this is the first item in a new layer add controls to the previous layer
					//to switch to this layer
					//if not on layer 1 then make hidden
					if ( previousRoundedUp > 1 ) {
						$("<li><h3>+</h3></li>").addClass('tab').css("display","none").addClass('fwttl'+previousRoundedUp).click(fwst_display_tab_level).insertBefore($(this));
					} else {
						$("<li><h3>+</h3></li>").addClass('tab').addClass('fwttl'+previousRoundedUp).click(fwst_display_tab_level).insertBefore($(this));
					}
					//going to need another one for going the other way
					$("<li><h3>-</h3></li>").addClass('tab').css("display","none").addClass('fwttl'+Math.ceil( widthRatio )).click(fwst_display_tab_level).insertBefore($(this));
				
				}
				
				previousRoundedUp = Math.ceil( widthRatio );
								
			});
			
			//make the first tab selected
			$('li.tab:first h3.fwTabTitle' ,$(this)).click();
			
		});
	  
		function fwst_display_tab( eventObject ){
			
			$(this).parents('ul').children('li').find('h3.fwTabTitle').removeClass('selected');
			$(this).addClass('selected');
			$(this).parents('div.fw_tabs_tabdisplay').children('div').fadeOut('fast');
			$(this).parents('div.fw_tabs_tabdisplay').children('div').queue( function(clicked){
				$(this).empty();
				$(this).append($(this).parents('div.fw_tabs_tabdisplay').find('ul li h3.fwTabTitle.selected').siblings('div.tab-content').clone(true));
				$(this).dequeue();
				}
			)
			$(this).parents('div.fw_tabs_tabdisplay').children('div').fadeIn('fast');
			
			
		}
		
		function fwst_display_tab_level ( eventObject ){
			
			//hide the old ones
			$( 'li.fwttl'+ currentTabLevel , $(this).parents('div.fw_tabs_tabdisplay') ).hide();

			//show the new ones
			if ( $("h3" , $(this)).text() == '+' ){ 
				$( 'li.fwttl'+ (currentTabLevel+1) , $(this).parents("ul") ).show(); 
				currentTabLevel++;
				}
			if ( $("h3" , $(this)).text() == '-' ){ 
				$( 'li.fwttl'+ (currentTabLevel-1) , $(this).parents("ul") ).show(); 
				currentTabLevel--;
				}
			
		}
	  
     });

});