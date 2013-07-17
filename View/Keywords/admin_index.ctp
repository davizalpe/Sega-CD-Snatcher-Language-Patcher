<div class="keywords index">
	<h2>
		<?php echo __('Admin Keywords List'); ?>
	</h2>
	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('Add Keyword'), array('action' => 'add')); ?>
			</li>
		</ul>
	</div>
	<table>
		<tr>
			<th><?php echo $this->Paginator->sort('name', __('Name')); ?>
			</th>
			<th><?php echo $this->Paginator->sort('new_name', __('New Name')); ?>
			</th>
			<th title="<?=__('Translate title')?>"><?php echo __('Translate'); ?>
			</th>
			<th title="<?=__('Restore title')?>"><?php echo __('Restore'); ?>
			</th>
			<th class="actions"><?php echo __('Actions'); ?>
			</th>
		</tr>
		<?php foreach ($keywords as $keyword): ?>
		<tr>
			<td><?php echo h($keyword['Keyword']['name']); ?>
			</td>

			<td><?php echo h($keyword['Keyword']['new_name']); ?>
			</td>

			<td><?php 
			if( $keyword['BinaryText']['translate'] )
			{
				echo $this->Html->link(__('%d matches',
					$keyword['BinaryText']['translate']),
					array(
						'action' => 'view',
						$keyword['Keyword']['id'],
						'Search.text' => $keyword['Keyword']['name'])
					);
			}
			?></td>

			<td><?php 
			if( $keyword['BinaryText']['restore'] )
			{
				echo $this->Html->link(__('%d matches',
					$keyword['BinaryText']['restore']),
					array(
							'action' => 'view',
							$keyword['Keyword']['id'],
							'Search.new_text' => $keyword['Keyword']['new_name'])
					);
			}
			?></td>

			<td class="actions"><?php 
			if( $keyword['BinaryText']['translate'] )
			{
				echo $this->Form->postLink(__('Translate'),
						array_merge($this->passedArgs,
								array('action' => 'translate',
										$keyword['Keyword']['id'])),
						null,
						__('Translate Keyword %s?', $keyword['Keyword']['new_name']));
			}
			?> <?php
			if( $keyword['BinaryText']['restore'] )
			{
				echo $this->Form->postLink(__('Restore'),
						array_merge($this->passedArgs,
								array('action' => 'restore',
										$keyword['Keyword']['id'])),
						null,
						__('Restore Keyword %s?', $keyword['Keyword']['new_name']));
			}
			?> <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $keyword['Keyword']['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $keyword['Keyword']['id']), null, __('Delete %s?', $keyword['Keyword']['name'])); ?>
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
