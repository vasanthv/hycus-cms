<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

class hycusCommentView{
	function commentform(){
		if(hycus::getthisuserid() && hycus::getcleanvar("id")){
		?>
		<script type="text/javascript" src="<?php echo hycus::getroot(); ?>assets/watermark/jquery.watermarkinput.js"></script>
    	<script type="text/javascript">
		$(function($){
   			$("#commentbox").Watermark("<?php echo comment_watermark;?>");
   		});
		$(function()
		{
			$("#commentbox").focus(function()
			{
				$(this).animate({"height": "85px"}, "fast" );
				$("#button_block").slideDown("fast");
				return false;
			});
			$("#cancel").click(function()
			{
				$("#commentbox").animate({"height": "25px"}, "fast" );
				$("#button_block").slideUp("fast");
				return false;
			});
		});
		</script>

		<div id="commentform" class="hycusforms">
			<script type="text/javascript">
			$(function() {
				$(".comment_button").click(function()
				{
					var comment = $("#commentbox").val();
					var item_id = $("#item_id").val();
					var commentmodule = $("#commentmodule").val();
					var dataString = 'comment='+comment;
		 			if(comment=='')
					{
						alert("Please enter some text.");
					}
					else
					{
					$.ajax({
						type: "POST",
		 				url: "index.php?response=ajax&module=comment&task=addcomment&item_id="+item_id+"&cm="+commentmodule,
		   				data: dataString,
		  				cache: false,
		  				success: function(html){
				 				$("div#comments").prepend(html);
				  				$("div#comments").slideDown("slow");
				   				document.getElementById('commentbox').value='';
				   				alert("<?php echo commentsuccess ?>")
		  					}
		 				});
					}
				});
			});
			</script>
			<?php hycus::formvalidator("commentform"); ?>
			<form action="<?php echo huri::makeuri("?module=comment&task=addcomment"); ?>" method="post" name="commentform" id="commentform" style='clear:both;'>
  				<textarea type="text" name="commentbox" id="commentbox" type="text" cols="51" style="height:25px;"></textarea>
  				<br/>
  				<div id="button_block" style="display:none;">
  					<input type="hidden" name="item_id" id="item_id" value="<?php echo hycus::getcleanvar("id") ?>"/>
  					<input type="hidden" name="module" id="commentmodule" value="<?php echo hycus::getcleanvar("module") ?>"/>
  					<input type="button" class="comment_button button" value="<?php echo submitcomment; ?>"/>
  					<input type="button" class="button" id='cancel' value="<?php echo cancel; ?>"  />
  				</div>
			</form>
		</div>
		<?php
		}
		else
		{ echo "<div style='clear:both;'>".logintocomment."</div>"; }
	}
	function viewcomment($comment_data){
		$db = new hdatabase();

		if($comment_data){
			foreach($comment_data AS $comment){

				echo "<div class='commentview".$comment->id."' style='height:100%;clear:both;margin-top:5px;'>";
					echo "<div class='comment_button_set' style='float:right;'>"; ?>
						<script type="text/javascript">
						$(function() {
							$(".approve_button<?php echo $comment->id; ?>").click(function()
							{
								$.ajax({
								type: "POST",
				 				url: "<?php echo hycus::getroot(); ?>index.php?response=ajax&module=comment&task=approvecomment&commentid=<?php echo $comment->id; ?>&cm=<?php echo $comment->module; ?>&itemid=<?php echo $comment->item_id; ?>",
		  						cache: false,
		  						success: function(html){
				 					$(".approve_button<?php echo $comment->id; ?>").hide();
		  						}
								});
							});
							$(".delete_button<?php echo $comment->id; ?>").click(function()
							{
								$.ajax({
								type: "POST",
				 				url: "<?php echo hycus::getroot(); ?>index.php?response=ajax&module=comment&task=deletecomment&commentid=<?php echo $comment->id; ?>&commentuid=<?php echo $comment->uid; ?>",
		  						cache: false,
		  						success: function(html){
				 					$(".commentview<?php echo $comment->id; ?>").hide("slow");
		  						}
								});
							});
						});
						</script>

						<?php if(!$comment->approved){ ?>
						<input type="button" class="approve_button<?php echo $comment->id; ?>" value="" title="Approve and Add?" style="border:none;width:14px;height:14px;background: url(<?php echo hycus::getroot(); ?>modules/comment/comment-icons.png) left no-repeat;cursor:pointer;"/>
						<?php } ?>

						<?php if($comment->uid == hycus::getthisuserid() || !$comment->approved){ ?>
							<input type="button" class="delete_button<?php echo $comment->id; ?>" value="" title="Delete?"  style="border:none;width:14px;height:14px;background: url(<?php echo hycus::getroot(); ?>modules/comment/comment-icons.png) right no-repeat;cursor:pointer;"/>
						<?php } ?>

					<?php
					echo "</div>";
					echo "<div style='float:left;padding:5px;'>".hycus::getavatarthumb($comment->uid)."</div>";
					echo "<div>";
						echo "<span><b>".hycus::getuserfullname($comment->uid).": </b></span><br/>";
						echo "<span>".$comment->comment."</span><br/>";
						echo "<small><i>".hycus::showtime($comment->time)."</i></small>";
					echo "</div>";
				echo "</div>";

			}
		}

	}
}
?>
