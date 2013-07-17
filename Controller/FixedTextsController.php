<?php
App::uses('AppController', 'Controller');
/**
 * FixedTexts Controller
 *
 * @property FixedText $FixedText
 */
class FixedTextsController extends AppController {

	public $paginate = array(
			'order' => 'modified DESC',
			'limit' => '20',
			'contain' => 'User.username',
			'fields' => array('id', 'user_id', 'binary_text_count', 'text' , 'new_text', 'validated', 'modified')
	);	
	
	/**
	 * Get and set new_text to binary texts from fixed text.
	 * @param int $id from fixed text
	 * @return array $array
	 */
	private function _setBinaryText($id)
	{
		$result = $this->FixedText->BinaryText->find('all',
				array('fields' => array('id', 'new_text', 'fixed_text_id'),
						'conditions' => array('fixed_text_id' => $id)));
	
		$array = array();
	
		foreach ($result as $s)
		{
			$s['BinaryText']['new_text'] = $this->request->data['FixedText']['new_text'];
			$s['BinaryText']['validated'] = $this->request->data['FixedText']['validated'];
			$array[] = $s['BinaryText'];
		}
	
		return $array;
	}	
	
	/**
	 * admin search method
	 */
	function admin_search()
	{
		$url = array('admin' => true, 'action' => 'index');
	
		// build a URL will all the search elements in it
		foreach ($this->data as $k => $v){
			foreach ($v as $k2 => $v2){
				$url[$k . "." .$k2] = $v2;
			}
		}
	
		// redirect the user to the url
		$this->redirect($this->_redirectPassedArgs($url), null, true);
	}	
	
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
			
		$this->paginate['conditions'] = $this->_searchConditions($this->FixedText);
		
		// Get users who edited fixed texts
		$array = $this->FixedText->find('all',
				array('fields' => 'user_id',
						'contain' => array('User.username'),
						'group' => 'user_id',
						'order' => 'user_id'));
				
		$users = array();
		if(!empty($array)){
			foreach($array as $v){
				$users[$v['FixedText']['user_id']] = $v['User']['username'];
			}
		}		
		
		$fixedTexts = $this->paginate();
		
		$this->set(compact('fixedTexts', 'users'));
	}
	
	/**
	 * Admin view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_view($id = null) {
		if (!$this->FixedText->exists($id)) {
			throw new NotFoundException(__('Invalid fixed text'));
		}
		
		$this->paginate['BinaryText'] = array(
				'fields' => array('BinaryFile.id', 'BinaryFile.filename', 
						'(SELECT COUNT(id) FROM binary_texts s WHERE s.binary_file_id=BinaryFile.id 
						AND s.`fixed_text_id`=\''.$id.'\' ) as total'),
				'conditions' => array('fixed_text_id' => $id),
				'order' => 'BinaryFile.filename',
				'group' => 'BinaryFile.id',
				'limit' => 50,
		);
		$binary_texts = $this->paginate('BinaryText');
		
		$options = array(
				'conditions' => array('FixedText.' . $this->FixedText->primaryKey => $id));
		$fixedText = $this->FixedText->find('first', $options);
		
		$total = $this->FixedText->BinaryText->find('count', 
				array('conditions' => array('fixed_text_id' => $id)));
		
		$this->set(compact('fixedText', 'binary_texts', 'total'));
	}		

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) 
	{	

		if (!$this->FixedText->exists($id)) 
		{
			throw new NotFoundException(__('Invalid fixed text'));
		}
				
		if ($this->request->is('post') || $this->request->is('put')) 
		{

			// Set id and user_id
			$this->request->data['FixedText']['id'] = $id;
			$this->request->data['FixedText']['user_id'] = $this->Auth->user('id');

			$options = array('fieldList' => array('user_id', 'new_text', 'validated'));

			// Set all binaryText and replace new_text and validated
			$this->request->data['BinaryText'] = $this->_setBinaryText($id);
			
			if ($this->FixedText->saveAssociated($this->request->data, $options))
			{
				$this->Session->setFlash(__('The fixed text has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The fixed text could not be saved. Please, try again.'));
			}
		} else {
			$options = array(
					'conditions' => array('FixedText.' . $this->FixedText->primaryKey => $id));
			$this->request->data = $this->FixedText->find('first', $options);
		}
		
		$fixed_text = $this->FixedText->find(
				'first', 
				array(
					'contain' => array('BinaryText.character_id' => array('Character.new_name')),
					'fields' => 'text',
					'conditions' => array('FixedText.' . $this->FixedText->primaryKey => $id))
				);
		
		// Get if fixed text is a menu text
		$character_id = $fixed_text['BinaryText'][0]['character_id'];
		
		// Original text from binary_text
		$text = $fixed_text['FixedText']['text'];
				
		$this->set(compact('text', 'character_id', 'fixed_text'));
	}
	
}
