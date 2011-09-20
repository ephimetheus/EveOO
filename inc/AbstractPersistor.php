<?php

/**
 * Template for al persistors.
 *
 * @package EveOO
 * @author Paul Gessinger
 */
abstract class AbstractPersistor implements PersistorInterface
{
	var $data = false ;
	
	
	/**
	 * Takes an EveApiResult that is to be the base for the persist activity. 
	 *
	 * @param string $data 
	 * @return void
	 * @author Paul Gessinger
	 */
	function setData(EveApiResult $data)
	{
		$this->data = $data ;
	}

	/**
	 * Checks if the data is coming from cache or from api. If it is coming from api
	 * or if $force is set to true, the persist activity is triggered.
	 * This can only be called after data has been entered.
	 *
	 * @param string $force 
	 * @return void
	 * @author Paul Gessinger
	 */
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