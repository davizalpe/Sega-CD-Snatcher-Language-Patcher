<?php
App::uses('AppController', 'Controller');
/**
 * Reviews Controller
 *
 * @property Review $Review
*/
class ReviewsController extends AppController {

	/**
	 * Get binary text associated with review with binary text id $binary_text_id
	 * Obtain fields id, order, text and new_text and associated entities
	 * BinaryFile with filename, Character with name y OldCharacter with name and hex.
	 *
	 * @param int $binary_text_id
	 * @throws NotFoundException
	 * @return array $binary_text data
	 */
	private function _getBinaryText($binary_text_id)
	{
		$binary_text = $this->Review->BinaryText->find('first', array(
				'contain' => array('BinaryFile.filename', 'OldCharacter.hex'),
				'fields' => array('validated', 'id', 'order', 'text', 'new_text'),
				'conditions' => array('BinaryText.' . $this->Review->BinaryText->primaryKey => $binary_text_id,
						'BinaryText.validated' => true)));

		if( empty($binary_text) )
		{
			throw new NotFoundException(__('Invalid binary text'));
		}

		return $binary_text;
	}

	private function _getReviewId($binary_text_id, $conditions = array())
	{
		$reviews = $this->Review->find('all', array(
				'fields' => 'id',
				'conditions' => array_merge(
						array('Review.binary_text_id' => $binary_text_id),
						$conditions
				)
		));

		return $reviews;
	}

	/**
	 * search method
	 * @param $admin if is true redirects to admin index page. Default false.
	 */
	public function search($admin = false)
	{
		$url = array();

		if(isset($this->passedArgs['redirect'])){
			$url['action'] = $this->passedArgs['redirect'];
		}

		// build a URL will all the search elements in it
		foreach ($this->data as $k => $v)
		{
			foreach ($v as $k2 => $v2)
			{
				if( is_array($v2) )
				{
					foreach ($v2 as $k3 => $v3){
						$url[$k . "." .$k2 .".".$k3] = $v3;
					}
				}else{
					$url[$k . "." .$k2] = $v2;
				}
			}
		}

		// redirect the user to the url
		$this->redirect(
				$this->_redirectPassedArgs($url, $admin),
				null, true);
	}

	/**
	 * Get the binary file with $binary_file_id
	 * and check if user can access to the reviews list
	 *
	 * @param int $binary_file_id
	 * @throws NotFoundException
	 */
	private function _getBinaryFile($binary_file_id)
	{
		$binaryFile = $this->Review->BinaryText->BinaryFile->find('first', array(
				'fields' => 'filename',
				'conditions' => array(
						'BinaryFile.user_id' => $this->Auth->user('id'),
						'BinaryFile.id' => $binary_file_id)));

		if( empty($binaryFile) )
		{
			throw new NotFoundException(__('Invalid binary file'));
		}

		return $binaryFile;
	}

	/**
	 * Get the list of users from the reviews
	 * @return array $users list
	 */
	private function _getReviewUsers(){

		$review = $this->Review->find('all', array('fields' => 'user_id',
				'group' => 'user_id',
				'contain' => array('User.username'),
				'order' => 'User.username'
		));

		$users = array();

		if( !empty($review) )
		{
			foreach($review as $v)
			{
				$users[$v['User']['id']] = $v['User']['username'];
			}
		}

		return $users;
	}

	/**
	 * index method for translater users
	 *
	 * @param string $binary_text_id
	 * @return void
	 */
	public function index() {
		if( !isset($this->passedArgs['binary_file_id']) )
		{
			throw new NotFoundException(__('Invalid binary file'));
		}

		// only translater can view the reviews from binary_file_id
		$binaryFile = $this->_getBinaryFile($this->passedArgs['binary_file_id']);

		$this->paginate = array(
				'contain' => array('User.username', 'BinaryText'),
				'conditions' => array_merge(
						array('BinaryText.binary_file_id' => $this->passedArgs['binary_file_id']),
						$this->_searchConditions($this->Review)),
				'order' => 'BinaryText.order, User.Username',
		);

		$reviews = $this->paginate();

		// Data for select
		$users = $this->_getReviewUsers();

		$this->set(compact('reviews', 'binaryFile', 'characters', 'users'));
	}

