<div class="users form">
<?php echo $this->Form->create('User'); ?>
	<fieldset>
		<legend><?php echo __('Edit User'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('username', array(
				'label' => __('Username'), 
				'disabled' => true));
						
		echo $this->Form->input('role_id', array(
				'type'=>'select', 
				'label'=>__('Role'),
				'autofocus' => true,		 
				'selected'=> $this->data['User']['role_id']
				));
		
		echo $this->Form->input('timezone', array(
				'type'=>'select',
				'label'=>__('Timezone'),
				'autofocus' => true,
				'selected'=> $this->data['User']['timezone']
		));		
				
		echo $this->Form->input('active', array(
				'label' => __('Active')
				));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>