<?php

ob_start() ;

if(!isset($_GET['test']))
{
	header('Location: test') ;
	
	die() ;
}

$starttime = microtime(); 
$starttime = explode(" ",$starttime); 
$starttime = $starttime[1] + $starttime[0];

require_once 'inc/EveOO.php' ;

$eve = new Eve(array(
	'cache_compress' => false, // you must clear cache if you change this.
	//'cache_dir' => 'inc/cache/'
)) ;

$eve->setKeyID('33338') ;
$eve->setVCode('jNNcOcS2Q6t0RJRybautXVSFTGc6siXfGNOwEj67zABoJS8RCopN1ZrplVfOFGqc') ;

try
{



function draw_tree($var, $depth = 0)
{
	$newline = "\n";
	$indent = '&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' ;
	//$space = '' ;
	
	foreach($var as $k => $v)
	{
		if(is_string($v))
		{
			for($i = 0;$i<$depth;$i++)
			{
				echo $indent ;
			}
			
			echo '[<strong>'.$k.'</strong>] => '.$v.$newline ;
		}
		
		if(is_array($v) OR is_object($v))
		{
			for($i = 0;$i<$depth;$i++)
			{
				echo $indent ;
			}
			
			echo '[<strong>'.$k.'</strong>] => {'.$newline.'' ;
			draw_tree($v, $depth+1) ;
			
			
			for($i = 0;$i<($depth);$i++)
			{
				echo $indent ;
			}
			
			echo '} '.$newline ;
		}
	}
}

	if(!file_exists('test/tests/'.$_GET['test'].'.php'))
	{
		draw_tree(array('status' => 'fault', 'message' => 'Unable to load test.')) ;	
	}
	else
	{
		include 'test/tests/'.$_GET['test'].'.php' ;
	}



}
catch(Exception $e)
{
	draw_tree(array('status' => 'fault', 'message' => $e->getMessage(), 'code' => $e->getCode(), 'line' => (string)$e->getLine(), 'file' => $e->getFile())) ;
	
}


$ob = ob_get_clean() ;

$endtime = microtime(); 
$endtime = explode(" ",$endtime); 
$endtime = $endtime[1] + $endtime[0];

$totaltime = round(($endtime - $starttime)*1000, 2);

echo json_encode(array('data' => $ob, 'cache_period' => Cache::getInstance()->getCachePeriod())) ;


