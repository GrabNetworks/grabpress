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
<div id="gp-catalog-container">    
    <form method="post" action="" id="form-catalog-page">
        <div class="wrap" >
            <input type="hidden" name="player_id" value="<?php echo $player_id; ?>"  id="player_id" />
            <input type="hidden" name="environment" value="<?php echo GrabPress::$environment; ?>"  id="environment" />
            
		<fieldset id="preview-feed">
		<legend><?php if ($form['action'] == 'gp_get_preview') {
                                    echo 'Preview or Modify Feed Search Criteria';
                               } elseif (isset($form['display']) && $form['display'] == 'Tab') {
                                   echo 'Search Criteria';
                               } else {
                                   echo 'Insert Video'; 
                               }?>
                </legend>		
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

					<select name="channels[]" id="channel-select-preview" class="channel-select multiselect" multiple="multiple" style="width:500px" onchange="GrabPressCatalog.doValidation(1)">
						<?php	
							foreach ( $list_channels as $record ) {
								$channel = $record -> category;
								$name = $channel -> name;
								$id = $channel -> id;
								$selected = ((is_array($channels)) && ( in_array( $name, $channels ) )) ? 'selected="selected"':"";
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
					<select name="providers[]" id="provider-select-preview" class="multiselect" multiple="multiple" style="<?php GrabPress::outline_invalid() ?>" onchange="GrabPressCatalog.doValidation(1)" >
					<?php			
						foreach ( $list_providers as $record_provider ) {
							$provider = $record_provider->provider;
							$provider_name = $provider->name;
							$provider_id = $provider->id;
							$provider_selected = ((isset($providers)) && (is_array($providers)) && ( in_array( $provider_id, $providers ))) ?'selected="selected"':"";
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
					From<input type="text" value="<?php echo $created_after = isset($form['created_after']) ? $form['created_after'] : ''; ?>" maxlength="8" id="created_after" name="created_after" class="datepicker" />					
					To<input type="text" value="<?php echo $created_before = isset($form['created_before']) ? $form['created_before'] : ''; ?>" maxlength="8" id="created_before" name="created_before" class="datepicker" />
				</div>
			</div>	
			<div class="label-tile">				
				<div class="tile-right">					
					<a href="#" id="clear-search" onclick="return false;" >clear search</a>
					<input type="submit" value="Search " class="update-search" id="update-search" >
				</div>
			</div>
			<br/><br/>
		
		<div class="label-tile-one-column">
			Sort by: 
			<?php  $created_checked = ($form["sort_by"]!="relevance")?'checked="checked"':"";
					$relevance_checked = ($form["sort_by"]=="relevance")?'checked="checked"':"";

			?>
			<input type="radio" class="sort_by" name="sort_by" value="created_at" <?php echo $created_checked;?> /> Date
			<input type="radio" class="sort_by" name="sort_by" value="relevance" <?php echo $relevance_checked;?> /> Relevance<br>
                        <?php if (isset($form['display']) && $form['display'] == 'Tab') {
                                if(!empty($list_feeds["results"]) && GrabPress::check_permissions_for("gp-autopost")){ ?>
                                    <input type="button" id="btn-create-feed" class="button-primary" value="<?php _e( 'Create Feed' ) ?>" />
                         <?php  }
                            } ?>
		</div>	
		 	<?php if($empty == "false"){ ?>
		 	<div class="label-tile-one-column">
				
				<input type="hidden" id="feed_count" value="<?php echo ($list_feeds["total_count"]>400)?'400':$list_feeds["total_count"]; ?>" name="feed_count"/>
                                <input type="hidden" id="page" value="0" name="page"/>
			</div>
			<?php }?>
		<?php
                    if (count($list_feeds["results"])) {
			foreach ($list_feeds["results"] as $result) {
		?>
		<div data-id="<?php echo $result['video']['video_product_id']; ?>" class="result-tile">		
		<div class="tile-left">
			<img src="<?php echo $result['video']['media_assets'][0]['url']; ?>" height="72" width="123" onclick="grabModal.play('<?php echo $result["video"]["guid"]; ?>')">
		</div>
		<div class="tile-right">			
			<h2 class="video_title">
			<?php echo $result["video"]["title"]; ?>	
			</h2>
			<p class="video_summary">
				<?php echo $result["video"]["summary"] ?>
			</p>
			<p class="video_date">
				<?php $date = new DateTime( $result["video"]["created_at"] );
				$stamp = $date->format('m/d/Y') ?>
			<span><?php echo $stamp; ?>&nbsp;&nbsp;</span><span>SOURCE: <?php echo $result["video"]["provider"]["name"]; ?></span>
			<?php if ($form['action'] == 'gp_get_catalog') { 
                                if (isset($form['display']) && $form['display'] == 'Tab') {
                                    if(GrabPress::check_permissions_for("single-post")){ ?>
                                        <input type="button" class="button-primary btn-create-feed-single" value="<?php _e( 'Create Post' ) ?>" id="btn-create-feed-single-<?php echo $result['video']['id']; ?>" />
                                        <input type="button" class="button-primary" onclick="grabModal.play('<?php echo $result["video"]["guid"]; ?>')" value="Watch Video" /></p>
                              <?php } 
                                } else {?>
                                <input type="button" class="insert_into_post" value="<?php _e( 'Insert into Post' ) ?>" id="btn-create-feed-single-<?php echo $result['video']['id']; ?>" />
                                <input type="button" class="update-search" onclick="grabModal.play('<?php echo $result["video"]["guid"]; ?>')" value="Watch Video" /></p>
                          <?php }
                              } else { ?>
                                  <input type="button" class="update-search" onclick="grabModal.play('<?php echo $result["video"]["guid"]; ?>')" value="Watch Video" /></p>
                          <?php } ?>
			
			
		</div>
	</div>
		<?php
			}
		} elseif ($form['action'] == 'gp_get_preview') {
		?>
			<h1>It appears we do not have any content matching your search criteria. Please modify your settings until you see the kind of videos you want in your feed</h1>
			<?php
		}
		?>
		</fieldset>
	</div>
	</form>
	<script type="text/javascript">
        <?php if ($form['action'] == 'gp_get_catalog') { ?>                
                 <?php if (isset($form['display']) && $form['display'] == 'Tab' ) { ?>
                     jQuery(window).load(function () {
                        GrabPressCatalog.doValidation();
                        var action = jQuery('#action-catalog');	    
                        action.val("catalog-search");            
                    });    
                    jQuery(document).ready(function(){
                        GrabPressCatalog.initSearchForm(); 
                        GrabPressCatalog.tabSearchForm();    
                    });
                 <?php } else { ?>
                     ( function ( global, $ ) {
                        global.backup_tb_position = tb_position;
                        global.tb_position = GrabPressCatalog.TB_Position;
                    })(window, jQuery);
                    jQuery(document).ready(function(){                    
                        GrabPressCatalog.postSearchForm();
                    });
                 <?php } ?>
        <?php } elseif ($form['action'] == 'gp_get_preview') { ?>
                  jQuery(document).ready(function(){                    
                       GrabPressCatalog.previewSearchForm();
                  });
        <?php } ?>
            jQuery(window).load(function () {		                        
                
            });
            jQuery(document).ready(function(){                    
                GrabPressCatalog.doValidation(1);
                GrabPressCatalog.initSearchForm();
            });
	</script>


</div>