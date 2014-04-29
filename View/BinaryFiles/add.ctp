<div class="documents form">
<?php echo $this->Form->create('Document', array('type' => 'file', 'enctype' => 'multipart/form-data'));?>
	<fieldset>
		<legend><?php echo sprintf('Añadir fichero al Proyecto %s', $project['Project']['name']); ?></legend>		
		Fichero
	<?php
		echo $this->Form->file('filename', array('label' => 'Fichero'));
		echo $this->Form->input('description', array('type' => 'textarea'));
//		$label = '<span title='..'>'..'</span>';
		$label = $this->Html->tag('span', __('Traduccion automatica'), array('title' => __('Podria demorarse varios minutos')));
		echo $this->Form->input('autotranslate', array('type' => 'checkbox', 'label' => $label));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>