<?php 
	$providers = join($provider, ",");
	$provider_total = count(GrabPress::get_providers());
	if(($provider_total == count($provider)) || in_array("", $provider)){
		$provider_text = "All Providers";
	}else{
		$provider_text = count($provider)." of ".$provider_total." selected";
	}
	$json_preview = GrabPress::get_json('http://catalog.'.GrabPress::$environment
		.'.com/catalogs/1/videos/search.json?keywords_and='.urlencode($keywords)
		.'&categories='.urlencode($channel).'&order=DESC&order_by=created_at&providers='.urlencode($providers));
	$list_feeds = json_decode($json_preview, true);
?>
<form method="post" action="" id="preview-feed">
	<input type="hidden" id="action-preview-feed" name="action" value="" />
<div class="wrap">
			<img src="http://grab-media.com/corpsite-static/images/grab_logo.jpg"/>
			<h2>GrabPress: Autopost Videos by Channel and Tag</h2>
			<p>Grab video content delivered fresh to your blog <a href="#" onclick='return false;' id="how-it-works">how it works</a></p>
	<fieldset id="preview-feed">
	<legend>Preview Feed</legend>
		<?php if(isset($feed_id)){ ?>
		<input type="hidden" name="feed_id" value="<?php echo $feed_id; ?>"  />
		<?php } ?>
		<input type="hidden" name="referer" value="<?php echo $referer; ?>"  />
		<input type="hidden" name="active" value="<?php echo $active; ?>" id="active" />
		<input type="hidden" name="channel" value="<?php echo $channel; ?>" id="channel" />
		<input type="hidden" name="keywords" value="<?php echo $keywords; ?>" id="keyword" />	
		<input type="hidden" name="limit" value="<?php echo $limit; ?>" id="limit" />
		<input type="hidden" name="schedule" value="<?php echo $schedule; ?>" id="schedule" />
		<input type="hidden" name="publish" value="<?php echo $publish; ?>" id="publish" />
		<input type="hidden" name="click_to_play" value="<?php echo $click_to_play; ?>" id="click_to_play" />
		<input type="hidden" name="author" value="<?php echo $author; ?>" id="author" />	
		<select name="category[]" style="display:none;" multiple="multiple	">
			<?php foreach($category as $cat){ ?>
				<option value="<?php echo $cat;?>" selected="selected"/>
			<?php } ?>
		</select>
		<select name="provider[]" style="display:none;" multiple="multiple	">
			<?php foreach($provider as $prov){ ?>
				<option value="<?php echo $prov;?>" selected="selected"/>
			<?php } ?>
		</select>
		
		<input type="button" value="Close Preview" class="close-preview" id="close-preview" >
		<span class="preview-text"><b>Video Channel: </b><?php echo $channel; ?></span><br/>
		<span class="preview-text"><b>Keywords: </b><?php echo $keywords; ?></span><br/>
		<span class="preview-text"><b>Providers: </b><?php echo $provider_text; ?></span><br/>
	<?php
		foreach ($list_feeds["results"] as $result) {
	?>
	<div data-id="<?php echo $result['video']['video_product_id']; ?>" class="result-tile">
		<div class="tile-left">
			<img src="<?php echo $result['video']['media_assets'][0]['url']; ?>" height="72px" width="123px" onclick="grabModal.play('<?php echo $result["video"]["guid"]; ?>')">
			<p class="video_date">
				<?php $date = new DateTime( $result["video"]["created_at"] );
				$stamp = $date->format('m/d/Y') ?>
			<span><?php echo $stamp; ?></span>
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
</form>
<script type="text/javascript">
<?php $qa = GrabPress::$environment == 'grabqa'; ?>
if(!window.grabModal){
      try{
        window.grabModal = new com.grabnetworks.Modal( { id : <?php echo $qa ? '1000014775' : '1720202'; ?>, tgt: '<?php echo GrabPress::$environment; ?>', width: 800, height: 450 } );
        window.grabModal.hide();
      }catch(err){
        
      }
    }
	jQuery(function(){	
		//var feed_id = <?php echo $feed_id  = isset($_GET["feed_id"]) ? $_GET["feed_id"] : "undefined"; ?>;
		var feed_action = '<?php echo $action  = isset($_GET["action"]) ? $_GET["action"] : "default"; ?>';
		if(feed_action == "preview-feed"){
		  	jQuery("#close-preview").click(function() {		  
		  		window.location = "admin.php?page=autoposter";
	  		});
		}else{
			jQuery("#close-preview").click(function() {		  
			  var form = jQuery('#preview-feed');	
			  var action = jQuery('#action-preview-feed');
			  action.val(feed_action);
			  form.submit();	
		  	});
		}			

	  	jQuery("#how-it-works").simpletip({
		  	 content: 'The Grabpress plugin gives your editors the power of our constantly updating video catalog from the dashboard of your Wordpress CMS. Leveraging automated delivery, along with keyword feed curation, the Grabpress plugin delivers article templates featuring video articles that compliment the organic content creation your site offers.<br /><br /> As an administrator, you may use Grabpress to set up as many feeds as you desire, delivering content based on intervals you specify. You may also assign these feeds to various owners, if your site has multiple editors, and the articles will wait in your drafts folder until you see a need to publish. Additionally, for smaller sites, you can automate the entire process, publishing automatically and extending the reach of your site without adding work to your busy day. <br /><br /> To get started, select a channel from our catalog, hone your feed by adding keywords, set your posting interval, and check the posting options (post interval, player style, save as draft or publish) for that feed to make sure the specifications meet your needs. Click the preview feed button to see make sure your feed will generate enough content and that the content is what you are looking for. If the feed seems to be right for you, save the feed and you will start getting new articles delivered to your site at the interval you specified. <br /><br />', 
		  	 fixed: true, 
		  	 position: 'bottom'
		});
	});
</script>