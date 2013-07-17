<div class="login">
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo __('Please enter your username and password'); ?></legend>
        <?php 
        	echo $this->Form->input('username', array(
        			'label' => __('Username'),
        			'placeholder' => __('Username'), 
        			'autofocus' => true)
        	);
        	echo $this->Form->input('password', array(
					'label' => __('Password'), 
					'placeholder' => __('Password')));
    	?>
    </fieldset>
<?php echo $this->Form->end(__('Login')); ?>
</div>