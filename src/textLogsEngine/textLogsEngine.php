<?php

namespace App\Logs\Text;

use App\Internal\Keys\{
					Index,
					Babylone
				};
use App\Logs\Text\{
					Scribe,
					Indexer
				};

class TextLogsEngine{

	private $storeInternally = True;
	private $internalStorageDirectory;

	public function __construct($mainConfig){

		// We check the configuration (/config.json) to see if internal storage has been activated
		if(isset($mainConfig->save_internally)){
			if(Babylone::is_equivalent_to( $mainConfig->save_internally , Babylone::FALSE )){
				$this->storeInternally = false;
			}
		}


		// We check the configuration (/config.json) to see in which folder should the logs be saved
		if(isset($mainConfig->internal_storage)){
			$this->internalStorageDirectory = $mainConfig->internal_storage;
		}

		// We now check if the internal storage folder exists and is accessible
		$this->scribe 	= 	new Scribe();
		$this->indexer 	=	new Indexer();

		try{
			$this->internalStorageDirectory = $this->indexer->setup( $this->internalStorageDirectory ); 
		}catch (\Exception $e){
			$this->newEntry($e->getMessage());
		}	

	}


	//=====================


	/**
	 * Staging new log entries
	 * @param  string?array $data Data to be staged (can be either a string or an array)
	 * @example Entries formats : single string , array of strings, array(string,int), array containing a mix of strings and array(string,int)
	 * @return string?array $data Data is returned for smoothing out the development
	 */
	public function newEntry($data){

		if(is_string($data)){	// String entry
			$this->stage($data);
		}else{
			
			if(is_array($data) && sizeof($data) == 2 && is_integer($data[1])) { // array(string,int)
			
				$this->stage($data[0],$data[1]);
			
			}else{// array(str,str,...)

				foreach ($data as $value) {

					if(is_string($value)){
						$this->stage($value);
					}else{
						$this->stage("[Internal] - Warning: Failed to stage log entry",INDEX::TYPE_ERROR);
					}
				}
					
			}
			

		}

	}

	/**
	 * Stages the entries that will later be comitted
	 * @param  string $entry Entry to be staged
	 * @return string $entry Staged entry
	 */
	public function stage($entry,$code=0){
		
		echo "Staging the entry : $entry with code $code <br>";
	}


	/**
	 * Commits the entry to a log file
	 */
	public function commit(){

		if($this->storeInternally){

		}else{
			// No internal storage authorized
		}

	}
	

	//=====================
	



}