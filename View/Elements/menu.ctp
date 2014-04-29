<?php

function _isAdmin($params)
{
	$array_list = array('users',
			'quicksaves',
			'characters',
			'keywords',
			'faqs'
			);
			
	if(
			isset($params['admin']) &&
			in_array($params['controller'], $array_list)
	){
		return ' class=selected';
	}

	return '';
}

function _isManager($params)
{
	$array_list = array('binary_files',
			'binary_texts',
			'fixed_texts',
			'attachments',
			'reviews'
			);
	
	if(
			isset($params['admin']) &&
			in_array($params['controller'], $array_list)
	){
		return ' class=selected';
	}
	
	return '';	
}

function _isFile($params)
{
	$array_list = array('attachments', 'quicksaves');
			
	if(
			!isset($params['admin']) &&
			in_array($params['controller'], $array_list)
	){
		return ' class=selected';
	}
	
	return '';
}

function _isTranslate($params)
{	
	$array_list = array('binary_files', 'binary_texts');
	
	if(
			!isset($params['admin']) &&
			($params['action'] != 'test') &&			
			( 
				in_array($params['controller'], $array_list) ||
				(($params['controller'] == 'reviews') && ($params['action'] == 'index'))			 
			)
	){
		return ' class=selected';
	}
	
	return '';
}

function _isReview($params)
{		
	$array_actions_list = array('test', 'edit', 'add');
	$array_controller_list = array('binary_files', 'binary_texts');
	
	if( 
			!isset($params['admin']) &&
			(
				( in_array($params['action'], $array_actions_list) 
						&& ($params['controller'] == 'reviews') ) 
					||					
				( in_array($params['controller'], $array_controller_list) 
						&& ($params['action'] == 'test') )
			)
						
	){
		return ' class=selected';
	}
	
	return '';
}

function _isFaq($params)
{
	if(
			!isset($params['admin']) &&
			($params['controller'] == 'faqs')
	){
		return ' class=selected';
	}
	
	return '';
}


?>
<ul>
	<li class="main">
		<?php echo $this->Html->link(__('Project Nav Title', true), array('admin' => false, 'plugin' => false, 'controller' => 'pages', 'action' => 'display', 'home')); ?>
	</li>
<?php $id = $this->Session->read('Auth.User.id');?>
<?php if($id): ?>
	<?php $role_id = $this->Session->read('Auth.User.role_id');?>
	
	<li<?=_isTranslate($this->params);?>>
		<?php echo $this->Html->link(
					__('Translates', true),
					array('admin' => false, 'plugin' => false, 'controller' => 'binary_files', 'action' => 'index'));
			?>
	</li>	

	<li<?=_isReview($this->params);?>>
		<?php echo $this->Html->link(
					__('Reviews', true),
					array('admin' => false, 'plugin' => false, 'controller' => 'binary_files', 'action' => 'test'));
			?>		
	</li>	
	
	<li<?=_isFile($this->params);?>>
		<?php echo $this->Html->link(
					__('Files'),
					'#');
			?>
		<ul>
			<li>			
				<?php echo $this->Html->link(
							__('Attachments', true),
							array('admin' => false, 'plugin' => false, 'controller' => 'attachments', 'action' => 'index'));
				?>	
			</li>
			
			<li>			
				<?php echo $this->Html->link(
							__('Quicksaves', true),
							array('admin' => false, 'plugin' => false, 'controller' => 'quicksaves', 'action' => 'index'));
				?>	
			</li>			
		</ul>
			
	</li>
	
	<li<?=_isFaq($this->params);?>>
		<?php echo $this->Html->link(
					__('Faqs', true),
					array('admin' => false, 'plugin' => false, 'controller' => 'faqs', 'action' => 'index'));
		?>	
	</li>	
	
	<?php /* Manager Menu */?>
	<?php if($role_id <= 2):?>
	
	<li<?=_isManager($this->params);?>>
	
		<?php echo $this->Html->link(
					__('Manage'),
					'#');
			?>
		<ul>
			<li>
				<?php echo $this->Html->link(
						__('Manage Binary Files'),
						array('admin' => true, 'plugin' => false, 'controller' => 'binary_files', 'action' => 'index'));
				?>
			</li>
			
			<li>
				<?php echo $this->Html->link(
						__('Manage Binary Texts'),
						array('admin' => true, 'plugin' => false, 'controller' => 'binary_texts', 'action' => 'index'));
				?>
			</li>		

			<li>
				<?php echo $this->Html->link(
						__('Manage Reviews'),
						array('admin' => true, 'plugin' => false, 'controller' => 'reviews', 'action' => 'index'));
				?>
			</li>					
			
			<li>
				<?php echo $this->Html->link(
						__('Manage Fixed Texts'),
						array('admin' => true, 'plugin' => false, 'controller' => 'fixed_texts', 'action' => 'index'));
				?>
			</li>
			
			<li>
				<?php echo $this->Html->link(
							__('Manage Attachments', true),
							array('admin' => true, 'plugin' => false, 'controller' => 'attachments', 'action' => 'index'));
				?>
			</li>	
		
		</ul>		
	</li>
	<?php endif;?>
	
	<?php /* Admin Menu */?>
	<?php if($role_id == 1):?>
	
	<li<?=_isAdmin($this->params);?>>
	
		<?php echo $this->Html->link(
					__('Admin'),
					'#');
			?>
		<ul>
			<li>
				<?php echo $this->Html->link(
						__('Admin Users'),
						array('admin' => true, 'plugin' => false, 'controller' => 'users', 'action' => 'index'));
				?>
			</li>
			
			<li>
				<?php echo $this->Html->link(
						__('Admin Faqs'),
						array('admin' => true, 'plugin' => false, 'controller' => 'faqs', 'action' => 'index'));
				?>
			</li>							
			
			<li>			
				<?php echo $this->Html->link(
							__('Admin Quicksaves', true),
							array('admin' => true, 'plugin' => false, 'controller' => 'quicksaves', 'action' => 'index'));
				?>	
			</li>	
			
			<li>
				<?php echo $this->Html->link(
						__('Admin Characters'),
						array('admin' => true, 'plugin' => false, 'controller' => 'characters', 'action' => 'index'));
				?>
			</li>
			
			<li>
				<?php echo $this->Html->link(
						__('Admin Keywords'),
						array('admin' => true, 'plugin' => false, 'controller' => 'keywords', 'action' => 'index'));
				?>
			</li>			
		</ul>				
	</li>
	<?php endif;?>
	
	
	<?php /* User Menu */?>
	<li class="user-menu">
		<?php echo $this->Html->link(
				__('Logout'),
				array('admin' => false, 'plugin' => false, 'controller' => 'users', 'action' => 'logout'));
		?>
	</li>
	<li class="user-menu">
		<?php echo $this->Html->link(
				$this->Session->read('Auth.User.username'),
				'#');	
		?>		
		<ul>
			<li>
				<?php echo $this->Html->link(
						__('Edit Profile'),
						array('admin' => false, 'plugin' => false, 'controller' => 'users', 'action' => 'edit'));
				?>
			</li>		
			<li>
				<?php echo $this->Html->link(
						__('Change Password'),
						array('admin' => false, 'plugin' => false, 'controller' => 'users', 'action' => 'changePassword'));
				?>
			</li>
		</ul>
	</li>
<?php else: ?>
	<li class="user-menu">
		<?php echo $this->Html->link(
				__('Sign in'),
				array('admin' => false, 'plugin' => false, 'controller' => 'users', 'action' => 'login'));
		?>		
	</li>
<?php endif;?>
</ul>