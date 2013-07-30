<?php
App::uses('AppController', 'Controller');
/**
 * Saves Controller
 *
 * @property Safe $Safe
 */
class SavesController extends AppController {

	public $components = array('Binary', 'Quicksave');
	
	public $paginate = array(
				'contain' => array('User.username', 'BinaryFile.filename'),
				'order' => 'Safe.created desc',
			);
	
	/**
	 * @see AppController::beforeFilter()
	 */
	function beforeFilter() {
		parent::beforeFilter();
	
		$this->fileuploaddir = $this->files_path . $this->name . DS;
		$this->ori_files_path = $this->fileuploaddir . 'original' . DS;
		$this->new_files_path = $this->fileuploaddir . 'translated' . DS;
	
		$this->_checkdir($this->fileuploaddir);
		$this->_checkdir($this->ori_files_path);
		$this->_checkdir($this->new_files_path);
		
		$this->path = $this->files_path_relative . $this->name . DS;
		
		$this->filename = 'SNATCHER.gs';
		$this->sizelimit = Configure::read('Snatcher.BinaryFiles.sizelimit');
	}	
	
	/**
	 * Generate an unique name to add saves files
	 * @return string $filename uuid filename
	 */
	private function _getNameFile(){
		$filename = String::uuid();
	
		settype($filename, 'string');
	
		return $filename;
	}

	/**
	 * Check if it is a quicksave from KegaFusion
	 * comparing first 16 bits from file
	 * @param array $data
	 * @return boolean
	 */
	private function _validate($data = null){
		$file = file_get_contents($data['tmp_name']);
	
		if(bin2hex(substr($file, 0, 16)) == "475354000000e0400000000000000000")
		{
			return true;
		}
	
		return false;
	}

	/**
	 * Uploads a new file
	 * @param array $data
	 * @throws NotFoundException
	 */
	private function _fileUpload($data = null)
	{
		if($data['error'])
		{
			throw new NotFoundException(__('Invalid file'));
		}
	
		if( !$this->_validate($data) )
		{
			$this->Session->setFlash(__('The file %s is not a quicksave', $data['name']));
			$this->redirect($this->_redirectPassedArgs());
		}
	
		$filename = $this->_getNameFile();
	
		/* Copy original filename in*/
		$result = copy($data['tmp_name'], $this->ori_files_path . $filename);
	
		if( !$result )
		{
			$this->Session->setFlash(__('The file could not be saved.'));
			$this->redirect($this->_redirectPassedArgs());
		}
	
		return $filename;
	}	
	
	/**
	 * Create binary file to make saves
	 * @param int $id binary file id
	 * @param boolean $admin
	 * @return string path from new safe file
	 */
	private function _callCreateBinaryFile($id, $admin = false)
	{					
		// Get al binary texts
		$data = $this->Safe->BinaryFile->find('first', array(
					'fields' => 'filename',
					'contain' => array(
							'BinaryText' => array('character_id', 'text_offset', 'text', 'new_text', 'nchars', 'Character.hex', 'OldCharacter.hex', 'BinaryFile.filename')
							),
					'conditions' => array($this->Safe->BinaryFile->alias . '.' . $this->Safe->BinaryFile->primaryKey => $id)
				));		
				
		return $this->_createBinaryFile($data['BinaryFile']['filename'],
					Inflector::pluralize($this->Safe->BinaryFile->name),
					$data['BinaryText'],
					$admin
				);	
	}

	/**
	 * Create safe file
	 * @param string $filename
	 * @param string $binary_file_path
	 * @param boolean $admin
	 */
	private function _createSafeFile($filename, $binary_file_path, $admin)
	{
		if( !file_exists($this->ori_files_path . $filename) )
		{
			$this->Session->setFlash(__('The file not exists.'));
			$this->redirect($this->_redirectPassedArgs(array('action' => 'index'), $admin));			
		}
		
		// Create new save
		$this->Quicksave->writeFile(
				$this->ori_files_path . $filename,
				$this->new_files_path . $filename,
				$binary_file_path,
				$this->sizelimit
		);		
	}	
	
