<?php
App::uses('AppController', 'Controller');
/**
 * Characters Controller
 *
 * @property Character $Character
 */
class CharactersController extends AppController {

	/**
	 * @see AppController::beforeFilter()
	 */
	function beforeFilter() {
		parent::beforeFilter();
	
		App::import('Model', 'FixedText');
	
		$this->FixedText = new FixedText();
	}	
	
	/**
	 * Search character as a word limiter with REGEXP and accepting "¡" and "¿" for latin languages at first 
	 * @param string $character
	 * @return array with conditions
	 */
	private function _getBinaryTextsConditions($character, $model = 'BinaryText')
	{
		return array('OR' => array(
					array('BINARY (`'.$model.'`.`new_text`) REGEXP' => '([¿¡]|[[:<:]])' . $character . '[[:>:]]'),
					array('BINARY (`'.$model.'`.`new_text`) REGEXP' => '([¿¡]|[[:<:]])' . mb_strtoupper($character) . '[[:>:]]')
				));
	}
	
	/**
	 * Text to replace statement
	 * @param string $text
	 * @param string $search
	 * @param string $replace
	 * @return string sql to replace $text
	 */
	private function _getReplaceText($text, $search, $replace)
	{
		return "REPLACE(" . $text . ", '" . $search . "', '" . $replace . "')";
	}	
	
	/**
	 * Search and Replace 
	 * @param string $search
	 * @param string $replace
	 * @return int total affected rows
	 */
	private function _replaceBinaryTexts($search, $replace)
	{

		$this->Character->BinaryText->updateAll(
				array('BinaryText.new_text' =>
						$this->_getReplaceText(
								$this->_getReplaceText("BinaryText.new_text", $search, $replace),
								mb_strtoupper($search),
								mb_strtoupper($replace)
						)
				),
				$this->_getBinaryTextsConditions($search),
				-1
		);
		
		$count = $this->Character->BinaryText->getAffectedRows();

		$this->FixedText->updateAll(
				array('FixedText.new_text' =>
						$this->_getReplaceText(
								$this->_getReplaceText("FixedText.new_text", $search, $replace),
								mb_strtoupper($search),
								mb_strtoupper($replace)
						)
				),
				$this->_getBinaryTextsConditions($search, 'FixedText'),
				-1
		);
			
		return $count;
	}
	
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
	
		$this->Character->recursive = -1;
		
		$characters = $this->paginate();
				
		foreach($characters as $k => $data)
		{
			if( $data['Character']['translatable'] && 
				( $data['Character']['name'] != $data['Character']['new_name'] )		
			){
				// Translates
				$characters[$k]['BinaryText']['translate'] = 
					$this->Character->BinaryText->find('count', array(
						'contain' => array(),
						'conditions' => $this->_getBinaryTextsConditions($data['Character']['name']),
				));
					
				// Restore
				$characters[$k]['BinaryText']['restore'] =
				$this->Character->BinaryText->find('count', array(
						'contain' => array(),
						'conditions' => $this->_getBinaryTextsConditions($data['Character']['new_name']),
				));					
			}else{
				$characters[$k]['BinaryText']['translate'] = 0;
				$characters[$k]['BinaryText']['restore'] = 0;
			}
		}
		
