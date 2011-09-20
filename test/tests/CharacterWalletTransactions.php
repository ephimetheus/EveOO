<?php

$character = new Character('90423421') ;

$trans = $character->getWalletTransactions() ;

$trans->persist(false) ;

draw_tree($trans) ;