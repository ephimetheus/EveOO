//var host = '../index.php' ;

var apicall = function(url) {
	
	$('.test_link').unbind('click') ;
	
	
	var return_data = 'leer' ;
	
	$('#right_div span').css({opacity: 0.3}) ;
	$('#json_result').css({opacity: 0.3}) ;
	$('#json_result').val('') ;
	$('.loader').css({opacity: 1}) ;
	
	
	var ms = 0 ;
	var timer ;
	
	var stopwatch = function() {
		
		/*if(ms >= 90)
		{
			ms = 0 ;
			s += 1 ;
		}
		else
		{
			ms += 1 ;
			$('#stopwatch span').html(s+'.'+ms) ;
			
		} */
		
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
				data = JSON.stringify(data, {}, '	') ;
			}
			catch(error) {}

			$('#right_div span').css({opacity: 1}) ;
			$('#json_result').css({opacity: 1}) ;
			$('#json_result pre').html(data) ;
			$('.loader').css({opacity: 0}) ;
			
			
			
			$('.test_link').click(function() {
				apicall($(this).attr('href')) ;
				return false ;
			}) ;
			
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