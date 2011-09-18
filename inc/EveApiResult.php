<?php

/**
 * Data wrapper for results coming from the api. Transforms the xml from response into a hierarchy of EveApiResult,
 * EveApiRowsetResult and EveArray. This allows for accessing the entire tree through chaining.
 *
 * @package default
 * @author Paul Gessinger
 */
class EveApiResult implements IteratorAggregate, ArrayAccess
{
	var $data ;
	var $type ;
	
	
	/**
	 * Transform the xml data into an EveArray, and hands over rowset nodes to EveApiRowsetResult
	 *
	 * @param SimpleXMLElement $data_node 
	 * @param string $type 
	 * @author Paul Gessinger
	 */
	function __construct(SimpleXMLElement $data_node, $type = false)
	{
		$this->data = new EveArray ;
		
		$this->type = $type ;
		
		foreach($data_node->children() as $child)
		{
			if($child->getName() === 'rowset') 
			{
				$this->data[(string)$child['name']] = new EveApiRowsetResult($child) ;
			}
			else
			{
				$sub_children = $child->children() ;
				if(count($sub_children) == 0)
				{
					$this->data[$child->getName()] = (string)$child ;
				}
				else
				{
					$this->data[$child->getName()] = new EveApiResult($child) ;
				}
				
			}
			
		}
	}


	/**
	 * Returns an iterator for $embedded.
	 *
	 * @return void
	 * @author Paul Gessinger
	 */
	function getIterator()
	{
		return $this->data->getIterator() ;
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
		$this->data[$offset] = $value ;
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
		return isset($this->data[$offset]) ;
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
		unset($this->data[$offset]) ;
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
		return isset($this->data[$offset]) ? $this->data[$offset] : null ;
	}
	
	
	/**
	 * Magic getter for accessing properties within $data.
	 *
	 * @param string $name 
	 * @return void
	 * @author Paul Gessinger
	 */
	function __get($offset)
	{
		return $this->data[$offset] ;
	}
	
	
	/**
	 * Magic setter for properties within $data.
	 *
	 * @param string $name 
	 * @param string $value 
	 * @return void
	 * @author Paul Gessinger
	 */
	function __set($offset, $value)
	{
		$this->data[$offset] = $value ;
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
		return isset($this->data[$offset]);
	}
	
	
	/**
	 * Return class name on echoing.
	 *
	 * @return void
	 * @author Paul Gessinger
	 */
	function __toString()
	{
		return get_class($this) ;
	}
}