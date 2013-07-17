<div class="binary_texts form">
<?php echo $this->Form->create('BinaryText'); ?>
	<fieldset>
		<legend><?php echo __('Change Character from Binary Text'); ?></legend>
	<?php
	echo $this->Form->input('id');
	
	// Text
	echo $this->Form->input('text', array(
			'type' => 'textarea',
			'value' => str_replace("<0>", "\r\n", $binary_text['BinaryText']['text']),
			'disabled' => true,
			'label' => __('Text'))
		);
	
	// Original Character
	echo $this->Form->input('old_character', array(
			'type' => 'text',
			'value' => $binary_text['OldCharacter']['name'],
			'label' => __('Original Character'),
			'disabled' => true));	

	// Character
	echo $this->Form->input('character_id', array(
		'selected'=> $this->data['BinaryText']['character_id'],
		'label' => __('Character')));		
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>