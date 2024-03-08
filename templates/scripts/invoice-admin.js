/**
 * JS for ProcessPageEdit when editing an 'invoice' page
 * 
 * This file is loaded by the /site/templates/_admin-invoice.php file
 * 
 */

function InvoicePageEdit(options) {
	
	var settings = {
		currencySymbol: '$',
		currencyCode: 'USD',
		thousandsSeparator: ','
	};
	
	$.extend(settings, options);
	
	function calculateLineItem(lineItem) {
		
		var lineQty, lineRate, valueTotal, label, labelText;
		var repeaterItem = lineItem.closest('.InputfieldRepeaterItem');
		
		lineItem.find('input[type=number]').each(function() {
			var input = $(this);
			var name = input.attr('name');
			if(name.indexOf('qty') === 0) {
				lineQty = input;
			} else if(name.indexOf('rate') === 0) {
				lineRate = input;
			}
		});
		if(lineQty.val() && lineRate.val()) {
			valueTotal = parseFloat(lineQty.val()) * parseFloat(lineRate.val());
		} else {
			valueTotal = 0.0;
		}
		label = repeaterItem.find('.InputfieldRepeaterItemLabel');
		labelText = label.text();
		labelText = labelText.substring(0, labelText.indexOf(':') + 1) + ' ' + formatPrice(valueTotal);
		label.html(labelText);
		
		return valueTotal;
	}
	
	function calculateTotals() {
		
		var total;
		var subtotal = 0.0;
		var payments = calculatePayments();
		
		$('#wrap_Inputfield_invoice_items').find('.InputfieldRepeaterItem').each(function() {
			var item = $(this);
			if(item.hasClass('InputfieldRepeaterNewItem')) return;
			if(item.hasClass('InputfieldRepeaterDeletePending')) return;
			subtotal += calculateLineItem(item);
		});
		
		total = subtotal - payments;
		console.log('total=' + total);
		console.log('subtotal=' + subtotal);
		console.log('payments=' + payments);
		$('#invoice-subtotal').text(formatPrice(subtotal));
		$('#invoice-payments').text(formatPrice(payments));
		$('#invoice-total').text(formatPrice(total));
		// $('#Inputfield_total').val(total);
	}
	
	function calculatePayments() {
		var payments = 0.0;
		
		$('#wrap_Inputfield_invoice_payments').find('.InputfieldRepeaterItem').each(function() {
			$(this).find('input[type=number]').each(function() {
				if($(this).attr('name').indexOf('total') === 0) {
					if($(this).val()) payments += parseFloat($(this).val());
				}
			});
		});
		
		return payments;
	}
	
	function formatPrice(n) {
		var s = n.toFixed(2);
		if(n > 999 || n < -999) s = s.replace(/\B(?=(\d{3})+(?!\d))/g, settings.thousandsSeparator);
		return settings.currencySymbol + s + ' ' + settings.currencyCode;
	}
	
	function itemTypeSelectChange(input) {
		var id = input.attr('id');
		var option = input.children('option[selected]'); 
		var qty = parseFloat(option.attr('data-qty')).toFixed(2);
		var rate = parseFloat(option.attr('data-rate')).toFixed(2);
		var descInput = $('#' + id.replace('invoice_item_type', 'title'));
		if(descInput.val().length) return;
		var qtyInput = $('#' + id.replace('invoice_item_type', 'qty'));
		var rateInput = $('#' + id.replace('invoice_item_type', 'rate')); 
		qtyInput.val(qty);
		rateInput.val(rate);
	}
	
	function init() {
		
		$(document).on('input', '.Inputfield_invoice_items input[type=number]', function() {
			var lineItem = $(this).closest('.InputfieldRepeaterItem');
			if(lineItem.length && lineItem.closest('.Inputfield_invoice_items').length) {
				calculateLineItem(lineItem);
			}
		});
		
		$(document).on('change', '.InputfieldRepeater :input', function() {
			var input = $(this);
			if(input.attr('type') === 'number') {
				calculateTotals();
			} else if(input.attr('name').indexOf('invoice_item_type') === 0) {
				itemTypeSelectChange(input);
				calculateTotals();
			}
		});
		
		$(document).on('repeateradd repeaterinsert repeaterdelete repeaterundelete cloned', function() {
			calculateTotals();
		});
		
		calculateTotals();
	}
	
	init();
}

$(document).ready(InvoicePageEdit);