<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

 defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

/*this class deals with the hycus parameters and configuration through xml.
 * converts xml fields to html form elements*/
 class hycusParams{
 	function getParams($file,$data){
 		$html = "";
 		$xml = simplexml_load_file($file);
 		$count=1;
 		$html = "<table>";
		foreach($xml->children() as $child)
		{
			$fieldtype = $child['type'];
			switch($fieldtype){
				case "text":
					$html .= "<tr><td>";
					if($child['label']){$html .= "<label>".$child['label']." : </label></td>";}
					$html .= "<td><input type='text' name='".$child['name']."' value='";
					if(hycus::gethycusdata($data,$child['name'])){ $html.= hycus::gethycusdata($data,$child['name']); }
					elseif($child['defaultvalue']){ $html.= $child['defaultvalue']; }
					$html.="'";
					if($child['disable']=='disable'){ $html.= "disabled "; }
					if($child['size']){ $html.= "size='".$child['size']."'"; }
					$html .=" />";
					if($child['description']){$html .= hycus::htooltip("blockparamstt".$count, " ".hycus::iconimage('bulb.png'), $child['description']); }
					$html .= "</td></tr>";
				break;
				case "textarea":
					$html .= "<tr><td>";
					if($child['label']){ $html .= "<label>".$child['label']." : </label></td>"; }
					$html .= "<td><textarea name='".$child['name']."' ";
					$html .= " style='width:300px;height:100px;' >";
					if(hycus::gethycusdata($data,$child['name'])){ $html .= hycus::gethycusdata($data,$child['name']); }
					elseif($child['defaultvalue']){ $html .= $child['defaultvalue']; }
					$html.="</textarea>";
					if($child['description']){$html .= hycus::htooltip("blockparamstt".$count, " ".hycus::iconimage('bulb.png'), $child['description']); }
					$html .= "</td></tr>";
				break;
				case "checkbox":
					$html .= "<tr><td>";
					if($child['label']){$html .= "<label>".$child['label']." : </label></td>";}
					$html .= "<td><input type='checkbox' name='".$child['name']."' value='1'";
					if(hycus::gethycusdata($data,$child['name'])) {$html .= "CHECKED=CHECKED"; }
					elseif($child['defaultvalue']) {$html .= "CHECKED=CHECKED"; }
					$html.= "' />";
					if($child['description']){$html .= hycus::htooltip("blockparamstt".$count, " ".hycus::iconimage('bulb.png'), $child['description']); }
					$html .= "</td></tr>";
				break;
				case "radio":
					$html .= "<tr><td>";
					if($child['label']){$html .= "<label>".$child['label']." : </label></td>";}
					$html .= "<td><input type='radio' name='".$child['name']."' value='1'";
					if(hycus::gethycusdata($data,$child['name'])) {$html .= "CHECKED=CHECKED"; }
					elseif($child['defaultvalue']) {$html .= "CHECKED=CHECKED"; }
					$html.= "' />";
					if($child['description']){$html .= hycus::htooltip("blockparamstt".$count, " ".hycus::iconimage('bulb.png'), $child['description']); }
					$html .= "</td></tr>";
				break;
				case "selectlist":
					$html .= "<tr><td>";
					if($child['label']){$html .= "<label>".$child['label']." : </label></td>";}
					$html .= "<td><select name='".$child['name']."'";
					if($child['disable']=='disable'){ $html.= "disabled "; }
					if($child['multiple']=='multiple'){ $html.= "multiple "; }
					if($child['size']){ $html.= "size='".$child['size']."'"; }
					$html.=">";
					$html .= "<option></option>";
					foreach($child->children()->option AS $optionvalue){
						$html.= "<option value='".$optionvalue['value']."' ";
						if(hycus::gethycusdata($data,$child['name']) == $optionvalue['value']) {$html .= " SELECTED=SELECTED"; }
						elseif($child['defaultvalue'] == $optionvalue->id) {$html .= " SELECTED=SELECTED"; }
						$html .= ">".$optionvalue."</option>";
					}
					$html.= "</select>";
					if($child['description']){$html .= hycus::htooltip("blockparamstt".$count, " ".hycus::iconimage('bulb.png'), $child['description']); }
					$html .= "</td></tr>";
				break;
				case "menulist":
					$html .= "<tr><td>";
					$db = new hdatabase();
					$menusobj = $db->get_recs("#__menus", "*", "");
					if($child['label']){$html .= "<label>".$child['label']." : </label></td>";}
					$html .= "<td><select name='".$child['name']."'";
					$html.=">";
					$html .= "<option>Select Menu</option>";
					foreach($menusobj AS $optionvalue){
						$html.= "<option value='".$optionvalue->id."' ";
						if(hycus::gethycusdata($data,$child['name']) == $optionvalue->id) {$html .= " SELECTED=SELECTED"; }
						elseif($child['defaultvalue'] == $optionvalue->id) {$html .= " SELECTED=SELECTED"; }
						$html .= ">".$optionvalue->menuname."</option>";
					}
					$html.= "</select>";
					if($child['description']){$html .= hycus::htooltip("blockparamstt".$count, " ".hycus::iconimage('bulb.png'), $child['description']); }
					$html .= "</td></tr>";
				break;
				case "usertypelist":
					$html .= "<tr><td>";
					$db = new hdatabase();
					$usertypeobj = $db->get_recs("#__usertypes", "*", "");
					if($child['label']){$html .= "<label>".$child['label']." : </label></td>";}
					$html .= "<td><select name='".$child['name']."'";
					$html.=">";
					$html .= "<option>Select Usertype</option>";
					foreach($usertypeobj AS $optionvalue){
						$html.= "<option value='".$optionvalue->id."' ";
						if(hycus::gethycusdata($data,$child['name']) == $optionvalue->id) {$html .= " SELECTED=SELECTED"; }
						elseif($child['defaultvalue'] == $optionvalue->id) {$html .= " SELECTED=SELECTED"; }
						$html .= ">".$optionvalue->usertype."</option>";
					}
					$html.= "</select>";
					if($child['description']){$html .= hycus::htooltip("blockparamstt".$count, " ".hycus::iconimage('bulb.png'), $child['description']); }
					$html .= "</td></tr>";
				break;
				case "contentlist":
					$html .= "<tr><td>";
					$db = new hdatabase();
					$menusobj = $db->get_recs("#__contents", "*", "");
					if($child['label']){$html .= "<label>".$child['label']." : </label></td>";}
					$html .= "<td><select name='".$child['name']."'";
					$html.=">";
					$html .= "<option>Select Content</option>";
					foreach($menusobj AS $optionvalue){
						$html.= "<option value='".$optionvalue->id."' ";
						if(hycus::gethycusdata($data,$child['name']) == $optionvalue->id) {$html .= " SELECTED=SELECTED"; }
						elseif($child['defaultvalue'] == $optionvalue->id) {$html .= " SELECTED=SELECTED"; }
						$html .= ">".$optionvalue->title."</option>";
					}
					$html.= "</select>";
					if($child['description']){$html .= hycus::htooltip("blockparamstt".$count, " ".hycus::iconimage('bulb.png'), $child['description']); }
					$html .= "</td></tr>";
				break;
				case "folderlist":
					$html .= "<tr><td>";
					$dir = $child['dir']."/";
					if($child['label']){$html .= "<label>".$child['label']." : </label></td>";}
					$html .= "<td><select name='".$child['name']."'";
					$html.=">";
					$html .= "<option>Select a item</option>";
					if (is_dir($dir)) {
						if ($dh = opendir($dir)) {
							while (($file = readdir($dh)) !== false) {
								if(filetype($dir . $file)=="dir" && $file != "." && $file != "..")
								{
									$html.= "<option value='".$file."' ";
									if(hycus::gethycusdata($data,$child['name']) == $file) {$html .= " SELECTED=SELECTED"; }
									elseif($child['defaultvalue'] == $file) {$html .= " SELECTED=SELECTED"; }
									$html .= ">".$file."</option>";
								}
							}
						}
						closedir($dh);
					}
					$html.= "</select>";
					if($child['description']){$html .= hycus::htooltip("blockparamstt".$count, " ".hycus::iconimage('bulb.png'), $child['description']); }
					$html .= "</td></tr>";
				break;
				case "filelist":
					$html .= "<tr><td>";
					$dir = $child['dir']."/";
					if($child['label']){$html .= "<label>".$child['label']." : </label></td>";}
					$html .= "<td><select name='".$child['name']."'";
					$html.=">";
					$html .= "<option>Select a item</option>";
					if (is_dir($dir)) {
						if ($dh = opendir($dir)) {
							while (($file = readdir($dh)) !== false) {
								if(filetype($dir . $file)=="file")
								{
									$html.= "<option value='".$file."' ";
									if(hycus::gethycusdata($data,$child['name']) == $file) {$html .= " SELECTED=SELECTED"; }
									elseif($child['defaultvalue'] == $file) {$html .= " SELECTED=SELECTED"; }
									$html .= ">".$file."</option>";
								}
							}
						}
						closedir($dh);
					}
					$html.= "</select>";
					if($child['description']){$html .= hycus::htooltip("blockparamstt".$count, " ".hycus::iconimage('bulb.png'), $child['description']); }
					$html .= "</td></tr>";
				break;
				case "colorpicker":
					$html .= "<tr><td>";
					$dir = $child['dir']."/";
					if($child['label']){$html .= "<label>".$child['label']." : </label></td>";}
					$html .= "<td>";

					$html .= "<input type='text' maxlength='6' name='".$child['name']."' size='6' id='colorpickerField' value='";
					if(hycus::gethycusdata($data,$child['name'])){ $html.= hycus::gethycusdata($data,$child['name']); }
					elseif($child['defaultvalue']){ $html.= $child['defaultvalue']; }
					$html .= "' />";
					$html .= '<script>$(\'#colorpickerField\').ColorPicker({
								onSubmit: function(hsb, hex, rgb, el) {
								$(el).val(hex);
								$(el).ColorPickerHide();
							},
								onBeforeShow: function () {
									$(this).ColorPickerSetColor(this.value);
								}
							})
							.bind(\'keyup\', function(){
								$(this).ColorPickerSetColor(this.value);
							});
							</script>';

					if($child['description']){$html .= hycus::htooltip("blockparamstt".$count, " ".hycus::iconimage('bulb.png'), $child['description']); }
					$html .= "</td></tr>";
				break;
			}
			$count++;
		}
		$html .= "</table>";

		return $html;
 	}
 }
?>
