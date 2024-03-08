<?php namespace ProcessWire;

if(!defined('PROCESSWIRE')) die();

/**
 * /site/install/finish.php
 * 
 * Called after installer is finished
 * 
 * Update the invoice dates to be current dates so that it doesn't
 * appear that the example invoices are really old, down the road. 
 * 
 */

/** @var WireDatabasePDO $database */

$dates = [
	'2024-02-28 00:00:00' => '-10 DAYS',
	'2024-03-01 00:00:00' => '-8 DAYS',
	'2024-03-03 00:00:00' => '-5 DAYS',
	'2024-03-08 00:00:00' => '-1 DAY',
];

foreach($dates as $oldDate => $newDate) {
	$newDate = date('Y-m-d', strtotime($newDate)) . ' 00:00:00';
	$sql = 'UPDATE field_date SET `data`=:newDate WHERE `data`=:oldDate';
	$query = $database->prepare($sql);
	$query->bindValue(':newDate', $newDate);
	$query->bindValue(':oldDate', $oldDate);
	$query->execute();
}
