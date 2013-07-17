<?php 
	// Text number chars limit to show for each text
	$text_limit = Configure::read('Snatcher.Config.textlimit');
?>
<div class="binaryTexts index">
	<h2>
	<?php echo $this->Html->link(__('Translate'), array('controller' => 'binary_files', 'action' => 'index')); ?>
	/	
	<?php echo __('Binary Texts from %s', $binaryFile['BinaryFile']['filename']); ?>
	</h2>
	<table>
	<tr>
			<th width="5%"><?php echo $this->Paginator->sort('order', __('Order')); ?></th>
			<th width="15%"><?php echo $this->Paginator->sort('character_id', __('Character')); ?></th>
			<th width="30%"><?php echo $this->Paginator->sort('text', __('Text')); ?></th>
			<th width="20%"><?php echo $this->Paginator->sort('new_text', __('New Text')); ?></th>
			<th width="15%"><?php echo $this->Paginator->sort('modified', __('Modified')); ?></th>
			<th width="5%"><?php echo $this->Paginator->sort('validated', __('Validated')); ?></th>
			<th width="10%" class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<tr class="search">
	<?php echo $this->Form->create('BinaryText', 
			array('url' => array('action' => 'search', 
				'binary_file_id' => $this->params['named']['binary_file_id']), 
	));?>
			<th><?php echo $this->Form->input(
							'Search.order', 
							array('label' => false, 
									'type' => 'text', 
									'size' => 1, 
									'maxlength' => 3));
			?></th>
			<th><?php echo $this->Form->input(
							'Search.character_id', 
							array('label' => false, 
									'type' => 'select', 
									'empty' => __('Search Character', true),
									'onchange' => 'this.form.submit();',  
									));
			?></th>
			<th><?php echo $this->Form->input(
							'Search.text', 
							array('label' => false, 
									'type' => 'text', 
									'size' => 20, 
									'maxlength' => 255, 
									'title' => __('Search Title Text'))); 
			?></th>
			<th><?php echo $this->Form->input(
							'Search.new_text', 
							array('label' => false, 
									'type' => 'text', 
									'size' => 20, 
									'maxlength' => 255, 
									'title' => __('Search Title New Text'))); 
			?></th>
			
			<th></th>
			
			<th class="search">
				<?php echo $this->Form->select(
							'Search.validated', 
							array(1 => __('Yes'), 0 => __('No')), 
									array('label' => false, 
 									'onchange' => 'this.form.submit();',									
									'title' => __('Search Title  Validated')));?></th>
									
			<th class="search"><?php echo $this->Form->submit(__('Search'), array('div' => false)); ?></th>
	<?php echo $this->Form->end();?>			
	</tr>
	<?php
	foreach ($binaryTexts as $binaryText): ?>
	<tr>
		<td><?php echo h($binaryText['BinaryText']['order']); ?></td>
		<td><?php echo h($binaryText['Character']['new_name']);?></td>
		
		<td style="word-wrap:break-word;" title="<?=h($binaryText['BinaryText']['text'])?>">
			<?php 
				echo $this->Text->truncate(h($binaryText['BinaryText']['text']), 
					$text_limit, 
					array('ending' => ' ...', 'exact' => true, 'html' => false));
		?></td>
								
		<td style="word-wrap:break-word;" title="<?=h($binaryText['BinaryText']['new_text'])?>">
			<?php 
				echo $this->Text->truncate(h($binaryText['BinaryText']['new_text']), 
					$text_limit, 
					array('ending' => ' ...', 'exact' => true, 'html' => false)); ?>
		</td>
		
		<td><?php echo $this->Time->format('d/m/Y H:i', $binaryText['BinaryText']['modified']
				, null, $this->Session->read("Auth.User.timezone")); ?></td>
		
		<td><?php echo ($binaryText['BinaryText']['validated'] ? $this->Html->image('test-pass-icon.png', array('alt' => __('Yes', true))) : $this->Html->image('test-fail-icon.png', array('alt' => __('No', true)))); ?></td>
		
		<td class="actions">
		<?php 
		if( empty($binaryText['BinaryText']['fixed_text_id']) )
		{
			echo $this->Html->link(__('Edit'), 
				array_merge($this->passedArgs, 
					array('action' => 'edit', $binaryText['BinaryText']['id']))
			);
		}else{
			echo __('Fixed Text');
		}
		?>
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
		echo $this->Paginator->next(__('next') . '> ', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>