#!/usr/bin/env php
<?php

ini_set('memory_limit', '512M');

function set_separator(&$dir)
{	
	if(!is_dir($dir))
	{
		die("$dir is not a directory\n");
	}
	
	$pos = strrpos($dir, DIRECTORY_SEPARATOR);
	if( ($pos === FALSE)
	||
	(($pos !== FALSE	) && ($pos != strlen($dir)-1))
	)
	{
		$dir .= DIRECTORY_SEPARATOR;
	}	
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
function get_position($sector_LE)
{
	// Get offset in decimal with sector_BE * 2048(0x800)
	return base_convert(bin2hex($sector_LE), 16, 10) * 2048;	
	
}

/**
 * Get free space
 * @param unknown $file
 * @param unknown $string
 */
function get_free_space($file, $offset, $filesize, $original_filesize, $string)
{
	echo $file."\n";
		
	$zero = pack("H*", "00");
	$free_bytes = 0;
	$pos = $offset + $filesize;
	$len = strlen($string);
	
	// count free bytes plus if new filesize is less than old file
	$extra_bytes = 0;
	if($original_filesize > $filesize)
	{
		$extra_bytes = $original_filesize - $filesize;
		echo "\told_filesize: ". $filesize."\n";
		echo "\textra_byes: ". $extra_bytes."\n";
	}
	
	echo "\tfilesize: ". $filesize."\n";
	echo "\toffset: ". $offset."\n";
	
	// count bytes with 0x00 until next file
	while( ($pos < $len) && ($string[$pos] == $zero) )
	{
		$pos++;
		$free_bytes++;
	}
	
	echo "\textra bytes: ";
	echo ($free_bytes+$extra_bytes)."\n";

}

/**
 * Patch iso
 * @param string $str file iso content
 * @param array $array_binary_files array with files bin content
 */
function patch_iso($string, $array_binary_files, $dir)
{
	// Files Table from 0xA000 (40960) to 0x0B26A (45674)
	$orig_files_table = $files_table = substr($string, 40960, 45674-40960);
	
	$bak_offset = 0;
	foreach($array_binary_files as $file)
	{		
		if( ($pos=strpos($files_table, $file)) !== FALSE )
		{					
			// Get data from table file LittleEndian 
			$sector_LE = substr($files_table, $pos-27, 4);			
			$size_LE = substr($files_table, $pos-19, 4);
			
			// Get original filesize
			$original_filesize = base_convert(bin2hex($size_LE), 16, 10);

			// Get filesize from $file
			if(file_exists($dir . $file))
			{
				$filesize = filesize($dir . $file);
			}
			else
			{
				$filesize = $original_filesize;
			}								
			
			// get position in ISO from $file
			$offset = get_position($sector_LE);
						
			get_free_space($file, $offset, $filesize, $original_filesize, $string);			

			/*
			// Print info from the before bak $file
			if($bak_offset > 0)
			{
				echo $bak_file." with free space ".($offset - $bak_offset + $bak_filesize)." bytes\n";
			}						
			
			
			// Show file data
			//echo "original size ($original_filesize) / new size ($filesize)\n";
			
			// Store last file info
			$bak_filesize = $filesize;
			$bak_file = $file;
			$bak_offset = $offset;
			*/
		}
	}

}

/**
 * Check if it is a dir and read files from Snatcher CD
 *
 * @param string $dir
 * @return array $array_binary_files with list of valid files to replace from $dir
 */
function check_path(&$dir)
{	
	echo "Checking dir $dir\n";
	
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
	
	return $array_valid_files;
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
		
	$pal = "534547414449534353595354454D20205345474149504D454E552000010000014B4F4E414D492053303032000001000000000800000060000000000000000000000068000000180000000000000000003130313231393934202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020";
	$usa = "534547414449534353595354454D20205345474149504D454E552000010000014B4F4E414D492052303032000001000000000800000060000000000000000000000068000000180000000000000000003130313031393934202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020202020";
	

	$string = file_get_contents($filename);
	$fileData = strtoupper(bin2hex(substr($string, 0, 256)));
	
	if( ( $fileData != $pal) && ( $fileData != $usa) )
	{
		die("$filename is not a sega cd snatcher iso.\n");
	}
	
	if( $fileData == $pal)
	{
		echo "Region PAL\n";	
	}
	elseif ( $fileData == $usa)
	{
		echo "Region NTSC\n";
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
	if ( ($argc == 2) && ($argv[1] == "--version") )
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
	
	$fileiso = $argv[1];
	$dir	 = $argv[2];

	// Check ISO
	$string = check_iso($fileiso);	
	
	// Check dir and files
	// Set bar at end dir if not exists
	set_separator($dir);
	$array_binary_files = check_path($dir);

	// Patch ISO
	patch_iso($string, $array_binary_files, $dir);
}

main($argv);

?>