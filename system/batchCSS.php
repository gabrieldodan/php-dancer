<?php
/**
 *
 * @author Gabriel Dodan , gabriel.dodan@gmail.com
 * @copyright Copyright (c) 2012 Gabriel Dodan
 * @license The MIT License http://www.opensource.org/licenses/mit-license.php , see LICENSE.txt file also
 */

if ( @$_GET['f'] ) {
	$replace = array(
			'{1}'	=>	"/resources/jquery/ui/",
			'{2}'	=>	"/resources/jquery/",
			'{3}'	=>	"/resources/css/",
			'{4}'	=>	"/resources/js/"
	);
	$_GET['f'] = strtr($_GET['f'], $replace);
	chdir(".."); // change dir to base path of the site
	
	// find baseURL and baseDir
	$baseDir   = bs2s( getcwd() );
	$segments  = explode("/", bs2s(trim($_SERVER['PHP_SELF'], "/")));
	if ( count($segments) == 2 ) {
		$baseURL = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['SERVER_NAME'];
	}
	else {
		$urlPath = '';
		for ($i = 0; $i < (count($segments)-2); $i++ ) {
			$urlPath .= "/" . $segments[$i];
		}
		$baseURL = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['SERVER_NAME'] . $urlPath;
	}
	$GLOBALS['baseURL'] = $baseURL;
	$GLOBALS['baseDir'] = $baseDir;
	
	$files = explode(",", $_GET['f']);
	
	// keep only .css files
	foreach ($files AS $idx => $file) {
		if ( !endsWith(strtolower($file), ".css") ) {
			unset($files[$idx]);
		}
	}
	
	//print_r($files);exit;
	$etag  = etag($files);
	if ( isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag ) {
		header('HTTP/1.1 304 Not Modified');
		exit;
	}
	
	header("Content-type: text/css");
	ob_start("ob_gzhandler");
	$i = 0;
	$allContent = '';
	$GLOBALS['importsSection'] = "";
	foreach ($files AS $file) {
		$origFile   = $file;
		$file       = ltrim( str_replace("../", "", $file), "/" );
		$content    = @file_get_contents("./" . $file);
		if ( $content === FALSE ) {
			$content = ($i != 0 ? "\n\n" : "") . "/* File: {$file} not found or access denied */\n";
		}
		else {
			$content = ($i != 0 ? "\n\n" : "") . "/* File: {$file} */\n" . $content;
			$GLOBALS['batchCSS-baseURL'] = $baseURL . "/" . dirname($file);
			
			// images URLs normalization
			$content = preg_replace_callback('@(url\(\s*[\'"]*)(.*?)([\'"]*\))@i', 'cbImagesURLsNormalization', $content);
			
			// @import URLs normalization
			$content = preg_replace_callback(array('!(@import)(\s+url\([\'"]*)(.*?)([\'"]*\))(.*?;)!', '!(@import)(\s+[\'"])(.*?)([\'"])(.*?;)!i'), 'cbImportURLsNormalization', $content);
		}
		$allContent .= $content;
		$i++;
	}

	header('ETag: ' . $etag);
	echo $GLOBALS['importsSection'] . $allContent;
}

/**
 * HTTP ETag
 * @param array $files
 */
function etag($files) {
	$hash = "";
	foreach ($files AS $file) {
		$file = ltrim( str_replace("../", "", $file), "/" );
		$time = @filemtime($GLOBALS['baseDir'] . "/" . $file);
		if ( $time !== FALSE ) {
			$hash .= $time . $file;
		}
	}
	return md5($hash);
}

/**
 * Backslashes to slashes
 * @param string
 * @return string
 */
function bs2s($str) {
	return str_replace("\\", "/", $str);
}

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

/**
 * Call back function for URL normalization
 * @param unknown_type $matches
 * @return string
 */
function cbImagesURLsNormalization($matches) {
	$parsedUrl = parse_url($matches[2]);
	if ( @$parsedUrl['host'] ) {
		// full url , no normalization
		//return $matches[1] . $matches[2] . $matches[3];
		return $matches[0];
	}
	
	$url = urlNormalization($GLOBALS['batchCSS-baseURL'], $matches[2]);
	return ($url ? $matches[1] .  $url . $matches[3] : $matches[0]);
}


function cbImportURLsNormalization($matches) {
	$parsedUrl = parse_url($matches[3]);
	if ( @$parsedUrl['host'] ) {
		// full url , no normalization
		$GLOBALS['importsSection'] .= $matches[0] . "\n"; 
	}
	else {
		
		$url = urlNormalization($GLOBALS['batchCSS-baseURL'], $matches[3]);
		if ( $url ) {
			$GLOBALS['importsSection'] .= $matches[1] . $matches[2] .  $url .  $matches[4] . @$matches[5] . "\n";
		}
		else {
			$GLOBALS['importsSection'] .= $matches[0] . "\n";
		}
	}
	
	return "/* " . $matches[0] . " -- normalized and added at begining of the file */";
}

function endsWith($str, $endStr) {
	$pos = strrpos($str, $endStr);
	return ( substr($str, $pos) ==  $endStr ? TRUE : FALSE);
}
?>