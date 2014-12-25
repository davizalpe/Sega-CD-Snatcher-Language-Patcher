<?php
/**
 * Ayundante traducctor directo de la web translate.google.com
 * basado en la versión de:
 * http://bakery.cakephp.org/articles/alkavan/2010/01/05/google-translate-helper
 * @author elmadno
 *
 */
App::uses('HttpSocket', 'Network/Http');
class GoogleTranslateHelper extends AppHelper {
	
	public $settings = array();
	
	public $fromLang;
	public $toLang;
	public $baseUrl;
	public $charset;
	
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);
		
		$lang = Configure::read('Snatcher.Translate.language');
		if( empty($lang) ) $lang = 'es';	
		
		// Default settings
		$this->settings = array(
				'fromLang'=> 'en',
				'toLang'=> $lang,
				'baseUrl'=> array('host' => 'translate.google.com', 'path' => '/'),
				'charset' => 'ISO-8859-1',
		);
			
		// Configure settings
		$this->settings = array_merge(
			$this->settings, (array)$settings
		);
		
		// Set values
		$this->fromLang	= $this->settings['fromLang'];
		$this->toLang	= $this->settings['toLang'];
		$this->baseUrl	= $this->settings['baseUrl'];
		$this->charset	= $this->settings['charset'];
	}
	
	private function checkUrl()
	{
		$url['host'] = $this->baseUrl['host'];
		$url['path'] = $this->baseUrl['path'];
		$url['port'] = 443;
		
		$path = (isset($url['path'])) ? $url['path'] : '/';		
		
		if (isset($url['host']) && $url['host'] != gethostbyname($url['host'])) {
		
            $fp = fsockopen('ssl://' . $url['host'], $url['port'], $errno, $errstr, 30);
	
			if (!$fp) return false; //socket not opened
		
			fputs($fp, "HEAD $path HTTP/1.1\r\nHost: $url[host]\r\n\r\n"); //socket opened
			$headers = fread($fp, 4096);
			fclose($fp);
		
			return preg_match('#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers); //matching header
		
		} // if parse url
		else return false;
	}

	/**
	 * Get content from url with $text as uri.
	 * Fast mode using HtttSocket Class from CakePHP
	 * @param string $text
	 */
	private function getByHttpSocket($text){
		
		if( !$this->checkUrl() ){
			return null;
		}
	
/*
		$this->connect = new HttpSocket();
		$contents = $this->connect->get(
				$this->baseUrl,
				array('q' => $text, 'langpair' => $this->fromLang . '|' . $this->toLang)
				);
		
		if(!$contents->isOk())
		{
			return null;
		}
		
		return $contents->body();
*/

                $url = "https://". $this->baseUrl['host'] . $this->baseUrl['path'];
                $data = array('q' => $text, 'langpair' => $this->fromLang . '|' . $this->toLang);
                $options = array(
                                'http' => array(
                                                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                                                'method'  => 'POST',
                                                'content' => http_build_query($data),
                                )
                );

                $context  = stream_context_create($options);
                $result = file_get_contents($url, false, $context);

                if(!$result)
                {
                        return null;
                }

                return $result;

	}
	
	/**
	 * Get translated text
	 * @param String $contents
	 * @return boolean|mixed
	 */
	private function parseHtml($contents){
		$text_ini = "onmouseover=\"this.style.backgroundColor='#ebeff9'\" onmouseout=\"this.style.backgroundColor='#fff'\">";
		$text_fin = "</div>";
		$ini = strpos($contents, $text_ini);
		$fin = strpos($contents, $text_fin, $ini);		
			
		if( ($ini === false) || ($fin === false) ){
			return false;
		}
		
		$result = substr($contents, $ini+strlen($text_ini), $fin-$ini-strlen($text_ini));
					
		/* Remove all span tags beetwen the text */
		$result = preg_replace('/<\/span>/', '', $result);
		$result = preg_replace('/<span title=[^>].*>/', '', $result);
		
		return $result;
	}
	
	/**
	 * Translate a text with url
	 * @param String $text
	 * @return boolean|string
	 */
	public function translate($text){		
		if($text == ''){
			return false;
		}
		
		// Get contents
		$contents = $this->getByHttpSocket($text);	
		if($contents == false){
			return false;
		}
		 
		$result = $this->parseHtml($contents);		
		
		// Convert from HTML to text
		$result = html_entity_decode($result, ENT_QUOTES, $this->charset);
		
		// Replace with spaces and spcecial chars			
		$bad_chars = array('</span>', ' <0> ', '<4> ', ' <3>', '  ', ' ...');
		$good_chars = array('', '<0>', '<4>', '<3>', ' ', '...');
		$result = str_replace($bad_chars, $good_chars, $result);
					
		// Convert to UTF-8
		$result = iconv($this->charset, 'utf-8', $result);
		
		//Replace tags br
		$result = str_replace("<br>", "\n", $result);
					
		return $result;
	}
}
?>
