<div class="keywords form">
<?php echo $this->Form->create('Keyword'); ?>
	<fieldset>
		<legend><?php echo __('Admin Add Keyword'); ?></legend>
	<?php
		echo $this->Form->input('name', array(
			'label' => __('Name'),
			'placeholder' => __('Name'),
			'autofocus' => true
			));
		echo $this->Form->input('new_name', array(
			'label' => __('New Name'),
			'placeholder' => __('New Name'),
			));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>