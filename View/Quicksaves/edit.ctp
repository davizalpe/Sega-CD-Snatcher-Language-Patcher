<div class="saves form">
<?php echo $this->Form->create('Quicksave'); ?>
	<fieldset>
		<legend><?php echo __('Edit Quicksave'); ?></legend>
	<?php
		echo $this->Form->input('id');
				echo $this->Form->input('act', array(
				'label' => __('Act'), 
				'type' => 'select', 
				'options' => array(1 => 1, 2 => 2, 3 => 3),
				));
		echo $this->Form->input('binary_file_id', array('label' => __('Binary File')));
		
		echo $this->Form->input('slot', array('label' => __('Save slot'), 'type' => 'select', 'options' => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9)));
		
		echo $this->Form->input('description', array(
				'label' => __('Description'),
				'placeholder' => __('Description'),				 
				'type' => 'textarea'));		
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>