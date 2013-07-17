<?php
App::uses('AppController', 'Controller');
/**
 * Keywords Controller
 *
 * @property Keyword $Keyword
 */
class KeywordsController extends AppController {
	
	/**
	 * @see AppController::beforeFilter()
	 */
	function beforeFilter() {
		parent::beforeFilter();	

		App::import('Model', 'BinaryText');
		App::import('Model', 'FixedText');
		
		$this->BinaryText = new BinaryText();

		$this->FixedText = new FixedText();
	}

	/**
	 * Search keyword as a word limiter with REGEXP and accepting "¡" and "¿" for latin languages at first
	 * @param string $keyword
	 * @param string $model
	 * @return array with conditions
	 */
	private function _getBinaryTextsConditions($keyword, $model = 'BinaryText')
	{
		return array('OR' => array(
				array('BINARY (`'.$model.'`.`new_text`) REGEXP' => '([¿¡]|[[:<:]])' . $keyword . '[[:>:]]'),
				array('BINARY (`'.$model.'`.`new_text`) REGEXP' => '([¿¡]|[[:<:]])' . mb_strtolower($keyword) . '[[:>:]]'),
				array('BINARY (`'.$model.'`.`new_text`) REGEXP' => '([¿¡]|[[:<:]])' . mb_strtoupper($keyword) . '[[:>:]]')
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
		$text = "new_text";
		
		if(	( strtolower($search) != $search ) &&
			( strtolower($replace) != $replace )
		){
			$text = $this->_getReplaceText($text, mb_strtolower($search), mb_strtolower($replace));
		}

		// Update Binary Texts
		$this->BinaryText->recursive = -1;
		$this->BinaryText->updateAll(
				array('BinaryText.new_text' =>
					$this->_getReplaceText(						
						$this->_getReplaceText($text, $search, $replace),
						mb_strtoupper($search),
						mb_strtoupper($replace)
					)
				),
				$this->_getBinaryTextsConditions($search),
				-1
		);
		
		$count = $this->BinaryText->getAffectedRows();
		
		// Update Fixed Texts
		$this->FixedText->recursive = -1;
		$this->FixedText->updateAll(
				array('FixedText.new_text' =>
						$this->_getReplaceText(
								$this->_getReplaceText($text, $search, $replace),
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
		$this->Keyword->recursive = 0;
					
		$keywords = $this->paginate();
		
		foreach($keywords as $k => $data)
		{
			// Translates
			$keywords[$k]['BinaryText']['translate'] =
			$this->BinaryText->find('count', array(
					'contain' => array(),
					'conditions' => $this->_getBinaryTextsConditions($data['Keyword']['name']),
			));
			
			// Restore
			$keywords[$k]['BinaryText']['restore'] =
			$this->BinaryText->find('count', array(
					'contain' => array(),
					'conditions' => $this->_getBinaryTextsConditions($data['Keyword']['new_name']),
			));			
		}
		
		$this->set('keywords', $keywords);
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		if (!$this->Keyword->exists($id)) {
			throw new NotFoundException(__('Invalid keyword'));
		}
		
		$this->Keyword->recursive = -1;

		$data = $this->Keyword->find('first', array(
				'fields' => array('name', 'new_name'),
				'conditions' => array('Keyword.' . $this->Keyword->primaryKey => $id),
		));		
		
		if( isset($this->passedArgs['Search.text']) )
		{
			$conditions = $this->_getBinaryTextsConditions($data['Keyword']['name']);
		}
		elseif( isset($this->passedArgs['Search.new_text']) )
		{
			$conditions = $this->_getBinaryTextsConditions($data['Keyword']['new_name']);
		}
		else
		{
			throw new NotFoundException(__('Invalid keyword'));
		}
		
		$this->paginate = array('BinaryText' => array(
				'contain' => array('BinaryFile.id', 'BinaryFile.filename'),
				'fields' => array('BinaryText.order', 'BinaryText.text', 'BinaryText.new_text'),
				'conditions' => $conditions,
				'order' => 'BinaryFile.filename, BinaryText.order',
				'limit' => 20,
		));
		$binary_texts = $this->paginate('BinaryText');
		
		$options = array(
				'conditions' => array('Keyword.' . $this->Keyword->primaryKey => $id));
		$keyword = $this->Keyword->find('first', $options);
		
		$total = $this->BinaryText->find('count', array('conditions' => $conditions));
		
		$this->set(compact('keyword', 'binary_texts', 'total'));		
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->Keyword->create();
			if ($this->Keyword->save($this->request->data)) {
				$this->Session->setFlash(__('The keyword has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The keyword could not be saved. Please, try again.'));
			}
		}
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if (!$this->Keyword->exists($id)) {
			throw new NotFoundException(__('Invalid keyword'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			
			$options = array('fieldList' => array('new_name'));
			
			if ($this->Keyword->save($this->request->data, $options)) {
				$this->Session->setFlash(__('The keyword has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The keyword could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Keyword.' . $this->Keyword->primaryKey => $id));
			$this->request->data = $this->Keyword->find('first', $options);
		}
		
		$options = array('fields' => 'name', 'conditions' => array('Keyword.' . $this->Keyword->primaryKey => $id));
		$name = $this->Keyword->find('first', $options);

		$this->set('name', $name['Keyword']['name']);
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		$this->Keyword->id = $id;
		if (!$this->Keyword->exists()) {
			throw new NotFoundException(__('Invalid keyword'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Keyword->delete()) {
			$this->Session->setFlash(__('Keyword deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Keyword was not deleted'));
		$this->redirect(array('action' => 'index'));
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
		$this->Keyword->id = $id;
		if (!$this->Keyword->exists()) {
			throw new NotFoundException(__('Invalid keyword'));
		}
	
		if ($this->request->is('post') || $this->request->is('put')) {
	
			$options = array(
					'recursive' => -1,
					'conditions' => array('Keyword.' . $this->Keyword->primaryKey => $id));
			$data = $this->Keyword->find('first', $options);
	
			$search = $data['Keyword']['name'];
			$replace = $data['Keyword']['new_name'];
				
			$affectedRows = $this->_replaceBinaryTexts($search, $replace);
				
			$this->Session->setFlash(__('%d keywords was changed', $affectedRows));
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
		$this->Keyword->id = $id;
		if (!$this->Keyword->exists()) {
			throw new NotFoundException(__('Invalid keyword'));
		}
	
		if ($this->request->is('post') || $this->request->is('put')) {
	
			$options = array(
					'recursive' => -1,
					'conditions' => array('Keyword.' . $this->Keyword->primaryKey => $id));
			$data = $this->Keyword->find('first', $options);
	
			$search = $data['Keyword']['new_name'];
			$replace = $data['Keyword']['name'];
	
			$affectedRows = $this->_replaceBinaryTexts($search, $replace);
	
			$this->Session->setFlash(__('%d keywords was restored', $affectedRows));
			$this->redirect($this->_redirectPassedArgs());
		}
	}	
}
