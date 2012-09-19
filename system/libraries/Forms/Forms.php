<?php

/**
 * @author Gabriel Dodan , gabriel.dodan@gmail.com
 * @copyright Copyright (c) 2012 Gabriel Dodan
 * @license The MIT License http://www.opensource.org/licenses/mit-license.php , see LICENSE.txt file also
 */

/**
 * Class description  
 */
class Forms {
	
	static function urlControls($controls) {
		static $dependencesAdded = FALSE;
		if ( !is_array($controls) ) {
			$controls = explode(",", $controls);
		}
		
		$urls = array();
		foreach ($controls AS &$control) {
			$control	= preg_replace('/^(.*)\.js$/i', '${1}', trim($control)) . ".js";
			$urls[]		= Locator::urlThisLib() . "/js/controls/{$control}";
		}
		
		
		
		if ( !$dependencesAdded ) {
			Sys::importLib("Jquery", TRUE);
			$dependencesAdded	= TRUE;
			return array_merge( 
				array(
					Jquery::urlMainScript(),
					Locator::urlResDir("sys") . "/js/phpdancer.js",
					Locator::urlThisLib() . "/js/Forms.js"
				),
				$urls	
			);
		}
		return $urls;
	}
	
	static function urlTemplate($template) {
		$template	= preg_replace('/^(.*)\.js$/i', '${1}', $template) . ".js";
		return Locator::urlThisLib() . "/js/templates/{$template}";
	}
	
	static function init() {
		static $initialized = false;
		if ( !$initialized ) {
			Sys::importLib("Jquery", TRUE);
			View::jsBatchAdd(
				Jquery::urlMainScript(),
				Locator::urlResDir("sys") . "/js/phpdancer.js",
				Locator::urlThisLib() . "/Forms.js"
			);
			?>
			<script type="text/javascript">
				Pd.Forms.URL_HOME		= '<?php echo Locator::urlHome(); ?>';
				Pd.Forms.PATH_HOME		= '<?php echo Locator::pathHome(); ?>';
				//Pd.Forms.URL_APP_DIR	= '<?php echo Locator::urlAppDir(); ?>';
				//Pd.Forms.URL_SYS_DIR	= '<?php echo Locator::urlSysDir(); ?>';
				Pd.Forms.URL_FORM_LIB	= '<?php echo Locator::urlThisLib(); ?>';
				Pd.Forms.URL_BACKEND	= '<?php echo Locator::thisLibUrlToPage("FormsBackend", "resources") ?>';
			</script>
			<?php
			$initialized = true;
		}
	}
	
}
Forms::init();
?>
