<div class="wrap">
			<img src="http://grab-media.com/corpsite-static/images/grab_logo.jpg"/>
			<h2>GrabPress: Autopost Videos by Channel and Tag</h2>
			<p>Grab video content delivered fresh to your blog <a href="#" onclick='return false;' id="how-it-works">how it works</a></p>
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
	  	jQuery("#how-it-works").simpletip({
		  	 content: 'The Grabpress plugin gives your editors the power of our constantly updating video catalog from the dashboard of your Wordpress CMS. Leveraging automated delivery, along with keyword feed curation, the Grabpress plugin delivers article templates featuring video articles that compliment the organic content creation your site offers.<br /><br /> As an administrator, you may use Grabpress to set up as many feeds as you desire, delivering content based on intervals you specify. You may also assign these feeds to various owners, if your site has multiple editors, and the articles will wait in your drafts folder until you see a need to publish. Additionally, for smaller sites, you can automate the entire process, publishing automatically and extending the reach of your site without adding work to your busy day. <br /><br /> To get started, select a channel from our catalog, hone your feed by adding keywords, set your posting interval, and check the posting options (post interval, player style, save as draft or publish) for that feed to make sure the specifications meet your needs. Click the preview feed button to see make sure your feed will generate enough content and that the content is what you are looking for. If the feed seems to be right for you, save the feed and you will start getting new articles delivered to your site at the interval you specified. <br /><br />', 
		  	 fixed: true, 
		  	 position: 'bottom'
		});
	});
</script>