<?php

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