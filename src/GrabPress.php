<?php
/*
Plugin Name: GrabPress
Plugin URI: http://www.grab-media.com
Description: Configure Grab Media's Autoposter software to deliver fresh video to your Blog. Requires a Grab Media Publisher account.
Version: 0.0.1
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
		static function get_request($url, $optional_headers = null) {
			  $ch = curl_init();
			  $timeout = 5;
			  curl_setopt($ch, CURLOPT_URL,$url);
			  curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,$timeout);
		      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json') );
			  $response = curl_exec($ch);
			
			  curl_close($ch);
			  return $response;
		}
		static function post_request($url, $data, $optional_headers = null) {
		  $params = array('http' => array(
		              'method' => 'POST',
		              'content' => $data
		            ));
		  if ($optional_headers!== null) { 
			$params['http']['header'] = $optional_headers; 
	      }
	      $ch = curl_init();

	      curl_setopt($ch, CURLOPT_URL, $url);
	      curl_setopt($ch, CURLOPT_POST      ,1);
	      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	      curl_setopt($ch, CURLOPT_POSTFIELDS    ,$data);

	      curl_setopt($ch, CURLOPT_RETURNTRANSFER    , 1);
	      // Get the result. 	
	      $response = @curl_exec($ch); 
		curl_close($ch);
		  return $response;
		}
		static function get_connector_id(){
			$rpc_url = get_bloginfo('url').'/xmlrpc.php';
			$connectors_json = self::get_request('http://74.10.95.28/connectors?api_key='.self::$api_key);
			$connectors_data = json_decode( $connectors_json );
			for($n = 0; $n < count($connectors_data); $n++){
				$connector = $connectors_data[$n]->connector;
				if( $connector -> destination_address == $rpc_url ) {
					$connector_id = $connector -> id;
				}
			}
			if(! $connector_id) {//create connector
				$connector_types_json = self::get_request('http://74.10.95.28/connector_types?api_key='.self::$api_key);
				$connector_types = json_decode( $connector_types_json );
			    for ($n = 0; $n < count( $connector_types ); $n++){
			        $connector_type = $connector_types[$n] -> connector_type;
			        if($connector_type -> name =='wordpress'){
			            $connector_type_id = $connector_type -> id;
			        }
		        }
	            if(! $connector_type_id ){
	            	self::abort('Error retrieving Autoposter connector type "wordpress"');
				}
				global $blog_id;
				$rpc_url = get_bloginfo('url').'/xmlrpc.php';
				$connector_post = array(
					connector => array(
						connector_type_id => $connector_type_id,
						destination_name => get_bloginfo('name'),
						destination_address => $rpc_url,
						username=>$user_login,
						password=>self::$api_key,
						custom_options => array(
							blog_id=>$blog_id
						)
					)
				);
				$post_json = json_encode($connector_post);
				$connector_json = self::post_request('http://74.10.95.28/connectors?api_key='.self::$api_key, $post_json, 'Content-type: application/json\r\n' );
				$connector_data = json_decode($connector_json);
				$connector_id = $connector_result -> connector -> id;
			}
			return $connector_id;
		}
		static $feed_message = 'Fields marked with a * are required.';
		static function create_feed(){
			$post_data = array(
				feed => array (
					name => $_POST['channel'],
					posts_per_update => $_POST['limit'],
					url => 'http://catalog.grabnetworks.com/catalogs/1/videos/search.json?keywords_and='.$_POST['keyword'],
					embed_id => 123456,
					embed_width => 400,
					embed_height => 300,
					custom_options => array(
						category => get_cat_name( $_POST[ 'category' ] ),
						publish => (bool)( $_POST[ 'publish' ] )
					),
					update_frequency => 60 * 60 * $_POST[ 'schedule' ]
				)
			);
			$connector_id = self::get_connector_id();
			$post_json = json_encode( $post_data );
			$post_url = 'http://74.10.95.28/connectors/'.$connector_id.'/feeds/?api_key='.self::$api_key;
			$response_json = self::post_request($post_url, $post_json);
			$response_data = json_decode($response_json);
			if( $response_data -> feed -> active == true){
				self::$feed_message = 'Grab yourself a coffee. Your videos are on their way! <img valign="middle" src="'.plugin_dir_url( __FILE__ ).'g.png"/>';
			}else{
				self::$feed_message = 'Something went wrong grabbing your feed. Please <a href = "https://getsatisfaction.com/grabmedia" target="_blank">contact Grab support.</a>';
			}
		}
		static function create_connection(){
			$user_url = get_bloginfo( 'url' );
			$user_nicename = 'grabpress';
	        $user_login = $user_nicename;
            $url_array = explode(  '/' , $user_url );
	        $email_host =  $url_array[2];
	        $email_dir = $url_array[3];
	        $user_email = $user_nicename.'.'.$email_dir.'@'.$email_host;
			$display_name	= 'GrabPress';
			$nickname 	= 'GrabPress';
			$first_name 	= 'Grab';
			$last_name	= 'Press';
			$post_data = array( 'user' => array(
								'first_name' => $first_name,
								'last_name' => $last_name,
								'email' => $user_email
								) );
            $post_json = json_encode($post_data);
			$user_json = self::post_request('http://74.10.95.28/user', $post_json, 'Content-type: application/json\r\n');
			$json_data = json_decode( $user_json );
            
            $api_key = $json_data -> user -> access_key;
          
            if ( $api_key ) {//store api key
              update_option( 'grabpress_key', $api_key );
            }
            self::$api_key = get_option( 'grabpress_key' );
			
			if(! isset( self::$api_key ) ){
				self::abort('Error retrieving API Key');
			}
            //keep user up-to-date
			$description = 'Proxy user account to allow GrabPress to automatically post new videos to your blog';
			$role = 'contributor';// auto-publish (contributor) or manual publish (author)
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
                $msg='User doesn`t exist';
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
			self::create_connection();
			self::enable_xmlrpc();
		}
		static function validate(){
			if (self::$invalid){
				echo 'style="border:1px dashed red;" ';
			};
		}
		static function grabpress_plugin_options() {
			
			if (!current_user_can('manage_options'))  {
				wp_die( __('You do not have sufficient permissions to access this page.') );
			}
			?>
			<div class="wrap">
				<img src="http://grab-media.com/corpsite-static/images/grab_logo.jpg"/>
				<h2>GrabPress: Autopost videos by Channel and Tag</h2>
				<p>Configure Grab Media's Autoposter software to deliver fresh video to your Blog </p>
				<script language = "JavaScript" type = "text/javascript">
					(function (global, $) {
						global.previewVideos = function () {
							console.log('preview');
							var keywords =  escape($( '#keyword-input' ).val()) ;
							var category =  escape($( '#channel-select').val()) ;
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
		            		<?php settings_fields('grab_press'); ?>
		            		<?php $options = get_option('grab_press'); ?>
		            		<table class="form-table">
		                		<tr valign="top">
							<th scope="row">API Key</th>
		                	<td>
                              <?php echo get_option( 'grabpress_key' ); ?>
							</td>
		        		        </tr>
						<tr>
							<th scope="row">Video channel</th>
							<td >
									<select  <?php GrabPress::validate() ?> name="channel" id="channel-select">
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
								<input <?php GrabPress::validate() ?>type="text" name="keyword" id="keyword-input" /> *
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
								<span class="description" <?php GrabPress::validate() ?> style='color:red'>
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
		}//if
	}//class
}
GrabPress::$api_key = get_option('grabpress_key');
GrabPress::$invalid = false;
if(isset ( $_POST) ){
	die($_POST['channel'] .':::'. && $_POST['keyword']  );
	if(isset(GrabPress::$api_key) && $_POST['channel'] != '' && $_POST['keyword'] != ''){
		GrabPress::create_feed();
	}else{
		GrabPress::$invalid = true;
	}
}
register_activation_hook( __FILE__, array( GrabPress, 'setup') );
if(! function_exists( 'grabpress_plugin_menu')){
	function grabpress_plugin_menu() {
		add_menu_page('GrabPress', 'GrabPress', 'manage_options', 'grabpress', array(GrabPress, 'grabpress_plugin_options'), plugin_dir_url( __FILE__ ).'g.png',10);
	}
}
add_action('admin_menu', 'grabpress_plugin_menu' );
?>
