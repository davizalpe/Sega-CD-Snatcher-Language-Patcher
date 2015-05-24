<?php
App::uses('AppController', 'Controller');
/**
 * BinaryFiles Controller
 *
 * @property BinaryFile $BinaryFile
 */
class BinaryFilesController extends AppController {

	public $components = array('Binary', 'Compress');
	
	public $paginate = array(
			'order' => 'BinaryFile.filename',
			'limit' => '50',
			'fields' => array('id', 'user_id', 
					'binary_texts_count', 'binary_texts_validated', 'reviews_count', 'reviews_validated', 
					'filename', 'description')
	);
		
	function beforeRender()
	{
		//debug($this->BinaryFile->validationErrors);die;
		
		foreach($this->BinaryFile->hasAndBelongsToMany as $k=>$v) {
			if(isset($this->BinaryFile->validationErrors[$k]))
			{
				$this->BinaryFile->{$k}->validationErrors[$k] = $this->BinaryFile->validationErrors[$k];
			}
		}
		 
	}	
	
	/**
	 * @see AppController::beforeFilter()
	 */	
	function beforeFilter()
	{
		parent::beforeFilter();
		
		// path to upload files
		$this->fileuploaddir = $this->files_path . $this->name . DS;
		$this->ori_files_path = $this->fileuploaddir . 'original' . DS;

		// path to create new binary files
		$this->new_files_path = $this->files_path . $this->name . DS . 'translated' . DS;
		$this->path = $this->files_path_relative . $this->name . DS . 'translated' . DS;					
		
		$this->_checkdir($this->fileuploaddir);
		$this->_checkdir($this->ori_files_path);
		$this->_checkdir($this->new_files_path);		
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
	
		$filename = $data['name'];
		$fileupload = $this->ori_files_path . $filename;
		
		if(file_exists($fileupload))
		{
			$this->Session->setFlash(__('The file %s is already uploaded.', $filename));
			$this->redirect($this->_redirectPassedArgs());
		}
			
		/* Copy original filename in path */
		$result = copy($data['tmp_name'], $fileupload);
		unset($fileupload);
	
		if( !$result )
		{
			$this->Session->setFlash(__('The file could not be saved.'));
			$this->redirect($this->_redirectPassedArgs());
		}
	
		return $filename;
	}	
	
/**
 * index method
 *
 * @return void
 */
	public function index() {
	
		$this->BinaryFile->contain(array('Testers.username'));
	
		$data = $this->paginate(null, array('BinaryFile.user_id' => $this->Auth->user('id')));
	
		$this->set('binaryFiles', $data);
	}	

/**
 * revision method
 *
 * @return void
 */
	public function test() {
			
		$this->BinaryFile->contain(array('Testers.username', 'Translator.username'));	
		
		$this->paginate['joins'] = array(array('table'=> 'binary_files_testers',
            'type' => 'LEFT',
            'alias' => 'BinaryFileTesters',
            'conditions' => 'BinaryFileTesters.binary_file_id = BinaryFile.id'));
		
		$data = $this->paginate(null, array('BinaryFileTesters.user_id' => $this->Auth->user('id')));
				
		$this->set('binaryFiles', $data);
	}	
	
	/**
	 * Create a binary file with new texts 
	 * @param int $id
	 * @throws NotFoundException
	 */
	public function download($id, $admin = false)
	{		
		if (!$this->BinaryFile->exists($id))
		{
			throw new NotFoundException(__('Invalid binary file'));
		}
		
		// Check if user can download this $id
		if( !$this->_canDownload($id, $this->BinaryFile) )
		{
			throw new NotFoundException(__('Invalid binary file'));
		}
		
		// Get al binary texts
		$data = $this->BinaryFile->find('first', array(
					'fields' => 'filename',
					'contain' => array(
							'BinaryText' => array('character_id', 'text_offset', 'new_text', 'nchars', 'Character.hex', 'OldCharacter.hex', 'BinaryFile.filename')
							),
					'conditions' => array($this->BinaryFile->alias . '.' . $this->BinaryFile->primaryKey => $id)
				));
				
		$this->_createBinaryFile(
				$data['BinaryFile']['filename'], 
				$this->name, 
				$data['BinaryText'], 
				$admin);
		
		$this->viewClass = 'Media';
		$params = array(
				'id'        => $data['BinaryFile']['filename'],
				'download'  => true,
				'path'      => $this->path
		);
		$this->set($params);
	}
	
	/**
	 * admin index method
	 *
	 * @return void
	 */
	public function admin_index() {
		
		$this->BinaryFile->contain(array('Testers.username', 'Translator.username'));

		$binaryFiles = $this->paginate();
				
		$this->set(compact('binaryFiles'));
	}
	
