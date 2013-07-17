<div class="users form">
<?php echo $this->Form->create('User'); ?>
	<fieldset>
		<legend><?php echo __('Add User'); ?></legend>
	<?php
		echo $this->Form->input('username', array(
				'label' => __('Username'),
				'placeholder' => __('Username'),
				'autofocus' => true
				));
		
		echo $this->Form->input('password', array(
				'label' => __('Password'),
				'placeholder' => __('Password'),
				));
		
		echo $this->Form->input('repit_password', array(
				'type'=>'password',
				'label'=>__('Repit Password'),
				'placeholder' => __('Repit Password')
				));
		
		echo $this->Form->input('role_id', array(
				'type'=>'select', 
				'label'=>__('Role'),
				'placeholder' => __('Role'),				 
				'options' => $roles, 
				'selected' => '3'
				));
		
		echo $this->Form->input('timezone', array(
				'type'=>'select',
				'label'=>__('Timezone'),
				'autofocus' => true,
				'selected'=> 'Europe/Madrid'
		));		
				
		echo $this->Form->input('active', array(
				'label' => __('Active')
				));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>