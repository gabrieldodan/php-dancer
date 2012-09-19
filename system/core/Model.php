<?php
/**
 *
 * @author Gabriel Dodan , gabriel.dodan@gmail.com
 * @copyright Copyright (c) 2012 Gabriel Dodan
 * @license The MIT License http://www.opensource.org/licenses/mit-license.php , see LICENSE.txt file also
 */

class ModelBase {
	final function __construct() {
		Sys::errorHandler("Model instantiation not allowed");
	}
	
	
	protected static $loadedModels	= array();
	
	/**
	 * Loads and instantiate a model	
	 * @param type $model
	 * @return type 
	 */
	static function get($model) {
		
		$modelFile	= "{$model}Model.php";
		
		if (array_key_exists($modelFile, Model::$loadedModels) ) {
			return Model::$loadedModels[$modelFile];
		}
		
		$modelPath	= Locator::pathAppDir() . "/models/" . $modelFile;
		$modelClass	= "{$model}Model";
		
		if ( !is_file($modelPath) ) {
			Sys::errorHandler("Model::get(), Model '{$modelFile}' not found");
			return;
		}
		
		try {
			require $modelPath;
			return Model::$loadedModels[$modelFile] = new $modelClass();
		}
		catch( Exception $e ) {
			Sys::errorHandler("Model::get(), Can not instantiate model '{$modelClass}' model class");
			return;
		}
		
	}
}



/* extension mechanism */
$extFile = ltrim(APP_DIR . "/fw-core-extension/Model.php");
if ( is_file($extFile) ) {
	require_once($extFile);
}
if ( !class_exists('Model') ) {
	class Model extends ModelBase {
	}
}
?>
