#!/usr/bin/env php
<?php

/**
 * Create temporal file $filename with chars in $str
 * @param string $filename
 * @param string $str
 */
function create_file($filename, $str)
{
	$fp = fopen($filename, 'w') or die("can't open file");

	fwrite($fp, $str);

	fclose($fp);
	
	echo "New $filename created.\n";
}

/**
 * Change little endian to big endian on a little endian machine.
 * 
 * @param string $hex hexadecimal size in little endian with 0s.
 * @return string hex big endian
 */
function little2bigendian($hex) {	
	
	$bin = strrev(pack("H*", $hex));
	
	return bin2hex($bin);	
}

/**
 * Replaces files table content with new filesize from file.
 * 
 * @param string $string Files table content
 * @param int $pos Position from $single_file_table in $search 
 * @param string dir dir from binary files
 * @param string $file file name from actual binary file
 * @return string files table contents with new filesize from binary file
 */
function replace_table($string, $pos, $dir, $file, $filesize)
{	
	if($filesize <= 0)
	{
		die("File $file has not size.\n");
	}	
		
	$size_LE = sprintf("%08x", $filesize);
	$size_BE = little2bigendian($size_LE);		
	
	$replace = pack("H*", $size_BE . $size_LE);
		
	$result = substr_replace($string, $replace, $pos-23, 8);
	
	return $result; 
}

/**
 * Replace a file from $str
 * @param string $string Content from ISO 
 * @param string $sector_LE Sector from File  
 * @param string $dir dir from binary files
 * @param string $file name from binary file
 * @param int $filesize Size from new binary file
 * @param int $original_filesize Size from original binary file
 * @param string 
 * @return mixed Content from ISO with new binary file
 */
function replace_file($string, $sector_LE, $dir, $file, $filesize, $original_filesize)
{
	// Get offset in decimal with sector_BE * 2048(0x800)
	$offset = base_convert(bin2hex($sector_LE), 16, 10) * 2048;
	
	// Get content from new file
	$replacement = file_get_contents($dir . $file);
	
	echo bin2hex(substr($string, $offset+$original_filesize-8, 8))."\n";
	echo bin2hex(substr($string, $offset+$original_filesize, 8))."\n";

	// Save content from binary file until $original_filesize
	if( $original_filesize > $filesize)
	{
		//echo "$file es de menor tamaño\n";
		$replacement = str_pad($replacement, $original_filesize, pack("H*", "00"));
		$filesize = $original_filesize;
	}
	
	// Show file data
	echo "original size ($original_filesize) / new size ($filesize)\n";	
	
	// Replace the file content until $original_filesize
	$result = substr_replace($string, $replacement, $offset, $filesize);
	
	return $result;	
}

/**
 * Patch iso
 * @param string $str file iso content
 * @param array $array_bin array with files bin content
 */
function patch_iso($string, $array_bin, $dir)
{
	// Files Table from 0xA000 (40960) to 0x0B26A (45674)
	$orig_files_table = $files_table = substr($string, 40960, 45674-40960);

	echo "Patching: \n";
	
	// Replace files
	foreach($array_bin as $file)
	{		
		if( ($pos=strpos($files_table, $file)) !== FALSE )
		{		
			echo "\tFile $file... ";
			
			// Get data from table file LittleEndian 
			$sector_LE = substr($files_table, $pos-27, 4);			
			$size_LE = substr($files_table, $pos-19, 4);
			
			// Get original filesize
			$original_filesize = base_convert(bin2hex($size_LE), 16, 10);

			// Get new filesize
			$filesize = filesize($dir . $file);
			
			// Replace file		
			$string = replace_file($string, $sector_LE, $dir, $file, $filesize, $original_filesize);		

			// Replace table
			if($filesize != $original_filesize)
			{
				$files_table = replace_table($files_table, $pos, $dir, $file, $filesize);
			}						
		}
	}
	
	// Replace Files Table from ISO content
	$result = str_replace($orig_files_table, $files_table, $string);

	return $result;
}

/**
 * Check if it is a dir and read files from Snatcher CD
 * @param string $dir
 */
