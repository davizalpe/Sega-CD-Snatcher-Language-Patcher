<div class="FixedTexts view">
<h2><?php  echo __('Fixed Text'); ?></h2>
	<table>
	<tr>
		<th width="33%"><?php echo __('Text'); ?></th>
		<th width="33%"><?php echo __('New Text'); ?></th>
		<th width="34%" class="actions"><?php echo __('Validated'); ?></th>
	</tr>
	<tr>
		<td><?php echo h($fixedText['FixedText']['text']); ?></td>
		<td><?php echo h($fixedText['FixedText']['new_text']); ?></td>
		<td><p align="center"><?php echo ($fixedText['FixedText']['validated'] ?
				 		$this->Html->image('test-pass-icon.png', array('alt' => __('Yes', true))) : 
				 		$this->Html->image('test-fail-icon.png', array('alt' => __('No', true)))
					); ?></p></td>
	</tr>
	</table>
</div>
<div class="binary_texts index">
	<h2><?php echo __('%s Matches', $total);?></h2>
	<table>
	<tr>
		<th width="33%"><?php echo $this->Paginator->sort('BinaryFile.filename', __('Filename')); ?></th>
		<th width="33%"><?php echo __('Matches'); ?></th>
		<th width="34%" class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($binary_texts as $binary_text): ?>
	<tr>
		<td><?php echo h($binary_text['BinaryFile']['filename']); ?></td>
		<td><?php echo h($binary_text[0]['total']); ?></td>
		<td class="actions"><?php 
			echo $this->Html->link(__('View'), 
				array('controller' => 'binary_texts', 
					'action' => 'index', 
					'Search.binary_file_id' => $binary_text['BinaryFile']['id'], 
					'Search.fixed_text_id' => $fixedText['FixedText']['id']));
		?></td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>

	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . '> ', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>