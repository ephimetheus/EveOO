<?php

$istan = new Character('90423421') ;

$transactions_istan = $istan->getWalletTransactions() ;

$transactions_istan->persist(false) ;

$tsunta = new Character('90475343') ;

$transactions_tsunta = $tsunta->getWalletTransactions() ;

$transactions_tsunta->persist(false) ;

draw_tree($trans) ;