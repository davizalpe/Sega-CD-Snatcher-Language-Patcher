<div class="binaryFiles index">	
	<h2><?php echo __('Binary Files Test'); ?></h2>
	<table>
	<tr>
			<th width="15%"><?php echo $this->Paginator->sort('filename', __('Filename')); ?></th>
			<th width="20%"><?php echo $this->Paginator->sort('description', __('Description')); ?></th>
			<th width="15%"><?php echo $this->Paginator->sort('Translator.username', __('Translator')); ?></th>
			<th width="15%"><?php echo $this->Paginator->sort('binary_texts_validated', __('Validated texts')); ?></th>						
			<th width="15%"><?php echo __('Testers'); ?></th>
			<th width="15%" class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
	foreach ($binaryFiles as $binaryFile): ?>
	<tr>
		<td><?php 
				echo $this->Html->link(
					$binaryFile['BinaryFile']['filename'], 
					array('controller' => 'reviews', 'action' => 'test',
						'binary_file_id' => $binaryFile['BinaryFile']['id'])); 
		?></td>
		
		<td><?php echo h($binaryFile['BinaryFile']['description']); ?></td>
		
		<td><?php
			if( empty($binaryFile['Translator']['username']) ){
					echo "<span style='color:red;'>".__('Unassigned')."</span>";
			}else{
					echo h($binaryFile['Translator']['username']);
			}?>
		</td>

		<td><?php 
			echo __('%d of %d', $binaryFile['BinaryFile']['binary_texts_validated'], $binaryFile['BinaryFile']['binary_texts_count'])
				.' '
				.($binaryFile['BinaryFile']['binary_texts_validated'] == $binaryFile['BinaryFile']['binary_texts_count'] ?
					$this->Html->image('test-pass-icon.png', array('alt' => __('Yes', true))) : '');
			?>
		</td>		
		
		<td><?php
			if( !empty($binaryFile['Testers']) ){
				$users = array();
				foreach($binaryFile['Testers'] as $user){
					$users[] = $user['username'];
				}
				echo h(implode(", ", $users));
			}
			?>
		</td>
		
		<td class="actions">
			<?php echo $this->Html->link(__('Download'), array('action' => 'download', $binaryFile['BinaryFile']['id'])); ?>			
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
		echo $this->Paginator->prev(' < ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' > ', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>