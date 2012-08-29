<div class="wrap">
			<img src="http://grab-media.com/corpsite-static/images/grab_logo.jpg"/>
			<h2>GrabPress: Autopost Videos by Channel and Tag</h2>
			<p>Grab video content delivered fresh to your blog <a href="#">how it works</a></p>
<?php 
	$providers = join($provider, ",");

	$json_preview = GrabPress::get_json('http://catalog.'.GrabPress::$environment
		.'.com/catalogs/1/videos/search.json?keywords_and='.$keywords
		.'&categories='.$channel.'&order=DESC&order_by=created_at&providers='.$providers);
	$list_feeds = json_decode($json_preview, true);	
?>

<fieldset id="preview-feed">
	<legend>Preview Feed</legend>
		<input type="button" value="Close Preview" class="close-preview" id="close-preview">
		<span class="preview-text"><b><?php echo "Video Channel: "; ?></b><?php echo $channel; ?></span><br/>
		<span class="preview-text"><b><?php echo "Keywords: "; ?></b><?php echo $keyword; ?></span><br/>
		<span class="preview-text"><b><?php echo "Providers: "; ?></b><?php // $providersNames; ?></span><br/>
	<?php
		foreach ($list_feeds["results"] as $result) {
	?>
	<div data-id="<?php echo $result['video']['video_product_id']; ?>" class="result-tile">
		<div class="tile-left">
			<img src="<?php echo $result['video']['media_assets'][0]['url']; ?>" height="72px" width="123px" onclick="grabModal.play('<?php echo $result["video"]["guid"]; ?>')">
			<p class="video_date">
			<span><?php echo $result["video"]["created_at"]; ?></span>
			</p>
			<p class="video_logo">
			<span>SOURCE: <?php echo $result["video"]["provider"]["name"]; ?></span>
			</p>
		</div>
		<div class="tile-right">
			<h2 class="video_title" onclick="grabModal.play('<?php echo $result["video"]["guid"]; ?>')">
			<?php echo $result["video"]["title"]; ?>
			</h2>
			<p class="video_summary">		
			<?php echo $result["video"]["summary"]; ?>
			</p>
		</div>
	</div>
	<?php
		} 	
	?>
	</fieldset>
</div>
<script type="text/javascript">
	jQuery(function(){
		jQuery("#close-preview").click(function() {		  
		  window.history.back();		  	
	  	});
	});
</script>