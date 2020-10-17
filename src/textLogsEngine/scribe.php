<?php

/**
 * This class's role is to manage the I/O to log files
 */

namespace App\Logs\Text;

class Scribe{

	private $fileHandler;
	private $logLineFormat = "{date};{code};{message};{sub_path}"; // Default format
	private $separator = "|";
	private $sub_path = "";

	public function __construct($filePath,$sub_path=null){
		$this->filePath = $filePath;

		if(!is_null($sub_path))
			$this->sub_path = $sub_path;
	}

	public function insert($data){
		$formatted = $this->formatData($data);
		$formatted .= "\n\r";
		
		$this->fileHandler = fopen($this->filePath,"a");
		fwrite($this->fileHandler, $formatted);
		fclose($this->fileHandler);
	}

		private function formatData($data,$format=null){

			if(is_null($format)){
				$format = $this->logLineFormat;
			}


			$ex_format = explode(";", $format);

			$data["sub_path"] = $this->sub_path;
			$data["date"] = date("20y-m-d@H-m-s");

			$formattedLogMessage = "";
			foreach ($ex_format as $value) {
				$current_variable = str_replace(["{","}"], "", $value);
				$replacement = (isset($data[$current_variable])) ? $data[$current_variable] : "NULL";
				
				// Replacing "\" with "\\" for preg_replace()
				$replacement = str_replace('\\', '\\\\', $replacement);

				$formattedLogMessage .= preg_replace('/{(.*)}/',$replacement, $value);
				$formattedLogMessage .= $this->separator;
			}
			$formattedLogMessage[-1] = " ";

			return $formattedLogMessage;
		}

}