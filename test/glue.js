//var host = '../index.php' ;
var cache_timer ;


var apicall = function(url) {
	
	$('.test_link').unbind('click') ;
	
	
	var return_data = 'leer' ;
	
	$('#right_div span').css({opacity: 0.3}) ;
	$('#json_result').css({opacity: 0.3}) ;
	$('#json_result').val('') ;
	$('.loader').css({opacity: 1}) ;
	
	$('#cache').empty() ;
	$('#cache_rough').empty() ;
	
	
	var ms = 0 ;
	var timer ;
	
	var stopwatch = function() {
		
		ms += 10 ;
		if(ms % 1000 == 0)
		{	
			s = (ms/1000)+'.0'  ;
		}
		else
		{
			s = ms/1000 ;
		}
		
		$('#stopwatch').html(s+'s') ;
		
		
		timer = setTimeout(stopwatch, 10) ;
	}
	
	timer = setTimeout(stopwatch, 100) ;
	

	clearTimeout(cache_timer) ;
	
	
	$.ajax({
		async: true,
		url: url,
		type: 'POST',
		dataType: 'text',
		success: function(data){
				
			clearTimeout(timer) ;

			try
			{
				data = JSON.parse(data) ;
				//data = JSON.stringify(data, {}, '	') ;
			}
			catch(error) {}

			$('#right_div span').css({opacity: 1}) ;
			$('#json_result').css({opacity: 1}) ;
			
			$('#json_result pre').html(data.data) ;
			$('#cache').html(data.cache_period) ;
			$('#cache').prop('secs', data.cache_period) ;
			
			
			$('.loader').css({opacity: 0}) ;
			
			
			$('.test_link').click(function() {
				apicall($(this).attr('href')) ;
				return false ;
			}) ;
			
			var conversions = {
				'sec': 1,
				'min': 60,
				'hr': 60,
				'd': 24,
				'wk': 7,
				'mth': 4,
				'yr': 12
			} ;

			
			
			

			var countdown = function() {

				var current = $('#cache').prop('secs') ;
				current = current - 1 ;
				
				
				
				$('#cache').prop('secs', current) ;
				$('#cache').html('('+current+'s)') ;




				var units ;
				var delta = current ;
				
				if(delta <= 120)
				{
					var rough = 'refresh imminent' ;
				}
				else
				{
					
				for(var key in conversions)
				{
					if(delta < conversions[key]) {
						break;
					}
					else {
						units = key;
						delta = delta / conversions[key];
					}
				}
				
				if(units === 'minute' && delta <= 10)
				{
					delta = (Math.round(delta*10)/10);
				}
				else
				{
					delta = Math.round(delta);
				}
				
				

				if(delta != 1)
				{
					units = units + 's' ;
				}
				
				var rough = delta+' '+units ;

				}

				$('#cache_rough').html(rough) ;

				if(current <= 0)
				{
					$('#cache_rough').html('no') ;
					return true ;
				}

				cache_timer = setTimeout(countdown, 1000) ;
			}
			
			//cache_timer = setTimeout(countdown, 1000) ;
			countdown() ;
			
		}
	}) ;
	
	return return_data ;
} ;


$(document).ready(function() {
	
	$('.loader').css({opacity: 0}) ;
	
	$('.test_link').click(function() {
		apicall($(this).attr('href')) ;
		return false ;
	}) ;
	
	
}) ;