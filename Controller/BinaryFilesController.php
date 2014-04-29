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
		
		// path to create new binary files		
		$this->new_files_path = $this->files_path . $this->name . DS . 'translated' . DS;
		
		$this->path = $this->files_path_relative . $this->name . DS . 'translated' . DS;
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
							'BinaryText' => array('character_id', 'text_offset', 'new_text', 'nchars', 'Character.hex', 'OldCharacter.hex')
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
						'BinaryText' => array('character_id', 'text_offset', 'new_text', 'nchars', 'Character.hex', 'OldCharacter.hex')),				
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
 * Agrega datos de un nuevo fichero,
 * lo almacena y guarda las frases relaciandas en
 * modelo Sentence con relación hasMany
 *
 * @return void
 */
	public function admin_add() {
		$project_id = 1; //$this->params['named']['project_id'];

		if (empty($project_id)) {
			throw new NotFoundException(__('Invalid document'));
		}

		if ($this->request->is('post')) {
			// save file
			$this->_fileUpload($this->request->data['Document']['filename']);

			$this->request->data['Document']['project_id'] = $project_id;
			$this->request->data['Document']['user_id'] = $this->Auth->user('id');
			$this->request->data['Document']['filename'] = $this->request->data['Document']['filename']['name'];
	
			// get sentences
			$this->request->data['Sentence'] = $this->_getSentences($this->request->data['Document']['filename']);
	
			// Definir que campos guardar
			$options = array('validate' => false, 'fieldList' =>
						array(
								'Document' => array('project_id', 'user_id', 'filename', 'description'),
								'Sentence' => array('document_id', 'user_id', 'order', 'nchars', 'nlines', 'binary', 'text', 'new_text', 'offset', 'offset_prev')
						)
					);
			// Habilitar GoogleTranslate para autotraducción, MyMemory tiene API pero es más lento.
			if($this->request->data['Document']['autotranslate'])
				{
					$this->Document->Sentence->Behaviors->load('GoogleTranslate');
				}
			
				//$this->Document->create();
				if ($this->Document->saveAssociated($this->request->data, $options)) {
					$this->Session->setFlash(__('The document has been saved'));
					$this->redirect($this->_redirectPassedArgs());
				} else {
					$this->Session->setFlash(__('The document could not be saved. Please, try again.'));
					unlink(ROOT. DS . APP_DIR . DS . 'files' . DS . $this->request->data['Document']['filename']);
				}
			}
			$project = $this->Document->Project->find('first', array('conditions' => array('Project.id'=>$project_id), 'fields' => array('id','name'), 'recursive' => -1));
			$this->set(compact('project'));
		}

	/**
	 * Agrega datos de un nuevo fichero,
	 * lo almacena y guarda las frases relaciandas en
	 * modelo Sentence con relación hasMany
	 *
	 * @return void
	 */
	public function admin_addall() {
		$project_id = 1; //$this->params['named']['project_id'];

		if (empty($project_id)) {
			throw new NotFoundException(__('Invalid document'));
		}

		if ($this->request->is('post')) {
			// Guardar varios files
			foreach ($this->request->data['Document']['filenames'] as $filename){
				// Subir fichero
				$this->_fileUpload($filename);
				// Crear registro documento
				$array = array('Document' => array('project_id' => $project_id, 'user_id' => $this->Auth->user('id'), 'filename' => $filename['name']));

				// Obtener frases
				$sentences = $this->_getSentences($filename['name']);
				if($sentences != NULL){
					$array = array_merge($array, array('Sentence' => $sentences));
				}
				$new_data[] = $array;
			}

			$options = array(
					'validate' => false ,
					'deep' => true,
					'fieldList' =>
						array(
								'Document' => array('project_id', 'user_id', 'filename', 'description'),
								'Sentence' => array('document_id', 'user_id', 'order', 'nchars', 'nlines', 'binary', 'text', 'new_text', 'offset', 'offset_prev')
						)
					);

			// Habilitar GoogleTranslate
			if($this->request->data['Document']['autotranslate']){
				$this->Document->Sentence->Behaviors->load('GoogleTranslate');
			}

// 			var_dump($new_data);
// 			var_dump($new_data[0]['Sentence']);
//			die;
			//$this->Document->create();
			if ($this->Document->saveMany($new_data, $options)) {
				$this->Session->setFlash(__('The documents has been saved'));
				$this->redirect($this->_redirectPassedArgs());
			} else {
				$this->Session->setFlash(__('The documents could not be saved. Please, try again.'));
			}
		}
		$project = $this->Document->Project->find('first', array('conditions' => array('Project.id'=>$project_id), 'fields' => array('id','name'), 'recursive' => -1));
		$this->set(compact('project'));
	}	
	
 }