<div class="characters index">
	<h2><?php echo __('Admin Characters List'); ?></h2>
	<table>
	<tr>
			<th><?php echo $this->Paginator->sort('name', __('Name')); ?></th>
			<th><?php echo $this->Paginator->sort('new_name', __('New Name')); ?></th>
			<th title="<?=__('Code title')?>">
				<?php echo $this->Paginator->sort('hex', __('Code')); ?></th>
			<th title="<?=__('Readonly title')?>">
				<?php echo __('Read Only'); ?></th>
			<th title="<?=__('Translate title')?>">
				<?php echo __('Translate'); ?></th>
			<th title="<?=__('Restore title')?>">
				<?php echo __('Restore'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
	foreach ($characters as $character): ?>
	<tr>
		<td><?php echo h($character['Character']['name']); ?></td>
		
		<td><?php echo h($character['Character']['new_name']); ?></td>
		
		<td><?php echo h($character['Character']['hex']); ?></td>
		
		<td><?php 
		 if( $character['Character']['readonly'] )
		{
		 	echo __('Yes');
		}else{
			echo __('No');
		}
		?></td>
		
		<td><?php 
		if( $character['BinaryText']['translate'] )
		{
			echo $this->Html->link(__('%d matches', 
					$character['BinaryText']['translate']), 
					array(
						'action' => 'view', 
						$character['Character']['id'],
						'Search.text' => $character['Character']['name'])
					);
		}			
		?></td>	
			
		<td><?php 
		if( $character['BinaryText']['restore'] )
		{
			echo $this->Html->link(__('%d matches', 
					$character['BinaryText']['restore']),
					array(
							'action' => 'view',
							$character['Character']['id'],
							'Search.new_text' => $character['Character']['new_name'])
					);
		}
		?></td>
		
		<td class="actions">
			<?php 
			if( $character['BinaryText']['translate'] &&
				($character['Character']['name'] != $character['Character']['new_name']) 
			){
				echo $this->Form->postLink(__('Translate'), 
					array_merge($this->passedArgs, 
						array('action' => 'translate', 
						$character['Character']['id'])), 
					null, 
					__('Translate Character %s?', $character['Character']['new_name'])); 
			}
			
			if( $character['BinaryText']['restore'] ){
				echo $this->Form->postLink(__('Restore'),
					array_merge($this->passedArgs,
							array('action' => 'restore',
									$character['Character']['id'])),
					null,
					__('Restore Character %s?', $character['Character']['new_name']));
			}
			?>
			
			<?php echo $this->Html->link(__('Edit'), array_merge($this->passedArgs, array('action' => 'edit', $character['Character']['id']))); ?>			
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