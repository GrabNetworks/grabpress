<?php 
	$list_provider = GrabPress::get_providers();
	$providers_total = count( $list_provider );
	if(isset($form['provider'])){
		$providers = isset($form['provider']) ? join($form['provider'], ","): "";

		if(($providers_total == count($form['provider'])) || in_array("", $form['provider'])){
			$provider_text = "All Providers";
			$providers = "";
		}else{
			$provider_text = count($form['provider'])." of ".$providers_total." selected";
		}  
	}else{
		$providers = "";
	}

	$list_channels = GrabPress::get_channels();
	$channels_total = count( $list_channels );

	if(isset($form['channel'])){
		$channels = isset($form['channel']) ? join($form['channel'], ","): "";		
		$channel_total = count(GrabPress::get_channels());

		if(($channel_total == count($form['channel'])) || in_array("", $form['channel'])){
			$channel_text = "All Video Categories";
			$channels = "";
		}else{
			$channel_text = count($form['channel'])." of ".$channel_total." selected";
		}
	}else{
		$channels = "";
	}	

	$adv_search_params = GrabPress::parse_adv_search_string(isset($form["keywords"])?$form["keywords"]:"");

	if(isset($form['created_before']) && ($form['created_before'] != "")){
		$created_before_date = new DateTime( $form['created_before'] );	
		$created_before = $created_before_date->format('Ymd');
		$adv_search_params['created_before'] = $created_before;
	}
	
	if(isset($form['created_after']) && ($form['created_after'] != "")){
		$created_after_date = new DateTime( $form['created_after'] );
		$created_after = $created_after_date->format('Ymd');
		$adv_search_params['created_after'] = $created_after;
	}
	$adv_search_params["providers"] = $providers;
	$adv_search_params["categories"] = $channels;
	$url_catalog = GrabPress::generate_catalog_url($adv_search_params);

	$json_preview = GrabPress::get_json($url_catalog);

	$list_feeds = json_decode($json_preview, true);	
	
	if(empty($list_feeds["results"])){
		GrabPress::$error = 'It appears we do not have any content matching your search criteria. Please modify your settings until you see the kind of videos you want in your feed';
	}
	
	$id = GrabPress::get_connector_id();
	$player_json = GrabPress::api_call( 'GET',  '/connectors/'.$id.'/?api_key='.GrabPress::$api_key );
	$player_data = json_decode( $player_json, true );
	$player_id = isset($player_data["connector"]["ctp_embed_id"]) ? $player_data["connector"]["ctp_embed_id"] : '';	
