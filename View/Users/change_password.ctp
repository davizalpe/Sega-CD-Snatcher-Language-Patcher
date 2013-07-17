<div class="users form">
<?php echo $this->Form->create('User'); ?>
	<fieldset>
		<legend><?php echo __('Change Password'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('old_password', array(
				'type'=>'password', 
				'label'=>__('Old Password'),
				'placeholder' => __('Old Password'),
				'autofocus' => true
				));			
		echo $this->Form->input('password', array(
				'type'=>'password', 
				'label'=>__('New Password'),
				'placeholder' => __('New Password')
				));
		echo $this->Form->input('repit_password', array(
				'type'=>'password', 
				'label'=>__('Repit Password'),
				'placeholder' => __('Repit Password')
				));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>