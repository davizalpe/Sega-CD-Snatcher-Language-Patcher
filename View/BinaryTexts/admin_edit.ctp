<?php
	$options = array('onsubmit' => 
			"if($('select option:selected').val() != '" . $binary_text['OldCharacter']['id'] . "')
			{
				return confirm('" . __('Are you sure you want to change the character from the text?')."')
			};");
?>
<div class="binary_texts form">
<?php echo $this->Form->create('BinaryText', $options); ?>
	<fieldset>
		<legend><?php echo __('Manage Edit Binary Text'); ?></legend>
	<?php
	echo $this->Form->input('id');
	
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

	// Basic inputs
	echo $this->element('texts_edit_form');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>