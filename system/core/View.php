<?php
/**
 *
 * @author Gabriel Dodan , gabriel.dodan@gmail.com
 * @copyright Copyright (c) 2012 Gabriel Dodan
 * @license The MIT License http://www.opensource.org/licenses/mit-license.php , see LICENSE.txt file also
 */

class ViewBase {
	
	/**
	 * The page's output, all content
	 * @var string
	 */
	public		static $pageContent     	= '';

	protected	static $pageMainContent		= '';
	
	
	protected	static $pageMainContentFile	= '';
	
	
	/**
	 * List of CSS files added into CSS files placeholder 
	 * @var array
	 */
	protected	static $cssFiles   			= array();
	
	/**
	 * List of JS files added into JS files placeholder
	 * @var array
	 */
	protected	static $jsFiles    			= array();
	
	/**
	 * List of CSS files added into CSS batch placeholder
	 * @var array
	 */
	protected	static $cssBatch   			= array();
	
	/**
	 * List of JS files added into JS batch placeholder 
	 * @var array
	 */
	protected	static $jsBatch    			= array();
	
	/**
	 * Placeholders storage. key => val array, key=placeholder name, val=placeholder content
	 * @var array
	 */
	protected   static $placeholders		= array();
	
	final function __construct() {
		Sys::errorHandler("Page instantiation not allowed");
	}
	
	static function renderComp($component, $componentParams = array(), $returnContent = FALSE) {
		$arr = explode("->", $component);
		if ( count($arr) == 2 ) {
			// component as object method
			static $instances = array();
			
			$compsFile	= "{$arr[0]}Components.php";
			$compsClass = substr( $arr[0], (($pos = strrpos($arr[0], "/")) ? $pos + 1 : 0) ) . "Components";
			$method		= $arr[1];
			$file		= Locator::pathAppDir() . "/components/{$compsFile}";
			
			if ( !array_key_exists($compsFile, $instances) ) {
				if ( is_file($file) ) {
					require_once $file;
					$instances[$compsFile] =  new $compsClass();
				}
				else {
					Sys::errorHandler("renderComp(), Component '{$component}' not found");
					return NULL;
				}
			}
			
			
			$args = array_values($componentParams);
			
			if ( $returnContent ) {
				ob_start(); ob_implicit_flush(0);
				call_user_func_array(array($instances[$compsFile], $method), $args);
				return ob_get_clean();
			}
			else {
				call_user_func_array(array($instances[$compsFile], $method), $args);
			}
		}
		else {
			// component as script
			static $cachedComps = array();
			$compFile	= "{$component}Component.php";
			$file		= Locator::pathAppDir() . "/components/{$compFile}";
			
			// check the comp file exists
			if ( !in_array($compFile, $cachedComps) ) {
				if ( is_file($file) ) {
					$cachedComps[] = $compFile;
				}
				else {
					Sys::errorHandler("renderComp(), Component '{$component}' not found");
					return NULL;
				}
			}
	
			/* add params into component scope */
			foreach ($componentParams AS $key => $val) {
				${$key} = $val;
			}
			if ( $returnContent ) {
				ob_start(); ob_implicit_flush(0);
				require($file);
				return ob_get_clean();
			}
			else {
				require($file);
			}
		}
	
	}
	
	static function renderLayout($layout, $mainContent, $returnContent = FALSE, $mainContentIsFile = TRUE) {
		$layoutPath	= Locator::pathAppDir() . "/layouts/{$layout}.php";
		
		if ( $mainContentIsFile ) {
			View::$pageMainContentFile = $mainContent;
		}
		else {
			View::$pageMainContent = $mainContent;
		}
		
		if ( is_file($layoutPath) ) {
			if ( $returnContent ) {
				ob_start(); ob_implicit_flush(0);
				require $layoutPath;
				return ob_get_clean();
			}
			else {
				require $layoutPath;
			}
		}
		else {
			Sys::errorHandler("renderLayout(), Layout '{$layout}' not found", true);
		}
	}
	
	static function renderMainContent() {
		HooksManager::runHookActions(Workflow::HOOK_MAIN_CONTENT_START);
		
		if ( View::$pageMainContentFile ) {
			$path = Locator::pathAppDir() . "/main-contents/" . View::$pageMainContentFile . ".php";
			if ( is_file($path) ) {
				require $path;
			}
			else {
				Sys::errorHandler("Main content file '" . View::$pageMainContentFile . "' not found", true);
			}
		}
		else {
			echo View::$pageMainContent;
		}
		
		HooksManager::runHookActions(Workflow::HOOK_MAIN_CONTENT_END);
		
	}
	
	
	/**
	 * Registers a placeholder
	 * @param string $name
	 */
	static function placeholder($name) {
		View::$placeholders[$name] = '';
		echo '<!-- {placeholder-' . $name . '} -->';
	}
	