	/**
	 * test method for tester users
	 *
	 * @param string $binary_text_id
	 * @return void
	 */
	public function test() {
		if( !isset($this->passedArgs['binary_file_id']) )
		{
			throw new NotFoundException(__('Invalid binary file'));
		}

		$binary_file_id = $this->passedArgs['binary_file_id'];

		$binaryFile = $this->Review->BinaryText->BinaryFile->find('first', array(
				'fields' => 'filename',
				'conditions' => array('BinaryFile.id' => $binary_file_id)));

		if(empty($binaryFile))
		{
			throw new NotFoundException(__('Invalid binary file'));
		}	

		$conditions = $this->_searchConditions($this->Review);
		$search_review = false;
		foreach($conditions as $k => $v)
		{
			if( !strncmp($k, 'Review.', 7))
			{
				$search_review = true;
			}
		}			
		$conditions = array_merge($conditions, array('BinaryText.binary_file_id' => $binary_file_id));

		// Set paginate for binary texts and reviews		
		// When you search for review obtain data only from review
		if( $search_review )
		{
			$this->paginate = array(
					'contain' => array('BinaryText'),
					'conditions' => array_merge($conditions, array('Review.user_id' => $this->Auth->user('id'))),
					'order' => 'BinaryText.order',
			
			);			
			$reviews = $this->paginate();		
		// Normal index obtain all binary texts
		}else{
			$this->paginate = array('BinaryText' => array(
					'contain' => array('Review' => array('conditions' => array('Review.user_id =' => $this->Auth->user('id')))),
					'conditions' => $conditions,
					'order' => 'BinaryText.order'
			));	
			$reviews = $this->paginate('BinaryText');
		}		

		$this->set(compact('reviews', 'binaryFile', 'search_review'));
	}
	
	public function other_reviews($binary_text_id = null)
	{
		$this->Review->BinaryText->id = $binary_text_id;
		if (!$this->Review->BinaryText->exists($binary_text_id))
		{
			throw new NotFoundException(__('Invalid binary text'));
		}
				
		$this->Review->contain(array('User.username'));
		
		$this->set('reviews', $this->Review->findAllByBinaryTextId($binary_text_id));				
	}

