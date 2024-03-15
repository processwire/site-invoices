<?php namespace ProcessWire;

/**
 * Labels
 *
 * We most of our text labels in one file here just to reduce the 
 * number of files needed for translation, and because several
 * labels are used in more than one place. 
 * 
 * To use: call `_('invoice')` which would return the translation
 * for 'invoice', as an example. 
 *
 */
class Labels extends Wire {
	public function label($key) {
		switch(strtolower($key)) {
			// alphabetical A-Z
			case 'add new invoice': return $this->_('Add new invoice');
			case 'all': return $this->_('All');
			case 'amount': return $this->_('Amount');
			case 'amount due': return $this->_('Amount due');
			case 'billed to': return $this->_('Billed to');
			case 'client': return $this->_('Client');
			case 'date': return $this->_('Date');
			case 'description': return $this->_('Description');
			case 'due date': return $this->_('Due date');
			case 'edit': return $this->_('Edit');
			case 'error sending email': return $this->_('Error sending email');
			case 'langcode': return $this->_('en'); // language code for html tag
			case 'from': return $this->_('From');
			case 'invoice': return $this->_('Invoice');
			case 'invoice created': return $this->_('Invoice created');
			case 'invoice id': return $this->_('Invoice ID');
			case 'invoice status changed': return $this->_('Invoice status changed');
			case 'invoice status set': return $this->_('Invoice status set');
			case 'invoices': return $this->_('Invoices');
			case 'issue date': return $this->_('Issue date');
			case 'login': return $this->_('Login');
			case 'login to continue': return $this->_('Login to continue');
			case 'new': return $this->_('New');
			case 'no invoices to list': return $this->_('No invoices to list');
			case 'summary': return $this->_('Summary');
			case 'paid': return $this->_('Paid');
			case 'payments': return $this->_('Payments');
			case 'print': return $this->_('Print');
			case 'qty': return $this->_('Qty'); // Quantity abbreviation
			case 'rate': return $this->_('Rate');
			case 'received': return $this->_('Received');
			case 'sent email': return $this->_('Sent email');
			case 'status': return $this->_('Status');
			case 'subtotal': return $this->_('Subtotal');
			case 'invoice past due': return $this->_('Invoice past due');
			case 'invoice paid': return $this->_('Invoice paid');
			case 'total': return $this->_('Total');
			case 'total due': return $this->_('Total due');
			case 'unit': return $this->_('Unit');
			case 'upon receipt': return $this->_('Upon receipt');
			case 'view email': return $this->_('View email version');
		}
	}
}