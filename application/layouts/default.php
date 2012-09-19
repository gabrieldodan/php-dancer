<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
	<head>
		<meta http-equiv="content-type"  content="application/xhtml+xml; charset=UTF-8" /> 
		<title><?php View::pageTitle() ?></title> 
		<meta name="description" content="<?php View::pageDescription() ?>" /> 
		<meta name="keywords" content="<?php View::pageKeywords() ?>" />
		<?php
        // import Jquery library
        Sys::importLib("Jquery", TRUE);
        
		// Placeholder for CSS batch
		View::cssBatch();
		
		// Placeholder for JS batch
		View::jsBatch();
		
		// add CSS files in CSS batch
		View::cssBatchAdd(
			Locator::urlResDir(). "/css/default.css",
			Jquery::urlUiTheme("darkness")
		);
		?>
	</head>
	<body id="body">
		<div id="wrapper">
			<div id="header">HEADER</div>
			<div id="middle">
				<div id="left-side">
					<?php View::renderComp('leftSide'); ?>
				</div>
				<div id="content"><?php View::renderMainContent(); ?></div>
				<div style="clear: both;"></div>
			</div>
			
		</div>
	</body>
</html>