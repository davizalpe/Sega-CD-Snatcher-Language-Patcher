<?php
App::uses('AppModel', 'Model');
/**
 * Character Model
 *
 */
class Character extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'new_name';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'isUnique' => array(
					'rule' => 'isUnique',
					'message' => 'The name is already used',
			),	
		),
		'readonly' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'new_name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'The name is already used',		
		)),
	);
	
	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
			'BinaryText' => array(
					'className' => 'BinaryText',
					'foreignKey' => 'binary_file_id',
					'dependent' => false,
					'conditions' => '',
					'fields' => '',
					'order' => 'order',
					'limit' => '',
					'offset' => '',
					'exclusive' => '',
					'finderQuery' => '',
					'counterQuery' => ''
			)
	);	
}
