<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

/* This is module has the fnctionality of the overall pagination functions of Hycus cms*/

function paginationlinks($url, $totpages, $limit)
{

	$pages = round($totpages/$limit);

	/*This is the main pagination function*/
	$currentpage= hycus::getcleanvar("pge");
	if($pages > 1){
	echo "<div class='pagination'>";

	echo "<div class='pageresult'>";
		if($currentpage)
		{
			echo pages." $currentpage of $pages";
		}
		else{
			echo pages." 1 of $pages";
		}
	echo "</div>";

	prevlink($url);

	for($i=1;$i<=$pages;$i++)
	{
		if($currentpage == $i){
			echo "<span class='links'>".$i."</span>";
		}
		else{
			echo "<span class='links'><a href='".huri::makeuri($url."&pge=".$i)."'>".$i."</a></span>";
		}
	}
	nextlink($pages,$url);
	echo "</div>";
	}
}
function prevlink($url)
{
	/*This function diplays the previous page link*/
	$page= hycus::getcleanvar("pge");
	if($page<=1){}else{
		echo "<span class='previous_link'><a href='".huri::makeuri($url."&pge=".($page-1))."'>".previous."</a></span>";
	}

}
function nextlink($lastpage, $url)
{
	/*This function diplays the next page link*/
	$page= hycus::getcleanvar("pge");
	if($page>=$lastpage){}
	elseif($lastpage==1){
	}
	else{
		echo "<span class='next_link'><a href='".huri::makeuri($url."&pge=".($page+1))."'>".next."</a><span>";
	}

}