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
    <input type="hidden" name="player_id" value="<?php echo $player_id; ?>"  id="player_id" />

	<div class="wrap" >
		<fieldset id="preview-feed">
		<legend>Preview or Modify Feed Search Criteria</legend>		
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

					<select name="channel[]" id="channel-select-preview" class="channel-select-preview multiselect" multiple="multiple" style="width:500px" >
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
					<select name="provider[]" id="provider-select-preview" class="multiselect" multiple="multiple" style="<?php GrabPress::outline_invalid() ?>" onchange="doPreviewValidation()" >
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
		
		<hr class="results-divider">	
		<div class="label-tile-one-column">
			Sort by: 
			<?php  $created_checked = ($form["sort_by"]!="relevance")?'checked="checked"':"";
					$relevance_checked = ($form["sort_by"]=="relevance")?'checked="checked"':"";

			?>
			<input type="radio" class="sort_by" name="sort_by" value="created_at" <?php echo $created_checked;?> /> Date
			<input type="radio" class="sort_by" name="sort_by" value="relevance" <?php echo $relevance_checked;?> /> Relevance<br>
		</div>	
		<?php
			if(count($list_feeds["results"])){
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
				<input type="button" class="update-search" onclick="grabModal.play('<?php echo $result["video"]["guid"]; ?>')" value="Watch Video" /></p>
				
			</div>
		</div>
			<?php
				}
		}else{
			?>
			<h1>It appears we do not have any content matching your search criteria. Please modify your settings until you see the kind of videos you want in your feed</h1>
			<?php
		}
		?>
		</fieldset>
	</div>
	</form>
	<script type="text/javascript">
	<?php $qa = GrabPress::$environment == 'grabqa'; ?>
		( function ( global, $ ) {
			previewValidationErrors = function () {
				if(($("#channel-select-preview :selected").length == 0) || ($("#provider-select-preview :selected").length == 0)){
					return true;
				}
				else {
					return false;
				}
			}
			doPreviewValidation = function(){
		    	var errors = previewValidationErrors();
				if ( !errors ){
					$("#update-search").removeAttr("disabled");	
				}else{
					$("#update-search").attr("disabled", "disabled");
				}
			}

		} )( window, jQuery );	

		if(!window.grabModal){
	      try{
	        window.grabModal = new com.grabnetworks.Modal( { id : <?php echo $qa ? '1000014775' : '1720202'; ?>, tgt: '<?php echo GrabPress::$environment; ?>', width: 800, height: 450 } );
	        window.grabModal.hide();
	      }catch(err){
	        
	      }
	    }

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

		  	$("#how-it-works").simpletip({
			  	 content: 'The Grabpress plugin gives your editors the power of our constantly updating video catalog from the dashboard of your Wordpress CMS. Leveraging automated delivery, along with keyword feed curation, the Grabpress plugin delivers article templates featuring video articles that compliment the organic content creation your site offers.<br /><br /> As an administrator, you may use Grabpress to set up as many feeds as you desire, delivering content based on intervals you specify. You may also assign these feeds to various owners, if your site has multiple editors, and the articles will wait in your drafts folder until you see a need to publish. Additionally, for smaller sites, you can automate the entire process, publishing automatically and extending the reach of your site without adding work to your busy day. <br /><br /> To get started, select a channel from our catalog, hone your feed by adding keywords, set your posting interval, and check the posting options (post interval, player style, save as draft or publish) for that feed to make sure the specifications meet your needs. Click the preview feed button to see make sure your feed will generate enough content and that the content is what you are looking for. If the feed seems to be right for you, save the feed and you will start getting new articles delivered to your site at the interval you specified. <br /><br />', 
			  	 fixed: true, 
			  	 position: 'bottom'
			});

		  	$("#help").simpletip({
			  	 content: 'This input supports the following search syntax:<br/> Add a "+" before a term that must be included in your results.<br/> Add a "-" before any term that must be excluded.<br/> Add quotes around any "exact phrase" to look for <br /><br />', 
			  	 fixed: true,
			  	 position: 'bottom'
			});

			if($('#provider-select-preview option:selected').length == 0){
				$('#provider-select-preview option').attr('selected', 'selected');
			}

			if($('#channel-select-preview option:selected').length == 0){
				$('#channel-select-preview option').attr('selected', 'selected');
			}
			$("#channel-select-preview").multiselect(multiSelectOptionsChannels, {
			  	 uncheckAll: function(e, ui){
			  	 	doPreviewValidation();	 	 	
				 },
				 checkAll: function(e, ui){
				 	doPreviewValidation();	  	 	
				 }
		   });
		   $("#provider-select-preview").multiselect(multiSelectOptions, {
		  	 uncheckAll: function(e, ui){
		  	 	doPreviewValidation();
			 },
			 checkAll: function(e, ui){
			 	doPreviewValidation();
			 }
		   }).multiselectfilter();
		   
		   $(".datepicker").datepicker({
			   showOn: 'both',
			   buttonImage: '<?php echo plugin_dir_url( __FILE__ ); ?>images/icon-calendar.gif',
			   buttonImageOnly: true,
			   changeMonth: true,
			   changeYear: true,
			   showAnim: 'slideDown',
			   duration: 'fast'
			});
			var submitSearch = function(){
		   		var data = { "action" : "gp_get_preview",
		   					 "keywords" : $("#keywords").val(),
		   					 "providers" : $("#provider-select-preview").val(),
		   					 "channels" : $("#channel-select-preview").val(),
		   					 "sort_by" : $('.sort_by:checked').val(),
		   					 "created_before" : $("#created_before").val(),
		   					 "created_after" : $("#created_after").val()};
		   		$.post(ajaxurl, data, function(response) {
		   			$("#gp-catalog-container").replaceWith(response);
		   		});
		   }

		   $("#form-catalog-page").change(doPreviewValidation);
		   $("#form-catalog-page").submit(function(e){
		   		e.preventDefault();
		   		submitSearch();
		   		return false;
		   });
		   $(".sort_by").change(function(e){
		   		submitSearch();
		   });
		   
		   
		   	$('#clear-search').bind('click', function(e){
				 $("#keywords").val("");
				 $('#provider-select-preview option').attr('selected', 'selected');
				 $("#provider-select-preview").multiselect("refresh");
				 $("#provider-select-preview").multiselect({
				   selectedText: "All providers selected"
				});	
				 $('#channel-select-preview option').attr('selected', 'selected');
				 $("#channel-select-preview").multiselect("refresh");
				 $("#channel-select-preview").multiselect({
				   selectedText: "All Video Categories"
				}); 
				 $('.sort_by[value=relevance]').removeAttr("checked");
				 $('.sort_by[value=created_at]').attr("checked", "checked");
				 $("#created_before").val("");
				 $("#created_after").val("");				 			 
		   		submitSearch();
			});
			
			$(".video_summary").ellipsis(2, true, "more", "less");

		});
		
		jQuery(window).load(function () {
		    doPreviewValidation();
		});
		
	</script>


</div>