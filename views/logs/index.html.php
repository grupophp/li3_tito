<h2>Chat Logs</h2>
<?php if (empty($logs)): ?>
<ul class="channels">
	<?php foreach ((array)$channels as $channel): ?>
		  <li><?=$this->html->link('#' . $channel, 'bot/' . $channel); ?></li>
	<?php endforeach;?>
</ul>
<?php else: ?>
<ul>
  <?php foreach ((array)$logs as $date): ?>
    <li>
		<?php echo $this->html->link($date, array(
			'plugin' => 'li3_bot', 'controller' => 'logs', 'action' => 'view',
			'args' => array($channel, $date)
		));?>
    </li>
  <?php endforeach;?>
</ul>
<?php endif; ?>
