<p class="error">
	<?php echo $this->Html->image('metal.png', array('align' => 'center')); ?>
	<strong><?php echo __d('cake', 'Error'); ?>: </strong>
	<?php echo __('This page dont exists.'); ?> 	
</p>
<?php
if (Configure::read('debug') > 0 ):
	echo $this->element('exception_stack_trace');
endif;
?>