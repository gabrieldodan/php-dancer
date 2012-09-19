<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
	<head>
		<meta http-equiv="content-type"  content="application/xhtml+xml; charset=UTF-8" /> 
		<title><?php View::pageTitle() ?></title> 
		<meta name="description" content="<?php View::pageDescription() ?>" /> 
		<meta name="keywords" content="<?php View::pageKeywords() ?>" />
		<?php
		// Placeholder for CSS batch
		View::cssBatch();

		// Placeholder for JS batch
		View::jsBatch();
		?>
		
		<?php
		// add CSS file in CSS batch
		View::cssBatchAdd( array(Locator::urlResDir(). "/css/default.css") );
		?>
		</head>
	<body>
		<div id="wrapper">
			<div id="header">HEADER Layout 2</div>
			<div id="middle">
				<div id="left-side">
					<?php echo View::renderComp('left_side'); ?>
				</div>
				<div id="content"><?php View::mainContent(); ?></div>
				<div style="clear: both;"></div>
			</div>
			
		</div>
	</body>
</html>