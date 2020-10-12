<?php 

/**
 * This class manages the log files and directories
 * Its role is to return the appropriate folder and file to commit the logs to
 */

namespace App\Logs\Text;

use App\Internal\Keys\Index;

class Indexer{

	const DEFAULT_FOLDER_SUFFIXE = "logs-storage";

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
			foreach ($content as $key => $value) {
				if(preg_match('/'.self::DEFAULT_FOLDER_SUFFIXE.'-(.*)/', $value) == 1){
					$rand_folder_exists = true;
					$finalPath = $absPath.DS.$content[$key];
					break;
				}
			}

			//If the folder isn't empty, we create a saving subfolder inside it
			if(!empty($content) && !$rand_folder_exists){

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


		$this->addIndexFile($finalPath);

		$this->finalSavingDirectory = $finalPath;
		echo $finalPath."<br>";
		return $finalPath;

	}

	private function addIndexFile($path){
		/**
		 * @todo add an index file to the log saving directory
		 */
		echo "@todo indexer.php<br>";
	}

}