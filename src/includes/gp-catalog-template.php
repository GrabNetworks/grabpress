<?php 

	$providers_total = count( $list_providers );
	if(($providers_total == count($providers)) || in_array("", $providers)){
		$provider_text = "All Providers";
		$providers = "";
	}else{
		$provider_text = count($providers)." of ".$providers_total." selected";
	}  

	$channels_total = count( $list_channels );

	if(($channels_total == count($channels)) || in_array("", $channels)){
		$channel_text = "All Video Categories";
	}else{
		$channel_text = count($channels)." of ".$channels_total." selected";
	}

	$id = GrabPressAPI::get_connector_id();
	$player_json = GrabPressAPI::call( 'GET',  '/connectors/'.$id.'/?api_key='.GrabPress::$api_key );
	$player_data = json_decode( $player_json, true );
	$player_id = isset($player_data["connector"]["ctp_embed_id"]) ? $player_data["connector"]["ctp_embed_id"] : '';	
?>
<form method="post" action="" id="form-catalog-page">
        <input type="hidden" id="environment" name="environment" value="<?php echo GrabPress::$environment; ?>" />
        <input type="hidden" id="" name="action" value="catalog-search" />
	<input type="hidden" id="action-catalog" name="action" value="catalog-search" />
	<input type="hidden" id="list_provider" name="list_provider" value="<?php echo (string)$list_providers; ?>" />
	<input type="hidden" name="pre_content" value="<?php echo 'Content'; ?>"  id="pre_content" />
	<input type="hidden" name="player_id" value="<?php echo $player_id = isset($player_id) ? $player_id : '' ; ?>"  id="player_id" />
	<input type="hidden" name="bloginfo" value="<?php echo get_bloginfo('url'); ?>"  id="bloginfo" />
	<input type="hidden" name="publish" value="1" id="publish" />
	<input type="hidden" name="click_to_play" value="1" id="click_to_play" />
	<input type="hidden" id="post_id" name="post_id" value="<?php echo $post_id = isset($form['post_id']) ? $form['post_id'] : '' ?>" />
	<input type="hidden" id="pre_content2" name="pre_content2" value="<?php echo $pre_content2 = isset($form['pre_content2']) ? $form['pre_content2'] : '' ?>" />
	<input type="hidden" id="keywords_and" name="keywords_and" value="<?php echo $keywords_and = isset($keywords_and) ? $keywords_and : ''; ?>" />	
	<input type="hidden" id="keywords_not" name="keywords_not" value="<?php echo $keywords_not = isset($keywords_not) ? $keywords_not : ''; ?>" />
	<input type="hidden" id="keywords_or" name="keywords_or" value="<?php echo $keywords_or = isset($keywords_or) ? $keywords_or : ''; ?>" />
	<input type="hidden" id="keywords_phrase" name="keywords_phrase" value="<?php echo $keywords_phrase = isset($keywords_phrase) ? $keywords_phrase : ''; ?>" />
	<input type="hidden" name="post_title" value=""  id="post_title" />
	
