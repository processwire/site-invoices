<?php namespace ProcessWire;

/** @var InvoicePage $page */
/** @var Pages $pages */
/** @var Fields $fields */

$settings = settings();
$client = $page->client;
$paymentsTotal = $page->getPaymentsTotal();

if(input()->urlSegmentStr === 'email') {
	// render invoice email instead
	include('./parts/invoice-email.php'); 
	return $this->halt(); 
}

?>

<h1 id="headline">
	<?=_('invoice')?>
	<?=ukLabel($page->invoice_status->subtitle, $page->invoice_status->title)?>
</h1>

<div id="content">

	<div id="invoice-meta" class="uk-grid uk-margin" data-uk-grid>
		<div class="uk-width-1-2@s uk-width-1-4@m">
			<h4><?=_('Invoice ID')?></h4>
			<p><?=$page->title?></p>
		</div>
		<div class="uk-width-1-2@s uk-width-1-4@m">
			<h4><?=_('Amount due')?></h4>
			<p><?=price($page->getTotalDue())?></p>
		</div>
		<div class="uk-width-1-2@s uk-width-1-4@m">
			<h4><?=_('Issue date')?></h4>
			<p><?=$page->date?></p>
		</div>
		<div class="uk-width-1-2@s uk-width-1-4@m">
			<h4><?=_('Due date')?></h4>
			<p><?=$page->getDueDate(true)?></p>
		</div>
		<div class="uk-width-1-2@s">
			<h4><?=_('From')?></h4>
			<p>
				<?=$settings->subtitle?><br />
				<?=nl2br($settings->address)?>
				<?php if($settings->website) echo "<br /><a href='$settings->website'>$settings->website</a>";?>
			</p>
		</div>
		<div class="uk-width-1-2@s">
			<h4><?=_('Billed to')?></h4>
			<p>
				<?=$client->title?><br />
				<?=nl2br($client->address)?>
				<?php if($client->website) echo "<br /><a href='$client->website'>$client->website</a>";?>
			</p>
		</div>
	</div>

	<h3><?=_('Summary')?></h3>

	<?php if($page->summary) echo "<p>$page->summary</p>"; ?>

	<div class="uk-overflow-auto">
		<table id="line-items" class="uk-table uk-table-divider uk-table-justify uk-table-small">
			<thead>
			<tr>
				<th><?=_('Qty')?></th>
				<th><?=_('Unit')?></th>
				<th><?=_('Description')?></th>
				<th><?=_('Rate')?></th>
				<th><?=_('Total')?></th>
			</tr>
			</thead>
			<tbody>
			<?php foreach($page->invoice_items as $item): ?>
				<tr>
					<td><?=number_format($item->qty, 2)?></td>
					<td><?=$item->invoice_item_type->title?></td>
					<td><?=$item->title?></td>
					<td><?=price($item->rate)?></td>
					<td><?=price($item->rate * $item->qty)?></td>
				</tr>
			<?php endforeach; ?>
			<tr class="separate">
				<th colspan="4"><?=_('Subtotal')?></th>
				<td><?=price($page->getSubtotal())?></td>
			</tr>
			<tr>
				<th colspan="4"><?=_('Paid')?></th>
				<td><?=price($paymentsTotal)?></td>
			</tr>
			<tr>
				<th colspan="4"><?=_('Total due')?></th>
				<td><strong><?=price($page->getTotalDue())?></strong></td>
			</tr>
			</tbody>
		</table>
	</div>

	<?php if($paymentsTotal): ?>
		<h3><?=_('Payments')?></h3>
		<div class="uk-overflow-auto">
			<table class="uk-table uk-table-divider uk-table-justify uk-table-small">
				<thead>
				<tr>
					<th><?=_('Date')?></th>
					<th><?=_('Description')?></th>
					<th class="uk-text-right"><?=_('Amount')?></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach($page->invoice_payments as $payment): ?>
					<tr>
						<td><?=$payment->date?></td>
						<td><?=$payment->title?></td>
						<td><?=price($payment->total)?></td>
					</tr>
				<?php endforeach; ?>
				<tr class="separate">
					<td colspan="3" class="uk-text-right">
						<strong><?=price($paymentsTotal)?></strong>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</div>

<?php if(input()->urlSegmentStr === 'print'): ?>
<script pw-append="html-body">
	window.print();
</script>	
<?php endif; ?>