	/**
	 * Create new binary files with new texts from all binary_files
	 * @param int $id
	 * @throws NotFoundException
	 */
	public function admin_downloadall(){
					
		$binaryFiles = $this->BinaryFile->find('all', array(
				'fields' => 'filename', 
				'contain' => array(
						'BinaryText' => array('character_id', 'text_offset', 'new_text', 'nchars', 'Character.hex', 'OldCharacter.hex', 'BinaryFile.filename')),				
			));			
		
		foreach ($binaryFiles as $data)
		{
			$array_files[] = $this->_createBinaryFile(
					$data['BinaryFile']['filename'], 
					$this->name, 
					$data['BinaryText'], 
					true);				
		}		

		// zip all files
		$filezip = 'allfiles.zip';
		if( !$this->Compress->createZip(
				$array_files, 
				$this->new_files_path . $filezip, 
				true) )
		{
			$this->Session->setFlash(__('The zip file could not be created.'));
			$this->redirect($this->_redirectPassedArgs(array('admin' => true)));
		}

		$this->viewClass = 'Media';
		$params = array(
				'id'        => $filezip,
				'download'  => true,
				'path'      => $this->path
		);
		
		$this->set($params);
	}
	
/**
 * admin edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
public function admin_edit($id = null) {
	
	if (!$this->BinaryFile->exists($id))
	{
		throw new NotFoundException(__('Invalid binary file'));
	}
	
	if ($this->request->is('post') || $this->request->is('put'))
	{
		$options = array('validate' => 'only', 'fieldList' => array('description', 'user_id', 'Testers'));
		
		if ($this->BinaryFile->save($this->request->data, $options)) 
		{
			$this->Session->setFlash(__('The binary file has been saved'));
			$this->redirect($this->_redirectPassedArgs());
			
		} else {
			$this->Session->setFlash(__('The binary file could not be saved. Please, try again.'));
		}
		
	} else {					
		$options = array('fields' => array('id', 'filename', 'description', 'user_id'), 'contain' => 'Testers', 'conditions' => array('BinaryFile.' . $this->BinaryFile->primaryKey => $id));
		$this->request->data = $this->BinaryFile->find('first', $options);
	}
	
	$users = $testers = $this->BinaryFile->Testers->find('list', 
			array('conditions' => array('Testers.active' => 1),
					'order' => 'username asc'));
	
	$this->set(compact('users', 'testers'));		
}

/**
 * Add binary file
 *
 * @return void
 */
	public function admin_add() {

		if ($this->request->is('post')) {		
			
			// save file
			$this->_fileUpload($this->request->data['BinaryFile']['filename']);

			$this->request->data['BinaryFile']['user_id'] = $this->Auth->user('id');
			$this->request->data['BinaryFile']['filename'] = $this->request->data['BinaryFile']['filename']['name'];
	
			// get sentences
			$this->request->data['BinaryText'] = $this->_getBinaryTexts($this->request->data['BinaryFile']['filename']);		
	
			// options for save data
			$options = array('validate' => false, 'fieldList' =>
						array(
								'BinaryFile' => array('user_id', 'binary_texts_validated', 'binary_texts_count', 'filename', 'description', 'created', 'modified'),
								'BinaryText' => array('binary file_id', 'user_id', 'order', 'nchars', 'nlines', 'binary', 'text', 'new_text', 'offset', 'offset_prev')
						)
					);
			
			// Enable autotranslate using GoogleTranslate behavior
			if($this->request->data['BinaryFile']['autotranslate'])
			{
				$this->BinaryFile->BinaryText->Behaviors->load('GoogleTranslate');
			}
			
			$this->BinaryFile->create();
			if ($this->BinaryFile->saveAssociated($this->request->data, $options)) {
				$this->Session->setFlash(__('The binary file has been saved'));
				$this->redirect($this->_redirectPassedArgs());
			} else {
				$this->Session->setFlash(__('The binary file could not be saved. Please, try again.'));
				unlink(ROOT. DS . APP_DIR . DS . 'files' . DS . $this->request->data['BinaryFile']['filename']);
			}
			
		}
	}
	
	/**
	 * Agrega datos de un nuevo fichero,
	 * lo almacena y guarda las frases relaciandas en
	 * modelo BinaryText con relación hasMany
	 *
	 * @return void
	 */
	public function admin_addall() {
	
		if ($this->request->is('post')) {
			// Guardar varios files
			foreach ($this->request->data['BinaryFile']['filenames'] as $filename){
				// Subir fichero
				$this->_fileUpload($filename);
				// Crear registro binary fileo
				$array = array('BinaryFile' => array('user_id' => $this->Auth->user('id'), 'filename' => $filename['name']));

				// Obtener frases
				$sentences = $this->_getBinaryTexts($filename['name']);
				if($sentences != NULL){
					$array = array_merge($array, array('BinaryText' => $sentences));
				}
				$new_data[] = $array;
			}

			$options = array(
					'validate' => false ,
					'deep' => true,
					'fieldList' =>
						array(
								'BinaryFile' => array('user_id', 'filename', 'description'),
								'BinaryText' => array('binary file_id', 'user_id', 'order', 'nchars', 'nlines', 'binary', 'text', 'new_text', 'offset', 'offset_prev')
						)
					);

			// Habilitar GoogleTranslate
			if($this->request->data['BinaryFile']['autotranslate']){
				$this->BinaryFile->BinaryText->Behaviors->load('GoogleTranslate');
			}

// 			var_dump($new_data);
// 			var_dump($new_data[0]['BinaryText']);
//			die;
			//$this->BinaryFile->create();
			if ($this->BinaryFile->saveMany($new_data, $options)) {
				$this->Session->setFlash(__('The binary files has been saved'));
				$this->redirect($this->_redirectPassedArgs());
			} else {
				$this->Session->setFlash(__('The binary files could not be saved. Please, try again.'));
			}
		}

	}	
	
 }