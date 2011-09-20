<?php

/**
 * Persistor for WalletTransactions under char scope.
 *
 * @package EveOO
 * @author Paul Gessinger
 */
class CharWalletTransactionsPersistor extends AbstractPersistor
{
	function __construct() {}
	
	
	/**
	 * Do the actual persist. Try to locate transaction entries where the transactionID matches,
	 * if none is found, a new entry is created.
	 *
	 * @return void
	 * @author Paul Gessinger
	 */
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