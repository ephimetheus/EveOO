<?php

/**
 * Persistor for MailBodies under char scope.
 *
 * @package EveOO
 * @author Paul Gessinger
 */
class CharMailBodiesPersistor extends AbstractPersistor
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
		foreach($this->data->messages as $message)
		{
			$bean = R::findOne('message', 'messageID=?', array($message->messageID)) ;
			
			$id = $bean->{BeanFormatter::_formatBeanId('message')} ;
			
			if(empty($id))
			{
				continue;
			}
			
			//$bean = R::dispense('message') ;
			
			$bean->message = utf8_encode(html_entity_decode($message->data)) ;
			
			R::store($bean) ;
		}
	}
}