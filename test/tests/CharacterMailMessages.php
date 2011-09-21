<?php

$character = new Character('90423421') ;

$mail = $character->getMailMessages() ;

$mail->persist(true) ;

draw_tree($mail) ;