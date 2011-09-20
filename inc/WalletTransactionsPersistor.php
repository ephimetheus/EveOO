<?php

class WalletTransactionsPersistor extends AbstractPersistor
{
	function __construct() {}
	
	function performPersist()
	{
		foreach($this->data->transactions as $transaction)
		{
			$bean = R::findOrDispense('transaction', 'transactionID=?', array($transaction->transactionID)) ;
			
			$bean = array_shift($bean) ;

			
			foreach($transaction as $k => $v)
			{
				$bean->{$k} = $v ;
			}
			
			R::store($bean) ;
		}
	}
}