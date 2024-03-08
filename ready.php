<?php namespace ProcessWire;

/**
 * ProcessWire Bootstrap API Ready
 * 
 * This ready.php file is called during ProcessWire bootstrap initialization process.
 * This occurs after the current page has been determined and the API is fully ready
 * to use, but before the current page has started rendering. This file receives a
 * copy of all ProcessWire API variables.
 * 
 * This file is commonly used for adding hooks, especially those that might be used
 * both on the front-end and admin sides of the site. Hooks only used on the front
 * end of a site might better be placed in /site/templates/_init.php, while hooks
 * that only affect the admin side of the site might better be placed in the
 * /site/templates/admin.php file. Though when in doubt, this ready.php file is
 * a generally a good place. 
 *
 */

if(!defined("PROCESSWIRE")) die();

/** @var ProcessWire $wire */

// include our custom shared functions used in front-end and admin
require_once(__DIR__ . '/templates/_func.php');