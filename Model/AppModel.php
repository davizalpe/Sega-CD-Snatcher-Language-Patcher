<?php

App::uses('Model', 'Model');

class AppModel extends Model {

	/**
	 * Check isUnique for one or more fields
	 *
	 * @param array $ignoredData
	 * @param array $fields
	 * @param boolean $or if true check is unique for one field from $fields
	 * @link http://stackoverflow.com/questions/2461267/cakephp-isunique-for-2-fields
	 */
	public function checkUnique($ignoredData, $fields, $or = false)
	{
		return $this->isUnique($fields, $or);
	}

	/**
	 * Check if name value is equals to new_name
	 * @param array $data
	 */
	public function isSame($data)
	{
		if( isset($this->data[$this->name]['name']) )
		{
			$keyword = $this->data;
		}else{
			$keyword = $this->find('first', array('conditions' => array(
					$this->name . '.' . $this->primaryKey => $this->data[$this->name][$this->primaryKey]
			)));
		}

		return $data['new_name'] != $keyword[$this->name]['name'];
	}

	/**
	 * If you do a updateAll in a model with model->recursive = -1, the model behaviour as it have recursive turn on.
	 *
	 * @link http://blog.pepa.info/php-html-css/cakephp/getting-rid-of-joins-in-updateall-query/
	 * @param array $fields
	 * @param array $conditions
	 * @param int $recursive
	 */
	function updateAll($fields, $conditions = true, $recursive = null) {
		if (!isset($recursive)) {
			$recursive = $this->recursive;
		}

		if ($recursive == -1) {
			$belongsTo = $this->belongsTo;
			$hasOne = $this->hasOne;
			$this->unbindModel(array(
					'belongsTo' => array_keys($belongsTo),
					'hasOne' => array_keys($hasOne)
			), true);
		}

		$result = parent::updateAll($fields, $conditions);
		 
		if ($recursive == -1) {
			$this->bindModel(array(
					'belongsTo' => $belongsTo,
					'hasOne' => $hasOne
			), true);
		}
		 
		return $result;
	}

}