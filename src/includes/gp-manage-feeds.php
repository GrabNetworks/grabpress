<?php
	$feeds = GrabPressAPI::get_feeds();
	$num_feeds = count( $feeds ); 
	$active_feeds = 0;

?>
<fieldset id="manage-table" class="fieldset-manage">
	<legend><?php echo isset($form['action'])=='edit-feed' ? 'Current':'Manage'?> Feeds</legend>

<div>
	<table class="grabpress-table manage-table" cellspacing="0">
		<tr>
			<th>Active</th>
			<th>Name</th>
			<th>Video Categories</th>
			<th>Keywords</th>
			<th>Exclude<br/>Keywords</th>
			<th>Exact<br/>Phrase</th>
			<th>Any<br/>keyword</th>
			<th>Content<br/>Providers</th>			
			<th>Schedule</th>
			<th>Max<br/>Results</th>
			<th>Post<br/>Categories</th>
			<th>Author</th>					
			<th>Player<br/>Mode</th>
			<th>Delivery<br/>Mode</th>					
			<th></th>
			<th></th>
			<th></th>
		</tr>
		<?php
			$feeds = GrabPressAPI::get_feeds();
			$num_feeds = count( $feeds );
			

			for ( $n = 0; $n < $num_feeds; $n++ ) {
				$feed = $feeds[$n]->feed;
				$url = array();
				parse_str( parse_url( $feed->url, PHP_URL_QUERY ), $url );
				GrabPress::_escape_params_template($url);
				$feedId = $feed->id;
				$providers = explode( ",", $url["providers"] ); // providers chosen by the user
				$channels = explode( ",", $url["categories"] ); // Video categories chosen by the user
		?>
		<form id="form-<?php echo $feedId; ?>" action=""  method="post">
			<input type="hidden" id="action-<?php echo $feedId; ?>" name="action" value="" />
			<input type="hidden" name="referer" value="edit" />
			<input type="hidden" name="channels_total" value="<?php echo $channels_total; ?>" id="channels_total" />	
			<?php 
				if(isset($form['action']) && ($form['action']=='edit-feed') && ($form['feed_id']==$feedId)){
					$row_class = "editing-feed";
				}elseif(!$feed->active){
					$row_class = "inactive-row";
				}else{
					$row_class = "row-feed";
				}
			?>
			<tr id="tr-<?php echo $feedId; ?>" class="<?php echo $row_class; ?>">
				<td>
					<input type="hidden" name="feed_id" value="<?php echo $feedId; ?>" />
					<?php 
						if(isset($form['action'])=='edit-feed'){
							echo $checked = ( $feed->active  ) ? 'Yes' : 'No'; 
					 	}else{ 
							$checked = ( $feed->active  ) ? 'checked = "checked"' : '';
							echo '<input '.$checked.' type="checkbox" value="1" name="active" class="active-check" id="active-check-'.$feedId.'" />';
						} 
					?>
				</td>
				<td>		
					<?php 
						echo urldecode($feed->name);
					?>							
				</td>
				<td>		
					<?php 
						$video_categories_array = explode(",", $url['categories']);
						$video_categories_num = count($video_categories_array);
						if($url['categories'] == ""){
							echo "All Video Categories";
						}else if($video_categories_num == 1){							
							echo $video_categories = ($video_categories_num > 15) ? substr($url['categories'],0,15)."..." : $url['categories'];
						}else{
							echo $video_categories_num." selected";
						}
					?>							
				</td>
				<td>		
					<?php 
						if(isset($url['keywords_and'])){
							$keywords_and_num = strlen($url['keywords_and']);
							$keywords_and = $url['keywords_and'];
							echo $keywords_and = ($keywords_and_num > 15) ? substr($keywords_and,0,15)."..." : $keywords_and;
						}
					?>							
				</td>
				<td>		
					<?php 
						if(isset($url['keywords_not'])){
							$keywords_not_num = strlen($url['keywords_not']);
							$keywords_not = $url['keywords_not'];
							echo ($keywords_not_num > 15) ? substr($keywords_not,0,15)."..." : $keywords_not;
						}
					?>							
				</td>
				<td>		
					<?php 
						if(isset($url['keywords_phrase'])){
							$keywords_phrase_num = strlen($url['keywords_phrase']);
							$keywords_phrase = $url['keywords_phrase'];
							echo $keywords_phrase = ($keywords_phrase_num > 15) ? substr($keywords_phrase,0,15)."..." : $keywords_phrase;
						}
					?>							
				</td>
				<td>		
					<?php 
						if(isset($url['keywords'])){
							$keywords_or_num = strlen($url['keywords']);
							$keywords = $url['keywords'];
							echo $keywords_or = ($keywords_or_num > 15) ? substr($keywords,0,15)."..." : $keywords;
						}
					?>							
				</td>
				<td>
					<input type="hidden" name="providers_total" value="<?php echo $providers_total; ?>" class="providers_total" />
					<?php								
						$providers_selected = count($providers);
						if($providers_selected == 1){
							if ( in_array( "", $providers ) ) {
								echo "All providers";
							}else{	
								foreach ( $list_providers as $record_provider ) {
									$provider = $record_provider->provider;
									$provider_name = $provider->name;
									$provider_id = $provider->id;											
									if(in_array( $provider_id, $providers )) {											
										echo $provider_name;									
									}
								}
							}
						}else{
							echo $providers_selected." selected";
						}
					?>
				</td>
				<td>							
					<?php
						if ( GrabPress::$environment == 'grabqa' ) {
							$times = array( '15 mins', '30  mins', '45 mins', '01 hr', '02 hrs', '06 hrs', '12 hrs', '01 day', '02 days', '03 days' );
							$values = array( 15,  30,  45, 60, 120, 360, 720, 1440, 2880, 4320 );
						}
						else {
							$times = array( '06 hrs', '12 hrs', '01 day', '02 days', '03 days' );
							$values = array( 360, 720, 1440, 2880, 4320 );
						}
						for ( $o = 0; $o < count( $times ); $o++ ) {
							$time = $times[$o];
							$value = $values[$o];
							if($value == $feed->update_frequency){
								echo $time;
							}									
						}
					?>
				</td>
				<td>							
					<?php echo $feed->posts_per_update; ?>
				</td>
				<td>
				<?php	
					if ( isset( $feed->custom_options->category ) ) {
						$category_list = $feed->custom_options->category;
						$category_list_length = count( $feed->custom_options->category );							

						$category_ids = get_all_category_ids();							

						if($category_list_length == 0){
							echo "Uncategorized";
						}elseif($category_list_length == 1){
							foreach ( $category_ids as $cat_id ) {
								$cat_name = get_cat_name( $cat_id );
								if(in_array( $cat_name, $category_list )){
									echo $cat_name;								
								}
						    }
						}else {								
							echo $category_list_length." selected";
						}	
					}				
				?>
				</td>
				<td>
					
					<?php
						foreach ( $blogusers as $user ) {
							$author_name = $user->display_name;
							$author_id = $user->ID;									
							if($author_id == $feed->custom_options->author_id){
								echo $author_name = (strlen($author_name) > 8) ? substr($author_name,0,8)."..." : $author_name;
							}									
						}
					?>
				</td>
				<td>
					<?php echo $click_to_play = $feed->auto_play ? "Auto" : "Click"; ?>
				</td>	
				<td>
					<?php echo $publish = $feed->custom_options->publish ? "Publish" : "Draft"; ?>
				</td>			
				<?php				
					if(isset($form['action']) && ($form['action']=='edit-feed') && ($form['feed_id']==$feedId)){
						$class_preview_button = "hide-button";
						$text_edit_button = "editing";
						$class_edit_button = "display-element";
						$class_delete_button = "display-element";
					}elseif(isset($form['action']) && ($form['action']=='edit-feed')){
						$class_preview_button = "hide-button";
						$text_edit_button = "edit";
						$class_edit_button = "hide-button";
						$class_delete_button = "hide-button";
					}else{						
						$class_preview_button = "display-element";
						$text_edit_button = "edit";
						$class_edit_button = "display-element";
						$class_delete_button = "display-element";
					}
				?>
				<td>
					<a href="#"  data-id="<?php echo $feedId; ?>" class="<?php echo $class_preview_button; ?> btn-preview-feed" >preview</a>
				</td>
				<td>
					<?php if(isset($form['action']) && ($form['action']=='edit-feed') && ($form['feed_id']==$feedId)){ 
						echo $text_edit_button;
					 }else{ ?>				
					<a href="admin.php?page=gp-autoposter&action=edit-feed&feed_id=<?php echo $feedId; ?>" id="btn-update-<?php echo $feedId; ?>" class="<?php echo $class_edit_button; ?> btn-update-feed">
						<?php echo $text_edit_button; ?>
					</a>
					<?php } ?>
				</td>
				<td>
					<input type="button" class="btn-delete <?php echo $class_delete_button; ?>" value="<?php _e( 'x' ) ?>" onclick="deleteFeed(<?php echo $feedId; ?>);" />
				</td>
			</tr>
		</form>
		<?php } ?>
	</table>
</div>
<div class="result"> </div>
</fieldset>
