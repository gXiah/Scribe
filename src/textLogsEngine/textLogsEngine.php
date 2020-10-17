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

	const DEFAULT_ERROR_CODE = 0;

	private $storeInternally = True;
	private $internalStorageDirectory;

	public function __construct($mainConfig){

		// We check the configuration (/config.json) to see if internal storage has been activated
		if(isset($mainConfig->save_internally)){
			if(Babylone::is_equivalent_to( $mainConfig->save_internally , Babylone::FALSE )){
				$this->storeInternally = false;
			}
		}


		// We check the configuration (/config.json) to see which folder should the logs be saved in
		if(isset($mainConfig->internal_storage)){
			$this->internalStorageDirectory = $mainConfig->internal_storage;
		}

		// We now check if the internal storage folder exists and is accessible
		$this->indexer 	=	new Indexer();
		try{
			$this->internalStorageDirectory = $this->indexer->setup( $this->internalStorageDirectory ); 
		}catch (\Exception $e){
			$this->newEntry($e->getMessage());
		}	

		$this->scribe 	= 	new Scribe( $this->indexer->reachFile() , $this->indexer->sub_path);

	}


	//=====================


	/**
	 * Staging new log entries
	 * @param  string?array $data Data to be staged (can be either a string or an array)
	 * @example Entries formats : single string , array of strings, array(string,int), array containing a mix of strings and array(string,int)
	 * @return string?array $data Data is returned for smoothing out the development
	 */
	public function newEntry($data){

		$dataSet = array($data);
		$refined = array();


		foreach ($dataSet as $key => $value) {
			
			if(is_string($value)){	// newEntry("str message")

				$refined[] = array($value , self::DEFAULT_ERROR_CODE);

			}elseif(is_array($value)){ // newEntry( [...] )
				
				if(sizeof($value) == 2 && is_int($value[1])){ // newEntry(["message",int_code])
					
					$refined[] = array($value[0] , $value[1]);

				}else{ // newEntry(["msg1" , ["msg2",code2] , "msg3" , ... ])

					foreach ($value as $sub_key => $sub_value) {
						
						if(is_string($sub_value)){	// newEntry( ["message1","message2",...] )	
							
							$refined[] = array($sub_value , self::DEFAULT_ERROR_CODE);
							
						}elseif (is_array($sub_value)) { //newEntry( [ ["msg1",code1] , ["msg2",code2] ] )

							if(isset($sub_value[1])){
								$refined[] = array($sub_value[0] , $sub_value[1]);
							}else{
								$refined[] = array($sub_value[0] , self::DEFAULT_ERROR_CODE);
							}

						}

					}

				}

			}

		}

		foreach ($refined as $value) {
			$this->stage($value[0] , $value[1]);
		}

	}

	/**
	 * Stages the entries that will later be comitted
	 * @param  string $entry Entry to be staged
	 * @return string $entry Staged entry
	 */
	public function stage($entry,$code=0){

		$this->stagedEntries[] = array( strval($entry) , intval($code) );	

	}


	/**
	 * Commits the entry to a log file
	 */
	public function commit(){

		if($this->storeInternally){

			foreach ($this->stagedEntries as $data) {
				$message = (isset($data[0])) 	? $data[0] : "NULL";
				$code = (isset($data[1])) 		? $data[1] : self::DEFAULT_ERROR_CODE;

				$this->scribe->insert([ "message" => $message , "code" => $code ]);
			}

		}else{
			// No internal storage authorized
		}

	}
	

	//=====================
	



}