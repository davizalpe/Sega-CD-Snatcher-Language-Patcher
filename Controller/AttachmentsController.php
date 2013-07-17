<?php
App::uses('AppController', 'Controller');
/**
 * Attachments Controller
 *
 * @property Attachment $Attachment
 */
class AttachmentsController extends AppController {

	public $paginate = array(
			'fields' => array('id', 'user_id', 'filename', 'description', 'created'),
			'contain' => 'User.username',
			'order' => 'created DESC',
			'limit' => '20',
	);

	/**
	 * @see AppController::beforeFilter()
	 */
	function beforeFilter() {
		parent::beforeFilter();
		
		$this->fileuploaddir = $this->files_path . $this->name . DS;
		
		$this->path = $this->files_path_relative . $this->name . DS;				
	
		$this->_checkdir($this->fileuploaddir);
	}	
	
	/**
	 * private upload method
	 * @param array $data data from attachment
	 * @throws NotFoundException
	 */
	private function _fileUpload($data = null)
	{
		if($data['error'])
		{
			throw new NotFoundException(__('Invalid file'));
		}
	
		if(file_exists($this->fileuploaddir . $data['name']))
		{
			$this->Session->setFlash(__("The file %s already exists.", $data['name']));
			$this->redirect($this->_redirectPassedArgs());
		}
	
		$result = copy($data['tmp_name'], $this->fileuploaddir . $data['name']);
	
	
		return $result;
	}
	
	/**
	 * private method to delete file
	 * @param array $data
	 */
	private function _deleteFile($data)
	{		
		$filename = $this->fileuploaddir . $data['Attachment']['filename'];
	
		if(!file_exists($filename)) {
			unset($filename);
			return;
		}	
	
		if( !unlink($filename) ){
			$this->Session->setFlash(__('The attachment could not be delete.'));
			$this->redirect(array('action' => 'index'));
		}
				
		unset($filename);
	}	

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Attachment->recursive = 0;
		$this->set('attachments', $this->paginate());
	}
	
	/**
	 * download method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @param boolean admin
	 * @return void
	 */
	public function download($id = null, $admin = false) {
		if (!$this->Attachment->exists($id))
		{
			throw new NotFoundException(__('Invalid attachment'));
		}

		// Get filename
		$options = array(
				'fields' => 'filename',
				'conditions' => array('Attachment.' . $this->Attachment->primaryKey => $id));
		$data = $this->Attachment->find('first', $options);

		$filename = $data['Attachment']['filename'];

		// Check if exists
		if( !file_exists($this->fileuploaddir . DS . $filename) )
		{
			$this->Session->setFlash(__('The file %s not exists.', $filename));
			$this->redirect($this->_redirectPassedArgs(array(), $admin));
		}
	
		$this->viewClass = 'Media';
		$params = array(
				'id'        => $filename,
				'download'  => true,
				'path'      => $this->path
		);
		$this->set($params);
	}

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->Attachment->recursive = 0;
		$this->set('attachments', $this->paginate());
	}
	
	/**
	 * admin add method
	 *
	 * @return void
	 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->Attachment->create();
				
			$this->request->data['Attachment']['user_id'] = $this->Auth->user('id');
				
			if( !$this->_fileUpload($this->request->data['Attachment']['filename']) ){
				$this->Session->setFlash(__('The file could not be copy.'));
				$this->redirect(array('action' => 'index'));
			}
			$this->request->data['Attachment']['filename'] = $this->request->data['Attachment']['filename']['name'];
				
			$options = array('fielList' => array('user_id', 'filename', 'description'));
				
			if ($this->Attachment->save($this->request->data, $options)) {
				$this->Session->setFlash(__('The attachment has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The attachment could not be saved. Please, try again.'));
			}
		}
	}	

/**
 * admin edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if (!$this->Attachment->exists($id)) {
			throw new NotFoundException(__('Invalid attachment'));
		}

		$this->request->data['Attachment']['user_id'] = $this->Auth->user('id');
		
		$options = array('fieldList' => array('description', 'user_id'));
		
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Attachment->save($this->request->data, $options)) {
				$this->Session->setFlash(__('The attachment has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The attachment could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Attachment.' . $this->Attachment->primaryKey => $id));
			$this->request->data = $this->Attachment->find('first', $options);
		}		
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		$this->Attachment->id = $id;
		if (!$this->Attachment->exists()) {
			throw new NotFoundException(__('Invalid attachment'));
		}
		$this->request->onlyAllow('post', 'delete');
		
		$options = array(
				'fields' => 'filename',
				'conditions' => array('Attachment.' . $this->Attachment->primaryKey => $id));
		$data = $this->Attachment->find('first', $options);
		
		if ($this->Attachment->delete()) 
		{
			
			// Delete file
			$this->_deleteFile($data);
			
			$this->Session->setFlash(__('Attachment deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Attachment was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}