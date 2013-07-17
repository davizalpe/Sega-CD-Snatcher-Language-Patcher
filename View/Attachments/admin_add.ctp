<div class="attachments form">
<?php echo $this->Form->create('Attachment', array('type' => 'file', 'enctype' => 'multipart/form-data'));?>
	<fieldset>
		<legend><?php echo __('Add Attachment'); ?></legend>
	<?php
		echo $this->Form->input('filename', array(
							'type' => 'file', 
							'label' => __('Filename')));
		
		echo $this->Form->input('description', array(
							'type' => 'textarea', 
							'label' => __('Description'),
							'placeholder' => __('Description'),
							));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>