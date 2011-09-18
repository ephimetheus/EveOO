<?php

/**
 * Base class for all classes representing eve api scopes.
 *
 * @package default
 * @author Paul Gessinger
 */
abstract class EveApiObject implements IteratorAggregate, ArrayAccess
{
	protected $global = array() ;
	var $embedded = null ;
	
	
	/**
	 * Map calls to the object to api calls. getXX gets the content from either cache or api and just returns it,
	 * loadXX incorporates the data into the $embedded property.
	 *
	 * @param string $call 
	 * @param string $arguments 
	 * @return void
	 * @author Paul Gessinger
	 */
	function __call($call, $arguments = array())
	{
		if(substr($call, 0, 3) !== 'get')
		{
			if(substr($call, 0, 4) !== 'load')
			{
				throw new EveException('Unable to parse magic method call as api call.') ;
			}
			else
			{
				$method = substr($call, 4) ;
				return $this->loadIntoObject($method, $arguments) ;
			}
			
		}
		else
		{
			$method = substr($call, 3) ;
		}
		
		if(count($arguments) !== 0)
		{
			$arguments = $arguments[0] ;
		}
		
		$result = $this->call($this->scope, $method, $arguments) ;
		
		return $result ;
	}
	
	
	/**
	 * Merges the global values for this scope into the arguments given, and passes on to Eve
	 *
	 * @param string $scope 
	 * @param string $method 
	 * @param string $arguments 
	 * @return void
	 * @author Paul Gessinger
	 */
	function call($scope, $method, $arguments = array())
	{
		if(!is_array($arguments))
		{
			throw new EveException('$arguments is not an array. Invalid format.') ;
		}
		
		$arguments = array_merge($this->global, $arguments) ;
		
		
		
		return $this->eve->call($scope, $method, $arguments) ;
	}
	
	
	/**
	 * Embeds data from an api call (or cache) into this objects $embedded property.
	 *
	 * @param string $method 
	 * @param string $arguments 
	 * @return void
	 * @author Paul Gessinger
	 */
	function loadIntoObject($method, $arguments)
	{
		if($this->embedded === null)
		{
			$this->embedded = new EveArray ;
		}
		
		if(!isset($this->embedded[$method]))
		{
			//$tmp = $this->call($this->scope, $method, $arguments) ;
			
			$tmp = call_user_func_array(array($this, 'get'.$method), array($arguments)) ;
			foreach($tmp as $key => $value)
			{
				$this->embedded[$key] = $value ;
			}
		}
		
		return $this ;
	}
	
	
	/**
	 * Alias for loadIntoObject, allowing for multiple methods to be incorporated at once.
	 *
	 * @param array $methods 
	 * @return void
	 * @author Paul Gessinger
	 */
	function load(array $methods)
	{
		foreach($methods as $method)
		{
			$arguments = array() ;
			if(isset($method[1]) AND is_array($method[1]))
			{
				$arguments = $method[1] ;
			}
			
			$this->loadIntoObject($method[0], $arguments) ;
		}
		
		return $this ;
	}
	
	
	/**
	 * Magic getter for accessing properties within $embedded.
	 *
	 * @param string $name 
	 * @return void
	 * @author Paul Gessinger
	 */
	function __get($name) 
	{
		if(isset($this->embedded[$name]))
		{
			return $this->embedded[$name] ;
		}
		else
		{
			return null ;
		}
	}
	
	
	/**
	 * Magic setter for properties within $embedded.
	 *
	 * @param string $name 
	 * @param string $value 
	 * @return void
	 * @author Paul Gessinger
	 */
	function __set($name, $value)
	{
		return $this->embedded[$name] = $value ;
	}
	
	
	/**
	 * Setter for ArrayAccess
	 *
	 * @param string $offset 
	 * @param string $value 
	 * @return void
	 * @author Paul Gessinger
	 */
	public function offsetSet($offset, $value)
	{
		$this->embedded[$offset] = $value ;
	}
	
	
	/**
	 * Isset implementation for ArrayAccess
	 *
	 * @param string $offset 
	 * @return void
	 * @author Paul Gessinger
	 */
	public function offsetExists($offset)
	{
		return isset($this->embedded[$offset]);
	}
	
	
	/**
	 * Unset implementation for ArrayAccess
	 *
	 * @param string $offset 
	 * @return void
	 * @author Paul Gessinger
	 */
	public function offsetUnset($offset)
	{
		unset($this->embedded[$offset]) ;
	}
	
	
	/**
	 * Unset implementation for ArrayAccess
	 *
	 * @param string $offset 
	 * @return void
	 * @author Paul Gessinger
	 */
	public function offsetGet($offset)
	{
		return isset($this->embedded[$offset]) ? $this->embedded[$offset] : null;
	}
	
	
	/**
	 * Returns an iterator for $embedded.
	 *
	 * @return void
	 * @author Paul Gessinger
	 */
	function getIterator()
	{
		if($this->embedded === null)
		{
			$this->embedded = new EveArray ;
		}
		
		return $this->embedded->getIterator() ;
	}
}