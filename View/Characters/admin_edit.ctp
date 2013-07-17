<div class="characters form">
<?php echo $this->Form->create('Character'); ?>
	<fieldset>
		<legend><?php echo __('Admin Edit Character'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name', array('label' => __('Name'), 'readonly' => true));
		
		echo $this->Form->input('new_name', array(
			'label' => __('New Name'),
			'placeholder' => __('New Name'),
			'autofocus' => true,
			));

		if( !$readonly ){
			echo $this->Form->input('translatable', array('label' => __('Translatable')));
		}
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>