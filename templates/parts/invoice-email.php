<?php namespace ProcessWire;

/**
 * Invoice email
 * 
 * We use different markup for email since email clients are more limited in 
 * what they support and many do not support anything but inline styles. We
 * also use old school style HTML layout tables for email compatibility.
 * 
 */

/** @var ProcessWire $wire */
/** @var InvoicePage $page */
/** @var Pages $pages */
/** @var Fields $fields */
/** @var InvoiceSettingsPage $settings */
/** @var ClientPage $client */
/** @var float $paymentsTotal */

$logoHeight = 120; // max height for logo image in px
$logo = settings()->image;
if($logo) $logo = $logo->maxHeight($logoHeight * 2);
	
require_once('./classes/EmailTemplateHelper.php');

// helper to convert class attributes to inline styles for email
// since many email clients only work with inline styles
$emailTemplate = new EmailTemplateHelper();
$emailTemplate->styles([
	// element or element.class
	'body' => "
		background: #eee;
		font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,'Noto Sans',sans-serif;
		line-height: 22px;
		",
	'div.container' => "
		max-width: 1200px;
		margin: 0 auto;
		background: #fff;
		padding:20px;
		",	
	'h1' => "
		font-size: 50px;
		font-weight: normal;
		",
	'h2' => "
		font-size: 32px;
		font-weight: normal;
		",
	'table' => "
		width: 100%;
		border-collapse: collapse;
		border-spacing: 0;
		margin-bottom: 30px;
		",	
	'tr' => "
		border-top: 1px solid #ddd;
		",
	'tr.separator' => "
		border-top: 1px solid #777;
		",
	'tr.head' => "
		border: none;
		",
	'th' => "
		text-align: left;
		text-transform: uppercase;
		font-weight: normal;
		font-size: 0.875rem;
		color: #999;
		letter-spacing: 1px;
		padding: 10px;
		",	
	'th.right' => "
		text-align: right;
		",
	'td' => "
		padding: 10px;
		vertical-align: top;
		",
	'td.right' => "
		text-align: right;
		",
	'table.meta' => "
		border-spacing: 15px;
		border-collapse: separate;
		padding: 0;
		",
	'td.meta' => "
		padding: 0 0 12px 0;
		",
	'th.meta' =>"
		padding: 0 0 12px 0;
		border-bottom: 1px solid #ddd;
		",
	'td.headline' => "
		vertical-align: middle;
		width: 50%;
		",
	'td.logo' => "
		text-align: right;
		width: 50%;
		",
	'img.logo' => "
		max-height:{$logoHeight}px;
		",		
	'a.link' => "
		color: #999;
		",
]);

$emailTemplate->start(); // start recording HTML output

?>
<!DOCTYPE html>
<html lang="<?=_('langcode')?>">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<title><?=$page->title?></title>
	</head>
	<body>
		<div class="container">
			<table>
				<tr class="head">
					<td class="headline">
						<h1><?=_('invoice')?></h1>
					</td>
					<td class="logo">
						<?php if($logo): ?>
							<a href="<?=$settings->website?>">
								<img class="logo" src="<?=$logo->httpUrl?>" alt="<?=$logo->description?>" />
							</a>
						<?php endif; ?>
					</td>
				</tr>
			</table>
			
			<table class="meta">
				<tr>
					<th class="meta" width="25%"><?=_('Invoice ID')?></th>
					<th class="meta" width="25%"><?=_('Amount due')?></th>
					<th class="meta" width="25%"><?=_('Issue date')?></th>
					<th class="meta" width="25%"><?=_('Due date')?></th>
				</tr>
				<tr>
					<td class="meta"><?=$page->title?></td>
					<td class="meta"><?=price($page->getTotalDue())?></td>
					<td class="meta"><?=$page->date?></td>
					<td class="meta"><?=$page->getDueDate(true)?></td>
				</tr>	
			</table>
			
			<table class="meta">
				<tr>
					<th class="meta"><?=_('From')?></th>
					<th class="meta"><?=_('Billed to')?></th>
				</tr>
				<tr>
					<td class="meta" width="50%">
						<?=$settings->subtitle?><br />
						<?=nl2br($settings->address)?>
						<?php if($settings->website) echo "<br /><a class='link' href='$settings->website'>$settings->website</a>";?>
					</td>
					<td class="meta" width="50%">
						<?=$client->title?><br />
						<?=nl2br($client->address)?>
						<?php if($client->website) echo "<br /><a class='link' href='$client->website'>$client->website</a>";?>
					</td>
				</tr>	
			</table>	
	
			<div style="padding: 15px;">
				<h2 style='font-weight:normal'><?=_('Summary')?></h2>

				<?php if($page->summary) echo "<p>$page->summary</p><br />"; ?>
				
				<table>
					<thead>
						<tr class="head">
							<th><?=_('Qty')?></th>
							<th><?=_('Unit')?></th>
							<th><?=_('Description')?></th>
							<th class="right"><?=_('Rate')?></th>
							<th class="right"><?=_('Total')?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($page->invoice_items as $item): ?>
							<tr>
								<td><?=number_format($item->qty, 2)?></td>
								<td><?=$item->invoice_item_type->title?></td>
								<td><?=$item->title?></td>
								<td class="right"><?=price($item->rate)?></td>
								<td class="right"><?=price($item->rate * $item->qty)?></td>
							</tr>
						<?php endforeach; ?>
						<tr class="separator">
							<th class="right" colspan="4"><?=_('Subtotal')?></th>
							<td class="right"><?=price($page->getSubtotal())?></td>
						</tr>
						<tr>
							<th class="right" colspan="4"><?=_('Paid')?></th>
							<td class="right"><?=price($paymentsTotal)?></td>
						</tr>
						<tr>
							<th class="right" colspan="4"><?=_('Total due')?></th>
							<td class="right">
								<strong><?=price($page->getTotalDue())?></strong>
							</td>
						</tr>
					</tbody>
				</table>
				
				<?php if($paymentsTotal): ?>
					<h2><?=_('Payments')?></h2>
					<table>
						<thead>
							<tr class="head">
								<th><?=_('Date')?></th>
								<th><?=_('Description')?></th>
								<th class="right"><?=_('Amount')?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($page->invoice_payments as $payment): ?>
								<tr>
									<td><?=$payment->date?></td>
									<td><?=$payment->title?></td>
									<td class="right"><?=price($payment->total)?></td>
								</tr>
							<?php endforeach; ?>
							<tr class="separator">
								<td class="right" colspan="3">
									<strong><?=price($paymentsTotal)?></strong>
								</td>
							</tr>
						</tbody>
					</table>
				<?php endif; ?>
			</div>	
		</div>	
	</body>
</html>

<?php

// finish recording email output, process for styles, then send 
$emailTemplate->finish(); 