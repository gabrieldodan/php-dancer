<?php
/**
 * MAIN SECTION
 */
$config['defaultController']	= "default";

$config['defaultLayout']		= "default";

$config['siteName']				= "PHP Dancer - Pragmatic PHP Framework";

$config['enableETagMechanism']	= TRUE;

/* The Framework auto-detect the home URL and home Path. However you can set these config items manually. */
/*
$config['homeUrl'] 		            = "";
$config['homePath']	                = "";
*/

/**
 * DATABASE SECTION
 */
/* SAMPLE
$config["dbconfig"]["mysql1"] = array(
		"host"		=> "",
		"username"	=> "",
		"password"	=> "",
		"dbname"	=> "",
		"type"		=> "mysql"
);

$config["dbconfig"]["mysql2"] = array(
		"host"		=> "",
		"username"	=> "",
		"password"	=> "",
		"dbname"	=> "",
		"type"		=> "mysql"
);

$config["dbconfig"]["pg1"] = array(
		"host"		=> "",
		"username"	=> "",
		"password"	=> "",
		"dbname"	=> "",
		"type"		=> "pgsql"
);
*/

/**
 * REWRITE RULES SECTION
 */
/* SAMPLE
$config['rewrite-rules'][] = array(
		'pattern'	=> '.*-(\d+)\.html',
		'newPath'	=> 'product/$1'
);

$config['rewrite-rules'][] = array(
		'pattern'	=> '.*',
		'newPath'	=> 'main/$0'
);

$config['rewrite-rules'][] = array(
		'pattern'	=> 'iPhone 5 presentation',
		'newPath'	=> 'iphone5-presentation'
);
*/

/**
 * LIBRARIES AUTOLOADING SECTION
 */
/*SAMPLE 
$config['autoload-libraries'] = array(
	array("Library1Name", "app", $params),
	array("Library2Name", "sys", $params)
);
*/
?>