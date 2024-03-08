<?php namespace ProcessWire;

/**
 * ClientPage class to represent pages using template "client"
 *
 * @property string $title
 * @property string $address
 * @property string $website
 * @property string $email
 * @property-read int $num_invoices
 * @property-read PageArray|InvoicePage[] $invoices
 *
 */
class ClientPage extends Page {
	
	/**
	 * Get invoices for this client
	 * 
	 * @return PageArray|InvoicePage[]
	 * 
	 */
	function getInvoices() {
		$selector = "template=invoice, client=$this, sort=-date";
		/** @var InvoicePage[]|PageArray $items */
		$items = $this->wire()->pages->find($selector);
		return $items;
	}

	/**
	 * Get number of active/published invoices for this client
	 * 
	 * @return int
	 * 
	 */
	function getNumInvoices() {
		return $this->wire()->pages->count("template=invoice, client=$this"); 
	}

	/**
	 * Extend get() method to handle custom properties
	 * 
	 * @param string $key
	 * @return mixed
	 * 
	 */
	public function get($key) {
		if($key === 'num_invoices') return $this->getNumInvoices();
		if($key === 'invoices') return $this->getInvoices();
		return parent::get($key);
	}

	/**
	 * Get the label markup to display in the admin page-list
	 *
	 * @return string
	 *
	 */
	public function getPageListLabel() {
		$n = $this->getNumInvoices();
		$info = "· $n " . ($n === 1 ? _('invoice') : _('invoices'));
		return sanitizer()->entities1($this->title) . ' ' . ukText('meta', $info);
	}
}