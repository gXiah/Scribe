<?php

/**
 * @author Adam Aquesbi <contact@adaquesbi.me>
 * 10 / 09th / 2020
 */

namespace App;

use App\{
		Internal\JSON\JsonToolkit,
		Logs\Text\TextLogsEngine
	};

define("ROOT",dirname(__DIR__));
define("DS",DIRECTORY_SEPARATOR);



class Logger{

	private $configFilePath;
	private $mainConfig = array();

	// Stores temporary messages that have been sent before the complete initialisation of the Logger
	private $tmp = array();

	private $txtLogsEngine;

	public function __construct($configFileName=null){

		// Main Configuration file
		$this->configFilePath 	= (is_null($configFileName)) 
										? dirname(__DIR__).DS."config.json" 
										: dirname(__DIR__).DS.$configFileName.".json";
		$this->mainConfig 		= $this->getConfig($this->configFilePath);


		// Initaliazing the text logs engine (for internal saving)
		$this->txtLogsEngine 	= new TextLogsEngine($this->mainConfig);
		/**
		 * @todo Initialize for ext. saving
		 */
		$this->tmp[] = ["message",3];
		$this->tmp[] = ["another msg"];
		/**
		 * @todo Commit tmp logs
		 */
		$this->log($this->tmp);

		// Testing Zone
		
			$this->log("Test entry");
			$this->log(array("entry1","entry2"));
			$this->log(array("entryWcode",5));
			$this->log(array());
		
	}

	public function log($data){

		$this->txtLogsEngine->newEntry($data);
		
	}

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