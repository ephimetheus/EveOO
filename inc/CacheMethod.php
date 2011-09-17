<?php

interface CacheMethod 
{
	function store($key, $value, $expire) ;
	function retrieve($key) ;
	function remove($key) ;
	function exists($key) ;
}