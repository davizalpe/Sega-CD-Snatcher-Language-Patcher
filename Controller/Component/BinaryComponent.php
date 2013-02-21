<?php

/**
 * Component to copy and create Binary Files
 * with new translates
 * @author 3lm4dn0
 *
 */
class BinaryComponent extends Component {
		
	/**
	 * Initialize vars
	 * @param ComponentCollection $collection
	 * @param Array $settings
	 */
	function __construct(ComponentCollection $collection, $settings = array())
	{	
		$this->pos_ini = 14336; // 0x3800
		
		$this->separator = pack("H*" , "ff");
		$this->new_line = pack("H*" , "f2");
		$this->new_page = pack("H*" , "f6");

		/** 
		 * WARNING!!! NO CHANGE THE ORDER OF special_chars 
		 * */ 
		$this->special_chars_orig = array(
				$this->new_line, // <0>
				pack("H*" , "ec"), $this->new_page, // <1>, <2>
				pack("H*" , "ee"), pack("H*" , "f0"), // <3>, <4>
				pack("H*" , "fe"), pack("H*" , "f4"), pack("H*" , "fa"), pack("H*", "fb"), pack("H*", "fc"), // <5>, <6>, <7>, <8>
				pack("H*" , "fd"), pack("H*" , "f9"), // <9>, <10>
				pack("H*" , "f7"), pack("H*" , "f8")  // <11>, <12>
		);
				
		$this->special_chars_temp = array();
		foreach($this->special_chars_orig as $key=>$value){
			$this->special_chars_temp[] = "<".$key.">";
		}
		
		// Replace chars that not supports by default original binary file.
		$this->array_old_lang_chars = array('á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú', 'ñ', 'Ñ', '¡', '¿', 'ü', 'Ü');
		$this->array_new_lang_chars = array('`', '{', '|', '}', '~', 'A', 'E', 'I', 'O', 'U', '^', '=', '*', '\\', '$', 'U');
	
		$this->long_text = 224;  		// Min pixels text in lines 1, 2, 3 and 4
		$this->long_text_min = 208; 	// Min pixels text in lines 5 and 6
		$this->long_menu = 224; 		// Min pixels text in menu
		$this->long_jordan = 160; 		// Min pixels text in Jordan
		
		// Number of pixels for each char		
		$this->long_chars = array(
				' ' => 6, '!' => 7, '"' => 7, '#' => 8, '$' => 7, '%' => 9, '&' => 8, '\''=> 4, 
				'(' => 7, ')' => 6, '*' => 7, '+' => 7, ',' => 4, '-' => 7, '.' => 3, '/' => 9,  
				'0' => 8, '1' => 6, '2' => 8, '3' => 8, '4' => 8, '5' => 8, '6' => 8, '7' => 8, 
				'8' => 8, '9' => 8, ':' => 4, ';' => 4, '<' => 9, '=' => 7, '>' => 9, '?' => 7,
				'@' => 8, 'A' => 9, 'B' => 8, 'C' => 8, 'D' => 8, 'E' => 8, 'F' => 8, 'G' => 8, 
				'H' => 9, 'I' => 5, 'J' => 7, 'K' => 9, 'L' => 8, 'M' => 9, 'N' => 8, 'N' => 8, 'O' => 8,
				'P' => 8, 'Q' => 8, 'R' => 9, 'S' => 8, 'T' => 8, 'U' => 9, 'V' => 8, 'W' => 9, 
				'X' => 8, 'Y' => 8, 'Z' => 8, '[' => 6, '\\'=> 7, ']' => 7, '^' => 7, '_' => 8,
				'`' => 7, 'a' => 7, 'b' => 6, 'c' => 6, 'd' => 7, 'e' => 6, 'f' => 7, 'g' => 7, 
				'h' => 6, 'i' => 4, 'j' => 6, 'k' => 6, 'l' => 4, 'm' => 8, 'n' => 7, 'o' => 6,
				'p' => 6, 'q' => 7, 'r' => 7, 's' => 6, 't' => 5, 'u' => 7, 'v' => 6, 'w' => 8,
				'x' => 7, 'y' => 6, 'z' => 6, '{' => 7, '|' => 4, '}' => 7, '~' => 7, '©' => 9,
				pack("H*" , "ec") => 8, pack("H*" , "ee") => 8, pack("H*" , "f0") => 8, // <1>, <3>, <4>
		);

		$this->character_menu = 1;
		$this->character_computer = 2;
	}	
	
