<?php $checked = 'checked="checked"';?>
<div class="wrap">
	<img src="http://grab-media.com/corpsite-static/images/grab_logo.jpg"/>
	<h2>GrabPress: Edit the player template for video posts</h2>	
	<p>Video that fits your site design</p>
<form action="" method="POST" class="template-form">
	<input type="hidden" name="action" value="<?php echo $form["action"];?>" />
	<fieldset>
		<legend>Player</legend>

		<table class="form-table grabpress-table template-table">
		<tbody>
			<tr valign="bottom">
	   			<th scope="row">Ratio <span class="asterisk">*</span></th>
				<td>
					<input type="radio" name="ratio" value="widescreen" <?php echo $form["widescreen_selected"]?$checked:""; ?> /> Widescreen 16:9
					<input type="radio" name="ratio" value="standard" <?php echo $form["standard_selected"]?$checked:""; ?> /> Standard 4:3 
				</td>
			</tr>
			<tr valign="bottom">
	   			<th scope="row">Width</th>
				<td>
					<input type="text" name="width" value="<?php echo $form["width"];?>" />
				</td>
			</tr>
			<tr valign="bottom">
	   			<th scope="row">Height</th>
				<td>
					<span class="height"><?php echo isset($form["height"])?$form["height"]:"270";?></span>
				</td>
			</tr>
			<!-- <tr valign="bottom">
	   			<th scope="row">Playback <span class="asterisk">*</span></th>
				<td>
					<input type="radio" name="playback" value="auto" <?php //echo $form["auto_selected"]?$checked:""; ?> /> Auto
					<input type="radio" name="playback" value="click" <?php //echo $form["click_selected"]?$checked:""; ?> /> Click 
				</td>
			</tr> -->
			<tr valign="bottom">					
				<td class="button-tip" colspan="2">						
					<input type="submit" class="button-primary" value="Save" id="btn-create-feed" />
				</td>
			</tr>
		</tbody></table>
	</fieldset>
</form>

<div class="template-preview">
	<div class="widescreen" <?php if(!$form["widescreen_selected"]){?>style="display:none;" <?php }?> ></div>
	<div class="standard" <?php if(!$form["standard_selected"]){?>style="display:none;" <?php }?> ></div>
</div>
</div>
<script type="text/javascript">
 	jQuery(function($){
 		var updateHeightValue = function(){
 			var player_width = $("form input[name=width]").val();
 			$(".template-preview").width(player_width);
 			 
		 	if($("form input[name=ratio]:checked").val() == "widescreen"){
		 		var height = ($("form input[name=width]").val()/16)*9;
		 		var player_height = parseInt(height,10);
		 		var widescreen_width = (player_width * 3) / 4;
		 		var margin_left = (player_width - widescreen_width) / 2;
		 		$(".widescreen").width(widescreen_width);
		 		$(".widescreen").height(player_height);
		 		$(".widescreen").css({"border-top": "none", "border-bottom" : "none", "margin-left" : margin_left});
		 	}else{
				var height = ($("form input[name=width]").val()/4)*3;
				var player_height = parseInt(height,10);
				var standard_height = (player_height * 3) / 4;
				var margin_top = (player_height - standard_height) / 2;
				$(".standard").width(player_width);
		 		$(".standard").height(standard_height);
		 		$(".standard").css({"border-left": "none", "border-right" : "none", "margin-top" : margin_top });
		 	}
	 		$(".height").text(player_height);
	 		$(".template-preview").height(player_height);
 		};
 		$("form input[name=width]").change(updateHeightValue);
 		$("form input[name=ratio]").change(updateHeightValue);
 		$("form input[name=ratio]").change(function(){
			$(".template-preview .widescreen").toggle();
 			$(".template-preview .standard").toggle();
 		})
 	});
</script>
