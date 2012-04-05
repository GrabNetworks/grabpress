<?php
/*
Plugin Name: GrabPress
Plugin URI: http://www.grab-media.com
Description: Configure Grab's Autoposter software to deliver fresh video direct to your Blog. Requires a Grab Media Publisher account.
Version: 0.0.0
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

if( ! class_exists( 'GrabPress') ) {
	class GrabPress{
		static $api_key;
		static $invalid = false;
		static function abort( $message ) {
			die($message.'<br/>Please <a href = "https://getsatisfaction.com/grabmedia">contact Grab support</a>');
		}
		static function allow_tags( $allowedposttags ) {
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
			
			return $allowedposttags;
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
		static function post_json( $url, $data ) {
			$json = json_encode( $data );
				
			$ch = curl_init();
	    		curl_setopt( $ch, CURLOPT_URL, $url );
	    		curl_setopt( $ch, CURLOPT_POST, true );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
				'Content-type: application/json'
			) );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $json );
			curl_setopt( $ch, CURLOPT_VERBOSE, true ); // Display communication with server
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			$response = curl_exec( $ch ); 
			curl_close($ch);
		
			return $response;
		}
		static function get_connector_id(){
			$rpc_url = get_bloginfo('url').'/xmlrpc.php';
			$connectors_json = self::get_json( 'http://74.10.95.28/connectors?api_key='.self::$api_key );
			$connectors_data = json_decode( $connectors_json );
			for( $n = 0; $n < count( $connectors_data ); $n++ ) {
				$connector = $connectors_data[$n]->connector;
				if( $connector -> destination_address == $rpc_url ) {
					$connector_id = $connector -> id;
				}
			}
			if(! $connector_id) {//create connector
				$connector_types_json = self::get_json('http://74.10.95.28/connector_types?api_key='.self::$api_key);
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
				$rpc_url = get_bloginfo( 'url' ).'/xmlrpc.php';
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
				$connector_json = self::post_json( 'http://74.10.95.28/connectors?api_key='.self::$api_key, $connector_post, 'Content-type: application/json\r\n' );
				$connector_data = json_decode( $connector_json );
				$connector_id = $connector_result -> connector -> id;
			}
			return $connector_id;
		}
		static $feed_message = 'Fields marked with a * are required.';
		static function create_feed(){
			if( self::validate_key() ) {
				$categories = rawurlencode($_POST['category']);
				$keywords_and = rawurlencode($_POST['keyword']);
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
				$post_url = 'http://74.10.95.28/connectors/'.$connector_id.'/feeds/?api_key='.self::$api_key;
				$response_json = self::post_json( $post_url, $post_data );
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
			if( self::$api_key){
				$validate_json = self::get_json( 'http://74.10.95.28/user/validate?api_key='.self::$api_key );
				$validate_data = json_decode( $validate_json );
				return (! $validate_data -> error );
			}
			return false;
		}
		static function authorize_user(){
			$user_url = get_site_url();
			$user_nicename = 'grabpress'.rand();
	        $user_login = $user_nicename;
		$url_array = explode(  '/', $user_url );
		$email_host =  $url_array[ 2 ];
		$email_dir = $url_array[ 3 ];
	        $user_email = $user_nicename.'.'.$email_dir.'@'.$email_host;
			$display_name	= 'GrabPress';
			$nickname 	= 'GrabPress';
			$first_name 	= 'Grab';
			$last_name	= 'Press';
			if(! self::validate_key() ){
				$post_data = array( 
					'user' => array(
							'first_name' => $first_name,
						'last_name' => $last_name,
						'email' => $user_email
					)
				);
				$user_json = self::post_json('http://74.10.95.28/user', $post_data, 'Content-type: application/json\r\n');
				$user_data = json_decode( $user_json );
            
			$api_key = $user_data -> user -> access_key;
          
			if ( $api_key ) {//store api key
				update_option( 'grabpress_key', $api_key );
			}
			self::$api_key = get_option( 'grabpress_key' );
		}
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
		self::authorize_user();
		self::enable_xmlrpc();
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
			<h2>GrabPress: Autopost videos by Channel and Tag</h2>
			<p>Configure Grab Media's Autoposter software to deliver fresh video to your Blog </p>
			<script language = "JavaScript" type = "text/javascript">
				( function ( global, $ ) {
					global.previewVideos = function () {
						console.log('preview');
						var keywords =  $( '#keyword-input' ).val();
						var category =  $( '#channel-select').val();
						var limit =  $( '#limit-select').val() ; 
						window.open( 'http://catalog.grabnetworks.com/catalogs/1/videos/search.mrss?keywords_and=' + keywords + '&categories=' + category + '&limit=' + limit );	
					}	
				} )( window, jQuery );
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
							<th scope="row">Video channel</th>
							<td>
								<select  style="<?php GrabPress::outline_invalid() ?>" name="channel" id="channel-select">
									<option selected = "selected" value = "">Choose One</option>
									<?php 	
										$json = file_get_contents('http://catalog.grabnetworks.com/catalogs/1/categories');
										$list = json_decode($json);
										foreach ($list as $record) {
									   		$category = $record -> category -> name;
									   		echo "<option value = \"$category\"> $category </option>\n";
										} 
									?>
								</select> *
								<span class="description">Select a channel to grab from</span>								
							</td>
						</tr>
			        		<tr valign="top">
							<th scope="row">Keywords</th>
		        		           	<td >
								<input style="<?php GrabPress::outline_invalid() ?>" type="text" name="keyword" id="keyword-input" /> *
								<span class="description">Enter search keywords (e.g. <b>celebrity gossip</b>)</span>
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
							<input type="button" onclick="previewVideos()" class="button-secondary" value="<?php _e('Preview Videos') ?>" />
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
				<table>
			</form>
		</div>
		<?php
/* End HTML */
		}//if
	}//class
}
GrabPress::$api_key = get_option( 'grabpress_key' );
GrabPress::$invalid = false;
if( isset ( $_POST) ) {
	if( isset( GrabPress::$api_key ) && $_POST[ 'channel' ] != '' && $_POST[ 'keyword' ] != '' ) {
		GrabPress::create_feed();
	} else if( isset( $_POST['limit'] ) ) {
		GrabPress::$invalid = true;
	}
}
register_activation_hook( __FILE__, array( GrabPress, 'setup') );
if(! function_exists( 'grabpress_plugin_menu')){
	function grabpress_plugin_menu() {
		add_menu_page('GrabPress', 'GrabPress', 'manage_options', 'grabpress', array( GrabPress, 'grabpress_plugin_options' ), plugin_dir_url( __FILE__ ).'g.png', 10 );
	}
}
add_action('admin_menu', 'grabpress_plugin_menu' );
add_filter( 'edit_allowedposttags', array(GrabPress, 'allow_tags') );
?>