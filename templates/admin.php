<?php namespace ProcessWire;

/**
 * admin.php template file
 * 
 * This template file represents pages in ProcessWire’s admin,
 * and this template file is required to use it. 
 * 
 * Admin template just loads the admin application controller, 
 * and admin is just an application built on top of ProcessWire. 
 * 
 * This file is often used to add admin-specific hooks. Such
 * hooks could also go in /site/ready.php, but if the hooks
 * only affect the admin, it's more targeted to put them here.
 * 
 */

/** @var ProcessWire $wire */
/** @var Config $config */

/**
 * Add custom hooks when editing InvoicePage
 *
 * See /site/templates/admin/invoice-edit.php
 *
 */
$wire->addHookAfter('ProcessPageEdit::loadPage', function(HookEvent $event) use($wire) {
	$page = $event->return; 
	if($page instanceof InvoicePage) {
		// note: invoice-edit.php expects $page and $wire API vars
		include('./admin/invoice-edit.php');
	}
});

require($config->paths->core . 'admin.php'); 