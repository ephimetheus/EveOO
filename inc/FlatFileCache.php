<?php

/**
 * Implementation of the CacheMethod interface. Stores cached values in php files, one for each pair.
 *
 * @package EveOO
 * @author Paul Gessinger
 */
class FlatFileCache implements CacheMethod 
{
	protected $cache_dir ;
	
	
	/**
	 * Locate the cache directory and make sure it is writable.
	 *
	 * @author Paul Gessinger
	 */
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
	
	
	/**
	 * Store pairs in the cache. The expiration is prepended to the serialized string, and <?php /* is prepended to this
	 * so a client cannot access the cache files to retrieve API credential info.
	 * if cache_compress is set to true, cached data is gzcompress'd.
	 *
	 * @param string $key 
	 * @param string $value 
	 * @param string $expire 
	 * @return void
	 * @author Paul Gessinger
	 */
	function store($key, $value, $expire)
	{
		$data = serialize($value) ;
		
		if(Eve::$param['cache_compress'] === true)
		{
			$data = gzcompress($data) ;
		}
		
		Cache::getInstance()->setCachePeriod($expire - Eve::getDate('now')) ;
		
		file_put_contents($this->cache_dir.md5($key).'.php', '<?php /'.'*'.$expire.$data) ;
	}
	
	
	/**
	 * Get a handle for the cache file we are looking for. Deletes the file, if the 
	 * expiration date has passed.
	 *
	 * @param string $file 
	 * @return null or file handle
	 * @author Paul Gessinger
	 */
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
		
		Cache::getInstance()->setCachePeriod($expire - $now) ;
		
		return $handle ;
	}
	
	
	/**
	 * Retrieves data from the cache. Uses FlatFileCache::getHandle() and reads the rest of the files content.
	 *
	 * @param string $key 
	 * @return void
	 * @author Paul Gessinger
	 */
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
	
	
	/**
	 * Uses FlatFileCache::getHandle() to determine if data is associated to the key given.
	 *
	 * @param string $key 
	 * @return void
	 * @author Paul Gessinger
	 */
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
	
	
	/**
	 * Deletes the file belonging to the key given, if it exists.
	 *
	 * @param string $key 
	 * @return void
	 * @author Paul Gessinger
	 */
	function remove($key)
	{
		$file = $this->cache_dir.md5($key).'.php' ;
		if(file_exists($file))
		{
			unlink($file) ;
		}
	}
}