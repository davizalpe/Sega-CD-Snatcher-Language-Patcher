<?php
/**
 * Component to compress files
 * @author elmadno
 *
 */
//set_time_limit(0);
class CompressComponent extends Component {

	
	/**
	 * Create a zip with a list of files in destination
	 * @param array $array_files files from
	 * @param string $destination destination for file
	 * @param boolean $overwrite it true overwrites
	 * @return boolean if create a zip file
	 */
	public function createZip($array_files = array(), $destination = '', $overwrite = false)
	{
		//if the zip file already exists and overwrite is false, return false
		if(file_exists($destination) && !$overwrite) { return false; }
		//vars
		$valid_files = array();
		//if files were passed in...
		if(is_array($array_files)) {
			//cycle through each file
			foreach($array_files as $file) {
				//make sure the file exists
				if(file_exists($file)) {
					$valid_files[] = $file;
				}
			}
		}
	
		//if we have good files...
		if(count($valid_files)) {
			//create the archive
			$zip = new ZipArchive();
			if($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
				return false;
			}
			//add the files
			foreach($valid_files as $file) {
				$zip->addFile($file, substr($file, strrpos($file, DS)+1));
			}
	
			//close the zip -- done!
			$zip->close();
	
			//check to make sure the file exists
			return file_exists($destination);
		}
		else
		{
			return false;
		}
	}	
}