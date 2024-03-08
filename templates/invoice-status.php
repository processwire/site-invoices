<?php namespace ProcessWire;

/** @var InvoiceStatusPage $page  */

?>
<h1 id='headline'><?=_('invoices')?></h1>

<div id="content">
	<?php 
	include('./parts/status-nav.php');
	echo files()->render('./parts/invoice-list.php', [
		'items' => pages()->get('/invoices/')->children("invoice_status=$page")
	]);
	?>
</div>	