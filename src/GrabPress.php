<?php
/*
Plugin Name: GrabPress
Plugin URI: http://www.grab-media.com
Description: Configure Grab's Autoposter software to deliver fresh video direct to your Blog. Requires a Grab Media Publisher account.
Version: 0.0.1b2
Author: Grab Media
Author URI: http://www.grab-media.com/publisher/solutions/autoposter
License: GPL2
*/
/*  Copyright 2012  Grab Networks Holdings, Inc.  (email : licensing@grab-media.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if( ! class_exists( 'GrabPress' ) ) {
	class GrabPress{
		static $api_key;
		static $invalid = false;
		/**
 * Generic function to show a message to the user using WP's 
 * standard CSS classes to make use of the already-defined
 * message colour scheme.
 *
 * @param $message The message you want to tell the user.
 * @param $errormsg If true, the message is an error, so use 
 * the red message style. If false, the message is a status 
  * message, so use the yellow information message style.
 */
		static function showMessage($message, $errormsg = false)
		{
			if ($errormsg) {
				echo '<div id="message" class="error">';
			}
			else {
				echo '<div id="message" class="updated fade">';
			}
			$icon_src = plugin_dir_url( __FILE__ ).'g.png';
			echo '<p><img src="'.$icon_src.'" style="vertical-align:top; position:relative; top:-2px; margin-right:2px;"/>'.$message.'</p></div>';
		}    
		static function abort( $message ) {
			//die($message.'<br/>Please <a href = "https://getsatisfaction.com/grabmedia">contact Grab support</a>');
		}
		static function allow_tags() {
			global $allowedposttags;
			if(! isset( $allowedposttags[ 'div' ] ) ) {
				$allowedposttags[ 'div' ] = array();
			}
			$allowedposttags[ 'div' ][ 'id' ] = array();         			
			$allowedposttags[ 'div' ][ 'style' ] = array();

      			if(! isset( $allowedposttags[ 'object' ] ) ) {
				$allowedposttags[ 'object' ] = array();
			}
			$allowedposttags[ 'object' ][ 'id' ] = array();      			
			$allowedposttags[ 'object' ][ 'width' ] = array();   			
			$allowedposttags[ 'object' ][ 'height' ] = array();  			
			$allowedposttags[ 'object' ][ 'type' ] = array();    			
			$allowedposttags[ 'object' ][ 'align' ] = array();   			
			$allowedposttags[ 'object' ][ 'data' ] = array();
     			
			if(! isset( $allowedposttags[ 'param' ] ) ) {
				$allowedposttags[ 'param' ] = array();
			}            			
			$allowedposttags[ 'param' ][ 'name' ] = array();      			
			$allowedposttags[ 'param' ][ 'value' ] = array(); 
		}
		static function getApiLocation() {
			if ($_SERVER["SERVER_ADDR"] == "127.0.0.1"){
				$apiLocation = "10.3.1.37";
			}else{
				$apiLocation = "74.10.95.28";
			} 
			return $apiLocation;
		}
		static function get_json( $url, $optional_headers = null) {
			$ch = curl_init();
			$timeout = 5;
			curl_setopt( $ch, CURLOPT_URL, $url );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-type: application/json\r\n' ) );
			$response = curl_exec( $ch );
			curl_close( $ch );
			
			return $response;
		}
		function apiCall($method, $resource, $data=array()){
			$json = json_encode( $data );
			$apiLocation = self::getApiLocation();
			$location = "http://".$apiLocation.$resource;

			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $location );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_VERBOSE, true );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
				'Content-type: application/json'
			) );
			switch($method){
				case "GET":					
					curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 60 );
					break;
				case "POST";
					curl_setopt( $ch, CURLOPT_POST, true );
					curl_setopt( $ch, CURLOPT_POSTFIELDS, $json );
					break;
				case "PUT";
					//curl_setopt( $ch, CURLOPT_PUT, true );
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); 
					curl_setopt( $ch, CURLOPT_POSTFIELDS, $json );
					break;
				case "DELETE";
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
					break;
			}
			$response = curl_exec( $ch ); 
			curl_close($ch);
			return $response;
		}

		static function get_connector_id(){
			if( self::validate_key() ) {
				$rpc_url = get_bloginfo('url').'/xmlrpc.php';
				$connectors_json = self::apiCall("GET",  '/connectors?api_key='.self::$api_key );
				$connectors_data = json_decode( $connectors_json );
				for( $n = 0; $n < count( $connectors_data ); $n++ ) {
					$connector = $connectors_data[$n]->connector;
					if( $connector -> destination_address == $rpc_url ) {
						$connector_id = $connector -> id;
					}
				}
				if(! $connector_id) {//create connector
					$connector_types_json = self::apiCall("GET",  '/connector_types?api_key='.self::$api_key );
					$connector_types = json_decode( $connector_types_json );
					for ( $n = 0; $n < count( $connector_types ); $n++ ) {
						$connector_type = $connector_types[$n] -> connector_type;
						if( $connector_type -> name =='wordpress' ) {
							$connector_type_id = $connector_type -> id;
						}
					}
					if(! $connector_type_id ){
						self::abort( 'Error retrieving Autoposter id for connector name "wordpress"' );
					}
					global $blog_id;
					$connector_post = array(
						connector => array(
							connector_type_id => $connector_type_id,
							destination_name => get_bloginfo( 'name' ),
							destination_address => $rpc_url,
							username =>'grabpress',
							password => self::$api_key,
							custom_options => array(
								blog_id => $blog_id
							)
						)
					);

					$connector_json = self::apiCall("POST",  '/connectors?api_key='.self::$api_key, $connector_post); 
					$connector_data = json_decode( $connector_json );
					$connector_id = $connector_result -> connector -> id;
				}
				return $connector_id;
			}else{
				self::$feed_message = 'Your API key is no longer valid. Please <a href = "https://getsatisfaction.com/grabmedia" target="_blank">contact Grab support.</a>';
				return false;
			}
		}
		static $feed_message = 'Fields marked with a * are required.';
		static function create_feed(){
			if( self::validate_key() ) {
				$categories = rawurlencode( $_POST[ 'channel' ] );
				$keywords_and = rawurlencode( $_POST[ 'keyword' ] );
				$json = GrabPress::get_json( 'http://catalog.grabnetworks.com/catalogs/1/categories' );
				$list = json_decode( $json );
				foreach ( $list as $record ) {
			   		$category = $record -> category;
					$name = $category -> name;
					$id = $category -> id;
					if($name == $_POST['channel']){
						$cat_id = $id;
					}
				}
				$url = 'http://catalog.grabnetworks.com/catalogs/1/videos/search.json?keywords_and='.$keywords_and.'&categories='.$categories.'&order=DESC&order_by=created_at';
				$connector_id = self::get_connector_id();
				$post_data = array(
					feed => array(
						name => $_POST[ 'channel' ],
						posts_per_update => $_POST[ 'limit' ],
						url => $url,
						custom_options => array(
							category => get_cat_name( $_POST[ 'category' ] ),
							publish => (bool)( $_POST[ 'publish' ] )
						),
						update_frequency => 60 * $_POST[ 'schedule' ]
					)
				);
				$response_json = self::apiCall("POST", "/connectors/" . $connector_id . "/feeds/?api_key=".self::$api_key, $post_data);
				$response_data = json_decode( $response_json );
				if( $response_data -> feed -> active == true ){
					self::$feed_message = 'Grab yourself a coffee. Your videos are on the way!';
				}else{
					self::$feed_message = 'Something went wrong grabbing your feed. Please <a href = "https://getsatisfaction.com/grabmedia" target="_blank">contact Grab support</a>\n'.$response_data;
				}
			}else{
				self::$feed_message = 'Your API key is no longer valid. Please <a href = "https://getsatisfaction.com/grabmedia" target="_blank">contact Grab support.</a>';
			}
		}
		static function validate_key() {
			$api_key = get_option('grabpress_key');
			if( $api_key != '' ){
				$validate_json = self::apiCall("GET", "/user/validate?api_key=".$api_key);
				$validate_data = json_decode( $validate_json );
				if (  $validate_data -> error ) {
					return self::create_API_connection();
				}else {
					self::$api_key = $api_key;
					return true;
				}
			}else{
				return self::create_API_connection();
			}
			return false;
		}
		static function get_feeds(){
			if(self::validate_key()){
				$connector_id = self::get_connector_id();
				$feeds_json = self::apiCall("GET", '/connectors/'.$connector_id.'/feeds?api_key='.self::$api_key);
				$feeds_data = json_decode( $feeds_json );
				return $feeds_data;
			}else{
				self::abort('no valid key');
			}
		}
		static function create_API_connection(){
			$user_url = get_site_url();
			$user_nicename = 'grabpress';
	        $user_login = $user_nicename;
			$url_array = explode(  '/', $user_url );
			$email_host =  $url_array[ 2 ];
			$email_dir = $url_array[ 3 ];
	        $user_email = $user_nicename.'.'.$email_dir.'+'.rand().'@'.$email_host;//rand().
			$display_name	= 'GrabPress';
			$nickname 	= 'GrabPress';
			$first_name 	= 'Grab';
			$last_name	= 'Press';
			$post_data = array( 
				'user' => array(
						'first_name' => $first_name,
					'last_name' => $last_name,
					'email' => $user_email
				)
			);
			$user_json = self::apiCall("POST", '/user', $post_data);
			$user_data = json_decode( $user_json );
           
		$api_key = $user_data -> user -> access_key;
		if ( $api_key ) {//store api key
			update_option( 'grabpress_key', $api_key );
		}
		self::$api_key = get_option( 'grabpress_key' );
		if(! isset( self::$api_key ) ){
			self::abort('Error retrieving API Key');
		}
		//keep user up-to-date
		$description = 'Bringing you the best media on the Web.';
		$role = 'author';// minimum for auto-publish (author)
		$user_data = get_userdatabylogin($user_login);
		if ($user_data){// user exists, hash password to keep data up-to-date
			$msg = 'User Exists ('.$user_login.'): '.$user_data->ID;
			$user = array(
				id => $user_data -> ID,
				user_login => $user_login,
				user_nicename => $user_nicename,
				user_url => $user_url,
				user_email => $user_email,
				display_name => $display_name,
				user_pass => wp_hash_password( self::$api_key ),
				nickname => $nickname,
				first_name => $first_name,
				last_name => $last_name,
				description => $description,
				role => $role
			);
			$user_id = @wp_update_user($user);
	        }else{// user doesnt exist, store password with new data.
			$user = array(
                		user_login => $user_login,
	                	user_nicename => $user_nicename,
				user_url => $user_url,
				user_email => $user_email,
				display_name => $display_name,
				user_pass =>  self::$api_key ,
				nickname => $nickname,
				first_name => $first_name,
				last_name => $last_name,
				description => $description,
				role => $role
			);
			$user_id = @wp_insert_user( $user );
		}
		if(! isset($user_id) ){
			self::abort('Error creating user.');
		}
		return true;
	}
 
	static function enable_xmlrpc(){
		update_option( 'enable_xmlrpc', 1 );
		if (! get_option( 'enable_xmlrpc') ){
			self::abort('Error enabling XML-RPC.');
		}
	}
	static function register_settings(){
		register_setting( 'grab_press', 'access_key' );
	}
	static function setup(){
		self::validate_key();
		self::enable_xmlrpc();
	}
	static function cleanup(){
		delete_option('grabpress_key');
	}
	static function outline_invalid(){
		if (self::$invalid){
			echo 'border:1px dashed red;';
		};
	}
	static function grabpress_plugin_options() {	
		if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
/* Start HTML */ ?>
		<div class="wrap">
			<img src="http://grab-media.com/corpsite-static/images/grab_logo.jpg"/>
			<h2>GrabPress: Autopost Videos by Channel and Tag</h2>
			<p>New video content delivered fresh to your blog.</p>
			<h3>Create Feed</h3>
			<script language = "JavaScript" type = "text/javascript">
				( function ( global, $ ) {
					global.previewVideos = function () {
						console.log('preview');
						var keywords =  $( '#keyword-input' ).val();
						var category =  $( '#channel-select').val();
						var limit =  $( '#limit-select').val() ; 
						window.open( 'http://catalog.grabnetworks.com/catalogs/1/videos/search.mrss?keywords_and=' + keywords + '&categories=' + category );	
					}	
				} )( window, jQuery );		

			    function toggleButton(feedId) {
					jQuery('#btn-update-' + feedId).css({"visibility":"visible"});
				}
				function deleteFeed(id){
					var form = jQuery('#form-'+id);
					var action = jQuery('#action-'+id);
					action.val("delete");
					form.submit();
				}
				function updateFeed(id){
					var form = jQuery('#form-'+id);
					var action = jQuery('#action-'+id);
					action.val("modify");
					form.submit();
				}							
				
			</script>
			<?php 
				$rpc_url = get_bloginfo('url').'/xmlrpc.php';
				$connector_id = GrabPress::get_connector_id();
			?>
			<form method="post" action="">
	            		<?php settings_fields('grab_press');//XXX: Do we need this? ?>
	            		<?php $options = get_option('grab_press'); //XXX: Do we need this? ?>
	            		<table class="form-table">
	                		<tr valign="top">
						<th scope="row">API Key</th>
			                	<td>
							<?php echo get_option( 'grabpress_key' ); ?>
						</td>
					</tr>
						<tr>
							<th scope="row">Video Channel</th>
							<td>
								<select  style="<?php GrabPress::outline_invalid() ?>" name="channel" id="channel-select">
									<option selected = "selected" value = "">Choose One</option>
									<?php 	
										$json = GrabPress::get_json('http://catalog.grabnetworks.com/catalogs/1/categories');
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
								<input type="text" name="keyword" id="keyword-input" /> 
								<span class="description">Enter search keywords (e.g. <b>Dexter blood spatter</b>)</span>
							</td>
		        		        </tr>
		        		        <tr valign="top">
							<th scope="row">Max Results</th>
		        		           	<td>
								<select name="limit" id="limit-select">
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
								<select name="schedule" id="schedule-select">
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
					</tr>
		        		<tr valign="top">
						<th scope="row">Post Category</th>
						<td>
							<?php 	$args = array(	'hide_empty' => 0, 
	  										'child_of' => 0,
	  										'hierarchical' => 1, 
	  										'name' => 'category',
	  										'id' => 'category-select');
										wp_dropdown_categories( $args ); 
							?>
							<span class="description">Select a category for your autoposts</span>
						</td>
					</tr>
					<tr valign="top">
						<td>
							<input type="button" onclick="previewVideos()" class="button-secondary" value="<?php _e('Preview Feed') ?>" />
						</td>
						<td>
							<span class="description">Click to preview which videos will be autoposted on next grab (mrss feed.)</span>
						</td>
					</tr>
					<tr valign="top">
						<td>
							<input type="submit" class="button-primary" value="<?php _e('Grab Videos') ?>" />
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
			if( $num_feeds > 0 ) {
				$noun = 'feed';
				if($num_feeds > 1){
					$noun.='s';
				}
				GrabPress::showMessage('GrabPress Autoposter active with '.$num_feeds.' '.$noun.'.');
			?>
			<div>
				<h3>Manage Feeds</h3>
				<table>
					<tr>
						<th>Active</th>
						<th>Video Channel</th>
						<th>Keywords</th>
						<th>Schedule</th>
						<th>Max Results</th>
						<th>Publish</th>
						<th>Post Category</th>
						<th>Delete</th>
					</tr>
				<?php for ($n = 0; $n < $num_feeds; $n++ ) { 
					$feed = $feeds[$n]->feed;
					$url = array();
					parse_str( parse_url($feed->url, PHP_URL_QUERY), $url);
					$feedId = $feed->id;
				?>
				<form id="form-<?php echo $feedId; ?>" action=""  method="post">		
					<input type="hidden" id="action-<?php echo $feedId; ?>" name="action" value="" />
					<tr>											
						<td>
								<input type="hidden" name="feed_id" value="<?php echo $feedId; ?>" />	
								<?php 
									$checked = ( $feed->active  ) ? 'checked = "checked"' : '';
									echo '<input '.$checked.' type="checkbox" onclick="toggleButton('.$feedId.')" value="1" name="active" class="active-check"/>'
								?>
						<td>
							<select  style="<?php GrabPress::outline_invalid() ?>" name="channel" id="channel-select" >
								<?php 	
									$json = GrabPress::get_json('http://catalog.grabnetworks.com/catalogs/1/categories');
									$list = json_decode($json);
									foreach ($list as $record) {
								   		$category = $record -> category;
										$name = $category -> name;
										$id = $category -> id;
										$selected = ( $name == $url['categories'] )  ? 'selected = "selected"' : '';
								   		echo '<option '.$selected.' value = "'.$name.'">'.$name.'</option>\n';
									} 
								?>
								</select>
						</td>
						<td>	
								<input type="text" name="keywords_and" onblur="toggleButton(<?php echo $feedId; ?>)" value="<?php echo $url['keywords_and']; ?>" class="keywords_and"/>		
						</td>
						<td>
							<select name="schedule" id="schedule-select">
								<?php 
									$times = array( '15m', '30m', '45m', '1h', '2h',  '6h', '12h', '24h' );
									$values = array(  15,  30,  45, 60, 120, 360, 720, 1440 );
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
							<select name="limit" id="limit-select">
									<?php for ($o = 1; $o < 6; $o++) {
										$selected = ( $o == $feed->posts_per_update )? 'selected = "selected"' : '';
										echo '<option '.$selected.' value = "'.$o.'">'.$o.'</option>\n';
									 } ?>
							</select>
						</td>
						<td>
							<?php 
								$checked = ( $feed->custom_options->publish  ) ? ' checked = "checked"' : '';
								echo '<input'.$checked.' type="checkbox" value="1" name="publish" id="publish-check"/>';
							?>
						</td>
						<td>
							<?php 
								$category = get_term_by('name', $feed->custom_options->category, 'category');
								$selected = $category->term_id;
								$args = array(	'hide_empty' => 0, 
	  								'child_of' => 0,
	  								'hierarchical' => 1, 
	  								'name' => 'category',
	  								'id' => 'category-select-'.$feed->id,  								
									'selected' => $selected ,
									"class" => 'category-select');
								wp_dropdown_categories( $args );
								?>
								<script language="javascript">
									jQuery("#category-select-<?=$feed->id?>").change(function(){
										toggleButton(<?=$feed->id?>);
									});
								</script>
						</td>						
						<td>
							<input type="button" class="button-primary" style="background:red;border-color:red;" value="<?php _e('X') ?>" onclick="deleteFeed(<?php echo $feedId; ?>);" />
						</td>
						<td>	
							<button class="button-primary btn-update" id="btn-update-<?php echo $feedId; ?>" style="visibility:hidden;" onclick="updateFeed(<?php echo $feedId; ?>);">update</button>					 
						</td>

					</tr>	
					</form>				
				<?php } ?>				
				</table>
			</div>

<?php			}
/* End HTML */
		}//if
	}//class
}
function dispatcher($params){	
	if( count($_POST) > 0 ) {
		switch ($params['action']){
			case 'update':
					if( GrabPress::validate_key() && $_POST[ 'channel' ] != '' ) {
						GrabPress::create_feed();
					} else if( isset( $_POST['limit'] ) ) {
						GrabPress::$invalid = true;
					}
					break;
			case 'delete':
					$feed_id = $_POST["feed_id"];
					$connector_id = GrabPress::get_connector_id();
					GrabPress::apiCall("DELETE", "/connectors/" . $connector_id . "/feeds/".$feed_id."?api_key=".GrabPress::$api_key, $feed_id);
					break;	
			case 'modify':
					$feed_id = $_POST["feed_id"];
					$keywords_and = htmlspecialchars($_POST["keywords_and"]);
					$category = get_cat_name($_POST["category"]);
					$url = 'http://catalog.grabnetworks.com/catalogs/1/videos/search.json?keywords_and='.$keywords_and.'&categories='.$category.'&order=DESC&order_by=created_at';
					$connector_id = GrabPress::get_connector_id();	
					$active	= (bool)$_POST["active"];				
					$post_data = array(
						feed => array(
							active => $active,
							name => $_POST[ 'channel' ],
							posts_per_update => $_POST[ 'limit' ],
							url => $url,
							custom_options => array(
								category => get_cat_name( $_POST[ 'category' ] ),
								publish => (bool)( $_POST[ 'publish' ] )
							),
							update_frequency => 60 * $_POST[ 'schedule' ]
						)
					);				
					GrabPress::apiCall("PUT", "/connectors/" . $connector_id . "/feeds/".$feed_id."?api_key=".GrabPress::$api_key, $post_data);	
					break;				
		}
	}
}

