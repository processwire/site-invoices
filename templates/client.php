<?php namespace ProcessWire;

/** @var ClientPage $page  */

?>
<h1 id="headline"><?=$page->parent->title?></h1>

<div id="content">
	<h2><?=$page->title?></h2>
	<?php
	echo files()->render('./parts/invoice-list.php', [
		'items' => $page->getInvoices()
	]);
	?>
</div>	