<?php namespace ProcessWire;

/**
 * Breadcrumb style top navigation for invoice profile
 * 
 */

/** @var Page $page */

$topnav = [];

foreach($page->parents as $parent) {
	// breadcrumbs
	if($parent->isHidden() || !$parent->viewable()) continue;
	$label = $parent->id === 1 ? _('invoices') : $parent->title;
	if($parent->viewable()) $topnav[$parent->url] = $label;
}

if($page instanceof InvoicePage) { 
	// add some extra nav links when viewing an invoice
	if($page->client->id) $topnav[$page->client->url] = $page->client->title;
	$topnav[$page->url . 'email/'] = icon('mail', _('view email'));
	$topnav[$page->url . 'print/'] = icon('print', _('print'));
}

if($page->editable()) {
	// edit page link
	$topnav[$page->editUrl] = icon('file-edit', _('edit'));
	$topnav[newInvoiceUrl()] = icon('plus-circle', _('add new invoice'));
}

?>

<ul id="topnav" class="uk-breadcrumb">
	<?php 
	foreach($topnav as $url => $label) {
		echo "<li><a href='$url'>$label</a></li>";
	}
	?>
</ul>