<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

 defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );
global $paginationlimit;

$q = hycus::getcleanvar("q");
if($q){

 $dir = "modules/search/plugins/";
 $results = array();
 if (is_dir($dir)) {
	if ($dh = opendir($dir)) {
		while (($file = readdir($dh)) !== false) {
			if(filetype($dir . $file)=="file" && strpos($file,".php"))
			{
				include_once($dir . $file);
				$searchclass = "hycusSearch_".str_replace(".php", "", $file);

 				$class = $searchclass;
 				$newplugin = new $class;
 				$results = array_merge($newplugin->search($q), $results );
			}
		}
		closedir($dh);
	}
}

/*Pagination Starts*/

/* Pagination codes to get the start and end value */
$page= hycus::getcleanvar("pge");
if($page){$start = ($page-1)*$paginationlimit;}
else{$start = 0;}

/*Pagination codes to get the total number of results (for getting total page count)*/
$tot_results = count($results);

$results = array_slice($results,$start,$paginationlimit);
/*Pagination Ends*/

echo "<h4>".search_result_for." '$q'</h4>";
?>
<form action="<?php echo huri::makeuri("?module=search&menuid=".hycus::getcleanvar('menuid'));?>" method="post" name='searchbox'>
	<span><input type="text" name="q" value="<?php if(hycus::getcleanvar("q")){echo hycus::getcleanvar("q");} else{} ?>" /></span>
	<span>
		<input type="submit" value="<?php echo search; ?>" />
	</span>
</form>
<?php
echo "<div class='searchresults'>";
foreach($results AS $result)
{
	echo "<div class='searchresult' style='padding:10px;border-bottom: thin dotted #A2A2A2;'>";
		echo "<div class='searchresulttitle'>";
			echo "<a href='".$result[link]."'/>";
				echo $result[title];
			echo "</a>";
		echo "</div>";
		echo "<div class='searchresultdesc'>";
			echo $result[data];
		echo "</div>";
	echo "</div>";
}
echo "</div>";

/*Pagination Display Starts*/
// Loads the pagination module and its respective function to display pagination results
hycusLoader::loadModule("pagination");
$thisurl = "?module=search&q=".$q."&menuid=".hycus::getcleanvar("menuid");
paginationlinks($thisurl, $tot_results, $paginationlimit);
/*Pagination Display Ends*/

}
?>
