<?php
App::uses('AppModel', 'Model');
/**
 * Review Model
 *
 * @property BinaryText $BinaryText
*/
class Review extends AppModel {

	/**
	 * Display field
	 *
	 * @var string
	 */
	public $displayField = 'new_text';

	/**
	 * Use Behavior Containable
	 * @var array
	 */
	public $actsAs = array('Containable');	

	public function beforeSave($options = array())
	{
		// when insert
		if( !$this->id )
		{
			$review = $this->findByBinaryTextIdAndValidated($this->data['Review']['binary_text_id'], 1);
				
			$this->data['Review']['hasValidatedReview'] = !empty($review);
		}

		return true;
	}
	
	public function afterSave($created)
	{
		// Execute updateAll only when update
		if( !$created )
		{			
			if($this->field('validated'))
			{
				/* set hasValidatedReview as true to all reviews from same text */
				$this->updateAll(
						array('Review.hasValidatedReview' => true),
						array('Review.binary_text_id' => $this->field('binary_text_id'))
				);
				
				/* copy review to binary text */
				$data = $this->find('first', array(
						'fields' => array('new_text', 'binary_text_id'),
						'conditions' => array($this->alias.".".$this->primaryKey => $this->id)));
				
				$this->BinaryText->id = $data['Review']['binary_text_id'];
				$this->BinaryText->saveField('new_text', $data['Review']['new_text']);
				
			}else{
				/* set hasValidatedReview as false to all reviews from same text */
				$this->updateAll(
						array('Review.hasValidatedReview' => false),
						array('Review.binary_text_id' => $this->field('binary_text_id'))
				);
			}
		}

		return true;
	}

	/**
	 * Check if a review can be deleted
	 * @return boolean returns false if review is validated.
	 */
	public function beforeDelete(){
		if($this->field('validated')){
			return false;
		}

		return true;
	}

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
			'user_id' => array(
					'numeric' => array(
							'rule' => array('numeric'),
							//'message' => 'Your custom message here',
							//'allowEmpty' => false,
							//'required' => false,
							//'last' => false, // Stop validation after this rule
							//'on' => 'create', // Limit validation to 'create' or 'update' operations
					),
			),
			'binary_text_id' => array(
					'numeric' => array(
							'rule' => array('numeric'),
							//'message' => 'Your custom message here',
							//'allowEmpty' => false,
							//'required' => false,
							//'last' => false, // Stop validation after this rule
							//'on' => 'create', // Limit validation to 'create' or 'update' operations
					),
			),
			'new_text' => array(
					'notempty' => array(
							'rule' => array('notempty'),
							//'message' => 'Your custom message here',
							//'allowEmpty' => false,
							//'required' => false,
							//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
					),
					'checkUnique' => array(
							'rule' => array('checkUnique', array('new_text', 'binary_text_id'), false),
							'message' => 'This text is already used for this review',
					),
					'checkRepitedText' => array(
							'rule' => array('checkRepitedText'),
							'message' => 'The review is equals to the translation of binary text',
					),
					'isValidatedText' => array(
							'rule' => array('isValidatedText'),
							'message' => 'You can not change the review if has been validated.',
					),
			),
			'validated' => array(
					'boolean' => array(
					'rule' => array('boolean'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'hasValidatedReview' => array(
					'rule' => array('hasValidatedReview'),
					'message' => 'There is already a validated review for the same binary text.',
			)
			),
	);

	/**
	 * Returns true if there are not
	 * any review validated for same binary text
	 * @param array $data validated value review
	 * @return boolean
	*/
	public function hasValidatedReview($data)
	{
		if( isset($data['validated']) && ($data['validated']) )
		{
			return !$this->field('hasValidatedReview');
		}

		return true;
	}

	/**
	 * Checks if review is validated and his text has been changed. Must be equals
	 * @param array $data
	 * @return boolean return true if new review text and old are equals. 
	 */
	public function isValidatedText($data)
	{
		if( $this->id && $this->field('validated') && isset($data['new_text']) )
		{
			return $this->field('new_text') == $data['new_text'];
		}

		return true;
	}

	/**
	 * Checks if review is same as associated binary text. Must be differents
	 * @param array $data
	 * @return boolean true if reviews text and binary text are different. 
	 */
	public function checkRepitedText($data)
	{		
		if( isset($this->data['Review']['binary_text_id']) )
		{
			$this->BinaryText->id = $this->data['Review']['binary_text_id'];
		}else{
			$this->BinaryText->id = $this->field('binary_text_id');
		}		

		return $this->data['Review']['new_text'] != (string) $this->BinaryText->field('new_text');
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
							'reviews_validated' => array('Review.validated' => 1),
							'reviews_count' => array()
					)
			),
			'User' => array(
					'className' => 'User',
					'foreignKey' => 'user_id',
					'conditions' => '',
					'fields' => '',
					'order' => ''
			),
			'BinaryText' => array(
					'className' => 'BinaryText',
					'foreignKey' => 'binary_text_id',
					'conditions' => '',
					'fields' => '',
					'order' => '',
					'counterCache' => true,
					//'type' => 'right'
			)
	);

}