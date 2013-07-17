<div class="fixedTexts form">
<?php echo $this->Form->create('FixedText'); ?>
	<fieldset>
		<legend><?php echo __('Edit Fixed Text'); ?></legend>
		
	<?php 
	echo $this->Form->input('character_id', array(
			'type' => 'text',
			'value' => $fixed_text['BinaryText'][0]['Character']['new_name'],
			'label' => __('Character'),
			'disabled' => true));	
	?>		
		
	<?php echo $this->element('texts_edit_form'); ?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>