	/**
	 * Appends content to a placeholder
	 * @param string $name
	 * @param string $content
	 */
	static function placeholderAppend($name, $content) {
		View::$placeholders[$name] .= $content; 
	}
	
	/**
	 * Prepends content to a placeholder
	 * @param string $name
	 * @param string $content
	 */
	static function placeholderPrepend($name, $content) {
		View::$placeholders[$name] = $content . View::$placeholders[$name];
	}
	
	/**
	 * Sets content for a placeholder
	 * @param string $name
	 * @param string $content
	 */
	static function placeholderSet($name, $content) {
		View::$placeholders[$name] = $content;
	}
	
	/**
	 * Replace all placeholders with the actual placeholder content, it is called by @see Workflow after all content has been generated.
	 */
	static function replacePlaceholders(){
		
		// set JS batch placeholder
		View::placeholderSet("__jsbatch__", View::createTagForBatch('js'));
		
		// set CSS batch placeholder
		View::placeholderSet("__cssbatch__", View::createTagForBatch('css'));
		
		
		//echo self::$output ; print_r(self::$placeholders);exit;
		
		$search  = array();
		$replace = array();
		foreach ( View::$placeholders AS $name => $phContent ) {
			$search[]  = '<!-- {placeholder-' . $name . '} -->';
			$replace[] = $phContent; 
		}
		
		
		//View::$output = preg_replace($search, $replace, View::$output, 1);
		View::$pageContent = str_replace($search, $replace, View::$pageContent);
	}
	
	/*
	static function placeholder($name) {
		$content = ob_get_clean();
		if ( key_exists($name, View::$placeholders) ) {
			View::$placeholders[$name] = array( $content, View::$placeholders[1] );	
		}
		else {
			View::$placeholders[$name] = array( $content, '' );
		}
		
		ob_start();
	}
	static function placeholderAppend($name, $content) {
		View::$placeholders[$name][1] .= $content; 
	}
	static function placeholderPrepend($name, $content) {
		View::$placeholders[$name][1] = $content . View::$placeholders[$name][1];
	}
	static function placeholderSet($name, $content) {
		View::$placeholders[$name][1] = $content;
	}
	static function replacePlaceholders(){
		
		// set JS batch placeholder
		View::placeholderSet("__jsbatch__", View::createTagForBatch('js'));
		
		// set CSS batch placeholder
		View::placeholderSet("__cssbatch__", View::createTagForBatch('css'));
		
		$placeHoldersContent = '';
		foreach (View::$placeholders AS $name => $data) {
			$placeHoldersContent .= @$data[0] . @$data[1];
		}
		View::$output = $placeHoldersContent . View::$output;
	}
	*/
	
	/**
	 * Placeholder for title
	 */
	static function pageTitle() {
		View::placeholder("__pagetitle__");
	}
	
	/**
	 * Set title placeholder
	 * @param string $value
	 */
	static function setPageTitle($value) {
		View::placeholderSet("__pagetitle__", $value);
	}
	
	/**
	 * Placeholder for description
	 */
	static function pageDescription() {
		View::placeholder("__pagederscription__");
	}
	
	/**
	 * Set description placeholder
	 * @param string $value
	 */
	static function setPageDescription($value) {
		View::placeholderSet("__pagederscription__", $value);
	}
	
	/**
	 * Placeholder for keywords
	 */
	static function pageKeywords() {
		View::placeholder("__keywords__");
	}
	
