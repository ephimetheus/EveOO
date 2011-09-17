<?php

abstract class EveApiObject implements IteratorAggregate, ArrayAccess
{
	protected $global = array() ;
	var $embedded = null ;
	
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
	
	function call($scope, $method, $arguments = array())
	{
		if(!is_array($arguments))
		{
			throw new EveException('$arguments is not an array. Invalid format.') ;
		}
		
		$arguments = array_merge($this->global, $arguments) ;
		
		
		
		return $this->eve->call($scope, $method, $arguments) ;
	}
	
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
	
	function __set($name, $value)
	{
		return $this->embedded[$name] = $value ;
	}
	
	public function offsetSet($offset, $value)
	{
		$this->embedded[$offset] = $value ;
	}
	
	public function offsetExists($offset)
	{
		return isset($this->embedded[$offset]);
	}
	public function offsetUnset($offset)
	{
		unset($this->embedded[$offset]) ;
	}
	
	public function offsetGet($offset)
	{
		return isset($this->embedded[$offset]) ? $this->embedded[$offset] : null;
	}
	
	function getIterator()
	{
		if($this->embedded === null)
		{
			$this->embedded = new EveArray ;
		}
		
		return $this->embedded->getIterator() ;
	}
}