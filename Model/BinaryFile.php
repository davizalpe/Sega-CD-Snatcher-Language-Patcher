<?php
App::uses('AppModel', 'Model');
/**
 * BinaryFile Model
 *
 * @property BinaryText $BinaryText
*/
class BinaryFile extends AppModel {

	/**
	 * Display field
	 *
	 * @var string
	 */
	public $displayField = 'filename';

	/**
	 * Use Behavior Containable
	 * @var array
	 */
	public $actsAs = array('Containable');
		
	/**
	 * Added for save HABTM
	 * 
	 * @param array $options
	 * @return boolean
	 */
	 public function beforeSave($options = array())
	 {	 	
	    foreach (array_keys($this->hasAndBelongsToMany) as $model){
	      if(isset($this->data[$this->name][$model])){
	        $this->data[$model][$model] = $this->data[$this->name][$model];
	        unset($this->data[$this->name][$model]);
	      }
	    }
	    
	    return true;
	  }

	/**
	 * Validation rules
	 *
	 * @var array
	*/
	public $validate = array(
			'filename' => array(
					'notempty' => array(
							'rule' => array('notempty'),
							//'allowEmpty' => false,
							//'required' => false,
							//'last' => false, // Stop validation after this rule
							//'on' => 'create', // Limit validation to 'create' or 'update' operations
					),
			),
			'description' => array(
					'notempty' => array(
							'rule' => array('notempty'),
					),					
			),
			'Testers' => array(
					'multiple' => array(
							'rule' => array('multiple', array('max' => 3)),
							'message' => array('Please select less than %d users.', 4)
					),					
					'hasReviews' => array(
							'rule' => array('hasReviews'),
							'message' => 'You can not remove users with reviews.'
					),
			),
	);
	
	public function hasReviews($data)
	{
		$old_testers = $this->find('first', array(
				'fields' => 'id',
				'contain' => 'Testers.id',
				'conditions' => array($this->name . '.' . $this->primaryKey => $this->id)
		));
		
		foreach($old_testers['Testers'] as $value){
			if( !in_array($value['id'], $this->data['BinaryFile']['Testers']) )
			{
				$reviews = $this->BinaryText->Review->find('first', array(
						'fields' => 'id',
						'conditions' => array('Review.user_id' => $value['id']),
				));
		
				if( !empty($reviews) )
				{
					return false;
				}
			}
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
			'Translator' => array(
					'className' => 'User',
					'foreignKey' => 'user_id',
					'conditions' => '',
					'fields' => array('id', 'username'),
					'order' => ''
			),
	);

	/**
	 * hasMany associations
	 * You need bidirectional to create binary files with translated binary texts
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
					'order' => 'order', /* This is necesary to generate the binary files */
					'limit' => '',
					'offset' => '',
					'exclusive' => '',
					'finderQuery' => '',
					'counterQuery' => ''
			),
			'Review' => array(
					'className' => 'Review',
					'foreignKey' => 'binary_file_id',
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

	/**
	 * hasAndBelongsToMany associations
	 *
	 * @var array
	*/
	public $hasAndBelongsToMany = array(
			'Testers' =>
			array(
					'className'              => 'User',
					'joinTable'              => 'binary_files_testers',
					'foreignKey'             => 'binary_file_id',
					'associationForeignKey'  => 'user_id',
					'unique' 				 => 'keepExisting',
					'conditions'             => '',
					'fields'                 => array('id', 'username'),
					'order'                  => '',
					'limit'                  => '',
					'offset'                 => '',
					'finderQuery'            => '',
					'deleteQuery'            => '',
					'insertQuery'            => ''
			)
	);
}