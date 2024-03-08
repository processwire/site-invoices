<?php namespace ProcessWire;

/** @var PageArray $items */

$total = 0.0;

?>
<table class='invoice-list uk-table uk-table-divider uk-table-small'>
	<thead>
		<tr>
			<th><?=_('Invoice ID')?></th>
			<th><?=_('Client')?></th>
			<th><?=_('Date')?></th>
			<th><?=_('Subtotal')?></th>
			<th><?=_('Status')?></th>
		</tr>
	</thead>
	<tbody>
		<?php 
		foreach($items as $item): 
			/** @var InvoicePage $item */
			$subtotal = $item->getSubtotal();
			$total += $subtotal;
			?>
			<tr>
				<td><a href="<?=$item->url?>"><?=$item->title?></a></td>
				<td><a class="uk-link-text" href="<?=$item->client->url?>"><?=$item->client->title?></a></td>
				<td><?=$item->date?></td>
				<td><?=price($subtotal)?></td>
				<td><a class="uk-link-text" href="<?=$item->invoice_status->url?>"><?=$item->invoice_status->title?></a></td>	
			</tr>
		<?php endforeach; ?>
		
		<?php if(!$items->count()): ?>
			<tr>
				<td colspan="5" class="uk-text-left"><?=_('no invoices to list')?></td>	
			</tr>	
		<?php endif; ?>
		
		<tr class="separate">
			<td colspan="2">
				<a href="<?=newInvoiceUrl()?>">
					<span uk-icon="icon: plus-circle"></span>
					<?=_('new')?>
				</a>
			</td>
			<th class="uk-text-top"><?=_('Total')?></th>
			<td colspan="2" class="uk-text-left">
				<strong><?=price($total)?></strong>
			</td>
		</tr>
	</tbody>
</table>	