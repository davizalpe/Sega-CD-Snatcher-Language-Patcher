<div class="binaryFiles form">
<?php echo $this->Form->create('BinaryFile'); ?>
	<fieldset>
		<legend><?php echo __('Edit Binary File'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('filename', array('disabled' => true, 'label' => __('Filename')));
		echo $this->Form->input('description', array('type' => 'textarea', 'label' => __('Description')));
		
		echo $this->Form->input('user_id', array(
				'empty' => __('Select Translator'), 
				'label' => __('Translator'), 
				'title' => __('Edit Title BinaryFile Username')));
		
		echo $this->Form->input('BinaryFile.Testers', array(
				'empty' => __('Select Testers'),
				'type' => 'select', 
				'multiple' => true,	
				'label' => __('Testers'), 
				'title' => __('Edit Title BinaryFile Tester')));
	?>
	</fieldset>
<?php echo $this->Form->submit(__('Submit')); ?>
</div>