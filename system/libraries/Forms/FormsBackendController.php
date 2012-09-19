<?php

class FormsBackendController extends Controller {
	
	function resourcesAction(){
		
		function urlNormalization($baseUrl, $path) {
			$baseUrl = trim($baseUrl, "/") . "/";
			$res     = preg_match("!(https?://.*?)/(.*)!", $baseUrl, $m);
			if ( !$res ) {
				return FALSE;
			}


			$urlDomain = $m[1];
			$urlPath   = $m[2];
			if ( substr($path, 0, 1) != "/" ) {
				$path = $urlPath . $path;
			}

			$segments    = explode("/", $path);
			$newSegments = array();
			for ( $i=0; $i<count($segments); $i++ ) {
				if ( $segments[$i] == ".." ) {
					array_pop($newSegments);
				}
				else if ( $segments[$i] == "." ) {
					continue;
				}
				else {
					$newSegments[] = $segments[$i];
				}
			}

			return $urlDomain . "/" . trim(implode("/", $newSegments), "/");
		}
		function cbImagesURLsNormalization($matches) {
			$parsedUrl = parse_url($matches[2]);
			if ( @$parsedUrl['host'] ) {
				// full url , no normalization
				//return $matches[1] . $matches[2] . $matches[3];
				return $matches[0];
			}

			$url = urlNormalization($GLOBALS['FormsBackend-themeDirUrl'], $matches[2]);
			return ($url ? $matches[1] .  $url . $matches[3] : $matches[0]);
		}
		
		$controls			= Request::param("controls");
		$userControlsPath	= Request::param("userControlsPath");
		$template			= Request::param("template");
		$template			= ($template ? $template : "labels-left");
		$theme				= Request::param("theme");
		$theme				= ($theme ? $theme : "default");
		
		$standardControls	= array("ButtonSubmit", "CheckBox", "Radio", "Select", "TextArea", "TextInput");
		
		$jsFiles			= array();
		$userControlsPath	= ($userControlsPath ? Locator::pathHome() . "/" . trim( str_replace(Locator::pathHome(), "", $userControlsPath), "/" ) : "");
		$formsLibPath		= Locator::pathThisLib();
		$templateFile		= ( strpos($template, "/") === FALSE ? Locator::pathThisLib() . "/templates/{$template}.js" : Locator::pathHome() . "/" . trim($template, "/") );
		$themeDir			= ( strpos($template, "/") === FALSE ? Locator::pathThisLib() . "/themes/{$theme}" : Locator::pathHome() . "/" . trim($theme, "/") );
		$GLOBALS['FormsBackend-themeDirUrl'] = Locator::urlHome() . str_replace(Locator::pathHome(), "", $themeDir);
		
		$controlFiles		= array();
		foreach (explode(",", $controls) AS $control) {
			$file_1 = "{$userControlsPath}/{$control}.js";
			$file_2 = "{$formsLibPath}/controls/{$control}.js";
			
			if ( $userControlsPath && is_file($file_1) ) {
				$controlFiles[] = $file_1;
				continue;
			}
			if ( is_file($file_2) ) {
				$controlFiles[] = $file_2;
				continue;
			}
		}
		
		//css files
		$cssFiles = array();
		foreach (explode(",", $controls) AS $control) {
			if ( in_array($control, $standardControls) ) {
				$file = $themeDir . "/standard-controls.css";
			}
			else {
				$file = $themeDir . "/{$control}.css";
			}
			
			if ( !array_key_exists($file, $cssFiles) ) {
				$content			= @file_get_contents($file);
				$cssFiles[$file]	= preg_replace_callback('@(url\(\s*[\'"]*)(.*?)([\'"]*\))@i', 'cbImagesURLsNormalization', $content);
			}
		}
		
		//print_r($cssFiles);exit;
		//print_r($controlFiles);exit;
		$jsFiles = $this->resolveDeps($controlFiles);
		$jsFiles['template'] = @file_get_contents($templateFile);
		
		//$response = '{"js":"' . str_replace('"', '\"', implode("\n", $jsFiles)) . '","css":"' . str_replace('"', '\"', implode("\n", $cssFiles)) . '"}';
		$response = Sys::jsonEncode( array('js' => implode("\n", $jsFiles), 'css' => implode("\n", $cssFiles)) );
		if ( Request::param("callback") ) {
			$response = Request::param("callback") . "(" . $response . ");";
		}
		//$response = implode("\n", $cssFiles);
		
		$etag  = md5($response);
		header("Content-type: text/javascript");
		ob_end_clean();
		if ( isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag ) {
			header('HTTP/1.1 304 Not Modified');
			exit;
		}
		
		ob_start("ob_gzhandler");
		header('ETag: ' . $etag);
		echo $response;
		exit;
		
		
	}
	private	function fullPath($array, $basePath) {
		$result = array();
		foreach ($array AS $item) {
			$result[] = "{$basePath}/{$item}";
		}
		return $result;
	}
	private function resolveDeps($files) {
		static $allFiles	= array();
		//static $preventCyclical
		
		foreach ($files AS $file) {
			
			if ( !array_key_exists($file, $allFiles) ) {
				$content = @file_get_contents($file);
				
				preg_match('/@DEPENDENCIES\s*=\s*\[(.*?)\];/ism', $content, $matches);
				
				if ( count($matches) ) {
					
					$deps = explode(",", $matches[1]);
					
					foreach ($deps AS $dep) {
						$dep = str_replace(array('"', '"'), "", trim($dep) );
						if ( preg_match('/^FORMS_LIB\/(.*?)$/', $dep, $matches) ) {
							$f = Locator::pathThisLib() . "/" .  $matches[1];
							$this->resolveDeps( array($f) );
						}
						else if ( preg_match('/^THIS_DIR\/(.*?)$/', $dep, $matches) ) {
							$f = dirname($file) . "/" .  $matches[1];
							$this->resolveDeps( array($f) );
						}
						else if ( preg_match('/^JQUERY_UI\/(.*?)$/', $dep, $matches) ) {
							Sys::importLib("Jquery", TRUE);
							$uiDeps = Jquery::urlUiComponent($matches[1], TRUE, TRUE);
							foreach ($uiDeps AS $ui) {
								$uiPath	= Locator::pathHome()  . str_replace(Locator::urlHome(), "", $ui);
								if ( !array_key_exists($uiPath, $allFiles) ) {
									$allFiles[$uiPath] = @file_get_contents( $uiPath );
								}
							}
						}
						else if ( preg_match('/^JQUERY_PLUGIN\/(.*?)$/', $dep, $matches) ) {
							Sys::importLib("Jquery", TRUE);
							$depPlugin = Jquery::urlPlugin($matches[1]);
							$pluginPath	= Locator::pathHome()  . str_replace(Locator::urlHome(), "", $depPlugin);
							if ( !array_key_exists($pluginPath, $allFiles) ) {
								$allFiles[$pluginPath] = @file_get_contents( $pluginPath );
							}
						}
					}
				}
				$allFiles[$file] = $content;
			}
			
		}
		return $allFiles;
		
	}
	
}
?>
