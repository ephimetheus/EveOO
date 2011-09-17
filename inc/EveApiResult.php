<?php

class EveApiResult implements IteratorAggregate, ArrayAccess
{
	var $data ;
	var $type ;
	
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

	function getIterator()
	{
		return $this->data->getIterator() ;
	}
	
	public function offsetSet($offset, $value)
	{
		$this->data[$offset] = $value ;
	}
	
	public function offsetExists($offset)
	{
		return isset($this->data[$offset]) ;
	}
	public function offsetUnset($offset)
	{
		unset($this->data[$offset]) ;
	}
	
	public function offsetGet($offset)
	{
		return isset($this->data[$offset]) ? $this->data[$offset] : null ;
	}
	
	function __get($offset)
	{
		return $this->data[$offset] ;
	}
	
	function __set($offset, $value)
	{
		$this->data[$offset] = $value ;
	}
	
	function __isset($name)
	{
		return isset($this->data[$offset]);
	}
	
	function __toString()
	{
		return get_class($this) ;
	}
}