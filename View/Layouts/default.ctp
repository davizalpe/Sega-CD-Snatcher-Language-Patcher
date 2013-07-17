<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo __('Project Head Title:', true); ?>
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');
		
		// Include jQuery library
		echo $this->Html->script(array('jquery.min.js', 'texts_edit.js'), array('inline' => false));		

		echo $this->Html->css(array('cake.generic', 'basic', 'nav'), null, array('inline' => false));
		
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body>
	<header>
		<?php echo $this->element('menu'); ?>
	</header>
	<nav></nav>
	<section>
		<?php echo $this->Session->flash(); ?>
		<?php echo $this->Session->flash('auth'); ?>
		<?php echo $this->fetch('content'); ?>
	</section>
	<aside></aside>
	<footer>
		<?php echo $this->element('footer'); ?>			
	</footer>
	<?php 
		/* for each keyup reajust all textareas */
		$this->Js->get('textarea')->event('keyup', "textAreaAdjust(this);");
		
		/* for each all textareas on load, reajust */
		$this->Js->get('textarea')->each('textAreaAdjust(this);', true);
	?>
	<?php echo $this->Js->writeBuffer(); // Write cached scripts ?>	
	<?php //echo $this->element('sql_dump'); ?>
</body>
</html>