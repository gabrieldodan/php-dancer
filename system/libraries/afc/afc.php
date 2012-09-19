<?php
/**
 * 
 * AJAX function call
 * @author Gabriel Dodan
 *
 */
class afc {
	
	private static $sessKey = "__afc_functions__";
	
	
	static function registerFunction($funcName) {
		$functions = Sess::get(self::$sessKey);
		if ( !$functions ) {
			Sess::set(self::$sessKey, array());
			$functions = array();
		}
		
		$reflector   = new ReflectionFunction($funcName);
		
		$fileContent = file($reflector->getFileName());
		$funcDef     = implode( "", array_slice($fileContent, $reflector->getStartLine()-1, ($reflector->getEndLine()-$reflector->getStartLine())+1) );
		$functions[$funcName] = $funcDef;
		
		Sess::set(self::$sessKey, $functions);
	}
	
	static function getFunction($funcName) {
		$functions = Sess::get(self::$sessKey);
		return @$functions[$funcName];
	}
}

if ( !@$fromBackend ) { // not called by frontend script

	// add jQuery and phpdancer, afc plugins
	Sys::importLib("Jquery", TRUE);
	View::jsBatchAdd(
		Jquery::urlMainScript(),
		Locator::urlResDir("sys") . "/js/phpdancer.js",
		Locator::urlThisLib() . "/afc.js"
	);
	
	/*
	View::jsBatchAddJquery();
	View::jsBatchAddJqueryPlugin( Locator::urlResDir('sys'). "/js/phpdancer.js" );
	View::jsBatchAddJqueryPlugin( Locator::urlThisLib() . "/afc.js" );
	*/
	
	?>
	<script>
	// set backendUrl for afc
	Pd.afc.setBackendUrl("<?php echo Locator::thisLibUrlToPage("afcBackend") ?>");
	</script>
	<?php	
}
