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
										$json = GrabPress::get_json('http://catalog.'.GrabPress::$environment.'.com/catalogs/1/categories');
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
								<select name="schedule" id="schedule-select" class="schedule-select" style="width:90px;" >
									<?php 

if(GrabPress::$environment == 'grabqa'){
 $times = array( '15 mins', '30  mins', '45 mins', '01 hr', '02 hrs', '06 hrs', '12 hrs', '01 day', '02 days', '03 days' );
}
else{
 $times = array( '12 hrs', '01 day', '02 days', '03 days');
}
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
		
		<?php
			$feeds = GrabPress::get_feeds();
			$num_feeds = count($feeds);
		  	$active_feeds = 0;
			for ($i=0; $i < $num_feeds; $i++){
			 if($feeds[$i]->feed->active > 0){
			  $active_feeds++; 
			 }	
			}
			if( $active_feeds > 0 || $num_feeds > 0 ){
			 $noun = 'feed';	
			if( $active_feeds > 1 || $active_feeds == 0 ){
			 $noun .= 's';
			}		
			if(GrabPress::$environment == 'grabqa'){		
		GrabPress::showMessage('GrabPress plugin is enabled with '.$active_feeds.' '.$noun.' active.  ENVIRONMENT = ' . GrabPress::$environment);}
			else{
		GrabPress::showMessage('GrabPress plugin is enabled with '.$active_feeds.' '.$noun.' active.');
			}
			?>
			<div>
				<h3>Manage Feeds</h3>
				<table class="grabpress-table" style="margin-bottom:215px;">
					<tr>
						<th>Active</th>
						<th>Video Channel</th>
						<th>Keywords</th>
						<th>Schedule</th>
						<th>Max Results</th>
						<th>Publish</th>
						<th>Click to Play</th>
						<th>Post Categories</th>
						<th>Author</th>
						<th>Providers</th>
						<th>Delete</th>
						<th>Preview Feed</th>
						<th></th>						
					</tr>
				<?php for ($n = 0; $n < $num_feeds; $n++ ) { 
					$feed = $feeds[$n]->feed;
					$url = array();
					parse_str( parse_url($feed->url, PHP_URL_QUERY), $url);					
					$feedId = $feed->id;
					$providers = explode(",", $url["providers"]); // providers chosen by the user
				?>
				<form id="form-<?php echo $feedId; ?>" action=""  method="post">		
					<input type="hidden" id="action-<?php echo $feedId; ?>" name="action" value="" />
					<tr>											
						<td>
								<input type="hidden" name="feed_id" value="<?php echo $feedId; ?>" />	
								<?php 
									$checked = ( $feed->active  ) ? 'checked = "checked"' : '';
									echo '<input '.$checked.' type="checkbox" onchange="toggleButton('.$feedId.')" value="1" name="active" class="active-check"/>'
								?>
						<td>
							<select  name="channel" id="channel-select-<?php echo $feedId; ?>" onchange="toggleButton(<?php echo $feedId; ?>)" class="channel-select" >
								<?php 	
									$json = GrabPress::get_json('http://catalog.'.GrabPress::$environment.'.com/catalogs/1/categories');
									$list = json_decode($json);
									foreach ($list as $record) {
								   		$category = $record -> category;
										$name = $category -> name;
										$id = $category -> id;
										$selected = ( $name == $feed->name )  ? 'selected = "selected"' : '';
								   		echo '<option '.$selected.' value = "'.$name.'">'.$name.'</option>\n';
									} 
								?>
								</select>
						</td>
						<td>	
								<input type="text" name="keywords_and" onkeyup="toggleButton(<?php echo $feedId; ?>)" value="<?php echo $url['keywords_and']; ?>" class="keywords_and" id="keywords_and_<?php echo $feedId; ?>"/>		
						</td>
						<td>
							<select name="schedule" id="schedule-select" onchange="toggleButton(<?php echo $feedId; ?>)" class="schedule-select" style="width:90px;">
								<?php 
if(GrabPress::$environment == 'grabqa'){
 $times = array( '15 mins', '30  mins', '45 mins', '01 hr', '02 hrs', '06 hrs', '12 hrs', '01 day', '02 days', '03 days' );
 $values = array( 15,  30,  45, 60, 120, 360, 720, 1440, 2880, 4320 );
}
else{
 $times = array( '12 hrs', '01 day', '02 days', '03 days');
 $values = array( 720, 1440, 2880, 4320 );
}								

									for ( $o = 0; $o < count( $times ); $o++ ) {
										$time = $times[$o];
										$value = $values[$o];
										$selected = ( $value == $feed->update_frequency ) ? ' selected="selected"' : '';
										echo '<option'.$selected.' value="'.$time.'">'.$time.'</option>\n';
								 	} 
								?>
							</select>
						</td>

						<td>
							<select name="limit" id="limit-select" onchange="toggleButton(<?php echo $feedId; ?>)" class="limit-select" style="width:60px;" >
									<?php for ($o = 1; $o < 6; $o++) {
										$selected = ( $o == $feed->posts_per_update )? 'selected = "selected"' : '';
										echo '<option '.$selected.' value = "'.$o.'">'.$o.'</option>\n';
									 } ?>
							</select>
						</td>
						<td>
							<?php 
								$checked = ( $feed->custom_options->publish  ) ? ' checked = "checked"' : '';
								echo '<input'.$checked.' type="checkbox" value="1" name="publish" id="publish-check" onchange="toggleButton('.$feedId.')" />';
							?>
						</td>
						<td>
							<?php 
								$checked = ( $feed->auto_play  ) ? '' : ' checked = "checked"';
								echo '<input'.$checked.' type="checkbox" value="0" name="click-to-play" id="click-to-play-<?php echo $feedId; ?>" onchange="toggleButton('.$feedId.')" />';
							?>
						</td>
						<td>
							<?php 		
								$category_list_length = count($feed->custom_options->category);
								if(isset($feed->custom_options->category)){
									if($category_list_length == 1){
										$category_list = explode("\\r\\n", $feed->custom_options->category);									
									}else{
										$category_list = $feed->custom_options->category;
									}									
								}else{
									$category_list = str_split("Uncategorized");
								}														
								$category_ids = get_all_category_ids();
								$args = array( 'echo' => 0, 
										'taxonomy' => 'category', 
										'hide_empty' => 0, 
										'id' => 'category-select-'.$feed->id,
										'class' => 'category-select' );								
								$cats = wp_dropdown_categories($args);
								$cats = str_replace( "name='cat' id=", "name='category[]' multiple='multiple' id=", $cats );
                                $cats = str_replace("\n", "", $cats);
                                $cats = str_replace("\t", "", $cats);
                                $cats = str_replace("<select name='cat' id='cat' class='postform' ><option class=\"level-0\" value=\"", "", $cats);
                                $cats = str_replace("\">", "-", $select_cats);
                                $cats = str_replace("</option><option class=\"level-0\" value=\"", "_", $cats);
                                $cats = str_replace("</option></select>", "", $cats);
                                                                
                                echo "<select multiple='multiple' class=\"postcats\" name=\"category[]\" id=\"postcats-".$feedId."\" onchange=\"toggleButton(".$feedId.");\" >\n";
				                                foreach($category_ids as $cat_id) {	
                                                        $cat_name = get_cat_name($cat_id);
                                                        $sel= "";
                                                        $sel = in_array($cat_name, $category_list)  ? 'selected = "selected"' : '';
                                                        echo "<option ". $sel ." value=\"$cat_id\">";
                                                                echo $cat_name;
                                                        echo "</option>\n";
                                                }                                                
                                echo "</select>";				
							?>
						</td>
						<td>
							<select name="author" id="author-<?php echo $feedId; ?>" onchange="toggleButton(<?php echo $feedId; ?>);" class="author-select" >
								<!--<option selected="selected" value = "">Choose One</option>-->
								<?php
									foreach ($blogusers as $user) {
										$author_name = $user->display_name;
										$author_id = $user->ID;
										echo "CUSTOM OPTIONS ID: "; var_dump($feed->custom_options->author_id);
										$selected = ($author_id == $feed->custom_options->author_id)  ? 'selected = "selected"' : '';									
								   		echo '<option '.$selected.' value = "'.$author_id.'">'.$author_name.'</option>\n';
									} 
								?>
							</select>
						</td>
						<td>
							<input type="hidden" name="providers_total" value="<?php echo $providers_total; ?>" />
							<select name="provider[]" class="provider-select-update multiselect" id="provider-select-update-<?php echo $feedId; ?>" multiple="multiple" onchange="toggleButton(<?php echo $feedId; ?>);" >
								<!--<option selected="selected" value = "">Choose One</option>-->
								<?php
									foreach ($list_provider as $record_provider) {
								   		$provider = $record_provider->provider;
										$provider_name = $provider->name;
										$provider_id = $provider->id;
										$selected = in_array($provider_id, $providers)  ? 'selected = "selected"' : '';
										if(in_array("", $providers)){ 
											echo '<option selected = "selected" value = "'.$provider_id.'">'.$provider_name.'</option>\n';
										}else{
											echo '<option '.$selected.' value = "'.$provider_id.'">'.$provider_name.'</option>\n';
										}
								   		
									}
 
								?>
							</select>
						</td>

						<td>
							<input type="button" class="button-primary btn-delete" value="<?php _e('X') ?>" onclick="deleteFeed(<?php echo $feedId; ?>);" />
						</td>
						<td>								
							<input type="button" onclick="previewFeed(<?php echo $feedId; ?>)" class="button-secondary" value="<?php _e('View Feed') ?>" id="btn-preview-feed" />
						</td>
						<td>
							<button class="button-primary btn-update" id="btn-update-<?php echo $feedId; ?>" style="visibility:hidden;" name="<?php echo $feedId; ?>" >update</button>					 
						</td>
					</tr>	
					</form>				
				<?php } ?>				
				</table>
			</div>
		<?php } ?>