<div class="wrap" >
			<img src="http://grab-media.com/corpsite-static/images/grab_logo.jpg"/>
			<h2>GrabPress: Find a Video in our Catalog</h2>
			<p>Grab video content delivered fresh to your blog <a href="#" onclick='return false;' id="how-it-works">how it works</a></p>
	<fieldset id="preview-feed">
	<legend>Search Criteria</legend>
		<div class="label-tile-one-column">
			<span class="preview-text-catalog"><b>Keywords: </b><input name="keywords" id="keywords" type="text" value="<?php echo $keywords = isset($form['keywords']) ? $form['keywords'] : '' ?>" maxlength="255" /></span>
			<a href="#" id="help">help</a>
		</div>
		
		<div class="label-tile">
			<div class="tile-left">
				<input type="hidden" name="channels_total" value="<?php echo $channels_total; ?>" id="channels_total" />
				<span class="preview-text-catalog"><b>Grab Video Categories: </b>
				</span>
			</div>
			<div class="tile-right">
				<select name="channels[]" id="channel-select" class="channel-select multiselect" multiple="multiple" style="width:500px" onchange="GrabPressCatalog.doValidation()">
					<?php
						foreach ( $list_channels as $record ) {
							$channel = $record -> category;
							$name = $channel -> name;
							$id = $channel -> id;							
							$selected = (is_array($channels) && ( in_array( $name, $channels ) )) ? 'selected="selected"':"";							
							echo '<option value = "'.$name.'" '.$selected.' >'.$name.'</option>';
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
				<select name="providers[]" id="provider-select" class="multiselect" multiple="multiple" style="<?php GrabPress::outline_invalid() ?>" onchange="GrabPressCatalog.doValidation()" >
				<?php			
					foreach ( $list_providers as $record_provider ) {
						$provider = $record_provider->provider;
						$provider_name = $provider->name;
						$provider_id = $provider->id;
						$provider_selected = ( in_array( $provider_id, $providers ) )?'selected="selected"':"";
						echo '<option value = "'.$provider_id.'" '.$provider_selected.'>'.$provider_name.'</option>';
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
				From<input type="text" readonly="true" value="<?php echo $created_after = isset($form['created_after']) ? $form['created_after'] : ''; ?>" maxlength="8" id="created_after" name="created_after" class="datepicker" />
				To<input type="text" readonly="true" value="<?php echo $created_before = isset($form['created_before']) ? $form['created_before'] : ''; ?>" maxlength="8" id="created_before" name="created_before" class="datepicker" />
			</div>
		</div>	
		<div class="label-tile">	
			<div class="tile-right">
				<a href="#" id="clear-search" onclick="return false;" >clear search</a>				
				<input type="submit" value=" Search " class="update-search" id="update-search" >
			</div>
		</div>
		<br/><br/>	
		

	<?php
		if(isset($form["keywords"])){
	?>
	 <div class="label-tile-one-column">
               Sort by: 
               <?php  
                       $created_checked = ((isset($form["sort_by"])) && ($form["sort_by"]!="relevance")) ? 'checked="checked";':"";
                       $relevance_checked = ((isset($form["sort_by"])) &&($form["sort_by"]=="relevance")) ?'checked="checked";':"";
               ?>
               <input type="radio" class="sort_by" name="sort_by" value="created_at" <?php echo $created_checked;?> /> Date
               <input type="radio" class="sort_by" name="sort_by" value="relevance" <?php echo $relevance_checked;?> /> Relevance
               <?php if(!empty($list_feeds["results"]) && GrabPress::check_permissions_for("gp-autopost")){ ?>
                    <input type="button" id="btn-create-feed" class="button-primary" value="<?php _e( 'Create Feed' ) ?>" />
               <?php } ?>
       </div>
	<?php
			foreach ($list_feeds["results"] as $result) {
	?>
	<div data-id="<?php echo $result['video']['video_product_id']; ?>" class="result-tile" id="video-<?php echo $result['video']['id']; ?>">		
		<div class="tile-left">
			<img src="<?php echo $result['video']['media_assets'][0]['url']; ?>" height="72px" width="123px" onclick="grabModal.play('<?php echo $result["video"]["guid"]; ?>')">
		</div>
		<div class="tile-right">
			<h2 class="video_title" id="video-title-<?php echo $result['video']['id']; ?>">
			<?php echo $result["video"]["title"]; ?>	
			</h2>
			<p class="video_summary">		
				<?php echo $result["video"]["summary"];?>			
			</p>
			<p class="video_date">
				<?php $date = new DateTime( $result["video"]["created_at"] );
				$stamp = $date->format('m/d/Y') ?>
			<span><?php echo $stamp; ?>&nbsp;&nbsp;<span><span>SOURCE: <?php echo $result["video"]["provider"]["name"]; ?></span>
			<?php if(GrabPress::check_permissions_for("single-post")){ ?>
			<input type="button" class="button-primary btn-create-feed-single" value="<?php _e( 'Create Post' ) ?>" id="btn-create-feed-single-<?php echo $result['video']['id']; ?>" />
			<?php } ?>
			<input type="button" class="button-primary" onclick="grabModal.play('<?php echo $result["video"]["guid"]; ?>')" value="Watch Video" /></p>
			
		</div>
	</div>
	<?php
			} // end foreach
		} // end if	
	?>
	</fieldset>
</div>
</form>
<script>
    jQuery(window).load(function () {
	    GrabPressCatalog.doValidation();
	    var action = jQuery('#action-catalog');	    
	    action.val("catalog-search");            
	});    
    jQuery(document).ready(function(){
        GrabPressCatalog.initSearchForm(); 
        GrabPressCatalog.tabSearchForm();    
    }
);
</script>