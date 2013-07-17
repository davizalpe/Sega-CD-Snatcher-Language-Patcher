<div class="users form">
<?php echo $this->Form->create('User'); ?>
	<fieldset>
		<legend><?php echo __('Edit User'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('username', array(
				'label' => __('Username'), 
				'disabled' => true));
								
		echo $this->Form->input('timezone', array(
				'type'=>'select',
				'label'=>__('Timezone'),
				'autofocus' => true,
				'selected'=> $this->data['User']['timezone']
		));							
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>