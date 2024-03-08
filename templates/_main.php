<?php namespace ProcessWire;

/**
 * Main HTML document file (_main.php)
 * 
 * This optional main file is called after rendering page’s template file. 
 * This is defined by $config->appendTemplateFile in /site/config.php, and
 * is typically used to define and output markup common among most pages.
 *  	
 * When the Markup Regions feature is used, template files can prepend, append,
 * replace or delete any element defined here that has an "id" attribute. 
 * https://processwire.com/docs/front-end/output/markup-regions/
 * 
 */

// Using uikit version bundled with ProcessWire (feel free to change)
$ukUrl = urls()->get('AdminThemeUikit') . 'uikit/dist/';

?><!DOCTYPE html>
<html lang="<?=_('langcode')?>">
	<head id="html-head">
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title><?php echo page()->title; ?></title>
		<link href="<?=$ukUrl?>css/uikit.min.css" rel="stylesheet" />
		<link href="<?=urls()->templates?>styles/main.css" rel="stylesheet" />
		<script src="<?=$ukUrl?>js/uikit.min.js"></script>
		<script src="<?=$ukUrl?>js/uikit-icons.min.js"></script>
	</head>
	<body id="html-body">
		<div id="container" class="uk-container uk-container-large">
			<div id="masthead" class="uk-clearfix uk-margin-bottom">
				<?php include('./parts/logo.php')?>
				<?php include('./parts/topnav.php'); ?>
				<h1 id="headline"><?=page()->title?></h1>
			</div>	
			<div id="content">
				<!-- each template file populates this #content div -->
			</div>
		</div>
	</body>
 </html>