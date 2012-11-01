<?php 
	//$providers = join($provider, ",");
    /*
	$provider_total = count(GrabPress::get_providers());
	if(($provider_total == count($provider)) || in_array("", $provider)){
		$provider_text = "All Providers";
	}else{
		$provider_text = count($provider)." of ".$provider_total." selected";
	}
	*/


	$list_provider = GrabPress::get_providers();
	$providers_total = count( $list_provider );

	/*
	$channels = join($channel, ",");
	$channel_total = count(GrabPress::get_channels());
	if(($channel_total == count($channel)) || in_array("", $channel)){
		$channel_text = "All Video Categories";
	}else{
		$channel_text = count($channel)." of ".$channel_total." selected";
	}
	*/
	$json_preview = GrabPress::get_json('http://catalog.'.GrabPress::$environment
		.'.com/catalogs/1/videos/search.json?keywords_and='.urlencode($keywords_and).'&keywords_not='.urlencode($keywords_not)
		.'&categories='.urlencode($channels).'&order=DESC&order_by=created_at&providers='.urlencode($providers));
	$list_feeds = json_decode($json_preview, true);
	
	if(empty($list_feeds["results"])){
		GrabPress::$error = 'It appears we do not have any content matching your search criteria. Please <a href="#" class="close-preview">modify your settings</a> until you see the kind of videos you want in your feed';
	}
	
?>
<form method="post" action="" id="catalog-page">
	<input type="hidden" id="action-preview-feed" name="action" value="" />	
