<?php
/**
 * Base class for EveOO. Provides connection to the API Server.
 *
 * @package EveOO
 * @author Paul Gessinger
 */
class Eve extends EveApiObject {

	static $path ;
	protected $scope = 'eve' ;
	static $param = array() ;
	static $instance ;
	var $api_path = 'https:\/\/api.eveonline.com/' ;
	protected $global = array() ;


	/**
	 * Takes parameters, registers the autoloader and figures out path to lib.
	 *
	 * @param array $param 
	 * @author Paul Gessinger
	 */
	function __construct(array $param = array())
	{
		$this->api_path = stripslashes($this->api_path) ;
		
		Eve::$param = $param ;
		
		Eve::$instance = $this ;
		
		Eve::$path = dirname(__FILE__).'/' ;
		
		spl_autoload_register(array($this, 'autoload')) ;
	}
	
	
	/**
	 * Simple autoloader looking for classes in inc folder.
	 *
	 * @param string $name 
	 * @return void
	 * @author Paul Gessinger
	 */
	function autoload($name)
	{
		if(file_exists(Eve::$path.$name.'.php'))
		{
			include Eve::$path.$name.'.php' ;
		}
	}
	
	
	/**
	 * Set the keyID if you wish to use one keyID/vCode combination throughout the entire lib.
	 *
	 * @param string $key 
	 * @return void
	 * @author Paul Gessinger
	 */
	function setKeyID($key)
	{
		$this->global['keyID'] = $key ;
		return $this ;
	}
	
	
	/**
	 * Set the vCode if you wish to use one keyID/vCode combination throughout the lib.
	 *
	 * @param string $vcode 
	 * @return void
	 * @author Paul Gessinger
	 */
	function setVCode($vcode)
	{
		$this->global['vCode'] = $vcode ;
		return $this ;
	}
	
	
	/**
	 * Helper method returning a timestamp to the corresponding datetime identifier without setting the global TZ setting.
	 *
	 * @param string $sth 
	 * @return void
	 * @author Paul Gessinger
	 */
	static function getDate($sth = 'now')
	{
		$tz = new DateTimeZone('UTC') ;
		$expire = new DateTime($sth, $tz) ;
		return $expire->getTimestamp() ;
	}
	
	
	/**
	 * Perform a cURL call to the eve online api. Tries to find the call in the cache, using the entire url as
	 * a cache identifier. If fresh data is fetched, the entire parsed result is stored in cache, and
	 * the expiration date is set to what was specified in cachedUntil in the response.
	 *
	 * @param string $scope 
	 * @param string $method 
	 * @param string $arguments 
	 * @return void
	 * @author Paul Gessinger
	 */
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

		$result_object = new EveApiResult($xml->result, ucfirst($scope).$method) ;
		
		Cache::store($url, $result_object, Eve::getDate((string)$xml->cachedUntil)) ;
		
		return $result_object ;
	}
	
	
	/**
	 * Mapping of magic __call call because we need to change scope here.
	 *
	 * @param array $arguments 
	 * @return void
	 * @author Paul Gessinger
	 */
	function getCharacters(array $arguments = array())
	{
		return $this->call('account', 'Characters', $arguments) ;
	}
	
	
	/**
	 * Mapping of magic __call call because we need to change scope here.
	 *
	 * @param array $arguments 
	 * @return void
	 * @author Paul Gessinger
	 */
	function getAccountStatus(array $arguments = array())
	{
		return $this->call('account', 'AccountStatus', $arguments) ;
	}
}