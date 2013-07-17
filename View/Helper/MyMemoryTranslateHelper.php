<?php
App::uses('HttpSocket', 'Network/Http');
class MyMemoryTranslateHelper extends AppHelper{
	public $FromLang = 'en';
	public $ToLang = 'es';
	public $Text;

	private $BaseUrl = 'http://mymemory.translated.net/api/get';
	private $charset = 'ISO-8859-1';

	/**
	 * Obtain translatext. If empty or not response url $BaseUrl it returns null
	 * @param String $text
	 * @return boolean|string
	 */
	public function translate($text){
		if($text == ''){
			return null;	
		}
	
		$this->connect = new HttpSocket(array('timeout' =>6000000));
		$contents = $this->connect->get($this->BaseUrl, 'q='.urlencode($text).'&langpair='. $this->FromLang . '%7C' . $this->ToLang); 			
		
		$json = json_decode($contents->body(), true);
		if($json['responseStatus'] == 200){ //If request was ok
			$this->TranslatedText = $json['responseData']['translatedText'];
			$this->DebugMsg = $json['responseDetails'];
			$this->DebugStatus = $json['responseStatus'];
			return $this->TranslatedText;
		} else { //Return some errors
			return null;
		}
	}
}
?>