	/**
	 * Set keywords placeholder
	 * @param string $value
	 */
	static function setPageKeywords($value) {
		View::placeholderSet("__keywords__", $value);
	}
	
	
	/**
	 * Create either <link> or <script> tag. 
	 * @param string $batch either 'js' (for JS batch) or 'css' (for CSS batch)
	 */
	static protected function createTagForBatch($batch) {
    	$replace = array(
    			"/resources/jquery/ui/"	=>	'{1}',
    			"/resources/jquery/"	=>	'{2}',
    			"/resources/css/"		=>	'{3}',
    			"/resources/js/"		=>	'{4}'
    	);
		
		$urls  = ($batch == 'js' ? View::$jsBatch : View::$cssBatch);
		if ( !count($urls) ) {
			return '';
		}
		$files = array(); 
	    foreach ($urls AS $url) {
	    	$files[] = str_replace(Locator::urlHome(), "", $url); 
    	}
    	
    	
    	
		if ( $batch == 'js' ) {
    		$tagUrl = Locator::urlSysDir() . "/batchJS.php?f=" . strtr(implode(",", $files), $replace);
    		return "<script type=\"text/javascript\" src=\"" . $tagUrl . "\"></script>\n";
			
		}
		else if ( $batch == 'css' ) {
    		$tagUrl = Locator::urlSysDir() . "/batchCSS.php?f=" . strtr(implode(",", $files), $replace);
    		return "<link type=\"text/css\" rel=\"stylesheet\" href=\"" . $tagUrl . "\" />\n";
		}
	}
	
	/**
	 * Placeholder for CSS batch
	 */
	static function cssBatch() {
		View::placeholder("__cssbatch__");
	}
	
	static function cssBatchAdd() {
		$urls	= array();
		$args	= func_get_args();
		foreach ($args AS $arg) {
			if (is_array($arg)) {
				foreach ($arg AS $url) {
					$urls[]	= str_replace(Locator::urlHome(), "", $url);
				}
			}
			else {
				$urls[]	= str_replace(Locator::urlHome(), "", $arg);
			}
		}
    	View::$cssBatch = array_unique( array_merge(View::$cssBatch, $urls) );
	}
	
	/**
	 * Placeholder for JS batch
	 */
	static function jsBatch() {
		View::placeholder("__jsbatch__");
	}
	
	static function jsBatchAdd() {
		$urls	= array();
		$args	= func_get_args();
		foreach ($args AS $arg) {
			if (is_array($arg)) {
				foreach ($arg AS $url) {
					$urls[]	= str_replace(Locator::urlHome(), "", $url);
				}
			}
			else {
				$urls[]	= str_replace(Locator::urlHome(), "", $arg);
			}
		}
    	View::$jsBatch = array_unique( array_merge(View::$jsBatch, $urls) );
	}
	
	/**
	 * Placeholder for CSS files
	 */
	static function cssFiles() {
		View::placeholder("__cssfiles__");
	}
	
	/**
	 * Adds a file into CSS files placeholder
	 * @param string $url CSS file URL
	 * @param array $attrs attrName=>attrVal array with attributes for <link> tag except "type" and "href"  
	 */
	static function cssFilesAdd($url, $attrs=array()) {
    	foreach (View::$cssFiles AS $f) {
    		if ( $url == $f['url'] ) {
    			return;
    		}
    	}
    	View::$cssFiles[] = array(
    		'url'	=> $url,
    		'attrs'	=> $attrs
    	);
    	
    	$tag           = "<link";
    	$attrs['type'] = "text/css";
    	$attrs['href'] = $url;
    	$attrs['rel']  = @($attrs['rel'] ? $attrs['rel'] : "stylesheet");
    	foreach ($attrs AS $name => $val) {
    		$tag .= " {$name}=\"{$val}\"";
    	}
    	$tag .= " />";
    	View::placeholderAppend("__cssfiles__", $tag . "\n");
	}
	
	/**
	 * Placeholder for JS files
	 */
	static function jsFiles() {
		View::placeholder("__jsfiles__");
	}
	
	/**
	 * Adds a file into JS files placeholder
	 * @param string $url JS file URL
	 * @param array $attrs attrName=>attrVal array with attributes for <script> tag except "type" and "src"
	 */
	static function jsFilesAdd($url, $attrs=array()) {
    	foreach (View::$jsFiles AS $f) {
    		if ( $url == $f['url'] ) {
    			return;
    		}
    	}
    	View::$jsFiles[] = array(
    		'url'	=> $url,
    		'attrs'	=> $attrs
    	);
    	
    	
    	$tag           = "<script";
    	$attrs['type'] = "text/javascript";
    	$attrs['src']  = $url;
    	foreach ($attrs AS $name => $val) {
    		$tag .= " {$name}=\"{$val}\"";
    	}
    	$tag .= "></script>";
    	
    	View::placeholderAppend("__jsfiles__", $tag . "\n");
	}
    
    
}

/* extension mechanism */
$extFile = ltrim(APP_DIR . "/fw-core-extension/View.php");
if ( is_file($extFile) ) {
	require_once($extFile);
}
if ( !class_exists('View') ) {
	class View extends ViewBase {
	}
}
?>