<?php 
class EveException extends Exception {}
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
class Eve extends EveApiObject {
	static $path ;
	protected $scope = 'eve' ;
	static $param = array() ;
	static $instance ;
	var $api_path = 'https:\/\/api.eveonline.com/' ;
	protected $global = array() ;
	function __construct(array $param = array())
	{
		$this->api_path = stripslashes($this->api_path) ;
		Eve::$param = $param ;
		Eve::$instance = $this ;
		Eve::$path = dirname(__FILE__).'/' ;
		spl_autoload_register(array($this, 'autoload')) ;
	}
	function autoload($name)
	{
		echo $name.'<br/>' ;
		if(file_exists(Eve::$path.$name.'.php'))
		{
			include Eve::$path.$name.'.php' ;
		}
	}
	function setKeyID($key)
	{
		$this->global['keyID'] = $key ;
		return $this ;
	}
	function setVCode($vcode)
	{
		$this->global['vCode'] = $vcode ;
		return $this ;
	}
	static function getDate($sth = 'now')
	{
		$tz = new DateTimeZone('UTC') ;
		$expire = new DateTime($sth, $tz) ;
		return $expire->getTimestamp() ;
	}
	function call($scope, $method, $arguments = array()) 
	{
		if(!is_array($arguments))
		{
			throw new EveException('$arguments is not an array. Invalid format.') ;
		}
		$arguments = array_merge($this->global, $arguments) ;
		$url = $this->api_path.$scope.'/'.$method.'.xml.aspx' ;
		if(count($arguments) !== 0)
		{
			$tmp_arr = array() ;
			foreach($arguments as $key => $value)
			{
				$tmp_arr[] = $key.'='.$value ;
			}
			$url = $url.'?'.implode($tmp_arr, '&') ;
		}
		if(Cache::exists($url))
		{
			return Cache::retrieve($url) ;
		}
		$curl = curl_init() ;
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false
		)) ;
		$result = curl_exec($curl) ;
		$xml = new SimpleXMLElement($result) ;
		if(isset($xml->error))
		{
			throw new EveException((string)$xml->error, (int)$xml->error['code']) ;
		}
		$result_object = new EveApiResult($xml->result, $method) ;
		Cache::store($url, $result_object, Eve::getDate((string)$xml->cachedUntil)) ;
		return $result_object ;
	}
	function getCharacters(array $arguments = array())
	{
		return $this->call('account', 'Characters', $arguments) ;
	}
	function getAccountStatus(array $arguments = array())
	{
		return $this->call('account', 'AccountStatus', $arguments) ;
	}
}
class Character extends EveApiObject
{
	protected $scope = 'char' ;
	var $eve ;
	function __construct($characterID)
	{
		$this->global['characterID'] = $characterID ;
		$this->eve = Eve::$instance ;
	}
	function getMailBodies($param)
	{
		if(is_array($param['ids']))
		{
			$param['ids'] = implode(',', $param['ids']) ;
		}
		$result = $this->call($this->scope, 'MailBodies', $param) ;
		foreach($result->messages as $key => $message)
		{
			$message->data = htmlentities($message->data) ;
		}
		return $result ;
	}
}
class Cache
{
	static protected $instance ;
	protected $cache_provider ;
	private function __construct() 
	{
		if(!($this->cache_provider instanceof CacheMethod))
		{
			$this->cache_provider = new FlatFileCache ;
		}
	}
	static function getInstance()
	{
		if(!(self::$instance instanceof Cache))
		{
			self::$instance = new Cache() ;
		}
		return self::$instance ;
	}
	function setMethod(CacheMethod $method)
	{
		if(!(self::$instance instanceof Cache))
		{
			$this->cache_provider = $method ;
		}
	}
	static function store($key, $value, $expire)
	{
		return Cache::getInstance()->cache_provider->store($key, $value, $expire) ;
	}
	static function retrieve($key)
	{
		return Cache::getInstance()->cache_provider->retrieve($key) ;
	}
	static function remove($key)
	{
		return Cache::getInstance()->cache_provider->remove($key) ;
	}
	static function exists($key)
	{
		return Cache::getInstance()->cache_provider->exists($key) ;
	}
}
class FlatFileCache implements CacheMethod 
{
	protected $cache_dir ;
	function __construct()
	{
		if(isset(Eve::$param['cache_dir']))
		{
			$this->cache_dir = Eve::$param['cache_dir'] ;
		}
		else
		{
			$this->cache_dir = Eve::$path.'cache/' ;
		}
		if(!file_exists($this->cache_dir))
		{
			throw new EveException('Unable to find cache dir "'.$this->cache_dir.'"') ;
		}
		if(!is_writable($this->cache_dir))
		{
			throw new EveException('Cache dir "'.$this->cache_dir.'" is not writable.') ;
		}
	}
	function store($key, $value, $expire)
	{
		$data = serialize($value) ;
		if(Eve::$param['cache_compress'] === true)
		{
			$data = gzcompress($data) ;
		}
		file_put_contents($this->cache_dir.md5($key).'.php', ' /'.'*'.$expire.$data) ;
	}
	private function getHandle($file)
	{
		$tz = new DateTimeZone('UTC') ;
		$now = new DateTime('now', $tz) ;
		$now = $now->getTimestamp() ;
		if(!file_exists($file))
		{
			return null ;
		}
		$handle = fopen($file, 'r') ;
		fseek($handle, 8) ;
		$expire = (int)fread($handle, 10) ;
		if($expire < $now)
		{
			fclose($handle) ;
			unlink($file) ;
			return null ;
		}
		return $handle ;
	}
	function retrieve($key)
	{
		$file = $this->cache_dir.md5($key).'.php' ;
		$handle = $this->getHandle($file) ;
		if($handle === null)
		{
			return null ;
		}
		$data = fread($handle, filesize($file)) ;
		if(Eve::$param['cache_compress'] === true)
		{
			$data = gzuncompress($data) ;
		}
		return unserialize($data) ;
	}
	function exists($key)
	{
		$file = $this->cache_dir.md5($key).'.php' ;
		$handle = $this->getHandle($file) ;
		if($handle === null)
		{
			return false ;
		}
		return true ;
	}
	function remove($key)
	{
		$file = $this->cache_dir.md5($key).'.php' ;
		if(file_exists($file))
		{
			unlink($file) ;
		}
	}
}
interface CacheMethod 
{
	function store($key, $value, $expire) ;
	function retrieve($key) ;
	function remove($key) ;
	function exists($key) ;
}
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
				$sub_data = new EveApiResult($child) ;
				foreach($sub_data as $k => $v)
				{
					$row[$k] = $v ;
				}
			}
			$this->rowset[(string)$child[$key]] = $row ;
		}
	}
	function getIterator()
	{
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