		$this->set('characters', $characters);
	}
	
	/**
	 * Admin view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_view($id = null) {
		if (!$this->Character->exists($id)) {
			throw new NotFoundException(__('Invalid fixed text'));
		}
		
		$data = $this->Character->find('first', array(
				'fields' => array('name', 'new_name'),
				'conditions' => array('Character.' . $this->Character->primaryKey => $id),
				));
		
		$conditions = array('character_id' => $id);
		
		if( isset($this->passedArgs['Search.text']) )
		{
			$conditions = $this->_getBinaryTextsConditions($data['Character']['name']);
		}
		elseif( isset($this->passedArgs['Search.new_text']) )
		{
			$conditions = $this->_getBinaryTextsConditions($data['Character']['new_name']); 
		}
		
		$this->paginate = array('BinaryText' => array(
				'fields' => array('BinaryText.order', 'BinaryText.text', 'BinaryText.new_text', 'BinaryFile.id', 'BinaryFile.filename'),
				'conditions' => $conditions,
				'order' => 'BinaryFile.filename, BinaryText.order',
				'limit' => 20,
		));
		$binary_texts = $this->paginate('BinaryText');
	
		$options = array(
				'conditions' => array('Character.' . $this->Character->primaryKey => $id));
		$character = $this->Character->find('first', $options);
		
		$total = $this->Character->BinaryText->find('count', array('conditions' => $conditions));
	
		$this->set(compact('character', 'binary_texts', 'total'));
	}	

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if (!$this->Character->exists($id)) {
			throw new NotFoundException(__('Invalid character'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			
			$options = array('fieldList' => array('new_name', 'translatable'));
			
			if ($this->Character->save($this->request->data, $options)) 
			{
				$this->Session->setFlash(__('The character has been saved'));
				$this->redirect($this->_redirectPassedArgs());
			} else {
				$this->Session->setFlash(__('The character could not be saved. Please, try again.'));
			}
		} else {
			$options = array(
					'conditions' => array('Character.' . $this->Character->primaryKey => $id));
			$this->request->data = $this->Character->find('first', $options);
		}
		
		$options = array(
					'conditions' => array('Character.' . $this->Character->primaryKey => $id));
		$data = $this->Character->find('first', $options); 
		$this->set('readonly', $data['Character']['readonly']);
	}
	
/**
 * admin_translate method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function admin_translate($id = null) {
		$this->Character->id = $id;
		if (!$this->Character->exists()) {
			throw new NotFoundException(__('Invalid character'));
		}
		
		if ($this->request->is('post') || $this->request->is('put')) {		
		
			$options = array(				
					'recursive' => -1,
					'conditions' => array('Character.' . $this->Character->primaryKey => $id));
			$data = $this->Character->find('first', $options);			
			
			if( $data['Character']['readonly'] || !$data['Character']['translatable'])
			{
				throw new NotFoundException(__('Invalid character'));
			}
			
			if( $data['Character']['name'] == $data['Character']['new_name'] )
			{
				throw new NotFoundException(__('Invalid character'));			
			}						
		
			$search = $data['Character']['name'];
			$replace = $data['Character']['new_name'];
			
			$affectedRows = $this->_replaceBinaryTexts($search, $replace);
			
			$this->Session->setFlash(__('%d characters was changed', $affectedRows));
			$this->redirect($this->_redirectPassedArgs());					
		}
	}
	
	/**
	 * admin_restore method
	 *
	 * @throws NotFoundException
	 * @throws MethodNotAllowedException
	 * @param string $id
	 * @return void
	 */
	public function admin_restore($id = null) {
		$this->Character->id = $id;
		if (!$this->Character->exists()) {
			throw new NotFoundException(__('Invalid character'));
		}
	
		if ($this->request->is('post') || $this->request->is('put')) {
	
			$options = array(
					'recursive' => -1,
					'conditions' => array('Character.' . $this->Character->primaryKey => $id));
			$data = $this->Character->find('first', $options);
				
			if( $data['Character']['readonly'] || !$data['Character']['translatable'] )
			{
				throw new NotFoundException(__('Invalid character'));
			}
				
			if( $data['Character']['name'] == $data['Character']['new_name'] )
			{
				throw new NotFoundException(__('Invalid character'));
			}
				
			$search = $data['Character']['new_name'];
			$replace = $data['Character']['name'];

			$affectedRows = $this->_replaceBinaryTexts($search, $replace);
				
			$this->Session->setFlash(__('%d characters was restored', $affectedRows));
			$this->redirect($this->_redirectPassedArgs());
		}
	}
		
}