<?php

/**
 * @author Adam Aquesbi <contact@adaquesbi.me>
 * 10 / 09th / 2020
 */

namespace App;

use App\Internal\JSON\JsonToolkit;

define("DS",DIRECTORY_SEPARATOR);



class Logger{

	private $configFilePath;
	private $mainConfig = array();

	// Stores temporary messages that have been sent before the complete initialisation of the Logger
	private $tmp = array();

	public function __construct(){

		// Main Configuration file
		$this->configFilePath 	= dirname(__DIR__).DS."config.json";
		$this->mainConfig 		= $this->getConfig($this->configFilePath);


		


		// Testing Zone
		echo "<pre>";
			print_r($this->mainConfig);
			print_r($this->tmp);
		echo "</pre>";
	}

	public function log($data){}

	public function flash($data){}


	//==========
	
	/**
	 * Returns configuration file (or any .json file)
	 * @param  string 	$configFilePath File path
	 * @param  string 	$dataPoint      (optional) Exact key (data entry) to return
	 * @return array                 	Either entire configuration, or the value for the specified key
	 */
	private function getConfig($configFilePath,$dataPoint=null){
		$return = array();

		$jsonTK = new JsonToolkit();
		try{
			$return = $jsonTK->open($configFilePath);
		}catch (\Exception $e){
			$this->tmp[] = array( $e->getMessage() , $e->getCode() );
		}

		return $return;
	}

}