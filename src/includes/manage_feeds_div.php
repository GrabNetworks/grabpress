	<?php
			$feeds = GrabPress::get_feeds();
			$num_feeds = count($feeds);
			$active_feeds = 0;
                        for($i = 0; $i < $num_feeds; $i++){
                         if($feeds[$i]->feed->active > 0){
                           $active_feeds++;
                         }
                        }	
			if( $active_feeds > 0 || $num_feeds > 0 ) {
				$noun = 'feed';
				if($active_feeds > 1 || $active_feeds == 0){
					$noun.='s';
				}
				GrabPress::showMessage('GrabPress plugin is enabled with '.$active_feeds.' '.$noun.' active.');
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
f(GrabPress::$environment == 'grabqa'){
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
		<?php }//if ?>