?>
<div id="gp-catalog-container">

	<form method="post" action="" id="form-catalog-page">
    <input type="hidden" name="player_id" value="<?php echo $player_id; ?>"  id="player_id" />

	<div class="wrap" >
				<img src="http://grab-media.com/corpsite-static/images/grab_logo.jpg"/>
				<h2>GrabPress: Find a Video in our Catalog</h2>
				<p>Grab video content delivered fresh to your blog <a href="#" onclick='return false;' id="how-it-works">how it works</a></p>
		<fieldset id="preview-feed">
		<legend>Preview Feed</legend>		
			<div class="label-tile-one-column">
				<span class="preview-text-catalog"><b>Keywords: </b><input name="keywords" id="keywords" type="text" value="<?php echo $keywords = isset($form['keywords']) ? htmlentities(stripslashes($form['keywords']), ENT_QUOTES)  : '' ?>" maxlength="255" /></span>
				<a href="#" id="help">help</a>
			</div>	
			
			<div class="label-tile">
				<div class="tile-left">
					<input type="hidden" name="channels_total" value="<?php echo $channels_total; ?>" id="channels_total" />
					<span class="preview-text-catalog"><b>Grab Video Categories: </b>	
					</span>
				</div>
				<div class="tile-right">
					<?php 				
						if(isset($form["channel"])){
							if(is_array($form["channel"])){
								$channels = $form["channel"];
							}else{
								$channels = explode( ",", $form["channel"] ); // Video categories chosen by the user
							}
						}					
						$list = GrabPress::get_channels();		
					?>		
					<select name="channel[]" id="channel-select" class="channel-select multiselect" multiple="multiple" style="width:500px" >
						<!--<option <?php  //( !array_key_exists( "channel", $form ) || !$form["channel"] )?'selected="selected"':"";?> value="">Choose One</option>-->							
						<?php	
							foreach ( $list as $record ) {
								$channel = $record -> category;
								$name = $channel -> name;
								$id = $channel -> id;
								$selected = ( in_array( $name, $channels ) ) ? 'selected="selected"':"";
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
					<select name="provider[]" id="provider-select" class="multiselect" multiple="multiple" style="<?php GrabPress::outline_invalid() ?>" onchange="doValidation()" >
					<?php			
						foreach ( $list_provider as $record_provider ) {
							$provider = $record_provider->provider;
							$provider_name = $provider->name;
							$provider_id = $provider->id;
							$provider_selected = ( in_array( $provider_id, $form["provider"] ) )?'selected="selected"':"";
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
					<input type="submit" value=" Search " class="update-search" id="update-search" >
				</div>
			</div>
			<br/><br/>	
		<?php
			foreach ($list_feeds["results"] as $result) {
		?>
		<div data-id="<?php echo $result['video']['video_product_id']; ?>" class="result-tile">		
		<div class="tile-left">
			<img src="<?php echo $result['video']['media_assets'][0]['url']; ?>" height="72px" width="123px" onclick="grabModal.play('<?php echo $result["video"]["guid"]; ?>')">
		</div>
		<div class="tile-right">			
			<h2 class="video_title">
			<?php echo $result["video"]["title"]; ?>	
			</h2>
			<p class="video_summary">		
				<?php echo $result["video"]["summary"];?>			
			</p>
			<p class="video_date">
				<?php $date = new DateTime( $result["video"]["created_at"] );
				$stamp = $date->format('m/d/Y') ?>
			<span><?php echo $stamp; ?>&nbsp;&nbsp;<span><span>SOURCE: <?php echo $result["video"]["provider"]["name"]; ?></span>
			<input type="button" class="button-primary btn-create-feed-single" value="<?php _e( 'Create Post' ) ?>" id="btn-create-feed-single-<?php echo $result['video']['id']; ?>" /><input type="button" class="button-primary" onclick="grabModal.play('<?php echo $result["video"]["guid"]; ?>')" value="Watch Video" /></p>
			
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
		( function ( global, $ ) {
			global.defaultthickboxresizehandler = null;
			global.hasValidationErrors = function () {
				if(($("#channel-select :selected").length == 0) || ($("#provider-select :selected").length == 0)){
					return true;
				}
				else {
					return false;
				}
			}
			global.backup_tb_position = tb_position;
			global.tb_position = function(){
				var SpartaPaymentWidth			= 900;
				var TB_newWidth			= jQuery(window).width() < (SpartaPaymentWidth + 40) ? jQuery(window).width() - 40 : SpartaPaymentWidth;
				var TB_newHeight		= jQuery(window).height() - 70;
				var TB_newMargin		= (jQuery(window).width() - SpartaPaymentWidth) / 2;

				jQuery('#TB_window').css({'marginLeft': -(TB_newWidth / 2), "marginTop": -(TB_newHeight / 2)});
				jQuery('#TB_window, #TB_iframeContent').width(TB_newWidth).height(TB_newHeight);

			}
			global.doValidation = function(){
		    	var errors = hasValidationErrors();
				if ( !errors ){
					$('#btn-create-feed').removeAttr('disabled');
					$('#btn-preview-feed').removeAttr('disabled');

					if( $( '#btn-preview-feed' ).off ){
						$( '#btn-preview-feed' ).off('click');
					}else{
						$( '#btn-preview-feed' ).unbind('click');
					}
					$('.hide').show();					
				}else{
					$( '#btn-create-feed' ).attr('disabled', 'disabled');
					$( '#btn-preview-feed' ).attr('disabled', 'disabled');
				
					if( $( '#btn-preview-feed' ).off ){
						$( '#btn-preview-feed' ).off('click');
					}else{
						$( '#btn-preview-feed' ).unbind('click');
					}				

					$('.hide').hide();
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
			// jQuery.each( jQuery(window).data('events')['resize'], function(i, event) {
			// 	if(event){
			// 		var thisEvent		= event.handler.toString().replace(/\n/g, '').replace(/\t/g, '').split(' ').join('');
			// 		var expectedEvent	= 'function(){tb_position()}';

			// 	    if (thisEvent == expectedEvent) {
			// 			jQuery(window).unbind("resize", event.handler);
			// 			defaultthickboxresizehandler = event.handler;
			// 			return;
			// 		}
			// 	}
			// });

			

			var feed_action = '<?php echo $action = isset($_GET["action"]) ? $_GET["action"] : "default"; ?>';
			if(feed_action == "preview-feed"){
			  	$(".close-preview").click(function() {		  
			  		window.location = "admin.php?page=autoposter";
		  		});
			}else{
				$(".close-preview").click(function() {		  
				  var form = $('#preview-feed');	
				  var action = $('#action-preview-feed');
				  action.val(feed_action);
				  form.submit();	
			  	});
			}			

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

			if($('#provider-select option:selected').length == 0){
				$('#provider-select option').attr('selected', 'selected');
			}

			if($('#channel-select option:selected').length == 0){
				$('#channel-select option').attr('selected', 'selected');
			}
			$("#channel-select").multiselect(multiSelectOptionsChannels, {
			  	 uncheckAll: function(e, ui){
			  	 	doValidation();	 	 	
				 },
				 checkAll: function(e, ui){
				 	doValidation();	  	 	
				 }
		   });
		   $("#provider-select").multiselect(multiSelectOptions, {
		  	 uncheckAll: function(e, ui){
		  	 	doValidation();
			 },
			 checkAll: function(e, ui){
			 	doValidation();
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

		   $("#form-catalog-page").change(doValidation);
		   $("#form-catalog-page").submit(function(e){
		   		e.preventDefault();
		   		var data = { "action" : "get_catalog", 
		   					 "keywords" : $("#keywords").val(),
		   					 "providers" : $("#providers").val(),
		   					 "channels" : $("#channels").val(),
		   					 "created_before" : $("#created_before").val(),
		   					 "created_after" : $("#created_after").val()};
		   		$.post(ajaxurl, data, function(response) {
		   			$("#gp-catalog-container").replaceWith(response);
		   		});
		   		return false;
		   });
		   
		   
		   	$('.btn-create-feed-single').bind('click', function(e){
			    var form = $('#form-catalog-page');
			    var ctp_player_id = $('#player_id').val();
			    var bloginfo = $('#bloginfo').val();
			    var video_id = this.id.replace('btn-create-feed-single-','');

			    var data = {
					action: 'get_mrss_format',
					video_id: video_id
				};

				$.post(ajaxurl, data, function(response) {
					//alert('Got this from the server: ' + response);
					var content = response.replace(/1825613/g, ctp_player_id);

					var win = window.dialogArguments || opener || parent || top;
					win.send_to_editor(content);
					win.tb_remove();
					tb_position = backup_tb_position
					return false;
				});		  

			});	

		   	$('#clear-search').bind('click', function(e){
		   		window.location = "admin.php?page=catalog";		    
			});
		

		});

		jQuery(window).load(function () {
		    doValidation();
		});
		
	</script>


</div>