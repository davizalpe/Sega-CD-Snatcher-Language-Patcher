<?php 
// Text number chars limit to show for each text
$text_limit = Configure::read('Snatcher.Config.textlimit');
?>
<div class="reviews index">
	<h2>
		<?php echo $this->Html->link(__('Test'), array('controller' => 'binary_files', 'action' => 'test')); ?>
		/
		<?php echo __('Reviews from %s', $binaryFile['BinaryFile']['filename']); ?>
	</h2>
	<table>
		<tr>
			<th><?php echo $this->Paginator->sort('BinaryText.order', __('Order')); ?>
			</th>
			<th><?php echo $this->Paginator->sort('BinaryText.text', __('Text')); ?>
			</th>
			<th><?php echo $this->Paginator->sort('BinaryText.new_text', __('New Text')); ?>
			</th>
			<th><?php echo $this->Paginator->sort('BinaryText.validated', __('Validated texts')); ?>
			</th>		
			<?php if($search_review):?>
				<th><?php echo $this->Paginator->sort('new_text', __('Review')); ?>
				</th>
				<th><?php echo $this->Paginator->sort('validated', __('Validated reviews')); ?>
				</th>
				<th><?php echo $this->Paginator->sort('modified', __('Date')); ?></th>
			<?php else:?>
				<th><?php echo __('Review'); ?>
				</th>
				<th><?php echo __('Validated reviews'); ?>
				</th>
				<th><?php echo __('Date'); ?></th>
			<?php endif;?>			
			<th class="actions"><?php echo __('Actions'); ?></th>
		</tr>

		<tr class="search">
			<?php echo $this->Form->create('Review',
					array('url' => array('action' => 'search', 'redirect' => 'test',
                                'binary_file_id' => $this->params['named']['binary_file_id'])));
	?>
			<th><?php echo $this->Form->input(
					'Search.BinaryText.order',
					array('label' => false,
								'required' => false,
								'type' => 'text',
								'size' => 2,
								'maxlength' => 3,
								));
			?>
			</th>
			<th><?php echo $this->Form->input(
					'Search.BinaryText.text',
					array('label' => false,
									'required' => false,
									'type' => 'text',
									'size' => 20,
									'maxlength' => 255,
									));
			?>
			</th>
			<th><?php echo $this->Form->input(
					'Search.BinaryText.new_text',
					array('label' => false,
									'required' => false,
									'type' => 'text',
									'size' => 20,
									'maxlength' => 255,
									));
			?>
			</th>

			<th class="search"><?php echo $this->Form->select(
					'Search.BinaryText.validated',
					array(1 => __('Yes'), 0 => __('No')),
					array('label' => false,
 									'onchange' => 'this.form.submit();'));?>
			</th>

			<th />
			
			<th><?php echo $this->Form->input(
					'Search.new_text',
					array('label' => false,
									'type' => 'text',
									'size' => 20,
									'maxlength' => 255,
									));
			?>
			</th>

			<th class="search"><?php echo $this->Form->select(
					'Search.validated',
					array(1 => __('Yes'), 0 => __('No')),
					array('label' => false,
 									'onchange' => 'this.form.submit();'));?>
			</th>

			<th />

			<th class="search"><?php echo $this->Form->submit(__('Search'), array('div' => false)); ?>
			</th>
			<?php echo $this->Form->end();?>
		</tr>

		<?php foreach ($reviews as $review): ?>
		<?php 
		if( isset($review['Review'][0]) )
		{
			$review['Review'] = $review['Review'][0]; 
		}
		?>
		<tr>
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
					array('ending' => ' ...', 'exact' => true, 'html' => false));?>
			</td>

			<td>
				<p align="center">
					<?php echo ($review['BinaryText']['validated']
							? $this->Html->image('test-pass-icon.png', array('alt' => __('Yes', true)))
							: $this->Html->image('test-fail-icon.png', array('alt' => __('No', true))));
					?>
				</p>
			</td>

			<td style="word-wrap:break-word;" <?php if($review['Review']): ?>title="<?=h($review['Review']['new_text'])?>"<?php endif;?>>
			<?php 
			if($review['Review'])
			{
				echo $this->Text->truncate(
					h($review['Review']['new_text']),
					$text_limit,
					array('ending' => ' ...', 'exact' => true, 'html' => false));
			}
			?>
			</td>

			<td>
			<p align="center">
			<?php 
			if($review['Review'])
			{
				echo ($review['Review']['validated']
					? $this->Html->image('test-pass-icon.png', array('alt' => __('Yes', true)))
					: $this->Html->image('test-fail-icon.png', array('alt' => __('No', true))));
			}
			?>
			</p>
			</td>

			<td><?php 
			if($review['Review'])
			{
				echo $this->Time->format('d/m/Y H:i', $review['Review']['modified']
						, null, $this->Session->read("Auth.User.timezone"));
			}?></td>

			<td class="actions"><?php
			// If exists review and is not validated
			if( $review['Review']
				&& !$review['Review']['validated'] 
			){
				echo $this->Html->link(__('Edit'), array_merge(
					$this->passedArgs, array('action' => 'edit', $review['Review']['binary_text_id'])
				));

            	echo $this->Form->postLink(__('Delete'), array_merge(
					$this->passedArgs, array('action' => 'delete', $review['Review']['id'])),
					null,
					__('Delete %s?', $review['Review']['new_text']));

			// If not exists review and binary text it is validated
			}elseif( $review['BinaryText']['validated'] &&
				(	!isset($review['Review']['id']) ||
					$review['Review']['user_id'] != $this->Session->read('Auth.User.id')
				)
			){
				echo $this->Html->link(__('Add review'), array_merge(
						$this->passedArgs, array('action' => 'add', $review['BinaryText']['id'])
				));
			}
			
			if(
				($review['BinaryText']['review_count'] > 1)
				|| (empty($review['Review']) && $review['BinaryText']['review_count'])
			){
				echo $this->Html->link(__('Other Reviews'),
						array('action' => 'other_reviews', $review['BinaryText']['id']));
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
