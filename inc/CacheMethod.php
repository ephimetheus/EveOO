<?php

/**
 * Interface for all Cache methods that might be implemented.
 *
 * @package default
 * @author Paul Gessinger
 */
interface CacheMethod 
{
	function store($key, $value, $expire) ;
	function retrieve($key) ;
	function remove($key) ;
	function exists($key) ;
}