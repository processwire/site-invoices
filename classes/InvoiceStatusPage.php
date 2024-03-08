<?php namespace ProcessWire;

/**
 * InvoiceStatusPage class to represent pages using template "invoice-status"
 *
 * @property string $title
 * @property string $subtitle CSS class to represent status
 * 
 */
class InvoiceStatusPage extends Page {
	
	/**
	 * Get the label markup to display in the page-list
	 *
	 * @return string
	 *
	 */
	public function getPageListLabel() {
		return ukLabel($this->subtitle, $this->title);
	}
}