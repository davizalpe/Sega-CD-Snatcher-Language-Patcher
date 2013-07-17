<?php
	$message = $this->Html->tag('span', '', array('id' => 'counter', 'class' => 'counter'));
	echo $this->Form->input('new_text', array(											
											'type' => 'textarea',
											'id' => 'newtext', 
											'label' => __('New Text'),
											'placeholder' => __('New Text'), 
											'autofocus' => true,
											'after' => $message));

	// Validate
	$validated_label = $this->Html->tag('span', __('Validated'), array('class' => 'label_checkbox', 'title' => __('Edit Title Validated')));
	echo $this->Form->input('validated', array('label' => $validated_label));

	// Original Text
	$button_copy_original = $this->Html->tag('div', $this->Html->link(__('Copy Text'), '#', array('id' => 'copy_orig')), array('class' => 'actions'));
	echo $this->Form->input('text', array(
											'type' => 'textarea',
											'id' => 'orig_text',				
											'value' => str_replace("<0>","\r\n", $text),
											'label' => __('Text'),				 												 
											'after' => $button_copy_original,
											'readonly' => true));
	
	/* onclick buttons copy original text replace <0> with spaces
	 * and recalculate new chars and textarea size */
	$this->Js->get('#copy_orig')->event('click',
			"$('#newtext').val(replaceAll($('#orig_text').text(),'<0>',' '));
			textAreaAdjust($('#newtext').get(0));
			checkChars();");	
	
	/*
	 * Only shows suggestion if config translate is activated in core file 
	 */
	if( Configure::read('Snatcher.Translate.active') )
	{
	
		$button_copy_suggestion = $this->Html->tag('div', $this->Html->link(__('Copy Suggestion'), '#', array('id' => 'copy_sugg')), array('class' => 'actions'));
		echo $this->Form->input('suggestion', array(
												'type' => 'textarea', 
												'id' => 'sugg_text',  
												'value' => $this->GoogleTranslate->translate(str_replace("<0>"," ", $text)),
												'label' => __('Suggestion'),
												'after' => $button_copy_suggestion, 
												'readonly' => true));

		$this->Js->get('#copy_sugg')->event('click',
				"$('#newtext').val(replaceAll($('#sugg_text').text(),'<0>',' '));
			textAreaAdjust($('#newtext').get(0));
			checkChars();");		
	}

	/* on keyup recalculate chars */
	$this->element('texts_edit_js');
?>