<div class="attachments form">
<?php echo $this->Form->create('Attachment'); ?>
	<fieldset>
		<legend><?php echo __('Admin Edit Attachment'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('User.username', array('disabled' => true, 'label' => __('Username')));
		
		echo $this->Form->input('filename', array('disabled' => true, 'label' => __('Filename')));
		
		echo $this->Form->input('description', array(
							'type' => 'textarea', 
							'label' => __('Description'),
							'placeholder' => __('Description'),				
							));		
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>