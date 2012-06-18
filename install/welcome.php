<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>HycusCMS Installation Success!</title>
</head>
<body style="margin:0px;padding:0px;">
	<div id="id" style="background:#234A8B;color:#E4E8F0;font-size:30px;padding:10px;">Welcome! You have successfully installed HycusCMS.</div>
	<div id="wrapper" style="padding:0px 10px;font-size:20px;background: url(../images/adminbodybg.jpg) repeat-x;height:309px">
		<div style="padding:50px 10px 0px;">
			Your website URL is <a href='<?php echo $_GET["url"]; ?>' style="color:green;"><?php echo $_GET["url"]; ?></a>.
		</div>
		<div style="padding:10px;">
			Your admin URL is <a href='<?php echo $_GET["url"]; ?>?admin' style="color:green;"><?php echo $_GET["url"]; ?>?admin</a>.
			<small style="font-size:14px;color:red;"><i>Note: You can change this path later. </i></small>
			<div style="padding:10px 10px 0px;"><small>» Login with the <span style="color:#234A8B;font-weight:bold;">username</span> and <span style="color:#234A8B;font-weight:bold;">password</span> which you have provided in the previous step.</small></div>
			<div style="padding:10px;"><small>» For security reasons, we recommend you to remove the <b>install</b> directory.</small></div>
		</div>
	</div>
	<div style="width:330px;margin:30px auto;font-size:11px;color:#828282;"><a href="http://www.hycus.com/" style="color:#828282;" target="_blank">Hycus</a> is Free PHP Based CMS released under the GNU/GPL License. </div>
</body>