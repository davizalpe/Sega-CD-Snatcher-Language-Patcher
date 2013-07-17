<div class="binaryFiles index">	
	<h2><?php echo __('Binary Files Translate'); ?></h2>
	<table>
	<tr>
			<th width="15%"><?php echo $this->Paginator->sort('filename', __('Filename')); ?></th>
			<th width="25%"><?php echo $this->Paginator->sort('description', __('Description')); ?></th>
			<th width="15%"><?php echo $this->Paginator->sort('binary_texts_validated', __('Validated texts')); ?></th>
			<th width="15%"><?php echo __('Testers'); ?></th>			
			<th width="10%"><?php echo __('Reviews'); ?></th>
			<th width="25%" class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
	foreach ($binaryFiles as $binaryFile): ?>
	<tr>
		<td><?php 
				echo $this->Html->link(
					$binaryFile['BinaryFile']['filename'], 
					array('controller' => 'binary_texts', 
						'binary_file_id' => $binaryFile['BinaryFile']['id'])); 
		?></td>
		<td><?php echo h($binaryFile['BinaryFile']['description']); ?></td>
		
		<td><?php 
			echo __('%s of %s', $binaryFile['BinaryFile']['binary_texts_validated'], $binaryFile['BinaryFile']['binary_texts_count'])
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
			}else{
				echo "<span style='color:red;'>".__('Unassigned')."</span>";
			}
			?>
		</td>						
		
		<td class="actions"><?php 
		if($binaryFile['BinaryFile']['reviews_count'] > 0)
		{
			$txt_review = __('%d / %d reviews',
					$binaryFile['BinaryFile']['reviews_count'],
					$binaryFile['BinaryFile']['reviews_validated']
			);			
			echo $this->Html->link($txt_review, array('controller' => 'reviews', 'action' => 'index', 
				'binary_file_id' => $binaryFile['BinaryFile']['id']),
				array('title' => __('%d reviews and %d validated reviews', 
								$binaryFile['BinaryFile']['reviews_count'], $binaryFile['BinaryFile']['reviews_validated'])
				)); 
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