dispatcher($_REQUEST);
register_deactivation_hook(__FILE__, array( GrabPress, 'cleanup') );
register_activation_hook( __FILE__, array( GrabPress, 'setup') );
if(! function_exists( 'grabpress_plugin_menu')){
	function grabpress_plugin_menu() {
		add_menu_page('GrabPress', 'GrabPress', 'manage_options', 'grabpress', array( GrabPress, 'grabpress_plugin_options' ), plugin_dir_url( __FILE__ ).'g.png', 10 );
		add_submenu_page( 'grabpress', 'AutoPoster', 'AutoPoster', 'publish_posts', 'autoposter', array( GrabPress, 'grabpress_plugin_options' ));
		global $submenu;
		unset($submenu['grabpress'][0]);
		$feeds = GrabPress::get_feeds();
		$num_feeds = count($feeds);
		if( $num_feeds == 0){
			$admin = get_admin_url();
			$admin_page = $admin.'admin.php?page=autoposter';
			$current_page = 'http://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
			if( $current_page != $admin_page){
				$here = '<a href="'.$admin_page.'">here</a>';
			}else{
				$here = 'here';
			}

			GrabPress::showMessage('Thank you for activating Grab Autoposter. Try creating your first feed '.$here.'.');
		}
	}
}
add_action('admin_menu', 'grabpress_plugin_menu' );
GrabPress::allow_tags();

