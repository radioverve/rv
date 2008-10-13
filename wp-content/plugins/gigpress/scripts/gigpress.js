$gp=jQuery.noConflict();

$gp(document).ready(function()
	{

		var time = $gp('select#hh option:selected').parent().attr('label');
		$gp('span#ampm').text(time);
		
		$gp('select#hh').change(function()
			{
				var time = $gp('select#hh option:selected').parent().attr('label');
				$gp('span#ampm').text(time);
			}
		);
		
		$gp('tr#expire.inactive').hide();
		
		$gp('input#multi').click(function()
			{
				$gp('tr#expire').toggle();
				this.blur();
			}
		);
		
		$gp('input.required').each(function(){
		
			$gp(this).blur(function(){
			  
			  var e = $gp(this);
			  
			  if (e.val() == "") {
				e.addClass("missing");
			  }
			  
			  if (e.val() != "") {
				e.removeClass("missing");
			  }	
			  		  
			});
			
		});
					
	}
);