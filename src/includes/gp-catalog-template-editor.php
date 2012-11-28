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

	if(isset($form['keywords_and'])){
		
		preg_match_all('/"([^"]*)"/', $form['keywords_and'], $result_exact_phrase, PREG_PATTERN_ORDER);
		for ($i = 0; $i < count($result_exact_phrase[0]); $i++) {
			$matched_exact_phrase[] = stripslashes($result_exact_phrase[0][$i]);
		}		

		$sentence = preg_replace('/"([^"]*)"/', '', stripslashes($form['keywords_and']));
		
		$keywords = preg_split("/\s+/", $sentence);
		for ($i = 0; $i < count($keywords); $i++) {
			if (preg_match('/\+/', $keywords[$i])) {
			  //sscanf($keywords[$i], "+%s", $temp_and); # this is poor
			  $temp_and = str_replace('+', '', $keywords[$i]);
	          $keywords_and[] = $temp_and;
			}elseif (preg_match("/^-/", $keywords[$i])) { 
			  $temp_not = str_replace('-', '', $keywords[$i]);
	          $keywords_not[] = $temp_not;	          
			}else{
			  $keywords_or[] = $keywords[$i];
			}	
		}
	}

	$keywords_phrase = isset($matched_exact_phrase) ? implode(",", $matched_exact_phrase) : "";
	$keywords_and = isset($keywords_and) ? implode(",", $keywords_and) : "";
	$keywords_not = isset($keywords_not) ? implode(",", $keywords_not) : "";
	$keywords_or = isset($keywords_or) ? implode(",", $keywords_or) : "";

	if(isset($form['created_before'])){
		$created_before_date = new DateTime( $form['created_before'] );	
		$created_before = $created_before_date->format('Ymd');
	}else{
		$created_before = "";
	}

	if(isset($form['created_after'])){
		$created_after_date = new DateTime( $form['created_after'] );
		$created_after = $created_after_date->format('Ymd');	
	}else{
		$created_after = "";
	}
	
	
	$url = GrabPress::generate_catalog_url(array(
			"keywords_and" => $keywords_and,
			"keywords_not" => $keywords_not,
			"keywords_or" => $keywords_or,
			"keywords_phrase" => $keywords_phrase,
			"providers" => $providers,
			"categories" => $channels,
			"created_after" => $created_after,
			"created_before" => $created_before,
		), true);

	$json_preview = GrabPress::get_json($url);
	$list_feeds = json_decode($json_preview, true);	
	
	if(empty($list_feeds["results"])){
		GrabPress::$error = 'It appears we do not have any content matching your search criteria. Please <a href="#" class="close-preview">modify your settings</a> until you see the kind of videos you want in your feed';
	}
	
	$id = GrabPress::get_connector_id();
	$player_json = GrabPress::api_call( 'GET',  '/connectors/'.$id.'/?api_key='.GrabPress::$api_key );
	$player_data = json_decode( $player_json, true );
	$player_id = isset($player_data["connector"]["ctp_embed_id"]) ? $player_data["connector"]["ctp_embed_id"] : '';	
?>

<style>
/*
#adminmenuwrap {
    display: none !important;
    background-color:#FFF !important;
    border-color: #FFF !important;
}
#adminmenushadow, #adminmenuback{
	background-image: none !important;
    background-position: right top !important;
    background-repeat: repeat-y !important;
    background-color: #FFF !important;
    border-color: #FFF !important;
    border-width: 0 !important;
    border-style: none !important;    
}
#wpadminbar{
 	display: none !important;
}
*/
</style>

<form method="post" action="" id="form-catalog-page" name="form_catalog_page">
	<input type="hidden" id="action-catalog" name="action" value="catalog-search-editor" />
	<input type="hidden" id="keywords_not" name="keywords_not" value="<?php echo $keywords_not; ?>" />
	<input type="hidden" id="list_provider" name="list_provider" value="<?php echo $list_provider; ?>" />
	<input type="hidden" name="pre_content" value="<?php echo 'Content'; ?>"  id="pre_content" />
	<input type="hidden" name="player_id" value="<?php echo $player_id; ?>"  id="player_id" />
	<input type="hidden" name="bloginfo" value="<?php echo get_bloginfo('url'); ?>"  id="bloginfo" />
	<input type="hidden" id="post_id" name="post_id" value="<?php echo $post_id = isset($_REQUEST['post_id']) ? $_REQUEST['post_id'] : '' ?>" />
	<input type="hidden" id="pre_content2" name="pre_content2" value="<?php echo $post_id = isset($_REQUEST['pre_content2']) ? $_REQUEST['pre_content2'] : '' ?>" />
