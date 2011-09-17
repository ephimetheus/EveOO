<?php

class EveApiRowsetResult implements IteratorAggregate, ArrayAccess
{
	var $rowset = null ;
	
	function __construct(SimpleXMLElement $data_node)
	{	
		$this->rowset = new EveArray ;
		
		$key = (string)$data_node['key'] ;
		$columns = explode(',', (string)$data_node['columns']) ;

		foreach($data_node->children() as $child)
		{
			$row = new EveArray ;
			foreach($columns as $column)
			{
				$row[$column] = (string)$child[$column] ;
			}

			$sub_children = $child->children() ;
			//var_dump()
			if(count($sub_children) == 0)
			{
				$content = (string)$child ;
				if(!empty($content))
				{
					$row['data'] = $content ;
				}	
			}
			else
			{
				//$row['children'] = new EveApiResult($child) ;
				$sub_data = new EveApiResult($child) ;
				foreach($sub_data as $k => $v)
				{
					$row[$k] = $v ;
				}
			}
			/*if(isset($child->rowset)) 
			{
				$row['children'] = new EveApiRowsetResult($child->rowset) ;
			}*/
			
			
			
			
			$this->rowset[(string)$child[$key]] = $row ;
		}
	}
	
	function getIterator()
	{
		/*foreach($this->rowset as $key => $value)
		{
			echo $key.' => '.$value.'<br/>' ;
		}*/
		return $this->rowset->getIterator() ;
	}
	
	public function offsetSet($offset, $value)
	{	
		$this->rowset[$offset] = $value ;
	}
	
	public function offsetExists($offset)
	{
		return isset($this->rowset[$offset]);
	}
	public function offsetUnset($offset)
	{
		unset($this->rowset[$offset]) ;
	}
	
	public function offsetGet($offset)
	{
		return isset($this->rowset[$offset]) ? $this->rowset[$offset] : null;
	}
	
	function __get($offset)
	{
		return $this->rowset[$offset] ;
	}
	
	function __set($offset, $value)
	{
		$this->rowset[$offset] = $value ;
	}
	
	function __isset($name)
	{
		return isset($this->rowset[$offset]);
	}
	
	function __toString()
	{
		return get_class($this) ;
	}
}