	/**
	 * Obtain all binary_files for select input
	 * @return array $binary_files binary files list
	 */
	private function _getBinaryFiles(){
	
		$binary_files = array();
		$data = $this->Safe->BinaryFile->find('all', array(
				'recursive' => -1,
				'fields' => array('id' , 'filename', 'description'),
				'order' => 'filename'));
	
		foreach($data as $v)
		{
			$binary_files[$v['BinaryFile']['id']] = 
					$v['BinaryFile']['filename'] . ": "
					 . $v['BinaryFile']['description'];
		}
	
		return $binary_files;
	}
	
	/**
	 * deletes saaves file
	 * @param array $filename
	 */
	private function _deleteFile($filename)
	{			
		if( !file_exists($this->ori_files_path . $filename) ){
			return;
		}
	
		// delete original save file
		if( !unlink($this->ori_files_path . $filename) )
		{
			$this->Session->setFlash(__('The safe could not be delete.'));
			$this->redirect(array('action' => 'index'));
		}
		
		// delete translated save file
		if( 
			file_exists($this->new_files_path . $filename) &&		
			!unlink($this->new_files_path . $filename)
		){
				$this->Session->setFlash(__('The new safe could not be delete.'));
				$this->redirect(array('action' => 'index'));		
		}
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
		$user_id = $this->Auth->user('id');
		
		$this->paginate = array(
				'contain' => array('BinaryFile' => array('user_id', 'filename'), 'User.username'),
				'conditions' => array('AND' => 
						$this->_searchConditions($this->Safe),
						array('OR' => array(
								'BinaryFile.user_id' => $user_id,
								'BinaryFileTesters.user_id' => $user_id
								))												
						),
				'joins' => array(array(
							'table'=> 'binary_files_testers',
				            'type' => 'LEFT',
				            'alias' => 'BinaryFileTesters',
				            'conditions' => 'BinaryFileTesters.binary_file_id = Safe.binary_file_id')),
				'order' => 'Safe.modified DESC'
				);
		
		$saves = $this->paginate();
			
		$binaryFiles = $this->Safe->BinaryFile->find('list', array('fields' => array('id', 'filename'), 'order' => 'filename'));
		$users = $this->Safe->User->find('list', array('fields' => array('id', 'username'), 'order' => 'username'));		
		
		$this->set(compact('saves', 'binaryFiles', 'users'));
	}

/**
 * add method
 *
 * @return void
 */
	public function add($admin = false) {
		if ($this->request->is('post'))
		{
			// set user id
			$this->request->data['Safe']['user_id'] = $this->Auth->user('id');

			// set filename
			$this->request->data['Safe']['filename'] = $this->_fileUpload($this->request->data['Safe']['filename']);			
			
			$options = array('fieldList' => array('user_id', 'binary_file_id', 'act', 'slot', 'description', 'filename'));
			
			$this->Safe->create();
			if ($this->Safe->save($this->request->data, $options)) {
				$this->Session->setFlash(__('The safe has been saved'));
				$this->redirect($this->_redirectPassedArgs(array('index'), $admin));
			} else {
				debug($this->request->data);
				
				$this->Session->setFlash(__('The safe could not be saved. Please, try again.'));
			}
		}

		$this->set('binaryFiles', $this->_getBinaryFiles());
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) 
	{
		if (!$this->Safe->exists($id)) {
			throw new NotFoundException(__('Invalid safe'));
		}
		
		$data = $this->Safe->find('first',
				array('fields' => array('user_id'),
						'conditions' => array('Safe.' . $this->Safe->primaryKey => $id)));
		
		if( $data['Safe']['user_id'] != $this->Auth->user('id'))
		{
			throw new NotFoundException(__('Invalid safe'));
		}
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			$options = array('fieldList' => array('act', 'description', 'binary_file_id', 'slot'));
			
			if ($this->Safe->save($this->request->data, $options)) 
			{
				$this->Session->setFlash(__('The safe has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The safe could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Safe.' . $this->Safe->primaryKey => $id));
			$this->request->data = $this->Safe->find('first', $options);
		}

		$this->set('binaryFiles', $this->_getBinaryFiles());
	}	
	
