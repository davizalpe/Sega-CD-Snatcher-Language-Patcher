<?php 
// Text number chars limit to show for each text
$text_limit = Configure::read('Snatcher.Config.textlimit');
?>
<div class="reviews index">
	<table>
		<tr>
			<th><?php echo __('Review'); ?>
			</th>
			<th><?php echo __('Validated reviews'); ?>
			</th>
			<th><?php echo __('Username'); ?></th>
			<th><?php echo __('Date'); ?></th>
		</tr>
		<?php foreach ($reviews as $review): ?>
		<tr>
			<td style="word-wrap: break-word;" title="<?=h($review['Review']['new_text'])?>">
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
					echo ($review['Review']['validated']
						? $this->Html->image('test-pass-icon.png', array('alt' => __('Yes', true)))
						: $this->Html->image('test-fail-icon.png', array('alt' => __('No', true))));
					?>
				</p>
			</td>

			<td><?php echo $review['User']['username']; ?></td>

			<td><?php 
			echo $this->Time->format('d/m/Y H:i', $review['Review']['modified']
						, null, $this->Session->read("Auth.User.timezone"));
			?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>
