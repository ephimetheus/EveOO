<?php

/**
 * Persistor for MailMessages under char scope.
 *
 * @package EveOO
 * @author Paul Gessinger
 */
class CharMailMessagesPersistor extends AbstractPersistor
{
	function __construct() {}
	
	
	/**
	 * Do the actual persist.
	 *
	 * @return void
	 * @author Paul Gessinger
	 */
	function performPersist()
	{
		
		$character = $this->data->registry['scope'] ;
		
		foreach($this->data->messages as $message)
		{
			$bean = R::findOne('message', 'messageID=?', array($message->messageID)) ;
			
			$id = $bean->{BeanFormatter::_formatBeanId('message')} ;
			
			if(!empty($id))
			{
				continue;
			}
			
			$bean = R::dispense('message') ;
			
			$bean->characterID = $character->global['characterID'] ;
			$bean->message = null ;
			
			foreach($message as $k => $v)
			{
				$bean->{$k} = utf8_encode($v) ;
			}
			
			R::store($bean) ;
		}
	}
}