<div class="wrap" >
			<img src="http://grab-media.com/corpsite-static/images/grab_logo.jpg"/>
			<h2>GrabPress: Find a Video in our Catalog</h2>
			<p>Grab video content delivered fresh to your blog <a href="#" onclick='return false;' id="how-it-works">how it works</a></p>
	<fieldset id="preview-feed">
	<legend>Preview Feed</legend>
	<script type="text/javascript">
		var multiSelectOptionsChannels = {
		  	 noneSelectedText:"Select Video Categories",
		  	 selectedText:function(selectedCount, totalCount){
				if (totalCount==selectedCount){
		  	 		return "All Video Categories";
		  	 	}else{
		  	 		return selectedCount + " of " + totalCount + " Video Categories";
		  	 	}
		  	 }
		};
		var multiSelectOptions = {
	  	 noneSelectedText:"Select providers",
	  	 selectedText:function(selectedCount, totalCount){
			if (totalCount==selectedCount){
	  	 		return "All providers selected";
	  	 	}else{
	  	 		return selectedCount + " providers selected of " + totalCount;
	  	 	}
	  	 }
	    };
		jQuery(function($){
			$("#channel-select").multiselect(multiSelectOptionsChannels, {
			  	 uncheckAll: function(e, ui){			  	 	
				 },
				 checkAll: function(e, ui){			  	 	
				 }
		   });
		   $("#provider-select").multiselect(multiSelectOptions, {
		  	 uncheckAll: function(e, ui){
		  	 	id = this.id.replace('provider-select-update-','');
			 },
			 checkAll: function(e, ui){
		  	 	id = this.id.replace('provider-select-update-','');
			 }
		   }).multiselectfilter();

		   $(".datepicker").datepicker({
			   showOn: 'button',
			   buttonImage: '<?php echo plugin_dir_url( __FILE__ ); ?>images/icon-calendar.gif',
			   buttonImageOnly: true,
			   changeMonth: true,
			   changeYear: true,
			   showAnim: 'slideDown',
			   duration: 'fast'
			});
		});
	</script>	
		<?php if(isset($feed_id)){ ?>
		<input type="hidden" name="feed_id" value="<?php echo $feed_id; ?>"  />
		<?php } ?>
		<?php /*
		<input type="hidden" name="referer" value="<?php echo $referer; ?>"  />
		<input type="hidden" name="active" value="<?php echo $active; ?>" id="active" />
		<input type="hidden" name="name" value="<?php echo $name; ?>" id="name" />
		<input type="hidden" name="channel" value="<?php echo $channel; ?>" id="channel" />
		<input type="hidden" name="keywords_and" value="<?php echo $keywords_and; ?>" id="keywords_and" />
		<input type="hidden" name="keywords_not" value="<?php echo $keywords_not; ?>" id="keywords_not" />
		<input type="hidden" name="limit" value="<?php echo $limit; ?>" id="limit" />
		<input type="hidden" name="schedule" value="<?php echo $schedule; ?>" id="schedule" />
		<input type="hidden" name="publish" value="<?php echo $publish; ?>" id="publish" />
		<input type="hidden" name="click_to_play" value="<?php echo $click_to_play; ?>" id="click_to_play" />
		<input type="hidden" name="author" value="<?php echo $author; ?>" id="author" />	
		*/ ?>
		<select name="channel[]" style="display:none;" multiple="multiple	">
			<?php foreach($channel as $cat){ ?>
				<option value="<?php echo $cat;?>" selected="selected"/>
			<?php } ?>
		</select>
		<select name="provider[]" style="display:none;" multiple="multiple	">
			<?php foreach($provider as $prov){ ?>
				<option value="<?php echo $prov;?>" selected="selected"/>
			<?php } ?>
		</select>
						
		<div class="label-tile-one-column">
			<span class="preview-text-catalog"><b>Keywords: </b><input id="input-keyword" type="text" value="<?php echo $keywords_and = isset($keywords_and) ? $keywords_and : '' ; ?>" /></span>
		</div>	
		
		<div class="label-tile">
			<div class="tile-left">
				<input type="hidden" name="channels_total" value="<?php echo $channels_total; ?>" id="channels_total" />
				<span class="preview-text-catalog"><b>Grab Video Categories: </b>	
				</span>
			</div>
			<div class="tile-right">		
				<select style="<?php GrabPress::outline_invalid() ?>" name="channel[]" id="channel-select" class="channel-select multiselect" multiple="multiple" style="width:500px" >
					<!--<option <?php  //( !array_key_exists( "channel", $form ) || !$form["channel"] )?'selected="selected"':"";?> value="">Choose One</option>-->							
					<?php
						/*
						if(isset($form["channel"]) && (is_array($form["channel"]))){
							$channels = $form["channel"];
						}else{
							$channels = explode( ",", $form["channel"] ); // Video categories chosen by the user
						}
						*/
						
						$json = GrabPress::get_json( 'http://catalog.'.GrabPress::$environment.'.com/catalogs/1/categories' );
						$list = json_decode( $json );
						foreach ( $list as $record ) {
							$channel = $record -> category;
							$name = $channel -> name;
							$id = $channel -> id;
							//$selected = ( in_array( $name, $channels ) ) ? 'selected="selected"':"";
							echo '<option value = "'.$name.'" >'.$name.'</option>';
						}
					?>
				</select>
			</div>
			
		</div>
		
		<div class="label-tile">
			<div class="tile-left">
				<input type="hidden" name="providers_total" value="<?php echo $providers_total; ?>" class="providers_total" id="providers_total" />
				<span class="preview-text-catalog"><b>Providers: </b></span>
			</div>
			<div class="tile-right">
				<select name="provider[]" id="provider-select" class="multiselect" multiple="multiple" style="<?php GrabPress::outline_invalid() ?>" onchange="doValidation()" >
				<?php			
					foreach ( $list_provider as $record_provider ) {
						$provider = $record_provider->provider;
						$provider_name = $provider->name;
						$provider_id = $provider->id;
						//$provider_selected = ( in_array( $provider_id, $form["provider"] ) )?'selected="selected"':"";
						echo '<option value = "'.$provider_id.'">'.$provider_name.'</option>';
					}
				?>
				</select>
			</div>			
		</div>

		<div class="clear"></div>

		<div class="label-tile">
			<div class="tile-left">
				<span class="preview-text-catalog"><b>Date Range: </b></span>
			</div>				
			<div class="tile-right">
				Between<input type="text" value="&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;" maxlength="8" id="datepicker-between" class="datepicker" />
				and<input type="text" value="&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;" maxlength="8" id="datepicker-and" class="datepicker" />
			</div>
		</div>	
		<div class="label-tile">	
			<div class="tile-right">
				<input type="button" class="button-primary" disabled="disabled" value="<?php _e( 'Create Feed' ) ?>" id="btn-create-feed" />
				<input type="button" value="Update Search" class="update-search" id="update-search" >	
				<a href="#" id="cancel" >cancel</a>
			</div>
		</div>


		<br/><br/>  	
	
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
			<input type="button" class="button-primary" disabled="disabled" value="<?php _e( 'Create Feed' ) ?>" />
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
		var feed_action = '<?php echo $action = isset($_GET["action"]) ? $_GET["action"] : "default"; ?>';
		if(feed_action == "preview-feed"){
		  	jQuery(".close-preview").click(function() {		  
		  		window.location = "admin.php?page=autoposter";
	  		});
		}else{
			jQuery(".close-preview").click(function() {		  
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
