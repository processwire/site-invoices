<?php namespace ProcessWire; ?>

<div id="content">
	<ul>
		<?=page()->children->each(
			"<li><a href='{url}'>{title}</a> ({num_invoices})</li>"
		)?>
	</ul>	
</div>