<div class="wrap" >
			<img src="http://grab-media.com/corpsite-static/images/grab_logo.jpg"/>
			<h2>GrabPress: Find a Video in our Catalog</h2>
			<p>Grab video content delivered fresh to your blog <a href="#" onclick='return false;' id="how-it-works">how it works</a></p>
	<fieldset id="preview-feed">
	<legend>Search Video</legend>		

		<div class="label-tile-one-column">
			<span class="preview-text-catalog"><b>Keywords: </b><input name="keywords_and" id="keywords_and" type="text" value="" maxlength="255" /></span>
			<a href="#" id="help">help</a>
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
						
						$list = GrabPress::get_channels();
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
				Between<input type="text" value="" maxlength="8" id="created_after" name="created_after" class="datepicker" />
				and<input type="text" value="" maxlength="8" id="created_before" name="created_before" class="datepicker" />
			</div>
		</div>	
		<div class="label-tile">	
			<div class="tile-right">
				<a href="#" id="clear-search" onclick="return false;" >clear search</a>
				<input type="button" id="btn-create-feed" class="button-primary" value="<?php _e( 'Create Feed' ) ?>" />				
				<input type="submit" value="Update Search" class="update-search" id="update-search" >
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
			<input type="button" class="button-primary btn-create-feed-single" value="<?php _e( 'Create Feed' ) ?>" id="btn-create-feed-single-<?php echo $result['video']['id']; ?>" />
		</div>
	</div>
	<?php
		} 	
	?>
	</fieldset>
</div>
</form>

<script type="text/javascript">

var FileBrowserDialogue = {
    init : function () {
        // Here goes your code for setting your custom things onLoad.
        alert("entro a init");
    },
    mySubmit : function () {
    	alert("entro a mySubmit)";
        var URL = document.form_catalog_page.keywords_and.value;
        var win = tinyMCEPopup.getWindowArg("window");

        // insert information now
        win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = URL;

        // are we an image browser
        /*
        if (typeof(win.ImageDialog) != "undefined")
        {
            // we are, so update image dimensions and preview if necessary
            if (win.ImageDialog.getImageData) win.ImageDialog.getImageData();
            if (win.ImageDialog.showPreviewImage) win.ImageDialog.showPreviewImage(URL);
        }
        */

        // close popup window
        tinyMCEPopup.close();
    }
}
alert("entro a FileBrowserDialogue");
tinyMCEPopup.onInit.add(FileBrowserDialogue.init, FileBrowserDialogue);
</script>


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
	   
	   $('#btn-create-feed').bind('click', function(e){
		    var form = jQuery('#form-catalog-page');
		    var action = jQuery('#action-catalog');
		    action.val("prefill");
		    form.attr("action", "admin.php?page=autoposter");		    
		    //window.location = "admin.php?page=autoposter&action=prefill";
		    form.submit();
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
				alert(content);

				$('#pre_content').val(content);	

				alert("entro al boton");			
			    form.attr("ACTION", bloginfo+"/wp-admin/post-new.php");
			    form.submit();		
			    //form.FileBrowserDialogue.mySubmit();
			    //FileBrowserDialogue.mySubmit();		
			});		
			*/  

		});	

		/*
		$('.btn-preview-feed').bind("click",function(e){
          	id = this.id.replace('btn-preview-feed-','');
          	previewFeed(id);
	        return false;
	    });  
	    */ 

	   	$('#clear-search').bind('click', function(e){
	   		window.location = "admin.php?page=catalog";		    
		});

	});

	jQuery(window).load(function () {
	    doValidation();
	});
</script>
