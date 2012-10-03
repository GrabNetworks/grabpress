<?php
	$feeds = GrabPress::get_feeds();
	$num_feeds = count( $feeds ); 
	$active_feeds = 0;

?>
<fieldset id="manage-table" class="fieldset-manage">
	<legend><?php echo isset($_GET['action'])=='edit-feed' ? 'Current':'Manage'?> Feeds</legend>

<div>
	<table class="grabpress-table manage-table" cellspacing="0">
		<tr>
			<th>Active</th>
			<th>Video<br/>Categories</th>
			<th>Keywords</th>
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
			$feeds = GrabPress::get_feeds();
			$num_feeds = count( $feeds );
			$json = GrabPress::get_json( 'http://catalog.'.GrabPress::$environment.'.com/catalogs/1/categories' );
			$categories_list = json_decode( $json );

			for ( $n = 0; $n < $num_feeds; $n++ ) {
				$feed = $feeds[$n]->feed;
				$url = array();
				parse_str( parse_url( $feed->url, PHP_URL_QUERY ), $url );
				$feedId = $feed->id;
				$providers = explode( ",", $url["providers"] ); // providers chosen by the user
		?>
		<form id="form-<?php echo $feedId; ?>" action=""  method="post">
			<input type="hidden" id="action-<?php echo $feedId; ?>" name="action" value="" />
			<input type="hidden" name="referer" value="edit" />
			<?php 
				if(isset($_GET['action']) && ($_GET['action']=='edit-feed') && ($_GET['feed_id']==$feedId)){
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
						if(isset($_GET['action'])=='edit-feed'){
							echo $checked = ( $feed->active  ) ? 'Yes' : 'No'; 
					 	}else{ 
							$checked = ( $feed->active  ) ? 'checked = "checked"' : '';
							echo '<input '.$checked.' type="checkbox" onclick="toggleButton('.$feedId.')" value="1" name="active" class="active-check" id="active-check-'.$feedId.'" />';
						} 
					?>
				</td>
				<td>							
					<?php
						foreach ( $categories_list as $record ) {
							$category = $record -> category;
							$name = $category -> name;
							$id = $category -> id;
							if($name == $feed->name){
								echo $name;
							}									
						}
					?>
				</td>
				<td>		
					<?php 
						$keywords_and_num = strlen($url['keywords_and']);
						echo $keywords_and = ($keywords_and_num > 15) ? substr($url['keywords_and'],0,15)."..." : $url['keywords_and'];
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
								foreach ( $list_provider as $record_provider ) {
									$provider = $record_provider->provider;
									$provider_name = $provider->name;
									$provider_id = $provider->id;											
									if(in_array( $provider_id, $providers )) {											
										echo $provider_name;									
									}
								}
							}
						}else{
							echo $providers_selected." providers selected of ".$providers_total;
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
							$times = array( '12 hrs', '01 day', '02 days', '03 days' );
							$values = array( 720, 1440, 2880, 4320 );
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
								echo $author_name;
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
					if(isset($_GET['action']) && ($_GET['action']=='edit-feed') && ($_GET['feed_id']==$feedId)){
						$class_preview_button = "hide-button";
						$text_edit_button = "editing";
						$class_edit_button = "display-element";
						$class_delete_button = "display-element";
					}elseif(isset($_GET['action']) && ($_GET['action']=='edit-feed')){
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
					<a href="#" onclick="previewFeed(<?php echo $feedId; ?>);return false;" id="btn-preview-feed-<?php echo $feedId; ?>" class="<?php echo $class_preview_button; ?>" >preview</a>
				</td>
				<td>
					<?php if(isset($_GET['action']) && ($_GET['action']=='edit-feed') && ($_GET['feed_id']==$feedId)){ 
						echo $text_edit_button;
					 }else{ ?>				
					<a href="#" onclick="editFeed(<?php echo $feedId; ?>);return false;" id="btn-update-<?php echo $feedId; ?>" class="<?php echo $class_edit_button; ?>">						
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
