<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
	<title>EveOO</title> 
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"> 

	<script type="text/javascript" src="jquery.js"></script> 
	<script type="text/javascript" src="json2.js"></script> 
	<script type="text/javascript" src="glue.js"></script> 
	
	<style type="text/css">

	body {
		padding:0px;
		margin:0px;
		font-family:Arial;
	}
	
	.test_link {
		padding:0px 10px 0px 10px;
		display:block;
		text-decoration:none;
		color:#000000;
		font-size:14px;
	}
	
	.test_link:hover, .test_link.active {
		background-color:gray;
		color:#ffffff;
	}
	
	.sec {
		padding:20px 0px 10px 10px;
		display:block;
	}
	
	</style>
	
	</head> 
<body>

	<div style="width:200px;border:1px solid grey;position:absolute;top:20px;bottom:100px;left:20px;overflow:auto;padding-bottom:30px;">
   
		<?php
		
		$json = json_decode(file_get_contents('order.json')) ;	

		foreach($json as $section => $subs)
		{
			echo '<strong class="sec">'.$section.'</strong>' ;
			foreach($subs as $link)
			{
				echo '<a class="test_link" href="../index.php?test='.$section.$link.'">'.$link.'</a>' ;	
			}
		}

		?>
		
    </div>

	<div style="position:absolute;bottom:27px;left:20px;border:1px solid gray;width:180px;padding:10px;font-size:14px;">
		Response time: <span id="stopwatch"></span><br/>
		Cached: <span id="cache_rough"></span> <span id="cache"></span>
	</div>

    <div style="position:absolute;right:45px;left:240px;top:20px;bottom:50px;" id="right_div">
    <img src="ajax-loader.gif" class="loader" style="position:absolute;left:50%;top:50%;margin-left:-8px;margin-top:-5px;" />
   		<div style="border:1px solid gray;width:100%;height:100%;overflow:auto;padding:10px;" id="json_result"><pre style="margin:0px;"></pre></div>
    </div>

</body>
</html>