	/**
	 * Strpos recursive
	 * @param $haystack The string to search in
	 * @param $needle Character to search
	 * @return array with positions 
	 */
	private function strpos_recursive($haystack, $needle)
	{
		$results = array();
		$offset = 0;
		
	    $offset = strpos($haystack, $needle, $offset);
	    if($offset === false) {
	        return $results;
	    } else {
	        $results[] = $offset;
	        while($offset = strpos($haystack, $needle, ($offset+1))){
	        	$results[] = $offset;
	        }
	    }
	    
	    return $results;
	}
	
	/**
	 * Create temporal file $filename with chars in $str
	 * @param string $filename
	 * @param string $str
	 */
	private function _createFile($filename, $str){
		$fp = fopen($filename, 'w') or die("can't open file"); 
		
		fwrite($fp, $str);	
		
		fclose($fp);
	}
	
	/**
	 * Get number of pixels from a char $str
	 */
	private function str_pixel_len($str){
		$long = 0;
		
		for($i=0; $i < strlen($str); $i++)
		{
			if( isset($this->long_chars[substr($str, $i, 1)]) )
			{
				$long += $this->long_chars[substr($str, $i, 1)];
			}
		}

		return $long;
	}
	
	/**
	 * Check limits in pixels number os line $nlines.
	 * If character_id value is 2 it is a Jordan line.
	 * @param int $nlines Number of lines, start in 0. 
	 * @param booelan $iscomputer if true it is a Jordan line.
	 * @return int limite en pixels Number of limit in pixels
	 */
	private function checkLimitPixels($nlines, $iscomputer)
	{
		if($iscomputer){
			return $this->long_jordan;
		}
		
		if( ($nlines%6==4) || ($nlines%6==5) ){
			return $this->long_text_min;
		}
		
		return $this->long_text;
	}
	
	/**
	 * Split line at maximum pixels per line
	 * @param String $str complete text
	 * @param int $nlines
	 * @param boolean $iscomputer
	 * @return String splitted text
	 */
	private function splitLine($str, &$nlines = 0, $iscomputer = null)
	{		
		// if pixels < limit return an unique line
		$limit = $this->checkLimitPixels($nlines, $iscomputer);
		if( $this->str_pixel_len($str) <= $limit )
		{
			return $str;
		}

		// return cadena if unique word
		$array = explode(" ", $str);
		if(count($array) < 2)
		{
			return $str;
		}
		
		$text = "";
		foreach($array as $word){
			$limit = $this->checkLimitPixels($nlines, $iscomputer);

			if( $this->str_pixel_len($text.$word) > $limit )
			{
				$line[] = trim($text);
				$text = "";
				$nlines++;
			}

			$text .= $word." ";
		}
		$line[] = trim($text);
		return implode($this->new_line, $line);
	}
	
	/**
	 * Explode like original function but for n chars as delimits
	 * using strtok function.
	 * @param string $str to explode
	 * @return multitype:string
	 */
	private function multiple_explode($str){
		$array = array();
		$delim = "\r\n".$this->new_line;
		
		$tok = strtok($str, $delim);
		while ($tok !== false) {
			$array[] = $tok;
			$tok = strtok($delim);
		}
		
		return $array;
	}

