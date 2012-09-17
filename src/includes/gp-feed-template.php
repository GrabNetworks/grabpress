<!--<form method="post" action="" id="form-create-feed">-->
<div class="wrap">
	<img src="http://grab-media.com/corpsite-static/images/grab_logo.jpg"/>
	<h2>GrabPress: Autopost Videos by Category and Keywords</h2>
	<p>Feed your blog with fresh video content.</p>
		<fieldset style="border: 1px solid <?php echo isset($_GET['action'])=='edit-feed' ? 'red':'black'?> ">
		<legend><?php echo isset($_GET['action'])=='edit-feed' ? 'Edit':'Create'?> Feed</legend>
	<script type="text/javascript">
	( function ( global, $ ) {
	    //$("#form-create-feed input[name=action]").val("update");
		global.hasValidationErrors = function () {
			var category =  $('#channel-select').val();
			if(category == ''){
				return "Please select at least one video channel";
			}else if($("#provider-select :selected").length == 0){
				return "Please select at least one provider";
			}else {
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

		global.toggleButton = function (feedId) {
			$('#btn-update-' + feedId).css({"visibility":"visible"});
		}

		global.deleteFeed = function(id){	
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
					$('#tr-'+id+' td').css("background-color","#FFE4C4");
					return false;
				}
		}
		global.selectedCategories = <?php echo json_encode( $form["category"] );?>;
		<?php if(isset($_GET["feed_id"])) { ?>
			global.previewFeed = function(id) {			
			    var form = jQuery('#form-'+id);
			    var action = jQuery('#action-'+id);
			    action.val("edit-feed");
			    form.submit();
			}
			<?php }else{ ?>
			global.previewFeed = function(id) {			
				window.location = "admin.php?page=autoposter&action=preview-feed&feed_id="+id;
			}
		<?php } ?>		

		global.editFeed = function(id) {
			window.location = "admin.php?page=autoposter&action=edit-feed&feed_id="+id;
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

	function showButtons() {
		var errors = hasValidationErrors();
		if(!errors){
			jQuery('.hide').show();
		}else{
			jQuery('.hide').hide();
		}
	}

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

		// Show "Preview Feed" and "Create Feed" buttons
		$("#form-create-feed").bind("change", function(e) {
		   	if(!hasValidationErrors()){
				$('.hide').show();
			}else{
				$('.hide').hide();
				e.preventDefault();
				return false;
			}
		});
		//showButtons();
	   $("#form-create-feed input").keypress(function(e) {
		    if(e.which == 13) {
		        e.preventDefault();
		        return false;
		    }
		});

		if($('#provider-select option:selected').length == 0){
			$('#provider-select option').attr('selected', 'selected');
		}
		var category_options = $('#cat option');
		for(var i=0;i<category_options.length; i++){
			if($.inArray($(category_options[i]).val(),selectedCategories)>-1){
				$(category_options[i]).attr("selected", "selected");
			}
		}

		$("#provider-select").multiselect(multiSelectOptions, {
	  	 uncheckAll: function(e, ui){
	  	 	$('.hide').hide();
		 },
		 checkAll: function(e, ui){
		 	/*
		 	if($("#provider-select :selected").length != 0){
				$('.hide').show();
			}
			*/
			var errors = hasValidationErrors();
			if(!errors){
				$('.hide').show();
			}
		 }
		  }).multiselectfilter();

		  $(".provider-select-update").multiselect(multiSelectOptions, {
		  	 uncheckAll: function(e, ui){
		  	 	id = this.id.replace('provider-select-update-','');
		  	 	toggleButton(id);
			 },
			 checkAll: function(e, ui){
		  	 	id = this.id.replace('provider-select-update-','');
		  	 	toggleButton(id);
			 }
		   }).multiselectfilter();


		  $('#create-feed-btn').bind('click', function(e){
		  	var errors = hasValidationErrors();
		  	var form = $('#form-create-feed');
			if(!errors){
				form.submit();
			}else{
				alert(errors);
				e.preventDefault();
				return false;
			}
		  });

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
		  	 	toggleButton(id);
			 },
			 checkAll: function(e, ui){
		  	 	id = this.id.replace('postcats-','');
		  	 	toggleButton(id);
			 }
		  }).multiselectfilter();

		  $(".channel-select").selectmenu();
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

	});

	jQuery(window).load(function () {
	    showButtons();
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
	                <tr valign="top">
						<th scope="row">Plug-in Version & Build Number</th>
			            <td>
							<?php echo GrabPress::$version ?>
						</td>
					</tr>
	                <tr valign="top">
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
				<tr valign="top">
					<th scope="row">Grab Video Categories*</th>
					<td>
						<select  style="<?php GrabPress::outline_invalid() ?>" name="channel" id="channel-select" class="channel-select" style="width:500px" >
							<option <?php  ( !array_key_exists( "channel", $form ) || !$form["channel"] )?'selected="selected"':"";?> value="">Choose One</option>
							<?php
								$json = GrabPress::get_json( 'http://catalog.'.GrabPress::$environment.'.com/catalogs/1/categories' );
								$list = json_decode( $json );
								foreach ( $list as $record ) {
									$category = $record -> category;
									$name = $category -> name;
									$id = $category -> id;
									$selected = ( $name == $form["channel"] )?'selected="selected"':"";
									echo '<option value = "'.$name.'" '.$selected.'>'.$name.'</option>\n';
								}
							?>
						</select>
						<span class="description">Add or remove specific video categories from this feed</span>
					</td>
				</tr>
	        	<tr valign="top">
					<th scope="row">Keywords</th>
        		           	<td >
						<input type="text" name="keywords" id="keyword-input" class="ui-autocomplete-input" value="<?php echo $form["keywords"];?>" maxlength="255" />
						<span class="description">Enter search terms separated by spaces (e.g. <b>celebrity gossip</b>)</span>
					</td>
        		</tr>
        		<tr valign="top">
						<th scope="row">Providers</th>
						<td>
							<input type="hidden" name="providers_total" value="<?php echo $providers_total; ?>" class="providers_total" id="providers_total" />
							<select name="provider[]" id="provider-select" class="multiselect" multiple="multiple" style="<?php GrabPress::outline_invalid() ?>" onchange="showButtons()" >
							<?php
								foreach ( $list_provider as $record_provider ) {
									$provider = $record_provider->provider;
									$provider_name = $provider->name;
									$provider_id = $provider->id;
									$provider_selected = ( in_array( $provider_id, $form["provider"] ) )?'selected="selected"':"";
									echo '<option '.$provider_selected.' value = "'.$provider_id.'">'.$provider_name.'</option>\n';
								}
							?>
							</select> *
							<span class="description">Select providers for your autoposts</span>
						</td>
				</tr>
				<tr valign="top">
					<td colspan="4">
						<?php if(isset($_GET['action'])=='edit-feed'){ ?>
						<input type="button" onclick="previewVideos()" class="button-secondary hide" value="<?php _e( 'Preview Changes' ) ?>" id="btn-preview-feed" />
						<?php }else{ ?>
						<input type="button" onclick="previewVideos()" class="button-secondary hide" value="<?php _e( 'Preview Feed' ) ?>" id="btn-preview-feed" />
						<?php } ?>						
					</td>
				</tr>
				<tr>
					<td>
						<h3>Publish Settings</h3>
					</td>					
				</tr>
        		<tr valign="top">
					<th scope="row">Schedule*</th>
        		           	<td>
								<select name="schedule" id="schedule-select" class="schedule-select" style="width:90px;" >
									<?php
										
										if ( GrabPress::$environment == 'grabqa' ) {
											$times = array( '15 mins', '30  mins', '45 mins', '01 hr', '02 hrs', '06 hrs', '12 hrs', '01 day', '02 days', '03 days' );
										}
										else {
											$times = array( '12 hrs', '01 day', '02 days', '03 days' );
										}	

										if(!isset($form["schedule"])){
											for ( $o = 0; $o < count( $times ); $o++ ) {
												$time = $times[$o];
												echo "<option value = \"$time\" >$time</option>\n";
											}
										}else{
											if ( GrabPress::$environment == 'grabqa' ) {												
												$values = array( 15,  30,  45, 60, 120, 360, 720, 1440, 2880, 4320 );
											}
											else {
												$values = array( 720, 1440, 2880, 4320 );
											}
											for ( $o = 0; $o < count( $times ); $o++ ) {
												$time = $times[$o];
												$value = $values[$o];
												$selected = ( $value == $form["schedule"] )?'selected="selected"':"";
												echo "<option value = \"$time\" $selected >$time</option>\n";
											}
										}
									?>
								</select>
								<span class="description">Determine how often to search for new videos</span>
							</td>
				</tr>
				<tr valign="top">
					<th scope="row">Max Results*</th>
        		           	<td>
						<select name="limit" id="limit-select" class="limit-select" style="width:60px;" >
							<?php 
								for ( $o = 1; $o < 6; $o++ ) {
									$selected = ( $o == $form["limit"] )?'selected="selected"':"";
									echo "<option value = \"$o\" $selected>$o</option>\n";
								} 
							?>
						</select>
						<span class="description">Indicate the maximum number of videos to grab at a time</span>
					</td>
				</tr>
				<tr valign="top">
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
				<tr valign="top">
						<th scope="row">Post Author*</th>
						<td>
							<select name="author" id="author_id" class="author-select" >
							<?php
								foreach ( $blogusers as $user ) {
									$author_name = $user->display_name;
									$author_id = $user->ID;
									$selected = ( $form["author"]==$author_id )?'selected="selected"':"";
									echo '<option value = "'.$author_id.'" '.$selected.'>'.$author_name.'</option>\n';
								}
							?>
							</select>
							<span class="description">Select the default WordPress user to credit as author of the posts from this feed</span>
						</td>
			    </tr>
			   	<tr valign="top">
			   			<th scope="row">Player Mode*</th>
						<td>
							<?php 
								if(isset($_GET['action'])=='edit-feed'){
									if($form["click_to_play"]=='1'){
										$ctp_checked_click = 'checked="checked"';
										$ctp_checked_auto = "";
									}else{
										$ctp_checked_click = "";
										$ctp_checked_auto = 'checked="checked"';
									}
								}else{
									$ctp_checked_click = "";
									$ctp_checked_auto = 'checked="checked"';
								}							
							?>
							<input type="radio" name="click_to_play" value="0" <?php echo $ctp_checked_auto;?> /> Auto-Play
							<input type="radio" name="click_to_play" value="1" <?php echo $ctp_checked_click;?> /> Click-to-Play
							<span class="description">Check this to wait for the reader to click to start the video (this is likely to result in fewer ad impressions) <a href="#" onclick='return false;' id="learn-more">learn more</a></span>
						</td>
				</tr>
				<tr valign="top">		
						<th scope="row">Delivery Mode*</th>
						<td>
							<?php 
								if(isset($_GET['action'])=='edit-feed'){									
									if($form["publish"] == '1'){
										$publish_checked_automatic = 'checked="checked"';
										$publish_checked_draft = "";
									}else{
										$publish_checked_automatic = "";
										$publish_checked_draft = 'checked="checked"';
									}
								}else{
									$publish_checked_draft = "";
									$publish_checked_automatic = 'checked="checked"';
								}							
							?>
							<input type="radio" name="publish" value="0" <?php echo $publish_checked_draft; ?> /> Create Drafts to be moderated and published manually
							<input type="radio" name="publish" value="1" <?php echo $publish_checked_automatic; ?> /> Publish Posts Automatically
						</td>
				</tr>
				<tr valign="top">
					<td/>
					<td>
						<span class="description" style="<?php GrabPress::outline_invalid() ?>color:red">
						<?php
							echo GrabPress::$feed_message;
						?>
						</span>
					</td>
					<?php if(isset($_GET['action'])=='edit-feed'){ ?>
					<td>						
						<a href="#" id="cancel-editing" >cancel editing</a>						
					</td>
					<?php } ?>
					<td>
						<a href="#" id="reset-form" >reset form</a>
					</td>
					<td>
						<?php if(isset($_GET['action'])=='edit-feed'){ ?>
						<input type="submit" class="button-primary hide" value="<?php _e( 'Save Changes' ) ?>" id="create-feed-btn" />
						<?php }else{ ?>
						<input type="submit" class="button-primary hide" value="<?php _e( 'Create Feed' ) ?>" id="create-feed-btn" />
						<?php } ?>
					</td>
				</tr>
				</table>
			</form>
</fieldset>
<?php $display_message = isset($_GET['action'])=='edit-feed' ? "display-element" : "hide"; ?>
<span class="edit-form-text <?php echo $display_message ?>" >Please use the form above to edit the settings of the feed marked "editing" below</span>
<?php
	echo GrabPress::fetch('includes/gp-manage-feeds.php',
				array( "form" => $_POST,
					"list_provider" => $list_provider,
					"providers_total" => $providers_total,
					"blogusers" => $blogusers )); 
?>
</div>
<!--</form>-->