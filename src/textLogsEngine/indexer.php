<?php 

/**
 * This class manages the log files and directories
 * Its role is to return the appropriate folder and file to commit the logs to
 */

namespace App\Logs\Text;

use App\Internal\Keys\Index;

class Indexer{

	const DEFAULT_FOLDER_SUFFIXE = "logs-storage";
	const DEFAULT_INDEX_NAME = ".index.json";

	private $finalSavingDirectory;

	public function __construct(){}


	/**
	 * Sets up a saving folder for the logs. If the provided folder cannot be used (Doesn't exist, has got the same name as an existing one, ...) then the function create a new folder and returns it's relative path (relative to the root of the library). If the folder exists and can be used, then the function returns it's relative path.
	 * @param  string $folderPath Saving folder path
	 * @param  string $root If provided, changes the root for all paths (If we wish to create an external saving folder for example, then the folder root is not the same as the library root)
	 * @return string $finalPath  Final saving folder path
	 * @throws Exception If folder cannot be created
	 */
	public function setup($folderRelativePath,$root=null){

		if(is_null($root)){
			$root = ROOT;
		}

		$absPath = $root.DS.$folderRelativePath;
		$finalPath = $absPath;

		$pathinfo = pathinfo($absPath);
		$basename = (isset($pathinfo["basename"])) ? $pathinfo["basename"] : "";

		$randKey = substr(md5(rand()), 0, 10);

		if(is_dir($absPath)){
			$content = scandir($absPath);

			// Looking for, and unsetting, the values "." and ".."
			$current 	= array_search(".", $content);
			$parent 	= array_search("..", $content);
			unset($content[$current]);
			unset($content[$parent]);

			// If a folder with an appended random key exists (eg.: storage-2As53/) ..
			// ... that has previously been created
			// Then we choose to use that folder as a saving folder
			$rand_folder_exists = false; 
			$empty = true;
			foreach ($content as $key => $value) {

				if(preg_match('/'.self::DEFAULT_FOLDER_SUFFIXE.'-(.*)/', $value) == 1){
					$rand_folder_exists = true;
					$finalPath = $absPath.DS.$content[$key];
					break;
				}

				if($value != self::DEFAULT_INDEX_NAME)
					$empty = false;
			}


			//If the folder isn't empty, we create a saving subfolder inside it
			if(!$empty && !$rand_folder_exists){

				// Proposed new folder name 
				$proposedSubFolderPath = $absPath.DS.$basename;
				
				// Proposed folder name with random hash appended to it
				$alternativePath = $absPath.DS.self::DEFAULT_FOLDER_SUFFIXE."-$randKey"; 


				if(!is_dir($proposedSubFolderPath)){
					$finalPath = $proposedSubFolderPath;
				}elseif(!is_dir($alternativePath)){
					$finalPath = $alternativePath;
				}else{
					throw new Exception("[Internal] - Cannot create log folder.", Index::ELEMENT_EXISTS);
					
				}

				mkdir($finalPath);

			}

		}else{
			mkdir($finalPath);
		}


		$this->updateIndexFile($finalPath);
		$this->finalSavingDirectory = $finalPath;

		return $finalPath;

	}

	private function updateIndexFile($path){
		/**
		 * @todo add an index file to the log saving directory
		 */
		$indexPath = $path.DS.self::DEFAULT_INDEX_NAME;
		$indexContent = json_encode( array(
			"index_id"		=>	"@todo : manage ids",
			"index_path" 	=> 	$indexPath,
			"create_date"	=> 	date("20y-m-d @ H:m:s"),
			"layout"		=>	"@todo : create layouts management tool",
			"sub_indexes"	=>	array()
		) , JSON_PRETTY_PRINT);

		if(!file_exists($indexPath)){
			$indexFileHandle = fopen($indexPath,"w+");
			fwrite($indexFileHandle, $indexContent);
			fclose($indexFileHandle);
		}
			
		$indexContent = json_decode(file_get_contents($indexPath));
		print_r($indexContent);


	}

}