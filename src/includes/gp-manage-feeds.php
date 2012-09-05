<?php
	$feeds = GrabPress::get_feeds();
	$num_feeds = count( $feeds );
	$active_feeds = 0;
	for ( $i=0; $i < $num_feeds; $i++ ) {
		if ( $feeds[$i]->feed->active > 0 ) {
			$active_feeds++;
		}
	}
	if ( $active_feeds > 0 || $num_feeds > 0 ) {
		$noun = 'feed';
		if ( $active_feeds > 1 || $active_feeds == 0 ) {
			$noun .= 's';
		}
		if ( GrabPress::$environment == "grabqa" ) {
			GrabPress::show_message( 'GrabPress plugin is enabled with '.$active_feeds.' '.$noun.' active.  ENVIRONMENT = ' . GrabPress::$environment );
		}else {
			GrabPress::show_message( 'GrabPress plugin is enabled with '.$active_feeds.' '.$noun.' active.' );
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
			<th>Player Mode</th>
			<th>Post Categories</th>
			<th>Author</th>
			<th>Providers</th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
		<?php
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
			<tr>
				<td>
					<input type="hidden" name="feed_id" value="<?php echo $feedId; ?>" />
					<?php
						$checked = ( $feed->active  ) ? 'checked = "checked"' : '';
						echo '<input '.$checked.' type="checkbox" onclick="toggleButton('.$feedId.')" value="1" name="active" class="active-check"/>'
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
					<?php echo $url['keywords_and']; ?>							
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
					<?php echo $publish = $feed->custom_options->publish ? "Yes" : "No"; ?>
				</td>
				<td>
					<?php echo $click_to_play = $feed->auto_play ? "Auto" : "Click"; ?>
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
					<input type="button" onclick="previewFeed(<?php echo $feedId; ?>)" class="button-secondary" value="<?php _e( 'preview' ) ?>" id="btn-preview-feed" />
				</td>
				<td>
					<button class="button-primary btn-update" id="btn-update-<?php echo $feedId; ?>" name="<?php echo $feedId; ?>" >edit</button>
				</td>
				<td>
					<input type="button" class="button-primary btn-delete" value="<?php _e( 'X' ) ?>" onclick="deleteFeed(<?php echo $feedId; ?>);" />
				</td>
			</tr>
			</form>
		<?php } ?>
	</table>
</div>

<div class="result"> </div>
<?php } ?>