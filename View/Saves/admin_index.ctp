<div class="saves index">
	<h2>
		<?php echo __('Admin Saves List'); ?>
	</h2>
	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('Add Safe'), array('admin' => false, 'action' => 'add', true)); ?>
			</li>
		</ul>
	</div>
	<table>
		<tr>
			<th width="5%"><?php echo $this->Paginator->sort('act', __('Act')); ?>
			</th>
			<th width="5%"><?php echo $this->Paginator->sort('BinaryFile.filename', __('BinaryFile')); ?>
			</th>
			<th width="5%"><?php echo $this->Paginator->sort('slot', __('Slot')); ?>
			</th>
			<th width="10%"><?php echo $this->Paginator->sort('User.username', __('Username')); ?>
			</th>
			<th width="45%"><?php echo $this->Paginator->sort('description', __('Description')); ?>
			</th>
			<th width="15%"><?php echo $this->Paginator->sort('created', __('Date')); ?>
			</th>
			<th width="15%" class="actions"><?php echo __('Actions'); ?>
			</th>
		</tr>

		<tr>
		
		
		<tr class="search">
			<?php echo $this->Form->create('Safe', array('url' => array('admin' => false, 'action' => 'search', true)));?>

			<th><?php 
			echo $this->Form->input('Search.act', array(
				'required' => false,
				'label' => false,
				'type' => 'select',
				'empty' => '',
				'onchange' => 'this.form.submit();',
				'options' => array(1 => 1, 2 => 2, 3 => 3),
		));

		?></th>
			<th><?php echo $this->Form->input(
					'Search.binary_file_id',
					array('label' => false,
						'required' => false,
						'empty' => '',
						'onchange' => 'this.form.submit();')
				);
			?></th>

			<th><?php echo $this->Form->input(
					'Search.slot', 
					array(
					'label' => false,
					'required' => false,
					'type' => 'select',
					'empty' => '',
					'options' => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9),
					'onchange' => 'this.form.submit();')
			);
			?></th>


			<th><?php echo $this->Form->input(
					'Search.user_id',
					array('label' => false,
									'empty' => '',
									'onchange' => 'this.form.submit();')
							);
			?></th>

			<th><?php echo $this->Form->input(
					'Search.description',
					array('label' => false,
									'type' => 'text',
									'size' => 20,
									'maxlength' => 255));
			?>
			</th>

			<th></th>

			<th class="search"><?php echo $this->Form->submit(__('Search'), array('div' => false)); ?>
			</th>
			<?php echo $this->Form->end();?>
		</tr>

		<?php
	foreach ($saves as $safe): ?>
		<tr>
			<td><?php echo h($safe['Safe']['act']); ?>
			</td>
			<td><?php echo h($safe['BinaryFile']['filename']); ?>
			</td>
			<td><?php echo h($safe['Safe']['slot']); ?>
			</td>
			<td><?php echo h($safe['User']['username']); ?>
			</td>
			<td><?php echo h($safe['Safe']['description']); ?>
			</td>
			<td><?php echo $this->Time->format('d/m/Y H:i', $safe['Safe']['modified']
					, null, $this->Session->read("Auth.User.timezone")); ?>
			</td>
			<td class="actions">
				<?php echo $this->Html->link(__('Download'), array('admin' => false, 'action' => 'download', $safe['Safe']['id'], true)); ?>
				<?php echo $this->Html->link(__('Download Original'), array('admin' => false, 'action' => 'download', $safe['Safe']['id'], true, true)); ?>
				<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $safe['Safe']['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $safe['Safe']['id']), null, __('Delete Safe?')); ?>
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