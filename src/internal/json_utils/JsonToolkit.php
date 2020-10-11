<?php 

namespace App\Internal\JSON;

use App\Internal\Keys\Index;

class JsonToolkit{

	private $configFilePath;

	public function __construct(){}


	/**
	 * Opens a .json file and returns its content in an array
	 * @param  string $absolutePath Json file path
	 * @return array  $data         File data
	 * @throws \Exception In case file not found
	 */
	public function open($absolutePath){
		$data = array();

		$content = @file_get_contents($absolutePath);
		$data = json_decode($content);


		if(!$content){
			throw new \Exception(
						"[Internal] - Error while retrieving JSON data from file @$absolutePath.",
						Index::ERR_FILE_NOT_FOUND
					);
		}
			

		if(json_last_error() != JSON_ERROR_NONE){
			throw new \Exception(
						"[JSON] - Error : ".json_last_error_msg(),
						json_last_error());
			
		}

		return $data;

	}

}