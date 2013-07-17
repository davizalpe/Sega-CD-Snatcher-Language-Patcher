<?php
App::uses('AppModel', 'Model');
/**
 * BinaryText Model
 *
 * @property BinaryFile $BinaryFile
 * @property User $User
 */
class BinaryText extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'text';
	
/**
 * Use Behavior Containable
 * @var array
 */	
	public $actsAs = array('Containable');

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(			
		'character_id' => array(
			'notempty' => array(
					'rule' => array('notempty'),
			)
		),
		'order' => array(
			'notempty' => array(
				'rule' => array('notempty'),
			)
		),
		'text' => array(
			'notempty' => array(
				'rule' => array('notempty'),
			),
		),
		'new_text' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'on' => 'update', // Limit validation to 'create' or 'update' operations
			),
			'hasValidatedReview' => array(
				'rule' => array('hasValidatedReview'),
				'message' => 'You can not change the text if has a validated review.',
			),								
		),			
	);

	/**
	 * Checks if a binary text has validated review.
	 * @param array $data
	 * @return boolean return false if exists validated review.
	 */
	public function hasValidatedReview($data)
	{		
		if( isset($this->data['BinaryText']['new_text']) && 
			$this->data['BinaryText']['new_text'] != $this->field('new_text'))
		{
			$this->Review->recursive = -1;
			$review = $this->Review->find('first', array(
					'contain' => false,
					'fields' => 'id',
					'conditions' => array('binary_text_id' => $this->id, 'validated' => true)
					));
						
			return empty($review);
		}
		
		return true;
	}
	
	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(			
		'BinaryFile' => array(
			'className' => 'BinaryFile',
			'foreignKey' => 'binary_file_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'counterCache' => array(
					'binary_texts_validated' => array('BinaryText.validated' => 1),
					'binary_texts_count' => array()
			)
		),
		'FixedText' => array(
			'counterCache' => true,
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => array('id','username'),
			'order' => ''
		),
		'OldCharacter' => array(
				'className' => 'Character',
				'foreignKey' => 'character_id_old',
				'conditions' => '',
				'fields' => array('id','name'),
				'order' => ''
		),
		'Character' => array(
				'className' => 'Character',
				'foreignKey' => 'character_id',
				'conditions' => '',
				'fields' => array('id','name'),
				'order' => ''
		)
	);

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
			'Review' => array(
					'className' => 'Review',
					'foreignKey' => 'binary_text_id',
					'dependent' => false,
					'conditions' => '',
					'fields' => '',
					'order' => '',
					'limit' => '',
					'offset' => '',
					'exclusive' => '',
					'finderQuery' => '',
					'counterQuery' => ''
			)
	);	
		
}