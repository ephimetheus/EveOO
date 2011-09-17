<?

class EveArray implements IteratorAggregate, ArrayAccess
{
	var $EveArrayData = array() ;
	
	function __construct($data = array()) 
	{
		$this->EveArrayData = $data ;
	}
	
	function getIterator()
	{
		return new ArrayIterator($this->EveArrayData) ;
	}
	
	public function offsetSet($offset, $value)
	{
		$this->EveArrayData[$offset] = $value ;
	}
	
	public function offsetExists($offset)
	{
		return isset($this->EveArrayData[$offset]);
	}
	public function offsetUnset($offset)
	{
		unset($this->EveArrayData[$offset]) ;
	}
	
	public function offsetGet($offset)
	{
		return isset($this->EveArrayData[$offset]) ? $this->EveArrayData[$offset] : null;
	}
	
	function __get($offset)
	{
		return $this->EveArrayData[$offset] ;
	}
	
	function __set($offset, $value)
	{
		$this->EveArrayData[$offset] = $value ;
	}
	
	function __isset($name)
	{
		return isset($this->EveArrayData[$offset]);
	}
	
	function __toString()
	{
		return get_class($this) ;
	}
}