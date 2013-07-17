<?php
        // Text number chars limit to show for each text
        $text_limit = Configure::read('Snatcher.Config.textlimit');
?>
<div class="fixedTexts index">
	<h2><?php echo __('Manage Fixed Texts'); ?></h2>
	<table>
	<tr>
		<th width="25"><?php echo $this->Paginator->sort('text', __('Text')); ?></th>
		<th width="25%"><?php echo $this->Paginator->sort('new_text', __('New Text')); ?></th>
		<th width="10%"><?php echo $this->Paginator->sort('User.username', __('Username')); ?></th>
		<th width="15%"><?php echo $this->Paginator->sort('modified', __('Date')); ?></th>
		<th width="5%"><?php echo $this->Paginator->sort('validated', 'Validated'); ?></th>
		<th width="10%"><?php echo $this->Paginator->sort('binary_text_count', 'Binary Texts'); ?></th>		
		<th width="10%" colspan="2" class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	
	
	<tr class="search">
	<?php 
		echo $this->Form->create('FixedText', 
			array('url' => array('admin' => true, 'action' => 'search')
	));?>
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
			
			<th><?php echo $this->Form->select(
							'Search.user_id', 
							$users,
							array('label' => false,  
									'onchange' => 'this.form.submit();')
							);
			?></th>			
		
			<th></th>
			
			<th class="search">
				<?php echo $this->Form->select(
							'Search.validated', 
							array(1 => __('Yes'), 0 => __('No')), 
									array('label' => false, 
 									'onchange' => 'this.form.submit();',									
									'title' => __('Search Title  Validated')));?></th>
									
			<th><?php echo $this->Form->input(
				'Search.binary_text_count', 
				array('label' => false, 
						'type' => 'text', 
						'size' => 2, 
						'maxlength' => 6, 
						)); 
			?></th>									
									
			<th class="search"><?php echo $this->Form->submit(__('Search'), array('div' => false)); ?></th>
	<?php echo $this->Form->end();?>			
	</tr>	
	
	
	<?php foreach ($fixedTexts as $fixedText): ?>
	<tr>
		<td style="word-wrap:break-word;" title="<?=h($fixedText['FixedText']['text'])?>">
		<?php echo $this->Text->truncate(
				$fixedText['FixedText']['text'], 
				$text_limit, 
				array('ending' => '...', 'exact' => true, 'html' => false)); 
		?></td>
		
		<td style="word-wrap:break-word;" title="<?=h($fixedText['FixedText']['new_text'])?>">
		<?php echo $this->Text->truncate(
				$fixedText['FixedText']['new_text'], 
				$text_limit, 
				array('ending' => '...', 'exact' => true, 'html' => false)); 
		?></td>
		<td><?php echo h($fixedText['User']['username']); ?></td>
		
		<td><?php echo $this->Time->format('d/m/Y H:i', $fixedText['FixedText']['modified']
				, null, $this->Session->read("Auth.User.timezone")); ?></td>		
		
		<td><?php echo ($fixedText['FixedText']['validated'] ?
				 		$this->Html->image('test-pass-icon.png', array('alt' => __('Yes', true))) : 
				 		$this->Html->image('test-fail-icon.png', array('alt' => __('No', true)))
					); ?>
		</td>
		
		<td class="actions"><?php echo $this->Html->link(
					__('%d Matches', $fixedText['FixedText']['binary_text_count']), 
					array('action' => 'view', $fixedText['FixedText']['id'])); ?>
		</td>
		
		<td class="actions">
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $fixedText['FixedText']['id'])); ?>
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