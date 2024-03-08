<?php namespace ProcessWire;

/**
 * Shared functions for invoice site profile (_func.php)
 * 
 */

require_once(__DIR__ . '/classes/Labels.php');

/**
 * Function to return our /settings/ page (for convenience)
 *
 * @return InvoiceSettingsPage
 *
 */
function settings() {
	static $settings = null;
	if($settings === null) $settings = pages()->get('/settings/');
	return $settings;
}

/**
 * Commonly used translation labels
 * 
 * We keep all the translatable text labels in the 
 * /site/templates/classes/Labels.php file so that they can
 * all be translated (if needed) in one file. 
 *
 * @param string $key
 * @return string
 *
 */
function _($key) {
	static $labels = null;
	if($labels === null) $labels = new Labels();
	return $labels->label($key);
}

/**
 * Format an invoice price
 *
 * @param int|float $price
 * @param bool $formatted 
 * @return string
 *
 */
function price($price, $formatted = true) {
	static $currency = null;
	if($currency === null) $currency = settings()->getCurrencyInfo();
	$price = number_format($price, 2, $currency['decimal'], $currency['thousands']);
	$code = $formatted ? "<small>$currency[code]</small>" : $currency['code'];
	return "$currency[symbol]$price $code";
}

/**
 * Render an icon
 * 
 * @param string $name
 * @param string $title Optional title/tooltip for icon
 * @return string
 * 
 */
function icon($name, $title = '') {
	$attr = "uk-icon='icon: $name'";
	if($title) {
		$title = sanitizer()->entities1($title);
		$attr .= " title='$title' uk-tooltip='$title'";
	}
	return "<span $attr></span>";
}

/**
 * Render Uikit label (uk-label-*)
 *
 * @param string $type One of: default, success, warning, danger
 * @param string $text
 * @return string
 *
 */
function ukLabel($type, $text, $encode = true) {
	if($encode) $text = sanitizer()->entities1($text);
	if(in_array($type, [ 'default', 'success', 'warning', 'danger' ])) {
		return "<span class='uk-label uk-label-$type'>$text</span>";
	} else {
		return "<span class='$type'>$text</span>";
	}
}

/**
 * Render Uikit text (uk-text-*)
 *
 * <https://getuikit.com/docs/text>
 *
 * @param string $type One or more (space separated) of: lead, meta,
 *   small, default, large, light, normal, bold, lighter, bolder,
 *   capitalize, uppercase, lowercase, decoration-none, muted,
 *   emphasis, primary, secondary, success, warning, danger,
 *   background, left, right, center, justify, top, middle, bottom,
 *   baseline, truncate, break, nowrap.
 * @param string $text
 * @param bool $encode Entity encode? (default=true)
 * @return string
 *
 */
function ukText($type, $text, $encode = true) {
	if($encode) $text = sanitizer()->entities1($text);
	$type = 'uk-text-' . str_replace(' ', ' uk-text-', $type);
	return "<span class='$type'>$text</span>";
}

/**
 * Render and send given invoice page to given email address
 * 
 * @param InvoicePage $page
 * @param string $emailTo Email address to send to
 * @return bool True on success
 * 
 */
function sendInvoiceEmail(InvoicePage $page, $emailTo) {

	$settings = settings();
	$emailFrom = $settings->email;
	$http = new WireHttp();
	$url = $page->httpUrl() . 'email';
	$bodyHTML = $http->get($url);
	$result = 0;

	if($bodyHTML && $http->getHttpCode() == 200) {
		$title = $page->getUnformatted('title');
		$company = $settings->getUnformatted('subtitle');
		$result = wireMail()
			->to($emailTo)
			->from($emailFrom)->fromName($company)
			->subject(_('invoice') . " - $title")
			->bodyHTML($bodyHTML)
			->send();
	}

	if($result) {
		$msg = _('sent email') . " - $emailTo";
		$page->message($msg, Notice::noGroup);
	} else {
		$msg = _('error sending email') . " - $url - " . $http->getHttpCode(true);
		$page->error($msg);
	}

	$page->addLog($msg, true);

	return (bool) $result;
}

/**
 * Get URL to add a new invoice
 * 
 * @return string
 * 
 */
function newInvoiceUrl() {
	return config()->urls->admin . 'page/add/?parent_id=1016';
}