	/**
	 * Split a text with $this->new_line or \r or \n
	 * que superar un límite dado por $this->long_text
	 * @param String $str
	 * @param boolean $iscomputer
	 * @return string
	 */
	private function splitText($str, $iscomputer)
	{	
		$array = $this->multiple_explode($str);
		
		$lines = array();
		$nlines = 0;
		foreach ($array as $line)
		{
			$lines[] = $this->splitLine($line, $nlines, $iscomputer);
			$nlines++;
		}

		return implode($this->new_line, $lines);
	}
	
	/**
	 * Explode cadena with $new_page special character if exists.
	 * If some sentence uses $new_page it will create a new page in game.
	 * If not exists $new_page in sentence, return $str splitted by $new_line
	 * @param string $str
	 * @param int $count number of actual line
	 * @param int $character_id
	 * @return string
	 */
	private function splitPage($str, $iscomputer){
	
		// Si No hay caracter especial salto de página (<2>)
		if(strpos($str, $this->new_page) === FALSE){
			return $this->splitText($str, $iscomputer);
		}
	
		$pages = array();
		$array = explode($this->new_page, $str);
		foreach($array as $page){
			$pages[] = $this->splitText($page, $iscomputer);
		}

		return implode($this->new_page, $pages);
	}	
	
	/**
	 * A partir de un array de Sentence obtiene un array con los textos.
	 * @param multitype:int $data
	 * @param int $array_offsets
	 * @return mixed
	 */
	private function getNewTexts($data, $str, &$array_offsets = array())
	{
		$sum_offset = 0;
		$pos = 0;
	
		foreach($data as $value)
		{
			$ismenu = ($value['character_id'] == $this->character_menu);
			
			if($sum_offset != 0)
			{
				$array_offsets[] = array(
						'old_offset' => sprintf("%04x", $value['position'] ),
						'new_offset' => sprintf("%04x", $value['position'] + $sum_offset),
						'ismenu'=> $ismenu,
				);
			}
	
			$str = str_replace($this->array_old_lang_chars, $this->array_new_lang_chars, $value['new_text']); // sustituye caracteres con tilde
			$str = str_replace($this->special_chars_temp, $this->special_chars_orig, $str);				 // sustituye caracteres especiales <n>
			
			if( !$ismenu ) // Si no es menú, dividir texto en lineas
			{
				$str = $this->splitPage($str, ($value['character_id']==$this->character_computer)); 
			}
			
			$sum_offset += strlen($str) - $value['nchars']; // Necesario para calcular el nuevo offset de siguientes textos.
	
			$array[] = $str;
		}

		return implode($this->separator, $array);
	}	
	
	/**
	 * Reemplaza el offset anterior por el nuevo en el texto con valores propios:
	 * 432000XXoffset donde XX es un valor entre 02 y 48 ó XX38offset 
	 * @param $offset Array con los desplazamiento original y nuevo del texto traducido. 
	 * Incluye character_id indicando si es un menú
	 * @param $subject Texto donde buscar los offsets
	 */
	private function str_replace_offset($offsets, $subject)
	{
		if( $offsets['ismenu'] )
		{
			// Search if it is a MENU
			$search  = "38" . $offsets['old_offset'];
			$replace = "38" . $offsets['new_offset'];
				
			$result = str_replace($search, $replace, $subject, $count);
			if($count > 0){
				return $result;
			}
		}
		else
		{	
			// Search texts
 			$result = preg_replace('/432000([0-3])([a-f0-9])'.$offsets['old_offset'].'/', '432000${1}${2}'.$offsets['new_offset'], $subject, 1, $count);
 			
			if($count > 0){
				return $result;
			}
			// Search another
			$search  = "44" . $offsets['old_offset'];
			$replace = "44" . $offsets['new_offset'];
			$result = str_replace($search, $replace, $subject, $count);
			if($count > 0){
				return $result;
			}
		}
		
		return NULL;
	}
	
