<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

 defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

	class hycusTime
	{
		function hycusTime()
		{
		}
		function GMTTime($nTimeZone)
		{
			$nSecs = 0;
			if (strpos($nTimeZone,"."))
			{
				$sTimes = split("\.",$nTimeZone);
				$nSecs = ($sTimes[0]*3600);
				$nSecs += ($sTimes[1]*60);
			}
			else
			{
				$nSecs = ($nTimeZone*3600);
			}
			$nNew = time();
			$nNew += -$nSecs;
			return date("d - F - Y H:i:s",$nNew);
		}

		//ConvertTime - Converts Any Time to Any Time
		function ConvertTime($sDate,$nTimeZone1,$nTimeZone2)
		{
			$nSecs1 = 0;
			$nSecs2 = 0;

			if (strpos($nTimeZone1,"."))
			{
				$sTimes = split("\.",$nTimeZone1);
				$nSecs1 = ($sTimes[0]*3600);
				$nSecs1 += ($sTimes[1]*60);
			}
			else
			{
				$nSecs1 = ($nTimeZone1*3600);
			}
			if (strpos($nTimeZone2,"."))
			{
				$sTimes = split("\.",$nTimeZone2);
				$nSecs2 = ($sTimes[0]*3600);
				$nSecs2 += ($sTimes[1]*60);
			}
			else
			{
				$nSecs2 = ($nTimeZone2*3600);
			}

			//$sNew = strtotime($sDate);
			$sNew = $sDate;
			$sNew += -$nSecs1;

			$sNew += $nSecs2;

			return $sNew;
			//return date("d - F - Y H:i:s",$sNew);
		}
	}
?>