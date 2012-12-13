<!--<form method="post" action="" id="form-create-feed">-->
<div class="wrap">
	<img src="http://grab-media.com/corpsite-static/images/grab_logo.jpg"/>
	<h2>GrabPress: Autopost Videos by Category and Keywords</h2>
	<p>Feed your blog with fresh video content.</p>
		<fieldset id="create-form" class="<?php echo isset($_GET['action'])=='edit-feed' ? 'edit-mode':''?>">
		<legend><?php echo isset($_GET['action'])=='edit-feed' ? 'Edit':'Create'?> Feed</legend>
	<script type="text/javascript">
	( function ( global, $ ) {

		global.hasValidationErrors = function () {
			if(($("#channel-select :selected").length == 0) || ($("#provider-select :selected").length == 0)){
				return true;
			}
			else {
				return false;
			}
		}

		global.previewVideos = function () {
			var errors = hasValidationErrors();

			if(!errors){
				$("#form-create-feed input[name=action]").val("preview-feed");
				$("#form-create-feed").submit();
			}else{
				alert(errors);
			}
		}

		global.deleteFeed = function(id){
			var bg_color = $('#tr-'+id+' td').css("background-color")
			$('#tr-'+id+' td').css("background-color","red");	
			var form = $('#form-'+id);
			var action = $('#action-'+id);			
			var answer = confirm('Are you sure you want to delete this feed? You will no longer receive videos based on its settings. Existing video posts will not be deleted.');
				if(answer){					
				    var data = {
						action: 'delete_action',
						feed_id: id
					};

					$.post(ajaxurl, data, function(response) {
						window.location = "admin.php?page=autoposter";
					});

				} else{					
					$('#tr-'+id+' td').css("background-color", bg_color);
					return false;
				}
		}
		global.selectedCategories = <?php echo json_encode( $form["category"] );?>;

		global.editFeed = function(id) {
			window.location = "admin.php?page=autoposter&action=edit-feed&feed_id="+id;
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

		global.validateFeedName = function(edit){
			var feed_date = $('#feed_date').val();
			var name = $('#name').val();
			if(name == ""){
				$('#name').val(feed_date);
				$("#form-create-feed").submit();
			}
			name = $.trim($('#name').val());
			var regx_name = /\s/;		
			var regx = /^[a-zA-Z0-9,\s]+$/;

			var data = {
				action: 'get_name_action',
				name: name
			};

			// Update feed
			if(edit === "update"){ 
				if(!regx.test(name)){
					alert("The name entered contains special characters or starts/ends with spaces. Please enter a different name");
				}else if(name.length < 6){					
					alert("The name entered is less than 6 characters. Please enter a name between 6 and 14 characters");
				}else {
					$('#name').val(name);
					$("#form-create-feed").submit();
				}
	
			}else{  // Create feed
				$.post(ajaxurl, data, function(response) {
					//alert('Got this from the server: ' + response);
				    if(response != "true"){
					   	if((feed_date == name) && ((typeof edit === "undefined") || (edit===null))){
							$('#dialog-name').val(name);
							$('#dialog').dialog('open');
				    	}else{
							if(!regx.test(name)){
								alert("The name entered contains special characters or starts/ends with spaces. Please enter a different name");
							}else if(name.length < 6){					
								alert("The name entered is less than 6 characters. Please enter a name between 6 and 14 characters");
							}else {
								$('#name').val(name);
								$("#form-create-feed").submit();
							}				
						}
					}else{					
						alert("The name entered is already in use. Please select a different name");
					}				
				});	
			}

		}

	} )( window, jQuery );

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

	var multiSelectOptionsCategories = {
	  	 noneSelectedText:"Select categories",
	  	 selectedText: "# of # selected"
	};

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

	jQuery(function($){
		$('#reset-form').bind('click', function(e){
		    var referer = $("input[name=referer]").val();
		    
		    if( referer == "create" ){
		    	window.location = "admin.php?page=autoposter";
		    }else{
		    	var id = $("input[name=feed_id]").val();
		    	window.location = "admin.php?page=autoposter&action=edit-feed&feed_id="+id;
		    }
		    
		});

	   $("#form-create-feed input").keypress(function(e) {
		    if(e.which == 13) {
		        e.preventDefault();
		        return false;
		    }
		});

		if($('#provider-select option:selected').length == 0){
			$('#provider-select option').attr('selected', 'selected');
		}

		if($('#channel-select option:selected').length == 0){
			$('#channel-select option').attr('selected', 'selected');
		}

		var category_options = $('#cat option');
		for(var i=0;i<category_options.length; i++){
			if($.inArray($(category_options[i]).val(),selectedCategories)>-1){
				$(category_options[i]).attr("selected", "selected");
			}
		}

		$("#provider-select").multiselect(multiSelectOptions, {
	  	 uncheckAll: function(e, ui){
	  	 	doValidation();
		 },
		 checkAll: function(e, ui){
		 	/*
		 	if($("#provider-select :selected").length != 0){
				$('.hide').show();
			}
			*/
			doValidation();
		 }
		  }).multiselectfilter();

		  $(".provider-select-update").multiselect(multiSelectOptions, {
		  	 uncheckAll: function(e, ui){
		  	 	id = this.id.replace('provider-select-update-','');
			 },
			 checkAll: function(e, ui){
		  	 	id = this.id.replace('provider-select-update-','');
			 }
		   }).multiselectfilter();

		  $('.btn-update').bind('click', function(e){
		    id = $(this).attr('name');
			var form = $('#form-'+id);
			var action = $('#action-'+id);
			if($("#provider-select-update-" + id + " :selected").length == 0){
				alert("Please select at least one provider");
				e.preventDefault();
			}else{
				action.val("modify");
				form.submit();
			}
		  });

		  $("#cat").multiselect(multiSelectOptionsCategories,
		  {
		  	header:false
		  });

		  $(".postcats").multiselect(multiSelectOptionsCategories, {
		  	header:false,
		  	uncheckAll: function(e, ui){
		  	 	id = this.id.replace('postcats-','');
			 },
			 checkAll: function(e, ui){
		  	 	id = this.id.replace('postcats-','');
			 }
		  }).multiselectfilter();

		  //$(".channel-select").selectmenu();
		  $(".schedule-select").selectmenu();
		  $(".limit-select").selectmenu();
		  $(".author-select").selectmenu();

		  $("#learn-more").simpletip({
		  	 content: 'Please be aware that selecting a click-to-play player can negatively impact your revenue, <br />as not all users will generate an ad impression. If you are looking to optimize revenue <br />through Grabpress, all feeds should be set to autoplay. ',
		  	 fixed: true,
		  	 position: 'bottom'
		  });
		  $('input, textarea').placeholder();

		  $('.active-check').bind('click', function(e){

		  	var id = this.id.replace('active-check-','');
		  	var active_check = $(this);

		  	if(active_check.is(':checked')) {
		        var active = 1;		        
		        $('#tr-'+id+' td').css("background-color","#FFE4C4");
		    }else{
		    	var active = 0;
		    	$('#tr-'+id+' td').css("background-color","#DCDCDC");		    	
		    }		    

		    var data = {
				action: 'my_action',
				feed_id: id,
				active: active
			};

			$.post(ajaxurl, data, function(response) {
				//alert('Got this from the server: ' + response);
				var substr = response.split('-');
				var num_active_feeds = substr[0];
				var num_feeds =  substr[1];
				var noun = 'feed';	
				var autoposter_status = 'ON';
				var feeds_status = 'active';		

				/*
				if( (num_active_feeds == 1) || (num_feeds == 1) ){
					noun = 'feed';	
				}else */
				if(num_active_feeds == 0){
					var autoposter_status = 'OFF';
					var feeds_status = 'inactive';
					response = '';					
					num_active_feeds = num_feeds;
					if(num_feeds > 1){
						noun = noun + 's';
					}					
				}else if( (num_active_feeds == 1) ){
					noun = 'feed';	
				}else{
					noun = noun + 's';
				}
				
				$('#num-active-feeds').text(num_active_feeds);	
				$('#noun-active-feeds').text(noun);

				$('#autoposter-status').text(autoposter_status);
				$('#feeds-status').text(feeds_status);
			});


		  });	  

		   $('#cancel-editing').bind('click', function(e){ 
				var answer = confirm('Are you sure you want to cancel editing? You will continue to receive videos based on its settings. All of your changes will be lost.');
				if(answer){				
					window.location = "admin.php?page=autoposter";
				} else{				
					return false;
				}
		  });

		  $(".ui-selectmenu").click(function(){
			    $(".ui-multiselect-menu").css("display", "none");
			});		  

		  $("#channel-select").multiselect(multiSelectOptionsChannels, {
		  	 uncheckAll: function(e, ui){
		  	 	
			 },
			 checkAll: function(e, ui){
		  	 	
			 }
		   });

		  $("#form-create-feed").change(doValidation);	
		  //$("input").keyup(doValidation);
		  //$("input").click(doValidation);
		  //$("select").change(doValidation);

		  $('#dialog').dialog({
            autoOpen: false,
            width: 400,
            modal: true,
            resizable: false,
            buttons: {
            	"Cancel": function() {
                  $(this).dialog("close");
                },
                "Create Feed": function() {
                  var name = $("#dialog-name").val();
                  $("#name").val(name);
                  validateFeedName("edit");
                }
            }
          }); 

	       $(".btn-update-feed").mousedown(function(event) {
			   if( event.which == 2 ) {
			   	  return false;
			   	  id = this.id.replace('btn-update-','');
          	      editFeed(id); 
			   }
		   });
	      $('.btn-update-feed').bind("click",function(e){
          	id = this.id.replace('btn-update-','');
          	editFeed(id);
	        return false;
	      });

          $('.btn-update-feed').bind("contextmenu",function(e){
          	id = this.id.replace('btn-update-','');
          	editFeed(id);
	        return false;
	      });

	});

	jQuery(window).load(function () {
	    doValidation();
	});

	</script>
	<?php
		$rpc_url = get_bloginfo( 'url' ).'/xmlrpc.php';
		$connector_id = GrabPress::get_connector_id();
	?>
	<form method="post" action="" id="form-create-feed">
		<?php 
			if(isset($form["feed_id"])) {
				$feed_id = $form["feed_id"];
		?>
			<input type="hidden"  name="feed_id" value="<?php echo $feed_id; ?>" />
		<?php		
			}
		?>
		<?php 
			if(isset($form["active"])) {
				$active = $form["active"];
		?>
			<input type="hidden"  name="active" value="<?php echo $active; ?>" />
		<?php		
			}
		?>
		<?php  
			if(isset($form["referer"])){
				$referer = ($form["referer"] == "edit") ? 'edit' : 'create';
			}else{
				$referer = "create";
			}	
			if(isset($form["action"])){		
				$value = ($form["action"] == "modify") ? 'modify' : 'update';
			}else{
				$value = "update";
			}
		?>

		<input type="hidden"  name="referer" value="<?php echo $referer; ?>" />
		<input type="hidden"  name="action" value="<?php echo $value; ?>" />
        	<table class="form-table grabpress-table">
	            <?php if (GrabPress::$environment == 'grabqa'){ ?>
	                <tr valign="bottom">
						<th scope="row">Plug-in Version & Build Number</th>
			            <td>
							<?php echo GrabPress::$version ?>
						</td>
					</tr>
	                <tr valign="bottom">
						<th scope="row">API Key</th>
			            <td>
							<?php echo get_option( 'grabpress_key' ); ?>
						</td>
					</tr>
				<?php } ?>
				<tr>
					<td>
						<h3>Search Criteria</h3>
					</td>					
				</tr>
				<tr valign="bottom">
					<th scope="row">Feed Name</th>
        		    <td>
        		    	<?php $feed_date = date("YmdHis"); ?>	
        		    	<input type="hidden" name="feed_date" value="<?php echo $feed_date = isset($form["feed_date"])? $form["feed_date"] : $feed_date; ?>" id="feed_date" />
        		    	<?php $name = isset($form["name"])? urldecode($form["name"]) : $feed_date; ?>
						<input type="text" name="name" id="name" class="ui-autocomplete-input" value="<?php echo $name; ?>" maxlength="14" />
						<span class="description">A unique name of 6-14 characters. We encourage customizing it.</span>
					</td>
        		</tr>
				<tr valign="bottom">
					<th scope="row">Grab Video Categories<span class="asterisk">*</span></th>
					<td>
						<input type="hidden" name="channels_total" value="<?php echo $channels_total; ?>" id="channels_total" />					
						<select  style="<?php GrabPress::outline_invalid() ?>" name="channel[]" id="channel-select" class="channel-select multiselect" multiple="multiple" style="width:500px" >
							<?php								
								if(is_array($form["channel"])){
									$channels = $form["channel"];
								}else{
									$channels = explode( ",", rawurldecode($form["channel"])); // Video categories chosen by the user
								}
								
								foreach ( $list_channels as $record ) {
									$channel = $record -> category;
									$name = $channel -> name;
									$id = $channel -> id;
									$selected = ( in_array( $name, $channels ) ) ? 'selected="selected"':"";
									echo '<option value = "'.$name.'" '.$selected.'>'.$name.'</option>';
								}
							?>
						</select>
						<span class="description">Add or remove specific video categories from this feed</span>
					</td>
				</tr>
	        	<tr valign="bottom">
					<th scope="row">Keywords</th>
        		           	<td >
						<input type="text" name="keywords_and" id="keyword-input" class="ui-autocomplete-input" value="<?php echo $form['keywords_and']; ?>" maxlength="255" />
						<span class="description">Default search setting is 'all of these words'</span>
					</td>
        		</tr>
        		<tr valign="bottom">
					<th scope="row">Exclude these keywords</th>
        		           	<td >
						<input type="text" name="keywords_not" id="keywords_not" value="<?php echo $form["keywords_not"];?>" />						
						<span class="description">Exclude these keywords</span>
					</td>
        		</tr>
        		<tr valign="bottom">
					<th scope="row">Any of the keywords</th>
        		           	<td >
						<input type="text" name="keywords_or" id="keyword-input" class="ui-autocomplete-input" value="<?php echo $form["keywords_or"];?>" maxlength="255" />
						<span class="description">Any of these keywords</span>
					</td>
        		</tr>
        		<tr valign="bottom">
					<th scope="row">Exact phrase</th>
        		        <td >
						<input type="text" name="keywords_phrase" id="keyword-input" class="ui-autocomplete-input" value="<?php echo $form["keywords_phrase"];?>" maxlength="255" />
						<span class="description">Exact phrase</span>
					</td>
        		</tr>
        		<tr valign="bottom">
						<th scope="row">Content Providers</th>
						<td>
							<input type="hidden" name="providers_total" value="<?php echo $providers_total; ?>" class="providers_total" id="providers_total" />
							<select name="provider[]" id="provider-select" class="multiselect" multiple="multiple" style="<?php GrabPress::outline_invalid() ?>" onchange="doValidation()" >
							<?php
								foreach ( $list_providers as $record_provider ) {
									$provider = $record_provider->provider;
									$provider_name = $provider->name;
									$provider_id = $provider->id;
									$provider_selected = ( in_array( $provider_id, $form["provider"] ) )?'selected="selected"':"";
									echo '<option '.$provider_selected.' value = "'.$provider_id.'">'.$provider_name.'</option>\n';
								}
							?>
							</select>
							<span class="description">Add or remove specific providers content from this feed</span>
						</td>
				</tr>
				<tr valign="bottom">
					<td colspan="2" class="button-tip">						
						<input type="button" onclick="previewVideos()" class="button-secondary" disabled="disabled" value="<?php isset($_GET['action'])=='edit-feed' ?_e( 'Preview Changes' ):  _e( 'Preview Feed' )  ?>" id="btn-preview-feed" />
						<span class="hide preview-btn-text">Click here to sample the kinds of videos that will be auto posted by this feed in the future.</span>
					</td>
				</tr>
				<tr>
					<td>
						<h3>Publish Settings</h3>
					</td>					
				</tr>
        		<tr valign="bottom">
					<th scope="row">Schedule<span class="asterisk">*</span></th>
        		           	<td>
								<select name="schedule" id="schedule-select" class="schedule-select" style="width:90px;" >
									<?php
										
										if ( GrabPress::$environment == 'grabqa' ) {
											$times = array( '15 mins', '30  mins', '45 mins', '01 hr', '02 hrs', '06 hrs', '12 hrs', '01 day', '02 days', '03 days' );
										}
										else {
											$times = array( '06 hrs', '12 hrs', '01 day', '02 days', '03 days' );
										}	

										if ( GrabPress::$environment == 'grabqa' ) {												
											$values = array( 15,  30,  45, 60, 120, 360, 720, 1440, 2880, 4320 );
										}
										else {
											$values = array( 360, 720, 1440, 2880, 4320 );
										}

										if(!isset($form["schedule"])){
											for ( $o = 0; $o < count( $times ); $o++ ) {
												$time = $times[$o];
												$value = $values[$o];
												echo "<option value = \"$value\" >$time</option>\n";
											}
										}else{
											for ( $o = 0; $o < count( $times ); $o++ ) {
												$time = $times[$o];
												$value = $values[$o];
												$selected = ( $value == $form["schedule"] )?'selected="selected"':"";
												echo "<option value = \"$value\" $selected >$time</option>\n";
											}
										}
									?>
								</select>
								<span class="description">Determine how often to search for new videos</span>
							</td>
				</tr>
				<tr valign="bottom">
					<th scope="row">Max Results<span class="asterisk">*</span></th>
        		           	<td>
						<select name="limit" id="limit-select" class="limit-select" style="width:60px;" >
							<?php 
								for ( $o = 1; $o < 6; $o++ ) {
									$selected = ((isset($form["limit"])) && ( $o == $form["limit"] )) ?'selected="selected"':"";
									echo "<option value = \"$o\" $selected>$o</option>\n";
								} 
							?>
						</select>
						<span class="description">Indicate the maximum number of videos to grab at a time</span>
					</td>
				</tr>
				<tr valign="bottom">
						<th scope="row">Post Categories</th>
						<td>
							<?php
								$select_cats = wp_dropdown_categories  ( array( 'echo' => 0, 'taxonomy' => 'category', 'hide_empty' => 0 ) );
								$select_cats = str_replace( "name='cat' id=", "name='category[]' multiple='multiple' id=", $select_cats );
								echo $select_cats;
							?>
							<span class="description">If no selection is made, your default category '<?php echo get_cat_name("1") ?>' will be used.</span>
						</td>
				</tr>
				<tr valign="bottom">
						<th scope="row">Post Author<span class="asterisk">*</span></th>
						<td>
							<select name="author" id="author_id" class="author-select" >
							<?php
								foreach ( $blogusers as $user ) {
									$author_name = $user->display_name;
									$author_id = $user->ID;
									$selected = ((isset($form["author"])) && ( $form["author"]==$author_id ) )?'selected="selected"':"";
									echo '<option value = "'.$author_id.'" '.$selected.'>'.$author_name.'</option>\n';
								}
							?>
							</select>
							<span class="description">Select the default WordPress user to credit as author of the posts from this feed</span>
						</td>
			    </tr>
			   	<tr valign="bottom">
			   			<th scope="row">Player Mode<span class="asterisk">*</span></th>
						<td>
							<?php
								if(isset($form["click_to_play"]) && ($form["click_to_play"]=='1')){
									$ctp_checked_auto = 'checked="checked"';
									$ctp_checked_click = "";
								}else{
									$ctp_checked_auto = "";
									$ctp_checked_click = 'checked="checked"';									
								}					
							?>
							<input type="radio" name="click_to_play" value="1" <?php echo $ctp_checked_auto;?> /> Auto-Play
							<input type="radio" name="click_to_play" value="0" <?php echo $ctp_checked_click;?> /> Click-to-Play 
							<span class="description">(this is likely to result in fewer ad impressions <a href="#" onclick='return false;' id="learn-more">learn more</a>)</span>
						</td>
				</tr>
				<tr valign="bottom">		
						<th scope="row">Delivery Mode<span class="asterisk">*</span></th>
						<td>
							<?php								
								if(isset($form["publish"]) && ($form["publish"] == '1')){
									$publish_checked_draft = "";
									$publish_checked_automatic = 'checked="checked"';									
								}else{
									$publish_checked_draft = 'checked="checked"';
									$publish_checked_automatic = '';									
								}
							?>
							<input type="radio" name="publish" value="0" <?php echo $publish_checked_draft; ?> /> Create Drafts to be moderated and published manually
							<input type="radio" name="publish" value="1" <?php echo $publish_checked_automatic; ?> /> Publish Posts Automatically
						</td>
				</tr>
				<tr valign="bottom">					
					<td class="button-tip" colspan="2">						
						<?php $click = ( isset($_GET['action'])=='edit-feed' ) ? 'onclick="validateFeedName(\'update\')"' : 'onclick="validateFeedName()"' ?>
						<input type="button" class="button-primary" disabled="disabled" value="<?php ( isset($_GET['action'])=='edit-feed' ) ? _e( 'Save Changes' ) : _e( 'Create Feed' ) ?>" id="btn-create-feed" <?php echo $click; ?>  />
						<a id="reset-form" href="#">reset form</a>
						<?php if(isset($_GET['action'])=='edit-feed'){ ?><a href="#" id="cancel-editing" >cancel editing</a><?php } ?>				
						<span class="description" style="<?php GrabPress::outline_invalid() ?>color:red"> <?php echo GrabPress::$feed_message; ?> </span>
					</td>
				</tr>
				</table>
			</form>
</fieldset>
<?php if(isset($_GET['action'])=='edit-feed') { ?>
<span class="edit-form-text display-element" >Please use the form above to edit the settings of the feed marked "editing" below</span>
<?php } ?>

<div id="dialog" title="Name your feed">
	<p style="color:red; font-size:14px;"><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 0 0;"></span>Please Name Your Feed</p>
	<p>You have not provided a custom feed name. You may keep it
      as-is, but we recommend customizing it below.</p>
	<input type="text" name="dialog-name" id="dialog-name" maxlength="14" />
</div>

<?php
	$feeds = GrabPress::get_feeds();
	$num_feeds = count( $feeds );
	if($num_feeds > 0 ){
		echo GrabPress::fetch('includes/gp-manage-feeds.php',
			array( "form" => $_REQUEST,
				"list_providers" => $list_providers,
				"providers_total" => $providers_total,
				"list_channels" => $list_channels,
				"channels_total" => $channels_total,
				"blogusers" => $blogusers 
			)
		); 
	}
?>
</div>
<!--</form>-->
