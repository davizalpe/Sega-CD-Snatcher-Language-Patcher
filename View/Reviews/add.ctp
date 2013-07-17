<div class="reviews form">
<?php echo $this->Form->create('Review'); ?>
	<fieldset>
		<legend><?php echo __('Add Review'); ?></legend>
	<?php
		// Review
		$message = $this->Html->tag('span', '', array('id' => 'counter', 'class' => 'counter'));
		echo $this->Form->input('new_text', array(
				'id' => 'newtext',
				'type' => 'textarea',
				'label' => __('Review'),
				'placeholder' => __('Review'),
				'autofocus' => true,
				'after' => $message,
		));
		
		// New Text
		echo $this->Form->input('BinaryText.new_text', array(
				'type' => 'textarea',
				'value' => $binary_text['BinaryText']['new_text'],
				'label' => __('New Text'),
				'readonly' => true));
		
		// Original Text
		echo $this->Form->input('BinaryText.old_text', array(
				'type' => 'textarea',
				'value' => str_replace("<0>","\r\n", $binary_text['BinaryText']['text']),
				'label' => __('Original Text'),
				'readonly' => true));		

		/* on keyup recalculate chars */
		$this->element('texts_edit_js');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>