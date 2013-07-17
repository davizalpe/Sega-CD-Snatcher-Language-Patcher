<ul>
	<li><?php 					
			echo $this->Html->link(__('About Us'), 	
					array('admin' => false, 
							'controller' => 'pages', 
							'action' => 'display', 'about'));
	?></li>
	
	<li><?php echo $this->Html->link(__('Privacy Policy'), 
			array('admin' => false, 'controller' => 'pages', 'action' => 'display', 'privacy')); 
	?></li>		

	<li id="footer-cakephp"><?php echo $this->Html->link(
			$this->Html->image('cake.power.gif', array('alt' => __('CakePHP: the rapid development php framework'), 'border' => '0')),
			'http://www.cakephp.org/',
			array('target' => '_blank', 'escape' => false)
	);
	?>
	</li>
</ul>