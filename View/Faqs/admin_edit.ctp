<div class="faqs form">
<?php echo $this->Form->create('Faq'); ?>
	<fieldset>
		<legend><?php echo __('Edit Faq'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('order', array(
				'label' => __('Order'),
				'autofocus' => true,
				'placeholder' => __('Order'), 
				));	
		echo $this->Form->input('question', array(
				'label' => __('Question'),
				'placeholder' => __('Question'),
				));
		echo $this->Form->input('answer', array(
				'type' => 'textarea',
				'rows' => 3,
				'class' => 'ckeditor', 
				'label' => __('Answer'),
				'placeholder' => __('Answer'),
				));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<?php 
/* for each keyup recalculate chars and reajust textarea */
$this->Js->get('textarea')->event('keyup', "textAreaAdjust(this);");

/* for each textare ajust textarea on load */
$this->Js->get('textarea')->each('textAreaAdjust(this);', true);
?>