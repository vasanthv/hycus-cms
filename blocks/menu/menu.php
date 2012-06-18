<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

class hycusBlock_menu {
	function loadthisblock($id,$data) {
		$menuid = hycus::gethycusdata($data,"menuid");
		$menutype = hycus::gethycusdata($data,"menutype");

		$currentuser=hycus::getthisuserid();

		$db = new hdatabase();
		$where = "";
		$where .= "menuid='$menuid' AND enabled='1' AND parentid='0' AND ";
		if($currentuser)
			$where .= "(menuperms='0' OR menuperms='1' )";
		else
			$where .= "(menuperms='0' OR menuperms='2' )";
		$where .= "ORDER BY ordering ASC";

		$menuitems = $db->get_recs("#__menuitems", "*", $where);
		if($menuitems){
			if($menutype == "horizontaldropdown"){ $this->horizontaldropdownmenu($menuid, $menuitems); }
			elseif($menutype == "horizontal"){ $this->horizontalmenu($menuid, $menuitems); }
			else{ $this->verticalmenu($menuid, $menuitems); }
		}
	}
	function verticalmenu($menuid, $menuitems){

		echo "<ul>";
			foreach($menuitems AS $menuitem)
			{
				echo "<li ";
				if(hycus::getcleanvar("menuid") && $menuitem->id==hycus::getcleanvar("menuid")){echo " class='selected' ";}
				echo ">";
				if($menuitem->defaultmenu == 1){
					echo "<a href='".hycus::getroot()."'";
					if(hycus::getcleanvar("menuid") && $menuitem->id==hycus::getcleanvar("menuid")){echo " class='selected' ";}
					echo ">".$menuitem->itemtitle."</a>";
				}
				else{
					echo "<a href='";
						if(stripos("$menuitem->itemlink", "http")=="0" && stripos("$menuitem->itemlink", "://")){ echo $menuitem->itemlink; }else { echo huri::makeuri($menuitem->itemlink."&menuid=".$menuitem->id); }
					echo "'";
					if($menuitem->target){echo "target='_$menuitem->target'"; }
					if(hycus::getcleanvar("menuid") && $menuitem->id==hycus::getcleanvar("menuid")){echo " class='selected' ";}
					echo ">".$menuitem->itemtitle."</a>";
				}

				$this->getsubmenus($menuid, $menuitem->id);
				echo "</li>";
			}
		echo "</ul>";


	}
	function horizontalmenu($menuid, $menuitems){

		echo "<table><tr>";
			foreach($menuitems AS $menuitem)
			{
				echo "<td ";
				if(hycus::getcleanvar("menuid") && $menuitem->id==hycus::getcleanvar("menuid")){echo " class='selected' ";}
				echo ">";
				if($menuitem->defaultmenu == 1){
					echo "<a href='".hycus::getroot()."'";
					if(hycus::getcleanvar("menuid") && $menuitem->id==hycus::getcleanvar("menuid")){echo " class='selected' ";}
					echo ">".$menuitem->itemtitle."</a>";
				}
				else{
					echo "<a href='";
						if(stripos("$menuitem->itemlink", "http")=="0" && stripos("$menuitem->itemlink", "://")){ echo $menuitem->itemlink; }else { echo huri::makeuri($menuitem->itemlink."&menuid=".$menuitem->id); }
					echo "'";
					if($menuitem->target){echo "target='_$menuitem->target'"; }
					if(hycus::getcleanvar("menuid") && $menuitem->id==hycus::getcleanvar("menuid")){echo " class='selected' ";}
					echo ">".$menuitem->itemtitle."</a>";
				}

				$this->tdgetsubmenus($menuid, $menuitem->id);
				echo "</td>";
			}
		echo "</tr></table>";

	}
	function horizontaldropdownmenu($menuid, $menuitems){

		// loads the script for javascript dropdown.
		?>
		<link rel="stylesheet" type="text/css" href="<?php echo hycus::getroot(); ?>blocks/menu/superfish.css" media="screen" />
		<script type="text/javascript">
		// initialise plugins
			$(function(){
				$('ul.hycusmenu_sfmenu').superfish();
			});
		</script>

		<?php
		echo "<ul class='hycusmenu_sfmenu'>";
			foreach($menuitems AS $menuitem)
			{
				echo "<li ";
				if(hycus::getcleanvar("menuid") && $menuitem->id==hycus::getcleanvar("menuid")){echo " class='selected' ";}
				echo " style='float:left;'";
				echo ">";
				if($menuitem->defaultmenu == 1){
					echo "<a href='".hycus::getroot()."'";
					if(hycus::getcleanvar("menuid") && $menuitem->id==hycus::getcleanvar("menuid")){echo " class='selected' ";}
					echo ">".$menuitem->itemtitle."</a>";
				}
				else{
					echo "<a href='";
						if(stripos("$menuitem->itemlink", "http")=="0" && stripos("$menuitem->itemlink", "://")){ echo $menuitem->itemlink; }else { echo huri::makeuri($menuitem->itemlink."&menuid=".$menuitem->id); }
					echo "'";
					if($menuitem->target){echo "target='_$menuitem->target'"; }
					if(hycus::getcleanvar("menuid") && $menuitem->id==hycus::getcleanvar("menuid")){echo " class='selected' ";}
					echo ">".$menuitem->itemtitle."</a>";
				}

				$this->getsubmenus($menuid, $menuitem->id);
				echo "</li>";
			}
		echo "</ul>";

	}
	function getsubmenus($menuid, $parentid)
	{

		//gets the submenuitems for the given menuid
		$db = new hdatabase();

		$currentuser=hycus::getthisuserid();
		$where = "";
		$where .= "menuid='$menuid' AND enabled='1' AND parentid='$parentid' AND ";
		if($currentuser)
			$where .= "(menuperms='0' OR menuperms='1') ";
		else
			$where .= "(menuperms='0' OR menuperms='2') ";
		$where .= "ORDER BY ordering ASC";

		$menuitems = $db->get_recs("#__menuitems", "*", $where);
		if($menuitems){
			echo "<ul>";
				foreach($menuitems AS $menuitem)
				{
					echo "<li>";
					if($menuitem->defaultmenu == 1){
						echo "<a href='".hycus::getroot()."' ";
						if(hycus::getcleanvar("menuid") && $menuitem->id==hycus::getcleanvar("menuid")){echo " class='selected' ";}
						echo ">".$menuitem->itemtitle."</a>";
					}
					else{
						echo "<a href='";
							if(stripos("$menuitem->itemlink", "http")=="0" && stripos("$menuitem->itemlink", "://")){ echo $menuitem->itemlink; }else { echo huri::makeuri($menuitem->itemlink."&menuid=".$menuitem->id); }
						echo "'";
						if($menuitem->target){echo "target='_$menuitem->target'"; }
						if(hycus::getcleanvar("menuid") && $menuitem->id==hycus::getcleanvar("menuid")){echo " class='selected' ";}
						echo ">".$menuitem->itemtitle."</a>";
					}

					$this->getsubmenus($menuid, $menuitem->id);
					echo "</li>";
				}
			echo "</ul>";
		}
	}
	function tdgetsubmenus($menuid, $parentid)
	{

		//gets the submenuitems for the given menuid
		$db = new hdatabase();

		$currentuser=hycus::getthisuserid();
		$where = "";
		$where .= "menuid='$menuid' AND enabled='1' AND parentid='$parentid' AND ";
		if($currentuser)
			$where .= "(menuperms='0' OR menuperms='1') ";
		else
			$where .= "(menuperms='0' OR menuperms='2') ";
		$where .= "ORDER BY ordering ASC";

		$menuitems = $db->get_recs("#__menuitems", "*", $where);
		if($menuitems){
				foreach($menuitems AS $menuitem)
				{
					echo "<td>";
					if($menuitem->defaultmenu == 1){
						echo "<a href='".hycus::getroot()."' ";
						if(hycus::getcleanvar("menuid") && $menuitem->id==hycus::getcleanvar("menuid")){echo " class='selected' ";}
						echo ">".$menuitem->itemtitle."</a>";
					}
					else{
						echo "<a href='";
							if(stripos("$menuitem->itemlink", "http")=="0" && stripos("$menuitem->itemlink", "://")){ echo $menuitem->itemlink; }else { echo huri::makeuri($menuitem->itemlink."&menuid=".$menuitem->id); }
						echo "'";
						if($menuitem->target){echo "target='_$menuitem->target'"; }
						if(hycus::getcleanvar("menuid") && $menuitem->id==hycus::getcleanvar("menuid")){echo " class='selected' ";}
						echo ">".$menuitem->itemtitle."</a>";
					}

					$this->getsubmenus($menuid, $menuitem->id);
					echo "</td>";
				}
		}
	}
}
?>
