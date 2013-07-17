<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 */
class UsersController extends AppController {

	public $paginate = array(
			'order' => 'last_login DESC',
	);
	
	/**
	* @see AppController::beforeFilter()
	*/
	function beforeFilter() {
		parent::beforeFilter();				
		
		/* You can remove initDB after create the ACOs in BD */
		$this->Auth->allowedActions = array('login'); //, 'initDB');
		
		/**
		 * Allow create new admin users uncomment this lines
		 */
		//$this->Auth->allowedActions = array('admin_index', 'admin_add', 'admin_changePassword');		
	}
	
// 	 /**
// 	 * This method inserts ACL permisions by role id in db 	 
// 	 * 
// 	 * @link http://book.cakephp.org/2.0/en/tutorials-and-examples/simple-acl-controlled-application/part-two.html
// 	 */
// 	public function initDB() {
// 		$group = $this->User->Role;
// 		//Allow admins to everything
// 		$group->id = 1;
// 		$this->Acl->allow($group, 'controllers');		
		
// 		//allow managers to posts and widgets
// 		$group->id = 2;
// 		$this->Acl->deny($group, 'controllers');
// 		$this->Acl->allow($group, 'controllers/Users/login');
// 		$this->Acl->allow($group, 'controllers/Users/logout');
// 		$this->Acl->allow($group, 'controllers/Users/edit');
// 		$this->Acl->allow($group, 'controllers/Users/changePassword');
		
// 		$this->Acl->allow($group, 'controllers/BinaryFiles/index');
// 		$this->Acl->allow($group, 'controllers/BinaryFiles/test');
// 		$this->Acl->allow($group, 'controllers/BinaryFiles/download');
// 		$this->Acl->allow($group, 'controllers/BinaryFiles/admin_index');
// 		$this->Acl->allow($group, 'controllers/BinaryFiles/admin_edit');
// 		$this->Acl->allow($group, 'controllers/BinaryFiles/admin_downloadall');
		
// 		$this->Acl->allow($group, 'controllers/BinaryTexts/index');
// 		$this->Acl->allow($group, 'controllers/BinaryTexts/search');
// 		$this->Acl->allow($group, 'controllers/BinaryTexts/edit');
// 		$this->Acl->allow($group, 'controllers/BinaryTexts/admin_index');
// 		$this->Acl->allow($group, 'controllers/BinaryTexts/admin_edit');
// 		$this->Acl->allow($group, 'controllers/BinaryTexts/admin_changeCharacter');		
		
// 		$this->Acl->allow($group, 'controllers/FixedTexts/admin_index');
// 		$this->Acl->allow($group, 'controllers/FixedTexts/admin_search');
// 		$this->Acl->allow($group, 'controllers/FixedTexts/admin_edit');
// 		$this->Acl->allow($group, 'controllers/FixedTexts/admin_view');
		
// 		$this->Acl->allow($group, 'controllers/Attachments/index');
// 		$this->Acl->allow($group, 'controllers/Attachments/download');
// 		$this->Acl->allow($group, 'controllers/Attachments/admin_index');
// 		$this->Acl->allow($group, 'controllers/Attachments/admin_add');
// 		$this->Acl->allow($group, 'controllers/Attachments/admin_edit');
// 		$this->Acl->allow($group, 'controllers/Attachments/admin_delete');
		
// 		$this->Acl->allow($group, 'controllers/Saves/index');
// 		$this->Acl->allow($group, 'controllers/Saves/search');
// 		$this->Acl->allow($group, 'controllers/Saves/add');
// 		$this->Acl->allow($group, 'controllers/Saves/edit');
// 		$this->Acl->allow($group, 'controllers/Saves/delete');
// 		$this->Acl->allow($group, 'controllers/Saves/download');		
		
// 		$this->Acl->allow($group, 'controllers/Reviews/admin_index');
// 		$this->Acl->allow($group, 'controllers/Reviews/admin_edit');
// 		$this->Acl->allow($group, 'controllers/Reviews/admin_delete');
// 		$this->Acl->allow($group, 'controllers/Reviews/index');
// 		$this->Acl->allow($group, 'controllers/Reviews/search');
// 		$this->Acl->allow($group, 'controllers/Reviews/test');
// 		$this->Acl->allow($group, 'controllers/Reviews/add');
// 		$this->Acl->allow($group, 'controllers/Reviews/edit');
// 		$this->Acl->allow($group, 'controllers/Reviews/delete');
// 		$this->Acl->allow($group, 'controllers/Reviews/validation');
// 		$this->Acl->allow($group, 'controllers/Reviews/other_reviews');
		
// 		$this->Acl->allow($group, 'controllers/Faqs/index');

// 		//allow users to only add and edit on posts and widgets
// 		$group->id = 3;
// 		$this->Acl->deny($group, 'controllers');
// 		$this->Acl->allow($group, 'controllers/Users/login');
// 		$this->Acl->allow($group, 'controllers/Users/logout');
// 		$this->Acl->allow($group, 'controllers/Users/edit');
// 		$this->Acl->allow($group, 'controllers/Users/changePassword');
		
// 		$this->Acl->allow($group, 'controllers/BinaryFiles/index');
// 		$this->Acl->allow($group, 'controllers/BinaryFiles/test');
// 		$this->Acl->allow($group, 'controllers/BinaryFiles/download');
		
// 		$this->Acl->allow($group, 'controllers/BinaryTexts/index');
// 		$this->Acl->allow($group, 'controllers/BinaryTexts/search');
// 		$this->Acl->allow($group, 'controllers/BinaryTexts/edit');	

// 		$this->Acl->allow($group, 'controllers/Attachments/index');
// 		$this->Acl->allow($group, 'controllers/Attachments/download');

// 		$this->Acl->allow($group, 'controllers/Saves/index');
// 		$this->Acl->allow($group, 'controllers/Saves/search');
// 		$this->Acl->allow($group, 'controllers/Saves/add');
// 		$this->Acl->allow($group, 'controllers/Saves/edit');
// 		$this->Acl->allow($group, 'controllers/Saves/delete');
// 		$this->Acl->allow($group, 'controllers/Saves/download');

// 		$this->Acl->allow($group, 'controllers/Reviews/index');
// 		$this->Acl->allow($group, 'controllers/Reviews/search');
// 		$this->Acl->allow($group, 'controllers/Reviews/test');
// 		$this->Acl->allow($group, 'controllers/Reviews/add');
// 		$this->Acl->allow($group, 'controllers/Reviews/edit');
// 		$this->Acl->allow($group, 'controllers/Reviews/delete');
// 		$this->Acl->allow($group, 'controllers/Reviews/validation');
// 		$this->Acl->allow($group, 'controllers/Reviews/other_reviews');
		
// 		$this->Acl->allow($group, 'controllers/Faqs/index');

// 		//we add an exit to avoid an ugly "missing views" error message
// 		echo "all done";
// 		exit;		
// 	}

