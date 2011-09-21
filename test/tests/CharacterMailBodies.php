<?php

$character = new Character('90423421') ;

//$mailbodies = $character->getMailBodies(array('ids' => array('307459344', '307452903', '307442749'))) ;

if($mailbodies = $character->getEmptyMailBodies())
{
	$mailbodies->persist() ;
}



draw_tree($mailbodies) ;
