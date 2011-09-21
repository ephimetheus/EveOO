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
		$character = $this->data->registry['scope'] ;
		
		foreach($this->data->transactions as $transaction)
		{
			$bean = R::findOne('transaction', 'transactionID=?', array($transaction->transactionID)) ;
			
			$id = $bean->{BeanFormatter::_formatBeanId('transaction')} ;
			
			if(!empty($id))
			{
				continue;
			}
			
			$bean = R::dispense('transaction') ;

			$bean->characterID = $character->global['characterID'] ;
			
			foreach($transaction as $k => $v)
			{
				$bean->{$k} = $v ;
			}
			
			R::store($bean) ;
		}
	}
}