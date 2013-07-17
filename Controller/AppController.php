<?php

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	
   public $components = array(
   		'Session', // Important to be first for users/login
   		'Acl',
        'Auth' => array(
        	'authorize' => array(
        		'Actions' => array('actionPath' => 'controllers') // Uses ACL
       		),      		
        	'loginAction' => array('admin' => false, 'plugin' => false, 'controller' => 'users', 'action' => 'login'),
            'loginRedirect' => array('admin' => false, 'plugin' => false, 'controller' => 'binary_files', 'action' => 'index'),
            'logoutRedirect' => array('admin' => false, 'plugin' => false, 'controller' => 'pages', 'action' => 'display', 'home'),
        	/* Bcrypt auth since 2.3 recomended */
			//'authenticate'  => array('Blowfish' => array('scope' => array('User.active' => true))),
        	/* tradicional Auth login form */
        	'authenticate'  => array('all' => array('scope' => array('User.active' => true)), 'Form'),        
    ));
	   
	public $helpers = array('Html', 'Js' => array('Jquery'));
	
	/**
	 * @var boolean
	 */
	public $isAdmin = false;
	
	/**
	 * @var boolean
	 */
	public $isManager = false;
	
	/**
	 * default path from Files
	 * @var string
	 */
	public $files_path = '';
	
	
	/**
	 * Relative default path from Files
	 * @var string
	 */
	public $files_path_relative = '';
	
	/**
	 * Configure main vars in before filter callback
	 */
	function beforeFilter() {
		parent::beforeFilter();		
		
		// Only allow home access
		$this->Auth->allowedActions = array('display');		
		
		// isAdmin and isManager for Controllers and Views
		$this->isAdmin = $this->Auth->user('role_id') == 1;
		$this->isManager = $this->Auth->user('role_id') == 2;				
		
		// Files dir and permissions
		$this->files_path = ROOT. DS . APP_DIR . DS . 'Files' . DS;
		$this->files_path_relative = 'Files' . DS;
	}

	/**
	 * Create array conditions according $type
	 * @param string $alias
	 * @param string $column
	 * @param string $value
	 * @param string $type
	 * @return array $array search conditions
	 */
	private function _addCondition($alias, $column, $value, $type = 'integer')
	{
		$array = array();
	
		if(	$value == '' )
		{
			return $array;
		}
				
		$column = $alias . "." . $column;
	
		switch ($type) {
			case "integer":case "boolean":
				$array = array($column => $value);
				break;
			case "text":case "string":
				$array = array($column . " LIKE" => "%".$value."%");
				break;
			case "datetime":
				$array = array($column . " LIKE" => date('Y-m-d', strtotime($value))."%");
				break;
		}
			
		return $array;
	}	

	/**
	 *
	 * Search parameters values ​​given by an url and adds them to the redirect
	 * @param array $url url to redirect
	 * @param boolean $admin redirect to admin if true
	 *
	 */
	protected function _redirectPassedArgs($url = array(), $admin = false){
		
		if( !isset($url['action']) )
		{
			$url['action'] = 'index';
		}
		
		if($admin)
		{
			$url['admin'] = true;
		}
	
		if(isset($this->params['named'])){
			foreach($this->params['named'] as $key => $val)
				if($key != 'redirect')
				{
					$url = array_merge(array($key => $val), $url);
				}
		}
	
		return $url;
	}
	
	/**
	 * Checks if $path is created. If not exists it is created with $permissions.
	 * @param string $path
	 * @param boolean $check_writable if true check if dir is writable. Default true.
	 * @param string $permissions permissions if create path. Default '700'
	 */
	protected function _checkdir($path, $check_writable = true, $permissions = 0700){
		
		if(	$path == null){
			die("path is null");
		}
		
		if( !is_dir($path) )
		{
			mkdir($path, $permissions, true);			
		}
	
		if($check_writable){
			if (!is_writeable($path))
			{
				$this->Session->setFlash(__("The directory needs write permisions."));
				//$this->redirect(array('controller' => 'pages', 'action' => 'display', 'home'));
			}
		}
	}
	
	/**
	 * Add search conditions in a array from passedArgs
	 * @param Model $model
	 * @param string $formAlias Name from form alias input
	 * @return array $array conditions
	 */
	protected function _searchConditions(Model $model, $formAlias = 'Search' )
	{
		$array = array();	
		
		$len = strlen($formAlias)+1;
	
		foreach($this->passedArgs as $k => $v)
		{		
			// get column name
			$column = substr($k, $len);	
			
			if(
					($v != '') && 							// Not empty but accepts zero					
					!strncmp($k, $formAlias.".", $len) &&	// Started with $formAlias."."
					($type = $model->getColumnType($column))// Exists attibute in model
			){
				
				// set data in input form
				if( ($pos=strpos($column, ".")) !==FALSE )
				{
					$alias = substr($column, 0, $pos);
					$column = substr($column, $pos+1);
					
					$this->request->data[$formAlias][$alias][$column] = $v;
				}else{
					$alias = $model->alias;
					 
					$this->request->data[$formAlias][$column] = $v;
				}				
				
				// add condition
				$array = array_merge($array,
						$this->_addCondition($alias, $column, $v, $type)
				);						
			}
		}
		
		return $array;
	}

	/**
	 * Creates binary files with name $filename in $new_files_path path.
	 * It admin true, redirects to admin route
	 * @param string $filename
	 * @param string $pathname path name from Controller
	 * @param array $data BinaryTexts from BinaryFile
	 * @param boolean $admin for redirect to admin index
	 */
	protected function _createBinaryFile($filename,
			$pathname,
			$data,
			$admin = false)
	{
		// Set paths from binary files
		$fileuploaddir = $this->files_path . $pathname . DS;
		$ori_files_path = $fileuploaddir . 'original' . DS;
		$new_files_path = $fileuploaddir . 'translated' . DS;
		
		$this->_checkdir($fileuploaddir);
		$this->_checkdir($ori_files_path);
		$this->_checkdir($new_files_path);		
		
		// Checks filename
		if(		!file_exists($ori_files_path . $filename) ||
				empty($data)
		){
			$this->Session->setFlash(__('The file %s not exists.', $filename));
			$this->redirect($this->_redirectPassedArgs(array('action' => 'index'), $admin));
		}
		
		// Create binary file
		$filesize = $this->Binary->writeFile(
				$ori_files_path . $filename,
				$new_files_path . $filename,
				$data
		);
	
		// Checks if filesize is ok
		$sizelimit = Configure::read('Snatcher.BinaryFiles.sizelimit');
		if( $filesize >  $sizelimit )
		{
			$this->Session->setFlash(__('The file %s exceeds %d bytes in %d bytes.',
					$filename,
					$sizelimit,
					($filesize - $sizelimit) ));
				
			$this->redirect($this->_redirectPassedArgs(array('action' => 'index'), $admin));
		}
		
		return $new_files_path . $filename;
	}

	/**
	 * Check if an user (translater or tester) can download data from BinaryFile
	 * @param int $id Identifier from BinaryFile
	 * @param Model $this->BinaryFile BinaryFile model
	 * @return boolean if can access return true.
	 */
	protected function _canDownload($id, $model)
	{
		if( $this->isAdmin || $this->isManager)
		{
			return true;
		}
	
		$data = $model->find('first',
				array('fields' => 'user_id',
						'contain' => 'Testers.id',
						'conditions' => array($model->alias . '.' . $model->primaryKey => $id)));
	
		// Owners can access
		if( $data[$model->alias]['user_id'] == $this->Auth->user('id') )
		{
			return true;
		}
	
		// Testers from BinaryFiles can access
		if( isset($data['Testers']) ){
			foreach($data['Testers'] as $user_id){
				if($user_id['id'] == $this->Auth->user('id')){
					return true;
				}
			}
		}
	
		return false;
	}	

}