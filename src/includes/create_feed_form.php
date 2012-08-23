		<div class="wrap">
			<img src="http://grab-media.com/corpsite-static/images/grab_logo.jpg"/>
			<h2>GrabPress: Autopost Videos by Channel and Tag</h2>
			<p>New video content delivered fresh to your blog.</p>
			<h3>Create Feed</h3>
			<?php 				
				// List of all providers
				$json_provider = GrabPress::get_json('http://catalog.'.GrabPress::$environment.'.com/catalogs/1/providers?limit=-1');
				$list_provider = json_decode($json_provider);
				$providers_total = count($list_provider);
				$blogusers = get_users();
			?>
			<script language = "JavaScript" type = "text/javascript">
				function validateRequiredFields() {					
					var category =  jQuery('#channel-select').val();
					if(category == ''){						
						alert("Please select at least one video channel");					  
						e.preventDefault();
					}else if(jQuery("#provider-select :selected").length == 0){						
						alert("Please select at least one provider");					  
						e.preventDefault();
					}else {
						return true;
					}				
				}
				( function ( global, $ ) {
					global.previewVideos = function () {
						var keywords =  $( '#keyword-input' ).val();
						var category =  $( '#channel-select').val();
						var limit =  $( '#limit-select').val();
						var isValid = validateRequiredFields();
						var environment = "<?php echo GrabPress::$environment; ?>";
						if(isValid){							
							window.open( 'http://catalog.'+environment+'.com/catalogs/1/videos/search.mrss?keywords_and=' + keywords + '&categories=' + category );						
						}						
					}	
				} )( window, jQuery );	

			    function toggleButton(feedId) {
					jQuery('#btn-update-' + feedId).css({"visibility":"visible"});
				}

				function deleteFeed(id){
					var form = jQuery('#form-'+id);
					var action = jQuery('#action-'+id);
					var answer = confirm('Are you sure you want to delete the feed? You will no longer receive automatic posts with the specified settings.');
  					if(answer){
  						action.val("delete");
						form.submit();
  					} else{
  						return false;
  					}
				}
				
				function previewFeed(id) {
					var keywords =  jQuery( '#keywords_and_'+id ).val();
					var category =  jQuery( '#channel-select-'+id).val();	
					var environment = "<?php echo GrabPress::$environment; ?>";
					window.open( 'http://catalog.'+environment+'.com/catalogs/1/videos/search.mrss?keywords_and=' + keywords + '&categories=' + category );																				
				}

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
					var isValid = validateRequiredFields();
					if(isValid){
						jQuery('.hide').show();	
					}								
				}

				jQuery(function(){
				  jQuery('#provider-select option').attr('selected', 'selected');

				  jQuery("#provider-select").multiselect(multiSelectOptions, {
					 checkAll: function(e, ui){
				  	 	showButtons();      
					 }
				  }).multiselectfilter();	  		  

				  jQuery(".provider-select-update").multiselect(multiSelectOptions, {
				  	 uncheckAll: function(e, ui){
				  	 	id = this.id.replace('provider-select-update-',''); 	 	
				  	 	toggleButton(id);      
					 },
					 checkAll: function(e, ui){
				  	 	id = this.id.replace('provider-select-update-','');	
				  	 	toggleButton(id);      
					 }
				   }).multiselectfilter();

				  jQuery('#create-feed-btn').bind('click', function(e){
				  	var isValid = validateRequiredFields();
				  	var form = jQuery('#form-create-feed');
					if(isValid){				
						form.submit();
					}else{
						e.preventDefault();
					}
				  });

				  jQuery('.btn-update').bind('click', function(e){
				    id = jQuery(this).attr('name');		  
					var form = jQuery('#form-'+id);
					var action = jQuery('#action-'+id);
					if(jQuery("#provider-select-update-" + id + " :selected").length == 0){						
						alert("Please select at least one provider");					  
						e.preventDefault();
					}else{
						action.val("modify");
						form.submit();
					}
				  });

				  jQuery("#cat").multiselect(multiSelectOptionsCategories,
				  {
				  	header:false
				  });

				  jQuery(".postcats").multiselect(multiSelectOptionsCategories, {
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
				  
				  jQuery(".channel-select").selectmenu();
				  jQuery(".schedule-select").selectmenu();
				  jQuery(".limit-select").selectmenu();
				  jQuery(".author-select").selectmenu();			
				
				});
				
			</script>
			<?php 
				$rpc_url = get_bloginfo('url').'/xmlrpc.php';
				$connector_id = GrabPress::get_connector_id();		
			?>
			<form method="post" action="" id="form-create-feed">
	            		<?php settings_fields('grab_press');//XXX: Do we need this? ?>
	            		<?php $options = get_option('grab_press'); //XXX: Do we need this? ?>
	            		<table class="form-table grabpress-table">
	                		<tr valign="top">
						<th scope="row">API Key</th>
			                	<td>
							<?php echo get_option( 'grabpress_key' ); ?>
						</td>
					</tr>
						<tr>
							<th scope="row">Video Channel</th>
							<td>
								<select  style="<?php GrabPress::outline_invalid() ?>" name="channel" id="channel-select" class="channel-select" onchange="showButtons()">
									<option selected = "selected" value = "">Choose One</option>
									<?php 	
										$json = GrabPress::get_json('http://catalog.'.self::$environment.'.com/catalogs/1/categories');
										$list = json_decode($json);
										foreach ($list as $record) {
									   		$category = $record -> category;
											$name = $category -> name;
											$id = $category -> id;
									   		echo '<option value = "'.$name.'">'.$name.'</option>\n';
										} 
									?>
								</select> *
								<span class="description">Select a channel to grab from</span>								
							</td>
						</tr>
			        		<tr valign="top">
							<th scope="row">Keywords</th>
		        		           	<td >
								<input type="text" name="keyword" id="keyword-input" class="ui-autocomplete-input" /> 
								<span class="description">Enter search keywords (e.g. <b>celebrity gossip</b>)</span>
							</td>
		        		        </tr>
		        		        <tr valign="top">
							<th scope="row">Max Results</th>
		        		           	<td>
								<select name="limit" id="limit-select" class="limit-select" style="width:60px;" >
									<?php for ($o = 1; $o < 6; $o++) {
										echo "<option value = \"$o\">$o</option>\n";
									 } ?>
								</select>
								<span class="description">Indicate the maximum number of videos to grab at a time</span>
							</td>
						</tr>
		        		        <tr valign="top">
							<th scope="row">Schedule</th>
		        		           	<td>
								<select name="schedule" id="schedule-select" class="schedule-select" style="width:60px;" >
									<?php $times = array( '15m', '30m', '45m', '1h', '2h', '6h', '12h', '24h' );
										for ($o = 0; $o < count( $times ); $o++) {
											$time = $times[$o];
											echo "<option value = \"$time\">$time</option>\n";
									 	} 
									?>
								</select>
								<span class="description">Determine how often to grab new videos</span>
							</td>
						</tr>
		        		<tr valign="top">
						<th scope="row">Publish</th>
						<td>
							<input type="checkbox" value="1" name="publish" id="publish-check"/>
							<span class="description">Leave this unchecked to moderate autoposts before they go live</span>
						</td>
						<tr valign="top">
						<th scope="row">Click-to-Play Video</th>
						<td>
							<input type="checkbox" value="1" name="click-to-play" id="click-to-play" />
							<span class="description">Check this to wait for the reader to click to start the video (this is likely to result in fewer ad impressions) <a href="#">learn more</a></span>
						</td>
					</tr>
		        		<tr valign="top">
						<th scope="row">Post Category</th>
						<td>
							<?php 							
								$select_cats = wp_dropdown_categories( array( 'echo' => 0, 'taxonomy' => 'category', 'hide_empty' => 0 ) );
								$select_cats = str_replace( "name='cat' id=", "name='category[]' multiple='multiple' id=", $select_cats );
								echo $select_cats; 							
							?>
							<span class="description">Select a category for your autoposts</span>
						</td>
					</tr>
					</tr>
		        		<tr valign="top">
						<th scope="row">Post Author</th>
						<td>
							<select name="author" id="author_id" class="author-select" >
								<!--<option selected="selected" value = "">Choose One</option>-->
								<?php
									foreach ($blogusers as $user) {
										$author_name = $user->display_name;
										$author_id = $user->ID;										
								   		echo '<option value = "'.$author_id.'">'.$author_name.'</option>\n';
									} 
								?>
							</select>
							<span class="description">Select the default Wordpress user to credit as author of the posts from this feed</span>
						</td>
					</tr>
					</tr>
		        		<tr valign="top">
						<th scope="row">Providers</th>
						<td>
							<input type="hidden" name="providers_total" value="<?php echo $providers_total; ?>" />	
							<select name="provider[]" id="provider-select" class="multiselect" multiple="multiple" style="<?php GrabPress::outline_invalid() ?>" onchange="showButtons()" >
								<!--<option selected="selected" value = "">Choose One</option>-->
								<?php
									foreach ($list_provider as $record_provider) {
								   		$provider = $record_provider->provider;
										$provider_name = $provider->name;
										$provider_id = $provider->id;
								   		echo '<option value = "'.$provider_id.'">'.$provider_name.'</option>\n';
									} 
								?>
							</select> *
							<span class="description">Select providers for your autoposts</span>
						</td>
					</tr>
					<tr valign="top">
						<td>
							<input type="button" onclick="previewVideos()" class="button-secondary hide" value="<?php _e('Preview Feed') ?>" id="btn-preview-feed" />
						</td>
						<td>
							<span class="description">Click to preview which videos will be autoposted from this feed</span>
						</td>
					</tr>
					<tr valign="top">
						<td>
							<input type="submit" class="button-primary hide" value="<?php _e('Create Feed') ?>" id="create-feed-btn" />
						</td>
						<td>
							<span class="description" style="<?php GrabPress::outline_invalid() ?>color:red">
								<?php 
										echo GrabPress::$feed_message; 
								?>
							</span>
						</td>
					</tr>		
				</table>
			</form>
		</div>