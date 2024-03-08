<?php namespace ProcessWire;

/**
 * Initialization file (_init.php)
 *
 * Optional initialization file, called before rendering any template file.
 * This is defined by $config->prependTemplateFile in /site/config.php.
 * Use this to define shared variables, functions, classes, includes, etc. 
 * or include other files that have them. Note that this _init.php file
 * is not automatically included for the admin.php template file, but
 * it is for all others. 
 *
 * In this case we are using this file to limit access to only logged
 * in users, unless the current page template is 'home' or 'invoice'. 
 * If a guest attempts to view anything other than the homepage or
 * a specific invoice page, then we redirect them to the homepage,
 * which displays a login button. 
 * 
 */

if(!user()->isLoggedin() && page()->id != config()->http404PageID) {
	if(!in_array(page()->template->name, [ 'home', 'invoice' ])) {
		session()->redirect(urls()->root);
	}
}
