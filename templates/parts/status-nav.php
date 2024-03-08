<?php namespace ProcessWire;

/**
 * Navigation that displays invoice statuses
 * 
 * Statuses such as: Paid, Pending, Past Due, etc. 
 * 
 */

/** @var Page $page */

$statuses = pages()->get('/settings/invoice-statuses/')->children; 

?>
<ul class="uk-subnav uk-subnav-pill">
	<li<?php if($page->id === 1) echo ' class=uk-active'; ?>>
		<a href="<?=urls()->root?>"><?=_('all')?></a>
	</li>
	<?php foreach($statuses as $item): ?>
		<li<?php if($item->id === $page->id) echo ' class=uk-active'; ?>>
			<a href="<?=$item->url?>"><?=$item->title?></a>
		</li>
	<?php endforeach; ?>
</ul>