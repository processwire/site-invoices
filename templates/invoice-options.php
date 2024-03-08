<?php namespace ProcessWire;
?>
<div id="content">
	<ul>
		<?=page()->children->find('viewable=1')->each("<li><a href='{url}'>{title}</a></li>")?>
	</ul>
</div>	