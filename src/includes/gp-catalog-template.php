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
				<select name="channel[]" id="channel-select" class="channel-select multiselect" multiple="multiple" style="width:500px" >
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
				<select name="provider[]" id="provider-select" class="multiselect" multiple="multiple" style="<?php GrabPress::outline_invalid() ?>" onchange="doValidation()" >
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
<script type="text/javascript">
<?php $qa = GrabPress::$environment == 'grabqa'; ?>
	( function ( global, $ ) {
		global.hasValidationErrors = function () {
			if(($("#channel-select :selected").length == 0) || ($("#provider-select :selected").length == 0)){
				return true;
			}
			else {
				return false;
			}
		}
		global.doValidation = function(){
	    	var errors = hasValidationErrors();
			if ( !errors ){
				$('#btn-create-feed').removeAttr('disabled');
				$('#update-search').removeAttr('disabled');

				if( $( '#update-search' ).off ){
					$( '#update-search' ).off('click');
				}else{
					$( '#update-search' ).unbind('click');
				}
				$('.hide').show();					
			}else{
				$( '#btn-create-feed' ).attr('disabled', 'disabled');
				$( '#update-search' ).attr('disabled', 'disabled');
			
				if( $( '#update-search' ).off ){
					$( '#update-search' ).off('click');
				}else{
					$( '#update-search' ).unbind('click');
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
		var feed_action = '<?php echo $action = isset($_GET["action"]) ? $_GET["action"] : "default"; ?>';
		if(feed_action == "preview-feed"){
		  	$(".close-preview").click(function() {		  
		  		window.location = "admin.php?page=gp-autoposter";
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
		  	 content: "This search input supports Google syntax for advanced search:<br/><b>Every</b> term separated only by a space will be required in your results.<br/>At least one of any terms separated by an ' OR ' will be included in your results.<br/>Add a '-' before any term that must be <b>excluded</b>.<br/> Add quotes around any \"exact phrase\" to look for.<br /><br />", 
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
	   
	   $('#btn-create-feed').bind('click', function(e){
		    var form = jQuery('#form-catalog-page');
		    var action = jQuery('#action-catalog');
		    
		    action.val("prefill");
		    form.attr("action", "admin.php?page=gp-autoposter");
		    form.submit();
		});
		$(".sort_by").change(function(e){
			var form = jQuery('#form-catalog-page');
			form.submit();
		});


	   	$('.btn-create-feed-single').bind('click', function(e){
		    var v_id = this.id.replace('btn-create-feed-single-','');

		    var data = {
				action: 'gp_insert_video',
				format : 'post',
				video_id: v_id
			};
			
			$.post(ajaxurl, data, function(response) {
				if(response.status == "redirect"){
					window.location = response.url;
				}
			}, "json");	
		});	

	   	$('#clear-search').bind('click', function(e){
	   		window.location = "admin.php?page=gp-catalog";		    
		});
		$(".video_summary").ellipsis(2, true, "more", "less");

	});

	jQuery(window).load(function () {
	    doValidation();
	    var action = jQuery('#action-catalog');	    
	    action.val("catalog-search");
	});
</script>