	/**
	 * Recorre un array y sustituye los offsets
	 */
	private function array_replace_offsets($array, $str)
	{
		$str_hex = bin2hex($str);
		
		// Empezar a sustituir
		$black_list = array();
		foreach($array as $offsets)
		{
			if(in_array($offsets['old_offset'], $black_list, true))
			{
				var_dump($offsets['old_offset']);
				die("Error este offset ha sido insertado previamente.");
			}
				
			$str_hex = $this->str_replace_offset($offsets, $str_hex);
			if( $str_hex == NULL)
			{
				var_dump($offsets);
				echo "Original offset int: ".base_convert($offsets['old_offset'], 16, 10)."<br>";
				die("Upps. No se encontró el old_offset válido en el fichero");
			}
			
			$black_list[] = $offsets['new_offset']; // add to black list
		}
		
		return 	pack("H*" , $str_hex);
	}
	
	/**
	 * Ordena para que no coincidan los offsets
	 * @param $array Array con los datos
	 * @param $array_first Se inicializa a vacio
	 * @param $array_last  Se inicializa a vacio
	 */
	private function orderArrayFisrtLastOffsets($array = array(), &$array_first = array(), &$array_last = array())
	{
		$black_list = $array_first = $array_last = array();
		
		foreach ($array as $offsets)
		{
			if( in_array($offsets['old_offset'], $black_list, true) )
			{
				$array_first[] = $offsets;
			}else $array_last[] = $offsets;
			$black_list[] = $offsets['new_offset']; // add to black list
		}
	}
	
	/**
	 * Si hay nuevos offsets que se pudieran sobreescribir con otros offsets anteriores
	 * se reordena el array de offsets para que se cambien primero los anteriores.
	 * @param array() $array 
	 * @return $array
	 */
	private function orderArrayOffsets($array = array())
	{		
		$this->orderArrayFisrtLastOffsets($array, $array_first, $array_last);
		
		// Obtener primeros offsets que se deben cambiar
		while( !empty($array_first)  )
		{
			$this->orderArrayFisrtLastOffsets($array_first, $array_aux_first, $array_aux_last);
			$array_first = $array_aux_first;
			
			$order[] = array_merge($array_aux_first, $array_aux_last);
		}

		// Reordenar y meter del revés en array_first, los últimos offsets son los que deben de ir primero.
		if(isset($order))
		{
			$array_first = array();
			$order = array_reverse($order);
			foreach ($order as $v)
			{
				foreach ($v as $v2)
				{
					if( !in_array ( $v2 , $array_first, TRUE ) ){
						$array_first[] = $v2;
					}
				}	
			}
			$array = array_merge($array_first, $array_last);
		}

		return $array;
	}

	/**
	 * Permite crear un fichero con los nuevos textos y sus offsets desplazados
	 * a partir del fichero original. 
	 * @param $filename Fichero original
	 * @param $newfile Ruta del nuevo fichero a crear
	 * @param $data Información de Sentence con la información de todos los textos del fichero original y sus traducciones.
	 * @return devuelve NULL si ocurre algún error
	 */
	public function writeFile($filename, $newfile, $data){

		if(!file_exists($filename) || empty($data))
		{
			return null;
		}
		
		// Get content
		$str = file_get_contents($filename);
		$str_first = substr($str, 0, $this->pos_ini);
		
		// Obtener la parte del medio con los offsets
		$str_texts = $this->getNewTexts($data, $str, $array_offsets);
		
		// Buscar los que podrían coincidir con los offsets antiguos y obtener un array ordenado
		$array_offsets = $this->orderArrayOffsets($array_offsets);

		// Reemplaza los offsets anteriores por los nuevos
		$str_first = $this->array_replace_offsets($array_offsets, $str_first);
		
		// Merge
		$output = $str_first . $str_texts . $this->separator;
		if( strlen($output) & 1 ){
			$output .= pack("H*" , "00"); 
		}
		
		// Create new file
		$this->_createFile($newfile, $output);

		return filesize($newfile);
	}
	
}
?>
