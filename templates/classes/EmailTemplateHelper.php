<?php namespace ProcessWire;

/**
 * EmailTemplateHelper
 *
 * Simple class to assist with converting HTML for email to use inline styles
 * 
 * Direct output usage:
 * ~~~~~
 * $t = new EmailTemplate();
 * $t->start([
 *   'p' => 'margin: 1em 0; font-size: 16px; line-height: 26px;', 
 *   'h2' => 'font-size: 30px; color: #999;', 
 *   'h2.underline' => 'text-decoration: underline;', // also inherits 'h2'
 * ]); 
 * 
 * ...HTML direct output... 
 *   <html>
 *     <body>
 *        <h2>Hello</h2>
 *        <p>World</p>
 *        <h2 class="underline">Hello World</h2>
 *     </body>
 *   </html>
 * ...end HTML output...
 * 
 * $t->finish(); // sends output directly
 * ~~~~~
 * 
 * Delayed output usage:
 * ~~~~~
 * $styles = [
 *   'p' => 'margin: 1em 0; font-size: 16px; line-height: 26px;',
 *   'h2' => 'font-size: 30px; color: #999;',
 *   'h2.underline' => 'text-decoration: underline;', // also inherits 'h2'
 * ];
 * $html = '
 *   <html>
 *     <body>
 *        <h2>Hello</h2>
 *        <p>World</p>
 *        <h2 class="underline">Hello World</h2>
 *     </body>
 *   </html>
 * ';
 * $t = new EmailTemplate();
 * echo $t->process($html, $styles); 
 * ~~~~~
 * NOTE: when using elements with class attributes, the class attribute must be first.
 * For example, use `<td class="hi" colspan=2">` and NOT `<td colspan="2" class="hi">`
 * 
 * Copyright 2022 / Developed by Ryan Cramer
 * 
 */
class EmailTemplateHelper {
	
	protected $styles = [];
	protected $recording = false;

	/**
	 * Start recording html output
	 * 
	 * @param array $styles Optionally specify styles array
	 * 
	 */
	public function start(array $styles = []) {
		if(!empty($styles)) $this->styles($styles);
		ob_start();
		$this->recording = true;
	}

	/**
	 * Set styles array
	 * 
	 * @param array $styles
	 * 
	 */
	public function styles(array $styles) {
		// merge styles so that "table.meta" also inherits "table" styles (for example)
		foreach($styles as $name => $style) {
			if(strpos($name, '.') === false) continue;
			list($element,) = explode('.', $name, 2);
			if(isset($styles[$element])) $styles[$name] = $styles[$element] . $style;
		}
		$this->styles = $styles;
	}

	/**
	 * Process HTML for styles and return it 
	 * 
	 * @param string $html
	 * @param array $styles Optional if previously set
	 * @return string
	 * 
	 */
	public function process($html, $styles = array()) {
		
		if(!empty($styles)) $this->styles($styles);
	
		$a = [];
		
		foreach($this->styles as $name => $style) {
			$style = trim(str_replace(["\n", "\t", ": "], ['', '', ':'], $style));
			if(strpos($name, '.')) {
				// element with class attribute
				list($name, $class) = explode('.', $name, 2);
				$replace = "<$name style=\"$style\"";
				$a["<$name class=\"$class\""] = $replace;
				$a["<$name class='$class'"] = $replace;
			} else {
				// element with no attributes
				$a["<$name>"] = "<$name style=\"$style\">";
			}
		}
		
		$html = str_replace(array_keys($a), array_values($a), $html); 
		
		return trim($html);
	}

	/**
	 * Finish recording output, process and output it
	 * 
	 * @param bool $send Send to direct output (specify false to return string instead)
	 * @return string
	 * 
	 */
	public function finish($send = true) {
		
		// update elements in rendered markup to use inline styles (safer for email)
		$html = ob_get_contents();
		$html = $this->process($html);
		
		ob_end_clean();
		
		if($send) {
			echo $html;
			return '';
		} 
		
		return $html;
	}
}