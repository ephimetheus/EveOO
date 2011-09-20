<?php

class Persistence
{
	static protected $instance ;
	protected $redbean ;
	static protected $db_data = array() ;
	
	
	/**
	 * Constructs the persistence singleton. This configures the redbean lib with the values found in $db_data
	 *
	 * @author Paul Gessinger
	 */
	private function __construct() 
	{
		if(count(Persistence::$db_data) === 0)
		{
			throw new EveException('Cannot create database connection, not configured.') ;
		}
		
		include Eve::$path.'rb.php' ;
		
		$this->redbean = RedBean_Setup::kickstart(
			'mysql:host='.Persistence::$db_data['host'].';dbname='.Persistence::$db_data['name'].'', 
			Persistence::$db_data['usr'], 
			Persistence::$db_data['pwd']) ;
		
		$dbo = $this->redbean->getDatabaseAdapter() ;
		$dbo->exec("SET CHARACTER SET utf8") ; 
			
		R::configureFacadeWithToolbox($this->redbean) ;
		R::$writer->setBeanFormatter(new BeanFormatter()) ;
	}
	
	
	/**
	 * Store db configuration for redbean. Can only be called before the singleton has been instantiated.
	 *
	 * @param array $db_data 
	 * @return void
	 * @author Paul Gessinger
	 */
	static function configure(array $db_data)
	{
		if((self::$instance instanceof Persistence))
		{
			throw new EveException('Cannot configure Persistence after it has been instantiated.') ;
		}
		
		$merge = array(
			'host' => null,
			'name' => null,
			'usr' => null,
			'pwd' => null
		) ;
		
		Persistence::$db_data = array_merge($merge, $db_data) ;
	}
	
	/**
	 * Singleton getter for the object, creates instance on first call.
	 *
	 * @return void
	 * @author Paul Gessinger
	 */
	static function getInstance()
	{
		if(!(self::$instance instanceof Persistence))
		{
			self::$instance = new Persistence() ;
		}
	
		return self::$instance ;
	}
	
	
}