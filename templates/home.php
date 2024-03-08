<?php namespace ProcessWire;

// Template file for “home” template used by the homepage

?>

<div id="content">
	<?php 
	if(user()->isLoggedin()) {
		include('./parts/status-nav.php');
		$headline = _('invoices');
		echo files()->render('./parts/invoice-list.php', [ 
			'items' => pages()->get('/invoices/')->children
		]); 
	} else {
		$loginUrl = urls()->admin;
		$loginLabel = _('Login');
		$headline = _('Login to continue');
		echo "<p><a class='uk-button uk-button-primary' href='$loginUrl'>$loginLabel</a></p>";
	}
	?>
</div>

<h1 id="headline"><?=$headline?></h1>	