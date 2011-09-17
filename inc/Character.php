<?php

class Character extends EveApiObject
{
	protected $scope = 'char' ;
	var $eve ;
	
	function __construct($characterID, $eve)
	{
		$this->global['characterID'] = $characterID ;
		$this->eve = $eve ;
	}
	
	function getMailBodies($param)
	{
		
		if(is_array($param['ids']))
		{
			$param['ids'] = implode(',', $param['ids']) ;
		}
		
		
		
		
		$result = $this->call($this->scope, 'MailBodies', $param) ;
		
		foreach($result->messages as $key => $message)
		{
			$message->data = htmlentities($message->data) ;
		}
		
		return $result ;
	}
	
	function getMailMessages()
	{
		$result = $this->call($this->scope, 'MailMessages', array()) ;
		return new MessageResult($result) ;
	}
	
	/*function getCharacterSheet()
	{
		$data = $this->call($this->scope, 'CharacterSheet', array()) ;
		
		$this->data = $data ;
	}*/
	
}