<?php 

$is_edit = $form["action"] == "edit-feed" || $form["action"] == "modify" ;

?>
<div class="wrap">
	<img src="http://grab-media.com/corpsite-static/images/grab_logo.jpg"/>
	<h2>GrabPress: Autopost Videos by Category and Keywords</h2>
	<p>Feed your blog with fresh video content.</p>

		<fieldset id="create-form" class="<?php echo $is_edit ? 'edit-mode':''?>">
		<legend><?php echo $is_edit ? 'Edit':'Create'?> Feed</legend>	
	<?php
		$rpc_url = get_bloginfo( 'url' ).'/xmlrpc.php';
		$connector_id = GrabPressAPI::get_connector_id();
	?>
	<form method="post" action="" id="form-create-feed">
		<?php 
			if(isset($form["feed_id"]) && $form["feed_id"] > 0) {
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
			if($is_edit){		
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
						<th scope="row">Plug-in Version &amp; Build Number</th>
			            <td>
							<?php echo GrabPress::$version ?>
						</td>
					</tr> <?php } ?>
	                <tr valign="bottom">
						<th scope="row">API Key</th>
			            <td>
							<?php echo get_option( 'grabpress_key' ); ?>
						</td>
					</tr>
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
						<select  style="<?php GrabPress::outline_invalid() ?>" name="channels[]" id="channel-select" class="channel-select multiselect" multiple="multiple" style="width:500px" >
							<?php								
								if(!array_key_exists("channels", $form)){
									$form["channels"] = array();
								}
								
								if(is_array($form["channels"])){
									$channels = $form["channels"];
								}else{
									$channels = explode( ",", rawurldecode($form["channels"])); // Video categories chosen by the user
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
							<select name="providers[]" id="provider-select" class="multiselect" multiple="multiple" style="<?php GrabPress::outline_invalid() ?>" onchange="GrabPressAutoposter.doValidation()" >
							<?php
								foreach ( $list_providers as $record_provider ) {
									$provider = $record_provider->provider;
									$provider_name = $provider->name;
									$provider_id = $provider->id;
									$provider_selected = ( in_array( $provider_id, $form["providers"] ) )?'selected="selected"':"";
									echo '<option '.$provider_selected.' value = "'.$provider_id.'">'.$provider_name.'</option>\n';
								}
							?>
							</select>
							<span class="description">Add or remove specific providers content from this feed</span>
						</td>
				</tr>
				<tr valign="bottom">
					<td colspan="2" class="button-tip">						
						<input type="button" onclick="GrabPressAutoposter.previewVideos()" class="button-secondary" disabled="disabled" value="<?php $is_edit ?_e( 'Preview Changes' ):  _e( 'Preview Feed' )  ?>" id="btn-preview-feed" />
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
								          $values = array( 15*60,  30*60,  45*60, 60*60, 120*60, 360*60, 720*60, 1440*60, 2880*60, 4320*60 );
								        }
								        else {
								          $values = array( 360*60, 720*60, 1440*60, 2880*60, 4320*60 );
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
								<span class="description">Determine how often to search (posts created only if new matching videos have been added Grab's catalog)</span>
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
									if($author_name != "GrabPress"){
										$selected = ((isset($form["author"])) && ( $form["author"]==$author_id ) )?'selected="selected"':"";
										echo '<option value = "'.$author_id.'" '.$selected.'>'.$author_name.'</option>\n';
									}
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
						<?php $click = ( $is_edit ) ? 'onclick="GrabPressAutoposter.validateFeedName(\'update\')"' : 'onclick="GrabPressAutoposter.validateFeedName()"' ?>
						<input type="button" class="button-primary" disabled="disabled" value="<?php ( $is_edit ) ? _e( 'Save Changes' ) : _e( 'Create Feed' ) ?>" id="btn-create-feed" <?php echo $click; ?>  />
						<a id="reset-form" href="#">reset form</a>
						<?php if($is_edit){ ?><a href="#" id="cancel-editing" >cancel editing</a><?php } ?>	
						<span class="description" style="<?php GrabPress::outline_invalid() ?>color:red"> <?php echo GrabPress::$feed_message; ?> </span>
					</td>
				</tr>
				</table>
			</form>
</fieldset>
<?php if($is_edit) { ?>
<span class="edit-form-text display-element" >Please use the form above to edit the settings of the feed marked "editing" below</span>
<?php } ?>

<div id="dialog" title="Name your feed">
	<p style="color:red; font-size:14px;"><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 0 0;"></span>Please Name Your Feed</p>
	<p>You have not provided a custom feed name. You may keep it
      as-is, but we recommend customizing it below.</p>
	<input type="text" name="dialog-name" id="dialog-name" maxlength="14" />
</div>

<?php
	$feeds = GrabPressAPI::get_feeds();
	$num_feeds = count( $feeds );
	if($num_feeds > 0 ){
		echo GrabPress::fetch('includes/gp-manage-feeds.php',
			array( "form" => $form,
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
