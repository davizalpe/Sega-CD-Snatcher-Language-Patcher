<div class="attachments index">
	<h2><?php echo __('Admin Attachments List'); ?></h2>
	<table>
	<tr>
			<th><?php echo $this->Paginator->sort('filename', __('Filename')); ?></th>
			<th><?php echo $this->Paginator->sort('description', __('Description')); ?></th>
			<th><?php echo $this->Paginator->sort('User.username', __('Username')); ?></th>					
			<th><?php echo $this->Paginator->sort('created', __('Created')); ?></th>	
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
	foreach ($attachments as $attachment): ?>
	<tr>
		<td><?php echo h($attachment['Attachment']['filename']); ?></td>
		<td><?php echo $this->Text->autoLinkUrls($attachment['Attachment']['description'], array('target' => '_blank')); ?></td>
		<td><?php echo h($attachment['User']['username']); ?></td>			
		<td><?php echo $this->Time->format('d/m/Y H:i', $attachment['Attachment']['created']
				, null, $this->Session->read("Auth.User.timezone")); ?></td>	
		<td class="actions">
			<?php echo $this->Html->link(__('Download'), array('action' => 'download', $attachment['Attachment']['id'])); ?>
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