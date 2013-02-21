<?php

/**
 * Componente para parchear los saves de KegaFusion
 * @author 3lm4adn0
 *
 */
//set_time_limit(0);
class QuicksaveComponent extends Component {
	
	/**
	 * Inicialización de variables y datos que podrán ser modificados
	 * @param ComponentCollection $collection
	 * @param unknown_type $settings
	 */
	function __construct(ComponentCollection $collection, $settings = array())
	{
	}

	/**
	 * crea fichero temporal $filename con cadena $str
	 * @param unknown_type $filename
	 * @param unknown_type $str
	 */
	private function _createFile($filename, $str){
		$fp = fopen($filename, 'w') or die("can't open file");
	
		fwrite($fp, $str);
	
		fclose($fp);
	}
		
	/**
	 * Permite crear un quicksave para KegaFusion con los textos insertados
	 * @param $filename quicksave original
	 * @param $newfile Ruta del nuevo quicksave a crear
	 * @param $binary_file Binario del documento asociado al save con la última traducción 
	 * @return devuelve NULL si ocurre algún error
	 */
	public function writeFile($filename, $newfile, $binary_file){
		if(!file_exists($filename) || !file_exists($binary_file))
		{
			die("no existen los ficheros necesarios");
			return null;
		}
		
		// Guardar el contenido del binario a insertar + 0x00 hasta 55296 bytes (o chars)
		$binary = str_pad(file_get_contents($binary_file), 55296, pack("H*" , "00"));
		
		$str = file_get_contents($filename);
		
		$output_ini = substr($str, 0, 250098); // 250098 = 0x3D0F2(16) posición donde comienza el binario 
		$output_fin = substr($str, 305394); // 305394 = 0x4A8F2(16) posición donde termina el binario (incluye 0s al final de relleno)
		// Junta los nuevos trozos de texto y crea el fichero
		$output = $output_ini . $binary . $output_fin;
		
		$this->_createFile($newfile, $output);
		
		if(filesize($newfile) != filesize($filename)){
			debug("Tamaño de nuevo fichero distinto: ".filesize($newfile). " bytes");
			die;
		}
		
		return true;
	}
}
?>
