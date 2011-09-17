<?php

class FlatFileCache implements CacheMethod 
{
	protected $cache_dir ;
	
	function __construct()
	{
		$this->cache_dir = Eve::$path.'cache/' ;
		
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
		
		file_put_contents($this->cache_dir.md5($key).'.php', '<?php /*'.$expire.$data) ;
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
		
	}
}