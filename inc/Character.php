<?php
/**
 * Object for interfacing with characters.
 *
 * @package EveOO
 * @author Paul Gessinger
 */
class Character extends EveApiObject
{
	protected $scope = 'char' ;
	var $eve ;
	
	
	/**
	 * Takes the character id and saves it to the global param array, which is then merged into every call's arguments.
	 *
	 * @param string $characterID 
	 * @author Paul Gessinger
	 */
	function __construct($characterID)
	{
		$this->global['characterID'] = $characterID ;
		$this->eve = Eve::$instance ;
	}
	
	
	/**
	 * Alias for MailBodies to call htmlentities() on message contents and allow passing of an array of ids to fetch.
	 *
	 * @param string $param 
	 * @return void
	 * @author Paul Gessinger
	 */
	function getMailBodies($param)
	{
		if(is_array($param['ids']))
		{
			$param['ids'] = implode(',', $param['ids']) ;
		}
		
		
		
		$result = $this->call($this->scope, 'MailBodies', $param, false) ;
		
		foreach($result->messages as $key => $message)
		{
			$message->data = htmlentities($message->data) ;
		}
		
		
		return $result ;
	}
	
	function getEmptyMailBodies()
	{
		if(!Persistence::isConfigured())
		{
			throw new EveException('Cannot use getALlMailBodies when not using database persistence.') ;
		}
		
		Persistence::launch() ;
		
		$empties = R::find('message', 'message is NULL AND characterID=?', array($this->global['characterID'])) ;
		
		if(count($empties) === 0)
		{
			return false ;
		}
		
		$id_array = array() ;
		
		foreach($empties as $bean)
		{
			$id_array[] = $bean->messageID ;
		}
		
		return $this->getMailBodies(array('ids' => implode(',', $id_array))) ;
	}
	
}