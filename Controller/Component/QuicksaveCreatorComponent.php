<?php

/**
 * Component to parch save from Kega Fusion
 * with a binary file
 *
 */
//set_time_limit(0);
class QuicksaveCreatorComponent extends Component {
	
	/** 
	 * @param ComponentCollection $collection
	 * @param array $settings
	 */
	function __construct(ComponentCollection $collection, $settings = array())
	{
	}

	/**
	 * Create temp file $filename with the string $str
	 * @param string $filename
	 * @param string $str
	 */
	private function _createFile($filename, $str){
		$fp = fopen($filename, 'w') or die("can't open file");
	
		fwrite($fp, $str);
	
		fclose($fp);
	}
		
	/**
	 * Create a quicksave for Kega Fusion with a binary file
	 * @param $filename original quicksave
	 * @param $newfile path for the new save
	 * @param $binary_file  
	 * @param $sizelimit max binary file size limit. We need fill all data
	 */
	public function writeFile($filename, $newfile, $binary_file, $sizelimit = '55296')
	{
		if( !file_exists($filename) || !file_exists($binary_file) )
		{
			die("File not exists");
		}
		
		// Save content from binary file until $sizelimit (55296 bytes)
		$binary = str_pad(file_get_contents($binary_file), $sizelimit, pack("H*" , "00"));
		
		$str = file_get_contents($filename);
		
		$output_ini = substr($str, 0, 250098); 	// 250098 = 0x3D0F2(16) where starts binary file 
		$output_fin = substr($str, 305394); 	// 305394 = 0x4A8F2(16) where ends binary file
		
		// Join the code
		$output = $output_ini . $binary . $output_fin;
		
		// Create new file
		$this->_createFile($newfile, $output);
		
		if(filesize($newfile) != filesize($filename))
		{
			debug("New file has diferent size: ". filesize($newfile). " bytes");
			die;
		}
	}
}
?>