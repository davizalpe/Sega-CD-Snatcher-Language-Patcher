<div class="faqs index">
	<h2>
		<?php echo __('Admin Faqs List'); ?>
	</h2>
	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('Add Faq'), array('action' => 'add')); ?>
			</li>
		</ul>
	</div>
	<table>
		<tr>
			<th><?php echo $this->Paginator->sort('order', __('Order')); ?></th>
			<th><?php echo $this->Paginator->sort('question', __('Question')); ?>
			</th>
			<th><?php echo $this->Paginator->sort('answer', __('Answer')); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
		</tr>
		<?php
	foreach ($faqs as $faq): ?>
		<tr>
			<td><?php echo h($faq['Faq']['order']); ?></td>
			<td><?php echo h($faq['Faq']['question']); ?></td>
			<td><?php echo $this->Text->autoLink($faq['Faq']['answer'], array('escape' => false)); ?>
			</td>
			<td class="actions"><?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $faq['Faq']['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $faq['Faq']['id']), null, __('Delete %s?', $faq['Faq']['id'])); ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<p>
		<?php
		echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>
	</p>

	<div class="paging">
		<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
		?>
	</div>
</div>
