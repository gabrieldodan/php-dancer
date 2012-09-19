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
	chdir("..");
	
	$files = explode(",", $_GET['f']);
	
	// keep only .js files
	foreach ($files AS $idx => $file) {
		if ( !endsWith(strtolower($file), ".js") ) {
			unset($files[$idx]);
		}
	}
	
	$etag  = etag($files);
	if ( isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag ) {
		header('HTTP/1.1 304 Not Modified');
		exit;
	}
	
	header("Content-type: text/javascript");
	ob_start("ob_gzhandler");
	$i = 0;
	$allContent = '';
	foreach ($files AS $file) {
		$origFile   = $file;
		$file       = ltrim( str_replace("../", "", $file), "/" );
		$content    = @trim(file_get_contents("./" . $file));
		if ( $content === FALSE ) {
			$content = ($i != 0 ? "\n\n" : "") . "/* File: {$file} not found or access denied */\n";
		}
		else {
			$content = ($i != 0 ? "\n\n" : "") . "/* File: {$file} */\n" . $content . ($content[strlen($content)-1] == ";" ? "" : ";");
			//$content = ($i != 0 ? "\n\n" : "") . "/*-- File: {$file} --*/\n" . $content;
			//$content = "\n" . $content;
		}
		$allContent .= $content;
		$i++;
		if ($i==2) {
			//break;
		}
			
	}
	header('ETag: ' . $etag);
	echo $allContent;
}

/**
 * HTTP ETag
 * @param array $files
 */
function etag($files) {
	$hash = "";
	foreach ($files AS $file) {
		$file = ltrim( str_replace("../", "", $file), "/" );
		$time = @filemtime("./" . $file);
		if ( $time !== FALSE ) {
			$hash .= $time . $file;
		}
	}
	return md5($hash);
}
function endsWith($str, $endStr) {
	$pos = strrpos($str, $endStr);
	return ( substr($str, $pos) ==  $endStr ? TRUE : FALSE);
}


?>