function check_path(&$dir)
{	
	echo "Checking dir $dir\n";
	if(!is_dir($dir))
	{
		die("$dir is not a directory\n");
	}
	
	// Set bar at end dir if not exists
	$pos = strrpos($dir, DIRECTORY_SEPARATOR);
	if( ($pos === FALSE)
			||
			(($pos !== FALSE	) && ($pos != strlen($dir)-1))
	)
	{
		$dir .= DIRECTORY_SEPARATOR;
	}	
	
	$array_bin = array();

	$array_valid_files = array("ABS.TXT", "BIB.TXT", "CPY.TXT", "DATA_A0.BIN", "DATA_B0.BIN", "DATA_D0.BIN", "DATA_D1.BIN",
			"DATA_D2.BIN", "DATA_F0.BIN", "DATA_F4.BIN", "DATA_G0.BIN", "DATA_H00.BIN", "DATA_H01.BIN", "DATA_H02.BIN", 
			"DATA_H03.BIN", "DATA_H04.BIN", "DATA_H05.BIN", "DATA_H06.BIN", "DATA_H07.BIN", "DATA_H08.BIN", "DATA_H09.BIN", 
			"DATA_H11.BIN", "DATA_H12.BIN", "DATA_H13.BIN", "DATA_H14.BIN", "DATA_H15.BIN", "DATA_I0.BIN", "DATA_J0.BIN", 
			"DATA_K1.BIN", "DATA_M0.BIN", "DATA_O0.BIN", "DATA_P0.BIN", "DATA_Q3.BIN", "DATA_Q5.BIN", "DATA_Q8.BIN", 
			"DATA_S0.BIN", "DATA_S1.BIN", "DATA_S2.BIN", "DATA_T0.BIN", "DATA_U0.BIN", "DATA_Y00.BIN", "DATA_Y01.BIN", 
			"DATA_Y03.BIN", "DATA_Y04.BIN", "DATA_Y05.BIN", "DATA_Y06.BIN", "DATA_Y07.BIN", "DATA_Y08.BIN", "DATA_Y09.BIN", 
			"DATA_Y10.BIN", "DATA_Y11.BIN", "DATA_Y12.BIN", "DATA_Y13.BIN", "DATA_Y14.BIN", "DATA_Y15.BIN", "DATA_Y16.BIN", 
			"FMWR_1.BIN", "PCMDRMDT.BIN", "PCMLD_01.BIN", "PCMLT_01.BIN", "SP00.BIN", "SP01.BIN", "SP02.BIN", "SP03.BIN", 
			"SP04.BIN", "SP05.BIN", "SP06.BIN", "SP07.BIN", "SP08.BIN", "SP09.BIN", "SP10.BIN", "SP11.BIN", "SP12.BIN", 
			"SP13.BIN", "SP14.BIN", "SP15.BIN", "SP16.BIN", "SP17.BIN", "SP18.BIN", "SP19.BIN", "SP20.BIN", "SP21.BIN", 
			"SP22.BIN", "SP23.BIN", "SP24.BIN", "SP25.BIN", "SP26.BIN", "SP27.BIN", "SP28.BIN", "SP29.BIN", "SP30.BIN", 
			"SP31.BIN", "SP32.BIN", "SP33.BIN", "SP34.BIN", "SP35.BIN", "SP36.BIN", "SP37.BIN", "SP38.BIN", "SUBCODE.BIN");	
	
	$d = opendir($dir);
	while($file = readdir($d))
	{	
		if(is_file($dir. $file) 
			&& in_array(strtoupper($file), $array_valid_files, true) )
		{	
			$array_bin[] = $file;	
		}
	}
	
	if(empty($array_bin))
	{
		die("$dir has not binary files from Snatcher game.\n");
	}	
	
	return $array_bin;
}

/**
 * Check if exists iso
 * @param string $filename
 */
function check_iso($filename)
{
	echo "Checking iso $filename\n";
	if( !file_exists($filename) )
	{
		die("$filename not exists\n");
	}

	$string = file_get_contents($filename);
	
	if ( bin2hex(substr($string, 0, 256)) !=
			"534547414449534353595354454d20205345474149504d454e552000010000014b4f4e414d492053303032000001000000000800000060000000000000000000000068000000180000000000000000003130313231393934202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020"
	)
	{
		die("$filename is not a sega cd snatcher iso.\n");
	}

	return $string;
}


/**
 * Main program
 */
function main(&$argv)
{
	$argc = count($argv);

	// Show version
	if ( ($argc == 2) && ($argv[1] == "-version") )
	{
		echo "Version 0.1\n";
		echo "Sega CD Snatcher patcher for translate to spanish language\n";
		exit;
	}
	// When not intro 2 params
	elseif ( $argc != 3 )
	{
		echo "usage $argv[0] image.iso [folder from binary files]\n";
		echo "example: $argv[0] snatcher.iso allfiles/\n";
		echo "after patch creates the file output.iso\n\n";
		echo "where options include:\n";
		echo "\t-version\tprint product version and exit\n";
		echo "See https://github.com/3lm4dn0\n";
		exit;		
	}

	// Check ISO
	$string = check_iso($argv[1]);

	// Check and get files
	$array_bin = check_path($argv[2]);

	// Patch ISO
	$string = patch_iso($string, $array_bin, $argv[2]);
	
	// Create new ISO
	create_file("output.iso", $string);		
}

main($argv);

?>