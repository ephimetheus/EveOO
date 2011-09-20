<?php

abstract class AbstractPersistor implements PersistorInterface
{
	var $data = false ;
	
	function setData($data)
	{
		$this->data = $data ;
	}
	
	function persist($force = false)
	{
		if($this->data === false)
		{
			throw new EveException('Cannot persist without data.') ;
		}
		
		if(!($this->data->isFromCache()) OR $force === true)
		{
			$this->performPersist() ;
		}
	}
}