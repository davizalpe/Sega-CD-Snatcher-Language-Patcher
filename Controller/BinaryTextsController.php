<?php
App::uses('AppController', 'Controller');
/**
 * BinaryTexts Controller
 *
 * @property BinaryText $BinaryText
 */
class BinaryTextsController extends AppController {

	public $helpers = array('GoogleTranslate');

	/**
	 * Return true if user is tester
	 * @param array $data
	 * @return boolean
	 */
	private function _isTester($data)
	{
		if( isset($data['Testers']) ){
			foreach($data['Testers'] as $user_id){
				if($user_id['id'] == $this->Auth->user('id')){
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Get the list of Characters from the game
	 * @param id $binary_file_id
	 * @return array $characters list of characters
	 */
	private function _getCharacters($binary_file_id = null){
	
		$array = $this->BinaryText->find('all',
				array('fields' => 'character_id',
						'contain' => 'Character.new_name',
						'group' => 'character_id',
						'order' => 'id',
						'conditions' => array('binary_file_id' => $binary_file_id)
				));
	
		$characters = array();
	
		if( !empty($array) )
		{
			foreach($array as $v)
			{
				$characters[$v['BinaryText']['character_id']] = $v['Character']['new_name'];
			}
		}
	
		return $characters;
	}	

	/**
	 * Get data from one BinaryText to edit and change character
	 * @param int|null $id
	 */
	private function _getBinaryText($id = null)
	{	
		return $this->BinaryText->find('first', array(
				'contain' => array(
						'OldCharacter' => array('id', 'name'),
						'BinaryFile' => array('fields' => array('filename', 'user_id'))),
	
				'fields' => array('user_id', 'character_id', 'order', 'text', 'fixed_text_id'),
	
				'conditions' => array($this->BinaryText->alias. '.' .$this->BinaryText->primaryKey => $id)
		));
	}
	
	/**
	 * search method
	 * @param $admin if is true redirects to admin index page. Default false.
	 */
	public function search($admin = false)
	{
		$url = array();
		
		if(isset($this->passedArgs['redirect'])){
			$url['action'] = $this->passedArgs['redirect'];
		}
		
		// build a URL will all the search elements in it
		foreach ($this->data as $k => $v){
			foreach ($v as $k2 => $v2){
				$url[$k . "." .$k2] = $v2;
			}
		}
	
		// redirect the user to the url
		$this->redirect(
				$this->_redirectPassedArgs($url, $admin),
				null, true);
	}
	
	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {
		if( !isset($this->params['named']['binary_file_id']) )
		{
			throw new NotFoundException(__('Invalid binary file'));
		}
		
		$binary_file_id = $this->params['named']['binary_file_id'];
	
		if( !$this->BinaryText->BinaryFile->exists($binary_file_id) )
		{
			throw new NotFoundException(__('Invalid binary file'));
		}
	
		// Get Binary File name
		$binaryFile = $this->BinaryText->BinaryFile->find('first', array(
				'recursive'  => -1,
				'fields'     => array('id', 'filename', 'user_id'),
				'conditions' => array($this->BinaryText->BinaryFile->primaryKey => $binary_file_id))
		);
	
		if( $binaryFile['BinaryFile']['user_id'] != $this->Auth->user('id') )
		{
			throw new NotFoundException(__('Invalid binary file'));
		}
		
		// Get characters from this BinaryText
		$characters = $this->_getCharacters($binary_file_id);
	
		/* Paginate by binary_file_id and add Search conditions */
		$this->paginate = array(
				'fields' => array('id', 'order', 'character_id', 'validated', 'text', 'new_text', 'fixed_text_id', 'modified'),
				'contain' => array('Character.new_name', 'Review.id'),
				'conditions' => array_merge(
						array('BinaryText.binary_file_id' => $binary_file_id),
						$this->_searchConditions($this->BinaryText)),
				'order' => 'order',
				'limit' => '50',
		);
		
		$binaryTexts = $this->paginate();
			
		$this->set(compact('binaryTexts', 'binaryFile', 'characters'));		
	}
		
	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param int $id
	 */
	public function edit($id = null) {
					
		if ( !$this->BinaryText->exists($id) )
		{
			throw new NotFoundException(__('Invalid binary text'));
		}
	
		// Get data from binary text
		$binary_text = $this->_getBinaryText($id);			
		
		if( !empty($binary_text['BinaryText']['fixed_text_id']) 
				||
			($binary_text['BinaryFile']['user_id'] != $this->Auth->user('id'))
		){
			throw new NotFoundException(__('Invalid binary text'));
		}		
	
		if ($this->request->is('post') || $this->request->is('put'))
		{			
			// Save user_id that edit this binaryText
			$this->request->data['BinaryText']['user_id'] = $this->Auth->user('id');
	
			// An array of fields you want to allow for saving.
			$params = array('fieldList' => array('user_id', 'character_id', 'validated', 'new_text'));
	
			if ($this->BinaryText->save($this->request->data, $params))
			{
				$this->Session->setFlash(__('The binary text has been saved'));
				$this->redirect($this->_redirectPassedArgs());
			} else {
				$this->Session->setFlash(__('The binary text could not be saved. Please, try again.'));
			}
		} else {
	
			$options = array(
					'fields' => array('id', 'character_id', 'validated', 'new_text'),
					'conditions' => array('BinaryText.' . $this->BinaryText->primaryKey => $id));
			$this->request->data = $this->BinaryText->find('first', $options);
		}
	
		// Send vars $ismenu and $text to Element texts_edit_form
		$character_id = $binary_text['BinaryText']['character_id'];
		$text = $binary_text['BinaryText']['text'];
	
		// characters list
		$characters = $this->BinaryText->Character->find('list',
				array('order' => 'id'));
	
		$this->set(compact('text', 'character_id', 'binary_text', 'characters'));
	}
	
	/**
	 * Administration
	 */
	
	/**
	 * admin index method
	 *
	 * @return void
	 */
	public function admin_index()
	{		
		/* add search conditions */
		$this->paginate = array(
				'contain' => array('BinaryFile.filename', 'User.username', 'Character.new_name', 'OldCharacter.readonly'),
				'fields' => array('id', 'binary_file_id', 'user_id', 'order', 'character_id', 'validated', 'text', 'new_text', 'fixed_text_id', 'modified'),				
				'conditions' => $this->_searchConditions($this->BinaryText),
				'order' => 'BinaryFile.filename, BinaryText.order'
		);
	
		// Data for select
		$characters = $this->BinaryText->Character->find('list', array('fields' => array('id', 'new_name'), 'order' => 'id'));		
		$binaryFiles = $this->BinaryText->BinaryFile->find('list', array('fields' => array('id', 'filename'), 'order' => 'filename'));	
		$users = $this->BinaryText->User->find('list', array('fields' => array('id', 'username'), 'order' => 'username'));
			
		$binaryTexts = $this->paginate();	
			
		$this->set(compact('binaryTexts', 'characters', 'binaryFiles', 'users'));
	}
	
	/**
	 * admin change character method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_changeCharacter($id = null) {
			
		if ( !$this->BinaryText->exists($id) )
		{
			throw new NotFoundException(__('Invalid binary text'));
		}
	
		// Get data from binary text
		$binary_text = $this->_getBinaryText($id);	
	
		if ($this->request->is('post') || $this->request->is('put'))
		{
			// Save user_id that edit this binaryText
			$this->request->data['BinaryText']['user_id'] = $this->Auth->user('id');
	
			// An array of fields you want to allow for saving.
			$params = array('fieldList' => array('user_id', 'character_id'));
	
			if ($this->BinaryText->save($this->request->data, $params))
			{
				$this->Session->setFlash(__('The character from binary text has been changed'));
				$this->redirect($this->_redirectPassedArgs());
			} else {
				$this->Session->setFlash(__('The character from binary text could not be changed. Please, try again.'));
			}
		} else {
	
			// Get data from binary text
			$options = array(
					'fields' => array('id', 'character_id'),
					'conditions' => array('BinaryText.' . $this->BinaryText->primaryKey => $id));
			$this->request->data = $this->BinaryText->find('first', $options);
		}
	
		$characters = $this->BinaryText->Character->find('list',
				array('order' => 'id'));
	
		$this->set(compact('characters', 'binary_text'));
	}

	/**
	 * admin edit method
	 *
	 * @throws NotFoundException
	 * @param int $id
	 */
	public function admin_edit($id = null) {
			
		if( !$this->BinaryText->exists($id)
		){
			throw new NotFoundException(__('Invalid binary text'));
		}		
	
		// Get data from binary text
		$binary_text = $this->_getBinaryText($id);
		
		if( !empty($binary_text['BinaryText']['fixed_text_id']) )
		{
			throw new NotFoundException(__('Invalid binary text'));
		}		
	
		if ($this->request->is('post') || $this->request->is('put'))
		{				
			// Save user_id that edit this binaryText
			$this->request->data['BinaryText']['user_id'] = $this->Auth->user('id');
	
			// An array of fields you want to allow for saving.
			$params = array('fieldList' => array('user_id', 'character_id', 'validated', 'new_text'));
	
			if ($this->BinaryText->save($this->request->data, $params))
			{
				$this->Session->setFlash(__('The binary text has been saved'));
				$this->redirect($this->_redirectPassedArgs());
			} else {
				$this->Session->setFlash(__('The binary text could not be saved. Please, try again.'));
			}
		} else {
	
			$options = array(
					'fields' => array('id', 'character_id', 'validated', 'new_text'),
					'conditions' => array('BinaryText.' . $this->BinaryText->primaryKey => $id));
			$this->request->data = $this->BinaryText->find('first', $options);
		}
	
		// Sends vars character id and text to Element texts_edit_form
		$character_id = $binary_text['BinaryText']['character_id'];
		$text = $binary_text['BinaryText']['text'];
	
		// characters list
		$characters = $this->BinaryText->Character->find('list',
				array('order' => 'id'));
	
		$this->set(compact('text', 'character_id', 'binary_text', 'characters'));
	}	

}