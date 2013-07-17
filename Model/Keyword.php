<?php
App::uses('AppModel', 'Model');
/**
 * Keyword Model
 *
 */
class Keyword extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

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
			'isSame' => array(
					'rule' => array('isSame'),
					'message' => 'The new_name has same value than name',	
			),
			'isUnique' => array(
					'rule' => array('checkUnique', array('new_name', 'name'), false),
					'message' => 'Name and new name are already used',
			),				
		),
	);
}