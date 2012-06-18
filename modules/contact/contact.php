<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

 defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );
 $task = hycus::getcleanvar("task");
 $contactid = hycus::getcleanvar("id");

switch($task){
	case "sendmail":
		sendmail();
		break;
	case "contact":
		contactform($contactid);
		break;
	case "menucontactlist";
		menucontactlist();
		break;
	default:
		contactlist();
		break;
}
function menucontactlist()
{
	$db = new hdatabase();
	$contacts = $db->get_recs("#__contact", "id, title", "");
	if($contacts){
		echo "<select id='contactselect'>";
		echo "<option value=''>Select Content</option>";
		foreach($contacts as $contact)
		{
			echo "<option value='".$contact->id."'>".$contact->title."</option>";
		}
		echo "</select>";
		?>
		<script type="text/javascript">
			$(function() {
				$("#contactselect").change(function () {
					$("select#contactselect").attr("disabled", true);
					var contactselectvalue = $('select#contactselect option:selected').val();
					var existinglink = $('#menuitemlink').val();
					if(contactselectvalue){
						$('#menuitemlink').val(existinglink+"&id="+contactselectvalue);
					}
				});
			});
		</script>
		<?php
	}
}

function contactlist(){

	$db = new hdatabase();
	$contactobj = $db->get_recs("#__contact", "*", "");

	echo "<h4>Contacts</h4>";

	echo "<table width='100%' border='1' cellpadding='5' class='hycustable' >" .
			"<thead><tr>" .
			"<td align='center' width='3%'>S.No</td>" .
			"<td align='center'>Contact Title</td>" .
			"<td align='center' width='30%'>Email</td>" .
			"</tr></thead>";
	$count=1;
	echo "<tbody>";
	foreach($contactobj AS $contact)
	{
		if($count%2==0){ echo "<tr class='even'>"; }else{ echo "<tr class='odd'>"; }

		echo "<td align='center'>$count</td>";
		echo "<td>";
		echo "<a href='".huri::makeuri("?module=contact&task=contact&id=$contact->id")."'>".$contact->title."</a>";
		echo "</td>";
		echo "<td>";
		echo "<a href='mailto:".$contact->sendto."'>".$contact->sendto."</a>";
		echo "</td>";
		$count++;
	}
	echo "</tbody></table>";


}

 function sendmail(){
 	$contact_id = hycus::getcleanvar("contact_id");
	$contact_name = hycus::getcleanvar("contact_name");
 	$contact_email = hycus::getcleanvar("contact_email");
 	$contact_subject = hycus::getcleanvar("contact_subject");
 	$contact_desc = hycus::getcleanvar("contact_desc");
 	$contact_captcha = hycus::getcleanvar("concaptcha");

	if($contact_captcha==hycus::getcleanvar("captcha","session"))
	{
		$db = new hdatabase();
		$sendto = $db->get_rec("#__contact", "sendto", "id = '$contact_id'");

		//sendng email starts.
		$mail = new hycus_Mailer("$contact_name","$contact_email");
		$mail->subject("$contact_subject");
		$mail->to($sendto->sendto);
		$mail->cc("$contact_email");
		$mail->text("This is a contact email from ".hycus::getroot().". The message is as follows \n $contact_desc");
		$mail->send();
		//sending email ends

		$msg = "<div style='display:block;background: #80C080;padding:5px;'>".mail_success."</div>";
	}
	else{
		$msg = "<div style='display:block;background: #FF8080;padding:5px;'>".error_securityimage."</div>";
	}
	contactform($contact_id, $msg);
 }

 function contactform($contactid, $msg=null){
	 $db = new hdatabase();
	 $contactdetails = $db->get_rec("#__contact", "*", "id = '$contactid'");

	 echo $msg;

	 if($contactdetails->title)
	 echo "<h3>".$contactdetails->title."</h3>";
	 if($contactdetails->introtext)
	 echo "<div style='text-align:justify;'>".$contactdetails->introtext."</div>";
	 hycus::formvalidator("contactform");
	 echo "<form action='".huri::makeuri("?module=contact&menuid=".hycus::getcleanvar("menuid"))."' method='post' name='contactForm' id='contactform'>";
	 echo "<table cellspacing='15' style='padding:15px 0 0'>";
	 echo "<tr>";
		echo "<td><label>".name.": </label></td>";
		echo "<td><span><input type='text' name='contact_name' class='required' size='30' /></span></td>";
	 echo "</tr>";

	 echo "<tr>";
		echo "<td><label>".email.": </label></td>";
	 	echo "<td><span><input type='text' name='contact_email' class='required email' size='30' /></span></td>";
	 echo "</tr>";

	 echo "<tr>";
		echo "<td><label>".subject.": </label></td>";
		echo "<td><span><input type='text' name='contact_subject' class='required' size='30' /></span></td>";
	 echo "</tr>";

	 echo "<tr>";
		echo "<td valign='top'><label>".description.": </label></td>";
		echo "<td><span><textarea name='contact_desc' rows='6' cols='30' class='required'></textarea></span></td>";
	 echo "</tr>";

	 echo "<tr>";
		echo "<td valign='top'></td>";
		echo "<td>";
			$data = hycusLoader::loadmoduleconfig("contact");
			if(hycus::gethycusdata($data, "contactcaptcha")) {
				echo "<div>";
				hycus::captcha("concaptcha", "required textbox"); echo "</div>";
			}
		echo "</td>";
	 echo "</tr>";

	 echo "<tr>";
		echo "<td>";
			echo "<input type='hidden' name='task' value='sendmail' />";
			echo "<input type='hidden' name='contact_id' value='$contactdetails->id' />";
		echo "</td>";
		echo "<td><input type='submit' value='".submit."' class='button'/></td>";
	 echo "</tr>";

	 echo "</form>";
	 echo "</table>";
 }

?>
