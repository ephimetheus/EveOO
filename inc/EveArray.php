<?php

/**
 * Simple wrapper for an array, allowing both array style access and object style access at the same time, and mixed. 
 * (stdClass does NOT do this)
 *
 * @package EveOO
 * @author Paul Gessinger
 */
class EveArray implements IteratorAggregate, ArrayAccess
{
	var $EveArrayData = array() ;
	
	
	/**
	 * Construct the EveArray with a data array.
	 *
	 * @param string $data 
	 * @author Paul Gessinger
	 */
	function __construct($data = array()) 
	{
		$this->EveArrayData = $data ;
	}
	
	
	/**
	 * Return an iterator for $EveArrayData
	 *
	 * @return void
	 * @author Paul Gessinger
	 */
	function getIterator()
	{
		return new ArrayIterator($this->EveArrayData) ;
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
		$this->EveArrayData[$offset] = $value ;
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
		return isset($this->EveArrayData[$offset]);
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
		unset($this->EveArrayData[$offset]) ;
	}
	
	
	/**
	 * Getter for ArrayAccess
	 *
	 * @param string $offset 
	 * @return void
	 * @author Paul Gessinger
	 */
	public function offsetGet($offset)
	{
		return isset($this->EveArrayData[$offset]) ? $this->EveArrayData[$offset] : null;
	}
	
	
	/**
	 * Magic getter for accessing properties within $embedded.
	 *
	 * @param string $name 
	 * @return void
	 * @author Paul Gessinger
	 */
	function __get($offset)
	{
		return $this->EveArrayData[$offset] ;
	}
	
	
	/**
	 * Magic setter for properties within $embedded.
	 *
	 * @param string $name 
	 * @param string $value 
	 * @return void
	 * @author Paul Gessinger
	 */
	function __set($offset, $value)
	{
		$this->EveArrayData[$offset] = $value ;
	}
	
	
	/**
	 * Isset implementation
	 *
	 * @param string $name 
	 * @return void
	 * @author Paul Gessinger
	 */
	function __isset($name)
	{
		return isset($this->EveArrayData[$offset]);
	}
	
	
	/**
	 * Return class name on echo.
	 *
	 * @return void
	 * @author Paul Gessinger
	 */
	function __toString()
	{
		return get_class($this) ;
	}
}