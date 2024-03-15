<?php namespace ProcessWire;

/**
 * Setup invoice page edit / administration
 * 
 * This file is included by admin.php file when the page loaded in the 
 * editor is an InvoicePage. This file adds various hooks to the page
 * editor and also adds the /site/templates/admin/invoice-edit.js file. 
 * 
 */

/** @var InvoicePage $page */
/** @var ProcessWire $wire */

if($page->isPastDue()) {
	$page->warning(_('invoice past due'), 'nogroup');
} else if($page->isPaid()) {
	$page->message(_('invoice paid'), 'nogroup');
}

// set defaults for 'qty' and 'rate' inputs in invoice line items
$wire->addHookBefore('InputfieldFloat::render', function(HookEvent $event) {
	$f = $event->object; /** @var InputfieldFloat $f */
	if($f->val()) return;
	if(strpos($f->name, 'rate') === 0) {
		$f->val(settings()->get('rate'));
	} else if(strpos($f->name, 'qty') === 0) {
		$f->val(1.0);
	}
});

// for output of the invoice log
$wire->addHookBefore('InputfieldTextarea(name=invoice_log)::renderValue', function(HookEvent $event) {
	/** @var InputfieldTextarea $f */
	$f = $event->object;
	$event->return = "<pre>" . htmlspecialchars($f->val()) . "</pre>";
	$event->replace = true;
});

// add totals under the line items
$wire->addHookAfter('InputfieldRepeater(name=invoice_items)::render', function(HookEvent $event) {
	$currency = settings()->getCurrencyInfo(false);
	$jsSettings = json_encode([
		'thousandsSeparator' => $currency['thousands'],
		'currencySymbol' => $currency['symbol'],
		'currencyCode' => $currency['code']
	]);
	$jsUrl = urls()->templates . 'admin/invoice-edit.js';
	$event->return .= "
		<div class='uk-grid-small uk-margin-small uk-text-muted' uk-grid>
			<div class='uk-width-expand' uk-leader>" . _('subtotal') . "</div>
			<div id='invoice-subtotal'></div>
		</div>
		<div class='uk-grid-small uk-margin-small uk-text-muted' uk-grid>
			<div class='uk-width-expand' uk-leader>" . _('received') . "</div>
			<div id='invoice-payments'></div>
		</div>
		<div class='uk-grid-small uk-margin-small' uk-grid>
			<div class='uk-width-expand' uk-leader>" . _('total due') . "</div>
			<div id='invoice-total'></div>
		</div>
		<script src='$jsUrl'></script>
		<script>$(function() { InvoiceEdit($jsSettings) });</script>
	";
});

// set data-rate and data-qty attributes on invoice item type select options for use in JS
// note we use 'name^=invoice_item_type' so that we can match in repeaters, which have a suffix
$wire->addHookBefore('InputfieldSelect(name^=invoice_item_type)::render', function(HookEvent $event) {
	$f = $event->object; /** @var InputfieldSelect $f */
	foreach(pages()->find('template=invoice-item-type') as $p) {
		/** @var InvoiceItemTypePage $p*/
		$f->setOptionAttributes($p->id, [
			'data-rate' => $p->rate,
			'data-qty' => $p->qty
		]);
	}
});

/**
 * Called right before an InvoicePage is saved
 * 
 * Adjusts invoice_status and applies invoice_action when appropriate,
 * and updates the invoice log. 
 * 
 */
$wire->addHook('Pages::saveReady(<InvoicePage>)', function(HookEvent $event) {
	$page = $event->arguments(0); /** @var InvoicePage $page */
	$status = $page->getInvoiceStatus();
	$statusPrev = $page->invoice_status;
	$emailTo = '';
	
	if($statusPrev->id != $status->id) {
		if($status->id) {
			$page->invoice_status = $status;
			if($statusPrev->id) {
				$page->addLog(
					_('invoice status changed') . ' - ' . 
					"$statusPrev->title => $status->title"
				);
			} else {
				$page->addLog(_('invoice status set') . " - $status->title" );
			}
		}
	}

	if(count($page->invoice_action)) {
		foreach($page->invoice_action as $action) {
			if($action->value === 'client') {
				$emailTo = $page->client->email; // email client
			} else if($action->value === 'email') {
				$emailTo = $page->email; // email another address
			}
		}
		$page->invoice_action->removeAll(); // reset to no value
		$page->untrackChange('invoice_action');
	}

	if(!$page->invoice_log) {
		$page->addLog(_('invoice created'));
	}

	// set custom variable to indicate email should be sent after save
	$page->setQuietly('_emailTo', $emailTo);
});

/**
 * Called immediately after an InvoicePage is saved
 * 
 * Sends an invoice email when instructed to by the saveReady hook.
 * We send after page saved (rather than before) just to reduce the 
 * chance that some error from the email interferes with the save.
 * 
 */
$wire->addHook('Pages::saved(<InvoicePage>)', function(HookEvent $event) {
	$page = $event->arguments(0); /** @var InvoicePage $page */
	$emailTo = $page->get('_emailTo');
	if($emailTo) sendInvoiceEmail($page, $emailTo);
	$page->setQuietly('_emailTo',  '');
}); 