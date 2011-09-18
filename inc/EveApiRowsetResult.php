<?php

/**
 * Wrapper for data, that is structured in rowset/row type. This extrapolates the "columns" in the xml into
 * simple properties, merging it with actual properties for row nodes, and incorporates string value of row nodes
 * if they have one.
 *
 * @package EveOO
 * @author Paul Gessinger
 */
class EveApiRowsetResult implements IteratorAggregate, ArrayAccess
{
	var $rowset = null ;
	
	
	/**
	 * Compiles the complex rowset node structure into a simple hierarchy of objects.
	 *
	 * @param SimpleXMLElement $data_node 
	 * @author Paul Gessinger
	 */
	function __construct(SimpleXMLElement $data_node)
	{	
		$this->rowset = new EveArray ;
		
		$key = (string)$data_node['key'] ;
		$columns = explode(',', (string)$data_node['columns']) ;

		foreach($data_node->children() as $child)
		{
			$row = new EveArray ;
			
			// transform columns into properties.
			foreach($columns as $column)
			{
				$row[$column] = (string)$child[$column] ;
			}

			$sub_children = $child->children() ;
			if(count($sub_children) == 0) // this row has no children
			{
				$content = (string)$child ;
				if(!empty($content)) // this row has a string value
				{
					$row['data'] = $content ;
				}	
			}
			else // this row has children
			{
				$sub_data = new EveApiResult($child) ;
				foreach($sub_data as $k => $v)
				{
					$row[$k] = $v ;
				}
			}
			
			// store the row under the key that is specified in the attribute of the row node
			$this->rowset[(string)$child[$key]] = $row ;
		}
	}
	
	
	/**
	 * Returns an iterator for $rowset.
	 *
	 * @return void
	 * @author Paul Gessinger
	 */
	function getIterator()
	{
		return $this->rowset->getIterator() ;
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
		$this->rowset[$offset] = $value ;
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
		return isset($this->rowset[$offset]);
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
		unset($this->rowset[$offset]) ;
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
		return isset($this->rowset[$offset]) ? $this->rowset[$offset] : null;
	}
	
	
	/**
	 * Magic getter for accessing properties within $rowset.
	 *
	 * @param string $name 
	 * @return void
	 * @author Paul Gessinger
	 */
	function __get($offset)
	{
		return $this->rowset[$offset] ;
	}
	
	
	/**
	 * Magic setter for properties within $rowset.
	 *
	 * @param string $name 
	 * @param string $value 
	 * @return void
	 * @author Paul Gessinger
	 */
	function __set($offset, $value)
	{
		$this->rowset[$offset] = $value ;
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
		return isset($this->rowset[$offset]);
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