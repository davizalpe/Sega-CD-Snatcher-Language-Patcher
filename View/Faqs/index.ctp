<div class="faqs index">
	<h2><?php echo __('FAQ'); ?></h2>

	<?php
	foreach ($faqs as $faq): ?>
		<div class="index">
				<h3><?php echo h($faq['Faq']['question']); ?></h3>
				<span><?php echo $this->Text->autoLink($faq['Faq']['answer'], array('escape' => false)); ?></span>
		</div>
<?php endforeach; ?>
</div>