	/**
	 * add method
	 *
	 * @param string $binary_text_id
	 * @return void
	 */
	public function add($binary_text_id = null) {
		$this->Review->BinaryText->id = $binary_text_id;
		if (!$this->Review->BinaryText->exists($binary_text_id))
		{
			throw new NotFoundException(__('Invalid binary text'));
		}
			
		$user_id = $this->Auth->user('id');

		// Check if user already has added one review
		$this->Review->recursive = -1;
		if( $this->Review->findByUserIdAndBinaryTextId($user_id, $binary_text_id) )
		{
			throw new NotFoundException(__('Invalid review'));
		}

		if ($this->request->is('post')) {
			$this->Review->create();

			// Set association with testers and binary text
			$this->request->data['Review']['binary_file_id'] = (int)$this->Review->BinaryText->field('binary_file_id');
			$this->request->data['Review']['user_id'] = $user_id;
			$this->request->data['Review']['binary_text_id'] = $binary_text_id;

			$params = array('fieldList' => array('binary_file_id', 'user_id', 'binary_text_id', 'new_text', 'hasValidatedReview'));
			if ($this->Review->save($this->request->data, $params)) {
				$this->Session->setFlash(__('The review has been saved'));
				$this->redirect($this->_redirectPassedArgs(array('controller' => 'reviews', 'action' => 'test')));
			} else {
				$this->Session->setFlash(__('The review could not be saved. Please, try again.'));
			}
		}

		// Check if binary text is validated
		$binary_text = $this->_getBinaryText($binary_text_id);
		$ismenu = $binary_text['OldCharacter']['hex'] == Configure::read('Snatcher.Characters.menu');

		$this->set(compact('binary_text', 'ismenu'));
	}

	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $binary_text_id
	 * @return void
	 */
	public function edit($binary_text_id = null) {
		if ( !$this->Review->BinaryText->exists($binary_text_id)  ) {
			throw new NotFoundException(__('Invalid binary text'));
		}

		$user_id = $this->Auth->user('id');

		// Find review from the user
		$this->Review->recursive = -1;
		$review = $this->Review->findByUserIdAndBinaryTextId($user_id, $binary_text_id);
		if( empty($review) )
		{
			throw new NotFoundException(__('Invalid review'));
		}

		$this->Review->id = $review['Review']['id'];
		if( $this->Review->field('validated') ){
			throw new NotFoundException(__('Invalid review'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			$params = array('fieldList' => array('new_text'));

			if ($this->Review->save($this->request->data, $params)) {
				$this->Session->setFlash(__('The review has been saved'));
				$this->redirect($this->_redirectPassedArgs(array('controller' => 'reviews', 'action' => 'test')));
			} else {
				$this->Session->setFlash(__('The review could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Review.' . $this->Review->primaryKey => $this->Review->id));
			$this->request->data = $this->Review->find('first', $options);
		}

		$binary_text = $this->_getBinaryText($binary_text_id);
		$ismenu = $binary_text['OldCharacter']['hex'] == Configure::read('Snatcher.Characters.menu');

		$this->set(compact('binary_text', 'ismenu'));
	}

	/**
	 * delete method
	 *
	 * @throws NotFoundException
	 * @throws MethodNotAllowedException
	 * @param string $id
	 * @return void
	 */
	public function delete($id = null) {
		$this->Review->id = $id;
		if (!$this->Review->exists()
				|| $this->Review->field('validated')
		) {
			throw new NotFoundException(__('Invalid review'));
		}

		$this->request->onlyAllow('post', 'delete');

		// Only tester can delete his own review
		// Check if review is from tester
		$review = $this->Review->field('user_id', array('user_id' => $this->Auth->user('id')));
		if(empty($review))
		{
			throw new NotFoundException(__('Invalid review'));
		}

		if ($this->Review->delete()) {
			$this->Session->setFlash(__('Review deleted'));

			$this->redirect($this->_redirectPassedArgs(array('controller' => 'reviews', 'action' => 'test')));
		}

		if( $this->Review->field('validated') ){
			$this->Session->setFlash(__('You can not delete a validated review'));
		}else{
			$this->Session->setFlash(__('Review was not deleted'));
		}

		$this->redirect($this->_redirectPassedArgs(array('controller' => 'binary_texts', 'action' => 'test')));
	}

	/**
	 * validate method
	 *
	 * @throws NotFoundException
	 * @throws MethodNotAllowedException
	 * @param string $id
	 * @return void
	 */
	public function validation($id = null) {
		$this->Review->id = $id;
		if (!$this->Review->exists()) {
			throw new NotFoundException(__('Invalid review'));
		}

		$this->request->onlyAllow('post', 'put');

		$review = $this->Review->find('first', array(
				'contain' => array('BinaryText.binary_file_id'),
				'fields' => array('id', 'binary_text_id', 'validated', 'new_text'),
				'conditions' => array('Review.id' => $id)));

		// Only tranlator can validate the review
		$binaryFile = $this->Review->BinaryText->BinaryFile->find('first', array(
				'recursive' => -1,
				'fields' => 'user_id',
				'conditions' => array('id' => $review['BinaryText']['binary_file_id'], 'user_id' => $this->Auth->user('id'))
		));
		if( empty($binaryFile) )
		{
			throw new NotFoundException(__('Invalid review'));
		}

		// Set validated = 1 in new Validated Review
		$review['Review']['validated'] = true;

		$params = array('fieldList' => array('validated'));
		if ($this->Review->save($review, $params)) {
			$this->Session->setFlash(__('The review has been validated'));
		} else {
			$this->Session->setFlash(__('The review could not be validated.'));
		}

		$this->redirect($this->_redirectPassedArgs(array('controller' => 'reviews', 'action' => 'index', $review['BinaryText']['binary_file_id'])));
	}

	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index() {
		$this->Review->recursive = 0;

		$this->paginate = array(
				'conditions' => $this->_searchConditions($this->Review),
				'contain' => array('User.username', 'BinaryText' => array('BinaryFile.filename')),
				'order' => 'BinaryText.order, User.username'
		);

		$reviews = $this->paginate();

		// Data for select
		$binaryFiles = $this->Review->BinaryText->BinaryFile->find('list',
				array('fields' => array('id', 'filename'),
						'conditions' => array('reviews_count >' => 0),
						'order' => 'filename'));
		$users = $this->_getReviewUsers();

		$this->set(compact('reviews', 'binaryFiles', 'users'));
	}

	/**
	 * admin_edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_edit($id = null) {
		if (!$this->Review->exists($id))
		{
			throw new NotFoundException(__('Invalid review'));
		}

		$options = array(
				'fields' => array('binary_text_id'),
				'conditions' => array('Review.' . $this->Review->primaryKey => $id));
		$reviews = $this->Review->find('first', $options);
			
		if ($this->request->is('post') || $this->request->is('put'))
		{
			/* Quit the checkRepitedText validation from new_text
			 * in review model for you can set validated to false in reviews */
			unset($this->Review->validate['new_text']['checkRepitedText']);

			$params = array('fieldList' => array('new_text', 'validated'));
			if ($this->Review->save($this->request->data, $params)) {
				$this->Session->setFlash(__('The review has been saved'));
				$this->redirect($this->_redirectPassedArgs());
			} else {
				$this->Session->setFlash(__('The review could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Review.' . $this->Review->primaryKey => $id));
			$this->request->data = $this->Review->find('first', $options);
		}

		$binary_text = $this->_getBinaryText($reviews['Review']['binary_text_id']);
		$ismenu = $binary_text['OldCharacter']['hex'] == Configure::read('Snatcher.Characters.menu');

		$this->set(compact('binary_text', 'ismenu'));
	}

	/**
	 * admin_delete method
	 * Admin can delete validated reviews
	 *
	 * @throws NotFoundException
	 * @throws MethodNotAllowedException
	 * @param string $id
	 * @return void
	 */
	public function admin_delete($id = null) {
		$this->Review->id = $id;
		if (!$this->Review->exists()) {
			throw new NotFoundException(__('Invalid review'));
		}
		$this->request->onlyAllow('post', 'delete');

		// Unvalidate before delete
		if( $this->Review->field('validated') ){
			$this->Review->saveField('validated', false);
		}

		if ($this->Review->delete()) {
			$this->Session->setFlash(__('Review deleted'));
			$this->redirect($this->_redirectPassedArgs());
		}

		$this->Session->setFlash(__('Review was not deleted'));
		$this->redirect($this->_redirectPassedArgs());
	}
}
