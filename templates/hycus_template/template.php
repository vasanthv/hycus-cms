<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */


//getting the template configuration
global $defaultlang;
$themecolor = hycus::gethycusdata($templatedata,"themecolor");

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $defaultlang; ?>">
<head>
<?php hycusLoader::loadHead(); ?>
<link rel="stylesheet" type="text/css" href="<?php echo hycus::getroot(); ?>templates/hycus_template/style.css">
<link rel="icon" href="<?php echo hycus::getroot(); ?>templates/hycus_template/favicon.ico" type="image/x-icon" />

<style type="text/css">
div#middlebody{
	background-color:#<?php echo $themecolor ?>;
}
div#header h1 strong {
	color:#<?php echo $themecolor ?>;
}
a {
	color:#<?php echo $themecolor ?>;
}
a.selected{
	background-color:#<?php echo $themecolor ?>;
}
div#nav ul li ul a.selected{
	background-color:#<?php echo $themecolor ?>;
}
h1,h2,h3,h4,h5 {
	color:#<?php echo $themecolor ?>;
}
span.blocktitle, span.moduletitle {
	color:#<?php echo $themecolor ?>;
}
.button {
	background-color:#<?php echo $themecolor ?>;
}
</style>

</head>
<body>
<div id="header">
	<div id="innerheader">
		<div id="sitename">
			<a href="<?php echo hycus::getroot(); ?>"><h1>hycus<strong>cms</strong></h1></a>
		</div>
		<div id="nav">
			<?php hycusLoader::loadBlocks("topmenu"); ?>
		</div>
		<div class="clear"></div>
	</div>
</div>

<div id="middlebody">
	<div id="middleinner">

		<?php if(hycusLoader::checkBlocks("slideshow")){ ?>
			<div id="hycuslider">
			<?php hycusLoader::loadBlocks("slideshow"); ?>
			</div>
		<?php } ?>

		<?php if(hycusLoader::checkBlocks("topnews")){ ?>
			<div id="topnews">
			<?php hycusLoader::loadBlocks("topnews"); ?>
			</div>
		<?php } ?>

		<div class="clear"></div>
	</div>
</div>


<div id="body">


	<?php if(hycusLoader::checkBlocks("breadcrumb") || hycusLoader::checkBlocks("search")){ ?>
	<div id="middlelinner">
		<?php if(hycusLoader::checkBlocks("breadcrumb")){ ?>
		<div id="breadcrumb">
			<?php hycusLoader::loadBlocks("breadcrumb"); ?>
		</div>
		<?php } ?>
		<?php if(hycusLoader::checkBlocks("search")){ ?>
		<div id="searchbox">
			<?php hycusLoader::loadBlocks("search"); ?>
		</div>
		<?php } ?>
	</div>
	<?php } ?>


	<div id="innerbody">
		<div class="column <?php if(hycusLoader::checkBlocks("right")){ ?>column-60<?php }else { ?>column-100<?php } ?> left">
			<?php hycusLoader::loadModule(); ?>
		</div>

		<?php if(hycusLoader::checkBlocks("right")){ ?>
			<div class="column column-30 right">
				<?php hycusLoader::loadBlocks("right"); ?>
			</div>
		<?php } ?>
		<div class="clear"></div>
	</div>
</div>


<div id="footer">
	<div id="innerfooter">
		<p>&copy; 2009 <?php global $sitename; echo $sitename; ?>. </p>
	</div>
</div>
<?php hycusLoader::loadMessage(); ?>
<?php echo hycusLoader::loadsnippet("google_analytics"); ?>

</body>
</html>