	/**
	 * Login method
	 */
	public function login() {
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				// Set last login
				$this->User->id = $this->Auth->user('id');
				$this->User->saveField('last_login', date('Y-m-d H:i:s'));
				
				return $this->redirect($this->Auth->redirectUrl());
			} else {
				$this->Session->setFlash(__('Username or password is incorrect.'));
				$this->request->data['User']['password'] = "";
			}
		}
				
		if($this->Auth->user('id'))
		{
				$this->redirect($this->Auth->redirectUrl());		
		}
	}	
	
	/**
	 * Logout method
	 */
	public function logout() {
		$this->redirect($this->Auth->logout());
	}	
	
	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @return void
	 */
	public function edit() {
		$this->User->id = $this->Auth->user('id');
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
	
		if ($this->request->is('post') || $this->request->is('put')) {
				
			$fieldList = array('timezone');
							
			if ($this->User->save($this->request->data, true, $fieldList)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('controller' => 'pages', 'action' => 'display', 'home'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		} else {
			$options = array('fields' => array('id', 'timezone', 'username'), 'conditions' => array('User.' . $this->User->primaryKey => $this->User->id));
			$this->data = $this->User->find('first', $options);
		}
		
		App::uses('CakeTime', 'Utility');
		$timezones = CakeTime::listTimezones();
		$this->set(compact('timezones'));		
	}

/**
 * change password method
 *
 * @throws NotFoundException
 * @return void
 */
	public function changePassword() {
		$this->User->id = $this->Auth->user('id');
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			
			$fieldList = array('password', 'old_password', 'repit_password');
			
			if ($this->User->save($this->request->data, true, $fieldList)) {
				$this->Session->setFlash(__('The user password has been changed'));
				$this->redirect(array('controller' => 'pages', 'action' => 'display', 'home'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
				foreach($this->User->validationErrors as $v => $k){					
					if($v == 'password'){
						$this->request->data['User']['repit_password'] = "";
					}
					$this->request->data['User'][$v] = "";
				}				
			}
		} else {
			$options = array('fields' => 'id', 'conditions' => array('User.' . $this->User->primaryKey => $this->User->id));
			$this->request->data = $this->User->find('first', $options);			
		}
	}

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->User->create();
			
			$fieldList = array('role_id', 'active', 'timezone', 'password', 'username');
			
			if ($this->User->save($this->request->data, true, $fieldList)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->request->data['User']['password'] = "";
				$this->request->data['User']['repit_password'] = "";
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		}

		App::uses('CakeTime', 'Utility');
		$timezones = CakeTime::listTimezones();
		$roles = $this->User->Role->find('list');
		$this->set(compact('roles', 'timezones'));		
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}			
			
		if ($this->request->is('post') || $this->request->is('put')) {

			$fieldList = array('role_id', 'active', 'timezone');
			
			if ($this->User->save($this->request->data, true, $fieldList)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		} else {
			$options = array('fields' => array('id', 'username', 'role_id', 'active', 'timezone'), 'conditions' => array('User.' . $this->User->primaryKey => $id));			
			$this->request->data = $this->User->find('first', $options);			
		}
		
		App::uses('CakeTime', 'Utility');
		$timezones = CakeTime::listTimezones();
		$roles = $this->User->Role->find('list');
		$this->set(compact('roles', 'timezones'));	
	}
	
	/**
	 * admin reset password method
	 *
	 * @throws NotFoundException
	 * @return void
	 */
	public function admin_changePassword($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
	
		if ($this->request->is('post') || $this->request->is('put')) {
			
			$fieldList = array('password', 'repit_password');
			
			if ($this->User->save($this->request->data, true, $fieldList)) {
				$this->Session->setFlash(__('The user password has been changed'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
				foreach($this->User->validationErrors as $v => $k){
					if($v == 'password'){
						$this->request->data['User']['repit_password'] = "";
					}
					$this->request->data['User'][$v] = "";
				}
			}
		} else {
			$options = array('fields' => array('id', 'username', 'role_id'), 'conditions' => array('User.' . $this->User->primaryKey => $id));
			$this->request->data = $this->User->find('first', $options);
		}
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
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->User->delete()) {
			$this->Session->setFlash(__('User deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('User was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
