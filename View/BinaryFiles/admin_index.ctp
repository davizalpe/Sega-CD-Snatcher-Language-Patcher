<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Download All'), array('admin' => true, 'action' => 'downloadall')); ?></li>
	</ul>
</div>
<div class="binaryFiles index">	
	<h2><?php echo __('Manage Binary Files List'); ?></h2>	
	<table>
	<tr>
			<th width="15%"><?php echo $this->Paginator->sort('filename', __('Filename')); ?></th>
			<th width="20%"><?php echo $this->Paginator->sort('description', __('Description')); ?></th>
			<th width="20%"><?php echo $this->Paginator->sort('Translator.username', __('Translator')); ?></th>			
			<th width="20%"><?php echo __('Testers'); ?></th>
			<th width="15%"><?php echo $this->Paginator->sort('binary_texts_validated', __('Validated')); ?></th>
			<th width="15%" class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
	foreach ($binaryFiles as $binaryFile): ?>
	<tr>
		<td><?php echo $binaryFile['BinaryFile']['filename']; ?></td>
		<td><?php echo h($binaryFile['BinaryFile']['description']); ?></td>
		<td><?php
			if( empty($binaryFile['Translator']['username']) ){
					echo "<span style='color:red;'>".__('Unassigned')."</span>";
			}else{
					echo h($binaryFile['Translator']['username']);
			}?>
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
		
		<td><?php 
			echo __('%s of %s', $binaryFile['BinaryFile']['binary_texts_validated'], $binaryFile['BinaryFile']['binary_texts_count'])
				.' '
				.($binaryFile['BinaryFile']['binary_texts_validated'] == $binaryFile['BinaryFile']['binary_texts_count'] ?
					$this->Html->image('test-pass-icon.png', array('alt' => __('Yes', true))) : '');
			?>
		</td>
		
		<td class="actions">		
			<?php 
			
			echo $this->Html->link(__('Edit'), array('admin' => true, 'action' => 'edit', $binaryFile['BinaryFile']['id'])); 					
		
			echo $this->Html->link(__('Download'), array('admin' => false, 'action' => 'download', $binaryFile['BinaryFile']['id'], true)); 
			
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
		echo $this->Paginator->prev(' < ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' > ', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>