	/**
	 * download method
	 *
	 * @throws NotFoundException
	 * @param int $id
	 * @param boolean $admin
	 * @return void
	 */
	public function download($id = null, $admin = false, $original = false)
	{
		$this->Safe->id = $id;
		if (!$this->Safe->exists($id)) {
			throw new NotFoundException(__('Invalid safe'));
		}
		
		// Check if user can download this $id
		if( !$this->_canDownload((int)$this->Safe->field('binary_file_id'), $this->Safe->BinaryFile) )
		{
			throw new NotFoundException(__('Invalid safe'));
		}		
	
		$data = $this->Safe->find('first', 
				array('fields' => array('filename', 'slot', 'binary_file_id'), 
						'conditions' => array('Safe.' . $this->Safe->primaryKey => $id)));		
	
		if( !$original )
		{
			// Create binary file
			$binary_file_path = $this->_callCreateBinaryFile($data['Safe']['binary_file_id'], $admin);
		
			// Create save file
			$this->_createSafeFile($data['Safe']['filename'], $binary_file_path, $admin);
			
			$dir = 'translated' . DS;
		}else{
			$dir = 'original' . DS;
		}
	
		$this->viewClass = 'Media';
		$params = array(
				'id'        => $data['Safe']['filename'],
				'name'      => $this->filename . $data['Safe']['slot'],
				'download'  => true,
				'path'      => $this->path . $dir
		);
		
		$this->set($params);
	}	

/**
 * delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Safe->id = $id;
		if (!$this->Safe->exists()) {
			throw new NotFoundException(__('Invalid safe'));
		}
		
		$data = $this->Safe->find('first',
				array('fields' => array('user_id', 'filename'),
						'conditions' => array('Safe.' . $this->Safe->primaryKey => $id)));		
		
		if( $data['Safe']['user_id'] != $this->Auth->user('id'))
		{
			throw new NotFoundException(__('Invalid safe'));
		}
		
		$this->request->onlyAllow('post', 'delete');

		// Delete save file
		$this->_deleteFile($data['Safe']['filename']);
		
		if ($this->Safe->delete()) {
			$this->Session->setFlash(__('Safe deleted'));
			$this->redirect($this->_redirectPassedArgs());
		}
		$this->Session->setFlash(__('Safe was not deleted'));
		$this->redirect($this->_redirectPassedArgs());
	}

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->Safe->recursive = 0;
		
		$this->paginate = array(
				'conditions' => $this->_searchConditions($this->Safe),
				'order' => 'Safe.modified DESC'
		);
		
		$saves = $this->paginate();
		
		$binaryFiles = $this->Safe->BinaryFile->find('list', array('fields' => array('id', 'filename'), 'order' => 'filename'));
		$users = $this->Safe->User->find('list', array('fields' => array('id', 'username'), 'order' => 'username'));		
		
		$this->set(compact('saves', 'binaryFiles', 'users'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if (!$this->Safe->exists($id)) {
			throw new NotFoundException(__('Invalid safe'));
		}
		if ($this->request->is('post') || $this->request->is('put'))
		{			
			$options = array('fieldList' => array('act', 'description', 'binary_file_id', 'slot'));
			
			if ($this->Safe->save($this->request->data, $options)) {
				$this->Session->setFlash(__('The safe has been saved'));
				$this->redirect($this->_redirectPassedArgs());
			} else {
				$this->Session->setFlash(__('The safe could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Safe.' . $this->Safe->primaryKey => $id));
			$this->request->data = $this->Safe->find('first', $options);
		}
		$this->set('binaryFiles', $this->_getBinaryFiles());
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
		$this->Safe->id = $id;
		if (!$this->Safe->exists()) {
			throw new NotFoundException(__('Invalid safe'));
		}
		
		$this->request->onlyAllow('post', 'delete');
		
		$data = $this->Safe->find('first',
				array('fields' => array('filename'),
						'conditions' => array('Safe.' . $this->Safe->primaryKey => $id)));		
		
		// Delete file
		$this->_deleteFile($data['Safe']['filename']);		
		
		if ($this->Safe->delete()) {
			$this->Session->setFlash(__('Safe deleted'));
			$this->redirect($this->_redirectPassedArgs());
		}
		$this->Session->setFlash(__('Safe was not deleted'));
		$this->redirect($this->_redirectPassedArgs());
	}
	
}