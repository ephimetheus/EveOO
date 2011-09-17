<?php

$character = $eve->getCharacter('90423421') ;

$mailbodies = $character->getMailBodies(array('ids' => array('306700306', '306676108', '306725618'))) ;


draw_tree($mailbodies) ;
