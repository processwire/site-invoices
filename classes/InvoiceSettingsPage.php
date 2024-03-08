<?php namespace ProcessWire;

/**
 * InvoiceSettingsPage class represents page using template "invoice-settings"
 *
 * @property string $title
 * @property string $subtitle Company name
 * @property Pageimage|null $image Company logo image
 * @property string $address
 * @property string $website
 * @property string $email
 * @property float $rate
 * @property string $currency
 *
 */
class InvoiceSettingsPage extends Page {

	/**
	 * Get currency information
	 * 
	 * Takes the "currency" field containing JSON currency info and converts it to
	 * an associative array, returning the array of something from it. 
	 * 
	 * @param string|bool $property Optional, one of: code, symbol, decimal, thousands
	 *   or specify boolean false for unformatted array of all values
	 * @return array|string Returns string if given argument, array of all otherwise
	 * 
	 */
	public function getCurrencyInfo($property = '') {
		static $currency = null;
		if(!is_array($currency)) {
			$defaults = ['code' => 'USD', 'symbol' => '$', 'decimal' => '.', 'thousands' => ','];
			$currency = json_decode($this->getUnformatted('currency'), true);
			if(!is_array($currency)) $currency = [];
			$currency = array_merge($defaults, $currency);
			if($property === false) return $currency;
			if($this->of()) {
				foreach($currency as $key => $value) {
					$currency[$key] = sanitizer()->entities1($value);
				}
			}
		}
		if($property) {
			return isset($currency[$property]) ? $currency[$property] : '';
		}
		return $currency;
	}
	
}