<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

 defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

 class hycusBlock_slideshow {
	function loadthisblock($id,$data) {
		$navigationtype = hycus::gethycusdata($data,"navigationtype");
		$slidewidth = hycus::gethycusdata($data,"slideshowwidth");
		$slideheight = hycus::gethycusdata($data,"slideshowheight");

		?>
		<div>
			<script type='text/javascript' src='<?php echo hycus::getroot(); ?>assets/easyslider/easySlider1.7.js'></script>

			<link rel="stylesheet" type="text/css" href="<?php echo hycus::getroot(); ?>blocks/slideshow/style.css"/>

			<style type="text/css">
			#slider li, #slider2 li{
				width:<?php echo $slidewidth; ?>px;
				height:<?php echo $slideheight; ?>px;
				overflow:hidden;
			}
			<?php if($navigationtype=="none"){ ?>
			#prevBtn a, #nextBtn a,#slider1next a, #slider1prev a {
				display:none;
			}
			<?php } ?>
			</style>

			<script type="text/javascript">
			$(document).ready(function(){
				$("#slider").easySlider({
					auto: true,
					continuous: true<?php if($navigationtype=="numbers"){ ?>,
					numeric: true
					<?php } ?>
				});
			});
			</script>


			<?php

			$dir = "images/slideshow/";
			echo "<div class='slider' id='slider'>";

			echo "<ul>";
			// Open the slidshow directory, and proceed to read its contents and gets the image.
			if (is_dir($dir)) {
				if ($dh = opendir($dir)) {
					while (($file = readdir($dh)) !== false) {
						if(filetype($dir . $file)=="file" && $file != "." && $file != ".." && $file!="Thumbs.db")
						{
							echo "<li>";
								echo '<img src="'.hycus::getroot().$dir . $file.'" alt="slideshow" />';
							echo "</li>";
						}
					}
					closedir($dh);
				}
			}
			echo "</ul>";
			echo "</div>";

			?>
		</div>
		<?php
	}
 }
?>
