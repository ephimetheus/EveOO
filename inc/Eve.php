<?php

class Eve extends EveApiObject {

	static $path ;
	protected $scope = 'eve' ;
	static $param = array() ;
	static $instance ;
	var $api_path = 'https://api.eveonline.com/' ;
	protected $global = array() ;

	function __construct(array $param = array())
	{
		Eve::$param = $param ;
		
		Eve::$instance = $this ;
		
		Eve::$path = dirname(__FILE__).'/' ;
		
		spl_autoload_register(array($this, 'autoload')) ;
	}
	
	function autoload($class)
	{
		if(file_exists(Eve::$path.$class.'.php'))
		{
			include Eve::$path.$class.'.php' ;
		}
	}
	
	function setKeyID($key)
	{
		$this->global['keyID'] = $key ;
	}
	
	function setVCode($vcode)
	{
		$this->global['vCode'] = $vcode ;
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
		
		//echo $url ;

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
	
	function getCharacter($characterID)
	{
		return new Character($characterID, $this) ;
	}
	
	function getAccountStatus(array $arguments = array())
	{
		return $this->call('account', 'AccountStatus', $arguments) ;
	}
}