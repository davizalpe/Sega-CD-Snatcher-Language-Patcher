<div class="keywords form">
<?php echo $this->Form->create('Keyword'); ?>
	<fieldset>
		<legend><?php echo __('Admin Edit Keyword'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name', array(
			'label' => __('Name'),
			'value' => $name,
			'disabled' => true,
			));
		echo $this->Form->input('new_name', array(
			'label' => __('New Name'),
			'placeholder' => __('New Name'),
			'autofocus' => true
			));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>