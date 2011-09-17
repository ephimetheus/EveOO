<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
	<title>EveOO</title> 
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"> 

	<script type="text/javascript" src="jquery.js"></script> 
	<script type="text/javascript" src="json2.js"></script> 
	<script type="text/javascript" src="glue.js"></script> 
	
	</head> 
<body>

	<div style="width:33%;float:left;margin-right:20px;margin-bottom:20px;">
   
		<?php
		
		$json = json_decode(file_get_contents('order.json')) ;	

		foreach($json as $section => $subs)
		{
			echo '<strong>'.$section.'</strong><br/>' ;
			foreach($subs as $link)
			{
				echo '<a class="test_link" href="../index.php?test='.$link.'">'.$link.'</a><br/>' ;	
			}
			echo '<br/>' ;
		}

		?>
		
		Response time: <span id="stopwatch"></span>
		
    </div>
    <div style="width:85%;height:99%;position:absolute;right:20px;" id="right_div">
    <img src="ajax-loader.gif" class="loader" style="position:absolute;left:50%;top:50%;margin-left:-8px;margin-top:-5px;" />
    	<span>Result: <br/></span>
   		<div style="border:1px solid gray;width:100%;height:95%;overflow:auto;padding:10px;" id="json_result"><pre style="margin:0px;"></pre></div>
    </div>

</body>
</html>
