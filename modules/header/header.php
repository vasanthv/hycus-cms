<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

$db = new hdatabase();
if(hycus::getcleanvar("module")){
	$modulename = hycus::getcleanvar("module");
}
else{
	$homepageobj = $db->get_rec("#__menuitems", "itemlink", "defaultmenu ='1'" );
	$queryurl = parse_url($homepageobj->itemlink);
	$separatevalues = explode("&", $queryurl['query']);

	foreach($separatevalues AS $separatevalue)
	{
		$getvalues = explode("=", $separatevalue);
		if($getvalues[0]=="module")
		{
			$modulename=$getvalues[1];
		}
		else{
			$_REQUEST[$getvalues[0]]=$getvalues[1];
		}
	}
}
$metafile = "modules/$modulename/metadata.php";
if(is_file($metafile))
{
	include_once ($metafile);
}

?>
<title><?php echo getpagetitle(); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="<?php echo getmetakeywords(); ?>" />
<meta name="description" content="<?php echo getmetadesc(); ?>" />
<meta name="generator" content="Hycus - Open Source PHP Based Content Management" />

<script type="text/javascript" src="<?php echo hycus::getroot(); ?>assets/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?php echo hycus::getroot(); ?>assets/jquery-ui-1.7.1.custom.min.js"></script>
<script type="text/javascript" src="<?php echo hycus::getroot(); ?>assets/validator/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo hycus::getroot(); ?>assets/form/jquery.form.js"></script>
<script type="text/javascript" src="<?php echo hycus::getroot(); ?>assets/tooltip/jquery.simpletip-1.3.1.js"></script>
<script type="text/javascript" src="<?php echo hycus::getroot(); ?>assets/dropdown/hoverIntent.js"></script>
<script type="text/javascript" src="<?php echo hycus::getroot(); ?>assets/dropdown/superfish.js"></script>

<link rel="stylesheet" href="<?php echo hycus::getroot(); ?>assets/hycus.css" type="text/css" />

<?php

function getpagetitle()
{
	global $sitename;
	$db = new hdatabase();

	$modulename = hycus::getcleanvar("module");
	$metafile = "modules/$modulename/metadata.php";
	if(is_file($metafile) && function_exists ("setTitle"))
	{
		$title = setTitle();
	}

	if(hycus::getcleanvar("module") && hycus::getcleanvar("menuid")){
		$modulename = hycus::getcleanvar("module");
		$pagetitleobj = $db->get_rec("#__menuitems", "itemtitle, itemlink, pagetitle", "id = '".hycus::getcleanvar("menuid")."'");
		$pagetitle = $pagetitleobj->pagetitle;

		if($pagetitle && hycus::getcurrenturl()==huri::makeuri($pagetitleobj->itemlink."&menuid=".hycus::getcleanvar("menuid")))
		{ return $pagetitle; }
		elseif($title)
		{ return $title; }
		else
		{ return $pagetitleobj->itemtitle; }
	}
	elseif(hycus::getcleanvar("module") && !hycus::getcleanvar("menuid")){
		return ucfirst(hycus::getcleanvar("module"));
	}
	else{
		$pagetitleobj = $db->get_rec("#__menuitems", "itemtitle, pagetitle", "defaultmenu = '1'");
		$pagetitle = $pagetitleobj->pagetitle;
		if($pagetitle)
		{ return $pagetitle; }
		elseif($title)
		{ return $title; }
		else
		{ return $pagetitleobj->itemtitle; }
	}
}

function getmetakeywords()
{
	global $globalmetakeys;
	$db = new hdatabase();

	if(hycus::getcleanvar("module")){
		$modulename = hycus::getcleanvar("module");
	}
	else{
		$homepageobj = $db->get_rec("#__menuitems", "itemlink", "defaultmenu ='1'" );
		$queryurl = parse_url($homepageobj->itemlink);
		$separatevalues = explode("&", $queryurl['query']);

		foreach($separatevalues AS $separatevalue)
		{
			$getvalues = explode("=", $separatevalue);
			if($getvalues[0]=="module")
			{
				$modulename=$getvalues[1];
			}
			else{
				$_REQUEST[$getvalues[0]]=$getvalues[1];
			}
		}
	}

	$metafile = "modules/$modulename/metadata.php";
	if(is_file($metafile) && function_exists ("setMetakeywords"))
	{
		$metakeywords = setMetakeywords();
	}
	if($metakeywords)
		return $globalmetakeys.", ".$metakeywords;
	else
	{
		return $globalmetakeys;
	}
}
function getmetadesc()
{
	global $globalmetadesc;
	$db = new hdatabase();

	if(hycus::getcleanvar("module")){
		$modulename = hycus::getcleanvar("module");
	}
	else{
		$homepageobj = $db->get_rec("#__menuitems", "itemlink", "defaultmenu ='1'" );
		$queryurl = parse_url($homepageobj->itemlink);
		$separatevalues = explode("&", $queryurl['query']);

		foreach($separatevalues AS $separatevalue)
		{
			$getvalues = explode("=", $separatevalue);
			if($getvalues[0]=="module")
			{
				$modulename=$getvalues[1];
			}
			else{
				$_REQUEST[$getvalues[0]]=$getvalues[1];
			}
		}
	}

	$metafile = "modules/$modulename/metadata.php";
	if(is_file($metafile) && function_exists ("setMetadesc"))
	{
		$metadesc = setMetadesc();
	}
	if($metadesc)
		return $globalmetadesc." ".$metadesc;
	else
	{
		return $globalmetadesc;
	}
}
?>