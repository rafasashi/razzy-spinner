<?php
/**
 *@Author : Razak Zakari (Razzbee)
 * @Email : razzsense@gmail.com
 * @Whatsapp : +233244827491
 * @Facebook : facebook.com/razzbee
 * @description : Cache Class for caching objects , array , strings .....
 * @License : See license.txt 
 */
use Flintstone\Flintstone;

class cache extends functions{

public static function init_cache(){

//db path
$dbPath = realpath(dirname(__DIR__))."/bin/cache/";

// Set options
$options = array('dir' =>$dbPath,"cache"=>true);

$cacheObj = new Flintstone('copierCacheDB', $options);

return $cacheObj;
}//end init cache

	//clean key
	private static function clean_key($key){
		return preg_replace("/([^a-z\-\_\.0-9])/i","",$key);
	}

	//fetch cache
	public static function fetch($cache_key){
	 return null;

     $initCache = self::init_cache();

	echo $cache_key = self::clean_key($cache_key);

	try{
	 return $initCache->get("hello");
	}catch(Exception $e){}

	}//end fetch cache


	//store cache
	public static function store($cache_key,$cache_value){

		return null;

		  $initCache = self::init_cache();

		  $cache_key  = self::clean_key($cache_key);

		  $initCache->set($cache_key,$cache_value);

		  return true;
	}//end store Cache

}
