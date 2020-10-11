<?php

namespace App\Logs\Text;

use App\Internal\Keys\Index;

class TextLogsEngine{

	

	public function __construct(){}

	/**
	 * Staging new log entries
	 * @param  string?array $data Data to be staged (can be either a string or an array)
	 * @example Entries formats : single string , array of strings, array(string,int), array containing a mix of strings and array(string,int)
	 * @return string?array $data Data is returned for smoothing out the development
	 */
	public function newEntry($data){

		if(is_string($data)){
			$this->stage($data);
		}else{

			if(is_array($data) && sizeof($data) == 2) {
			
				$this->stage($data[0],$data[1]);

			}else{
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

	}

}