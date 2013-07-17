<?php 
	// Text number chars limit to show for each text
	$text_limit = Configure::read('Snatcher.Config.textlimit');
?>
<div class="reviews index">
	<h2><?php echo __('Manage Reviews'); ?></h2>
	<table>
	<tr>
			<th><?php echo __('Filename'); ?></th>
			<th><?php echo $this->Paginator->sort('BinaryText.order', __('Order')); ?></th>			
			<th><?php echo $this->Paginator->sort('BinaryText.text', __('Text')); ?></th>
			<th><?php echo $this->Paginator->sort('BinaryText.new_text', __('New Text')); ?></th>
			<th><?php echo $this->Paginator->sort('new_text', __('Review')); ?></th>
			<th><?php echo $this->Paginator->sort('User.username', __('Tester')); ?></th>			
			<th><?php echo $this->Paginator->sort('validated', __('Validated')); ?></th>
			<th><?php echo $this->Paginator->sort('modified', __('Date')); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	
	<tr class="search">
	<?php echo $this->Form->create('Review', 
			array('url' => array('admin' => false, 'action' => 'search', true), 
	));?>	
			<th><?php echo $this->Form->input(
							'Search.BinaryText.binary_file_id', 
							array('label' => false,
									'empty' => '',
									'onchange' => 'this.form.submit();')
							);
			?></th>
			<th><?php echo $this->Form->input(
							'Search.BinaryText.order', 
							array('label' => false, 
									'required' => false,
									'type' => 'text', 
									'size' => 2, 
									'maxlength' => 3, 
									)); 
			?></th>			
			<th><?php echo $this->Form->input(
							'Search.BinaryText.text', 
							array('label' => false,
									'required' => false, 
									'type' => 'text', 
									'size' => 20, 
									'maxlength' => 255, 
									)); 
			?></th>
			<th><?php echo $this->Form->input(
							'Search.BinaryText.new_text', 
							array('label' => false, 
									'type' => 'text', 
									'size' => 20, 
									'maxlength' => 255, 
									)); 
			?></th>
			<th><?php echo $this->Form->input(
							'Search.new_text', 
							array('label' => false, 
									'type' => 'text', 
									'size' => 20, 
									'maxlength' => 255, 
									)); 
			?></th>
			
			<th><?php echo $this->Form->input(
							'Search.user_id', 
							array('label' => false,
									'empty' => '',
									'onchange' => 'this.form.submit();')
							);
			?></th>		
			
			<th class="search">
				<?php echo $this->Form->select(
							'Search.validated', 
							array(1 => __('Yes'), 0 => __('No')), 
							array('label' => false, 
 									'onchange' => 'this.form.submit();'));?>
 			</th>
 			
			<th/>
									
			<th class="search"><?php echo $this->Form->submit(__('Search'), array('div' => false)); ?></th>
	<?php echo $this->Form->end();?>			
	</tr>	
	
	<?php foreach ($reviews as $review): ?>
	<tr>
		<td><?php echo h($review['BinaryText']['BinaryFile']['filename']); ?></td>
		
		<td><?php echo h($review['BinaryText']['order']); ?></td>			

		<td style="word-wrap:break-word;" title="<?=h($review['BinaryText']['text'])?>">
		<?php echo $this->Text->truncate(
					h($review['BinaryText']['text']), 
					$text_limit, 
					array('ending' => ' ...', 'exact' => true, 'html' => false)); 
		?>
		</td>		
		
		<td style="word-wrap:break-word;" title="<?=h($review['BinaryText']['new_text'])?>">
		<?php echo $this->Text->truncate(
					h($review['BinaryText']['new_text']), 
					$text_limit, 
					array('ending' => ' ...', 'exact' => true, 'html' => false)); 
		?>
		</td>
		
		<td style="word-wrap:break-word;" title="<?=h($review['Review']['new_text'])?>">
		<?php echo $this->Text->truncate(
					h($review['Review']['new_text']), 
					$text_limit, 
					array('ending' => ' ...', 'exact' => true, 'html' => false)); 
		?>
		</td>
								
		<td><?php echo h($review['User']['username']); ?></td>

		<td>
		<p align="center">
			<?php echo ($review['Review']['validated'] 
				? $this->Html->image('test-pass-icon.png', array('alt' => __('Yes', true))) 
				: $this->Html->image('test-fail-icon.png', array('alt' => __('No', true)))); ?>
		</p>
		</td>
		
		<td><?php echo $this->Time->format('d/m/Y H:i', $review['Review']['modified']
				, null, $this->Session->read("Auth.User.timezone")); ?></td>
		<td class="actions">
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $review['Review']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $review['Review']['id']), null, __('Delete %s?', __('Review'))); ?>
		</td>
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
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>