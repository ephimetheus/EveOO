<?php
/**
 * Singleton Cache class, providing an interface for various cache methods.
 *
 * @package default
 * @author Paul Gessinger
 */
class Cache
{
	static protected $instance ;
	protected $cache_provider ;
	protected $cachePeriod ;
	
	
	/**
	 * Currently only sets FlatFileCache as cache method. More methods might be implemented later.
	 *
	 * @author Paul Gessinger
	 */
	private function __construct() 
	{
		if(!($this->cache_provider instanceof CacheMethod))
		{
			$this->cache_provider = new FlatFileCache ;
		}
	}
	
	
	/**
	 * Singleton getter for the object, creates instance on first call.
	 *
	 * @return void
	 * @author Paul Gessinger
	 */
	static function getInstance()
	{
		if(!(self::$instance instanceof Cache))
		{
			self::$instance = new Cache() ;
		}
	
		return self::$instance ;
	}
	
	
	/**
	 * Store the cache period.
	 *
	 * @param string $secs 
	 * @return void
	 * @author Paul Gessinger
	 */
	function setCachePeriod($secs)
	{
		 $this->cachePeriod = $secs ;
	}
	
	
	/**
	 * Retrieve the last cache period.
	 *
	 * @return void
	 * @author Paul Gessinger
	 */
	function getCachePeriod()
	{
		 return $this->cachePeriod ;
	}
	
	/**
	 * Set the cache method from the outside. May only be set before Cache has been instantiated.
	 *
	 * @param CacheMethod $method 
	 * @return void
	 * @author Paul Gessinger
	 */
	function setMethod(CacheMethod $method)
	{
		if(!(self::$instance instanceof Cache))
		{
			$this->cache_provider = $method ;
		}
	}
	
	
	/**
	 * Static method for storing data in the cache. $expire must be a timestamp.
	 *
	 * @param string $key 
	 * @param string $value 
	 * @param string $expire 
	 * @return void
	 * @author Paul Gessinger
	 */
	static function store($key, $value, $expire)
	{
		return Cache::getInstance()->cache_provider->store($key, $value, $expire) ;
	}
	
	
	/**
	 * Static method for retrieving content from the cache.
	 *
	 * @param string $key 
	 * @return void
	 * @author Paul Gessinger
	 */
	static function retrieve($key)
	{
		return Cache::getInstance()->cache_provider->retrieve($key) ;
	}
	
	
	/**
	 * Static function for removing cached items from the cache.
	 *
	 * @param string $key 
	 * @return void
	 * @author Paul Gessinger
	 */
	static function remove($key)
	{
		return Cache::getInstance()->cache_provider->remove($key) ;
	}
	
	
	/**
	 * Static function for caching if there is a cached value for the given key.
	 *
	 * @param string $key 
	 * @return void
	 * @author Paul Gessinger
	 */
	static function exists($key)
	{
		return Cache::getInstance()->cache_provider->exists($key) ;
	}
}