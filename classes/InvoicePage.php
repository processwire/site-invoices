<?php namespace ProcessWire;

/**
 * InvoicePage to represent pages using template "invoice"
 *
 * @property string $title
 * @property string|int $date
 * @property ClientPage $client
 * @property InvoiceDayPage|NullPage $invoice_days
 * @property Page|NullPage $invoice_status
 * @property SelectableOptionArray $invoice_action
 * @property string $invoice_log
 * @property RepeaterPageArray|InvoiceItemsRepeaterPage[] $invoice_items
 * @property RepeaterPageArray|InvoicePaymentsRepeaterPage[] $invoice_payments
 * @property float $total
 * @property string $summary
 * @property string $email 
 * 
 */
class InvoicePage extends Page {

	/**
	 * Get total of all invoice items
	 * 
	 * @return float
	 * 
	 */
	public function getSubtotal() {
		$subtotal = 0.00;
		foreach($this->invoice_items as $item) {
			$subtotal += (((float) $item->qty) * ((float) $item->rate));
		}
		return $subtotal;
	}

	/**
	 * Get total amount due (invoice total minus payments received)
	 * 
	 * @return float
	 * 
	 */
	public function getTotalDue() {
		$total = $this->getSubtotal() - $this->getPaymentsTotal();
		return $total;
	}

	/**
	 * Get total amount of all received payments
	 * 
	 * @return float
	 * 
	 */
	public function getPaymentsTotal() {
		$total = 0.0;
		foreach($this->invoice_payments as $item) {
			$total += (float) $item->total;
		}
		return $total;
	}

	/**
	 * Is invoice paid in full?
	 * 
	 * @return bool
	 * 
	 */
	public function isPaid() {
		return $this->getTotalDue() <= 0;
	}

	/**
	 * Is invoice past due? 
	 * 
	 * @return bool
	 * 
	 */
	public function isPastDue() {
		$dayPage = $this->invoice_days; 
		if(!$dayPage->id) return false;
		if(!$dayPage->qty) return false;
		$dueDate = $this->getDueDate();
		if(!$dueDate) return false;
		return time() > $dueDate && !$this->isPaid();
	}

	/**
	 * Get due date (timestamp)
	 * 
	 * @return int|false
	 * 
	 */
	public function getDueDate($formatted = false) {
		$invoiceDate = (int) $this->getUnformatted('date');
		$dayPage = $this->invoice_days; 
		if(!$dayPage->id) return false;
		$days = $dayPage->qty;
		if(!$days) return $this->_('Upon receipt');
		$dueDate = $invoiceDate + ($days * 86400);
		if($formatted) {
			$format = $this->wire()->fields->get('date')->get('dateOutputFormat');
			$dueDate = $this->wire()->datetime->formatDate($dueDate, $format); 
		}
		return $dueDate;
	}

	/**
	 * Get number of days remaining till payment is due
	 * 
	 * Returns negative days if payment is past due
	 * 
	 * @return int|false
	 * 
	 */
	public function getDaysRemaining() {
		if($this->isPaid()) return 0;
		$time = time();
		$dueDate = $this->getDueDate();
		if(!$dueDate) return false;
		if($time > $dueDate) {
			// past due, return negative days
			$days = -1 * ($time - $dueDate) / 86400; 
		} else {
			$days = ($dueDate - $time) / 86400;
		}
		return ceil($days);
	}

	/**
	 * Get days invoice was paid in or false if not yet paid
	 * 
	 * @return int|false
	 * 
	 */
	public function getPaidInDays() {
		if(!$this->isPaid()) return false;
		$lastDate = 0;
		foreach($this->invoice_payments as $item) {
			$date = $item->getUnformatted('date');
			if($date > $lastDate) $lastDate = $date;
		}
		$invoiceDate = $this->getUnformatted('date');	
		if($lastDate < $invoiceDate) return 0;
		$days = ($lastDate - $invoiceDate) / 86400; 
		return round($days);
	}
	
	/**
	 * Add an invoice line item (must be followed with a $page->save)
	 * 
	 * @param string $type One of 'hours', 'service' or 'product'
	 * @param string $description Description of item
	 * @param float $qty Quantity
	 * @param float $rate Price/rate
	 * @return InvoiceItemsRepeaterPage
	 * 
	 */
	public function addLineItem($type, $description, $qty, $rate) {
		$items = $this->invoice_items;
		$item = $items->getNewItem();
		$item->item_type = $type;
		$item->title = $description;
		$item->qty = (float) $qty;
		$item->rate = (float) $rate; 
		$this->trackChange('invoice_items');
		return $item;
	}

	/**
	 * Add a payment to the invoice (must be followed with a $page->save)
	 * 
	 * @param int|string $date
	 * @param float $amount
	 * @param string $description
	 * @return InvoicePaymentsRepeaterPage 
	 * 
	 */
	public function addPayment($date, $amount, $description) {
		$items = $this->invoice_payments; 
		$item = $items->getNewItem();
		$item->date = $date; 
		$item->total = (float) $amount; 
		$item->title = $description;
		$this->trackChange('invoice_payments');
		return $item;
	}

	/**
	 * Add a log entry
	 * 
	 * @param string $line
	 * 
	 */
	public function addLog($line, $saveNow = false) {
		$line = str_replace("\n", " ", $line);
		$line = date('Y-m-d H:i:s') . " $line";
		$log = trim($this->getUnformatted('invoice_log') . "\n$line");
		$this->invoice_log = $log;
		$this->trackChange('invoice_log');
		if($saveNow && $this->id) $this->setAndSave('invoice_log', $log);
	}

	/**
	 * Get invoice status page
	 * 
	 * @return NullPage|Page
	 * 
	 */
	public function getInvoiceStatus() {
		$total = $this->getTotalDue();
		$this->total = $total;
		
		if($this->isUnpublished()) {
			$statusName = 'draft';
		} else if($total > 0) {
			if($this->isPastDue()) {
				$statusName = 'past-due';
			} else {
				$statusName = 'pending';
			}
		} else {
			$statusName = 'paid';
		}

		$status = $this->wire()->pages->get("/settings/invoice-statuses/$statusName"); 
		
		return $status->id ? $status : $this->invoice_status;
	}
	
	/**
	 * Get the label markup to display in the admin page-list
	 * 
	 * @return string
	 * 
	 */
	public function getPageListLabel() {
		$status = $this->getInvoiceStatus();
		if(!$status->id) return ''; // makes it use default label
		static $icons = [ 
			'paid' => 'star', 
			'past-due' => 'ban', 
			'pending' => 'star-o',
			'partial' => 'star-half-o', 
			'draft' => 'star-o' 
		];
		if($status->name === 'pending' && $this->invoice_payments->count()) {
			$icon = $icons['partial'];
		} else {
			$icon = isset($icons[$status->name]) ? $icons[$status->name] : $icons['pending'];
		}
		$this->template->setIcon($icon);
		$labelType = sanitizer()->pageName($status->get('subtitle'));
		$title = sanitizer()->entities1($this->title);
		$client = $this->client;
		$client = $client->id ? sanitizer()->entities1($client->title) : '';
		$subtotal = $this->getSubtotal();
		$subtotal = $subtotal > 0 ? '(' . price($subtotal, false) . ')' : '';
		if(wireInstanceOf($this->wire()->adminTheme, 'AdminThemeUikit')) {
			return
				ukLabel($labelType, $status->title) . " $title " .
				ukText('meta', "· $client $subtotal", false);
		} else {
			return "$title · $client $subtotal - $status->title";
		}
	}
}