<?php
App::uses('AppModel', 'Model');
App::uses('Security', 'Utility'); /* Used for bcrypt auth */ 
/**
 * User Model
 *
 * @property Role $Role
 */
class User extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'username';
	
	/**
	 * Group-only ACL
	 * 
	 * @param array $user
	 * @return multitype:string NULL
	 */
	public function bindNode($user) {
		return array('model' => 'Role', 'foreign_key' => $user['User']['role_id']);
	}	
	
	/**
	 * Save hashing passwords  
	 * @param array $options
	 * @return boolean
	 */
	public function beforeSave($options = array()) {
		if (isset($this->data[$this->alias]['password'])) {
			
			/* Bcrypt auth since 2.3, recomended. Require PHP 5.3 */
			$hash = Security::hash($this->data[$this->alias]['password'], 'blowfish');
			$this->data[$this->alias]['password'] = $hash;
			
			/* Traditional password hash */
			// $this->data['User']['password'] = AuthComponent::password($this->data['User']['password']);
		}
		return true;
	}	

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'role_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'username' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'unique' => array(
					'rule' => array('isUnique'),
					'message' => 'Username taken. Please use another.'
			),
			'minLength' => array(
					'rule'    => array('minLength', 6),
					'message' => 'Username must be at least 6 characters long.'
			)
		),
		'old_password' => array(
				'notempty' => array(
						'rule' => array('notempty'),
						'allowEmpty' => false,
						'last' => true
				),
				'confirm' => array(
						'rule' => array('passCheck'),
						'message' => 'Old password incorrect',
						'last' => true
				),
		),
		'password' => array(		
				'minLength' => array(
						'rule' => array('minLength', 6),
						'message' => 'Password cannot be less than 6 characters',
				),
				'secure' => array(
						'rule' => array('passSecure'),
						'message' => 'Password must contain letters and numbers',						
				),
				'confirm' => array(
						'rule' => array('passCompare'),
						'message' => 'Passwords do not match',
						'last' => true
				),
		),
		'repit_password' => array(
			'notempty' => array(
					'rule' => array('notEmpty'),
					'allowEmpty' => false,
					'message' => 'Repit password',
					'last' => true,
			)
		),
		'active' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
		'timezone' => array(
			'notempty' => array(
					'rule' => array('notempty'),
			),
		),
	);
	
	/**
	 * Checks if new password is same than encrypted old password
	 * @return boolean
	 */
	public function passCheck() {
		$options = array('fields' => 'password', 'conditions' => array($this->alias . '.' . $this->primaryKey => $this->data[$this->alias]['id']));
		$someone = $this->find('first', $options);
		
		/* Bcrypt auth since 2.3, recomended */
// 		$hash = Security::hash(
// 				$this->data[$this->alias]['old_password'],
// 				'blowfish',
// 				$someone[$this->alias]['password']
// 		);
		
		/* Traditional password hash */
		$hash = AuthComponent::password($this->data[$this->alias]['old_password']);			
		
		if($someone[$this->alias]['password'] === $hash){
			unset($someone[$this->alias]['password']);
			return true;
		}
		
		return false;
	}

	/**
	 * Checks that password is not same than username
	 * @param unknown_type $data
	 * @return boolean
	 */
	public function passSecure($data){
		if(isset($this->data[$this->alias]['username']))
		{
			return $this->data[$this->alias]['username'] !== $data['password'];
		}elseif(isset($this->data[$this->alias]['id']))
		{
			$options = array('fields' => 'username', 'conditions' => array($this->alias . '.' . $this->primaryKey => $this->data[$this->alias]['id']));
			$someone = $this->find('first', $options);
				
			return $someone[$this->alias]['username'] !== $data['password'];				 
		}		 
		
		return true;
	}
	
	/**
	 * Checks if password is same than repit password
	 * @return boolean
	 */
	public function passCompare() {
		return ($this->data[$this->alias]['password'] === $this->data[$this->alias]['repit_password']);
	}	
	
	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Role' => array(
			'className' => 'Role',
			'foreignKey' => 'role_id',
			'conditions' => '',
			'fields' => 'name',
			'order' => ''
		)
	);

}
