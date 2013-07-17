<?php

$search_name = (isset($this->passedArgs['Search.new_text']) ? 
		$this->passedArgs['Search.new_text'] : 
		$keyword['Keyword']['name']); 

function _getPortion($text, $name)
{
	$text_limit = Configure::read('Snatcher.Config.textlimit');
	
	$len_name = strlen($name);
	$pos = strpos($text, $name);
	
	if($pos === FALSE) $pos = strpos($text, strtoupper($name));
	
	$left = ($pos < 20) ? $pos : 20;
	$right = 30; 
	
	$left_dots = ($pos > $left)   ? '... ' : '';
	$right_dots = ($pos+$len_name < strlen($text)-$right) ? ' ...' : '';
	
	if( (strlen($text) > $text_limit) &&
		$pos !== FALSE 
	)
	{
		return $left_dots .
				mb_substr($text, $pos - $left, $len_name+$right) .
				$right_dots;
	}else{
		return $text;
	}
}
?>
<div class="Keywords view">
<h2><?php  echo __("Keyword's Texts"); ?></h2>
	<table>
	<tr>
		<th width="33%"><?php echo __('Name'); ?></th>
		<th width="33%"><?php echo __('New Name'); ?></th>
	</tr>
	<tr>
		<td><?php echo h($keyword['Keyword']['name']); ?></td>
		<td><?php echo h($keyword['Keyword']['new_name']); ?></td>
	</tr>
	</table>
</div>
<div class="binary_texts index">
	<h2><?php echo __('%s Matches', $total);?></h2>
	<table>
	<tr>
		<th width="25%"><?php echo $this->Paginator->sort('BinaryFile.filename', __('Filename')); ?></th>
		<th width="25%"><?php echo __('Text'); ?></th>
		<th width="25%"><?php echo __('New Text'); ?></th>
		<th width="25%" class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($binary_texts as $binary_text): ?>
	<tr>
		<td><?php echo h($binary_text['BinaryFile']['filename']); ?></td>
		<td><?php echo h(_getPortion($binary_text['BinaryText']['text'], $keyword['Keyword']['name'])); 	?></td>
		<td><?php echo _getPortion($binary_text['BinaryText']['new_text'], $search_name); ?></td>
		<td class="actions"><?php 
			echo $this->Html->link(__('View'), 
				array('controller' => 'binary_texts', 
					'action' => 'index', 
					'Search.binary_file_id' => $binary_text['BinaryFile']['id'], 
					'Search.order' => $binary_text['BinaryText']['order']));
		?></td>
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