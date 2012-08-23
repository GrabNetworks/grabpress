<?php

/*
Plugin Name: GrabPress
Plugin URI: http://www.grab-media.com/publisher/solutions/autoposter
Description: Configure Grab's AutoPoster software to deliver fresh video direct to your Blog. Create or use an existing Grab Media Publisher account to get paid!
Version: 0.4.1b33
Author: Grab Media
Author URI: http://www.grab-media.com
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

/**
* Open, parse, and return the template file.
*
* @param $file string the template file name
*/

if( ! class_exists( 'GrabPress' ) ) {
	class GrabPress{
		static $api_key;
		static $invalid = false;
		static $environment = 'grabqa'; // or 'grabnetworks'
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
			
			if(! isset( $allowedposttags[ 'script' ] ) ) { 
				$allowedposttags[ 'script' ] = array(); 
			}
			$allowedposttags[ 'script' ][ 'type' ] = array();
			$allowedposttags[ 'script' ][ 'language' ] = array();
			$allowedposttags[ 'script' ][ 'src' ] = array();
			
			if(! isset( $allowedposttags[ 'style' ] ) ) {
				 $allowedposttags[ 'style' ] = array(); 
			}
		}
		static function get_api_location() {
			if ($_SERVER['SERVER_ADDR'] == '127.0.0.1'){
				$apiLocation = '10.3.1.37';
			}else{
				$apiLocation = '74.10.95.28';
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
			$apiLocation = self::get_api_location();			
			$location = 'http://'.$apiLocation.$resource;
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $location );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_VERBOSE, true );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
				'Content-type: application/json'
			) );
			switch($method){
				case 'GET':					
					curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 60 );
					break;
				case 'POST';
					curl_setopt( $ch, CURLOPT_POST, true );
					curl_setopt( $ch, CURLOPT_POSTFIELDS, $json );
					break;
				case 'PUT';
					//curl_setopt( $ch, CURLOPT_PUT, true );
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT'); 
					curl_setopt( $ch, CURLOPT_POSTFIELDS, $json );
					break;
				case 'DELETE';
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
					break;
			}
			$response = curl_exec( $ch ); 
			curl_close($ch);
			return $response;
		}

		static function get_connector_id(){
			if( self::validate_key() ) {
				$rpc_url = get_bloginfo('url').'/xmlrpc.php';
				$connectors_json = self::apiCall('GET',  '/connectors?api_key='.self::$api_key );
				$connectors_data = json_decode( $connectors_json );
				for( $n = 0; $n < count( $connectors_data ); $n++ ) {
					$connector = $connectors_data[$n]->connector;
					if( $connector -> destination_address == $rpc_url ) {
						$connector_id = $connector -> id;
					}
				}
				if(! isset($connector_id)) {//create connector
					$connector_types_json = self::apiCall('GET',  '/connector_types?api_key='.self::$api_key );
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
						'connector' => array(
							'connector_type_id' => $connector_type_id,
							'destination_name' => get_bloginfo( 'name' ),
							'destination_address' => $rpc_url,
							'username' =>'grabpress',
							'password' => self::$api_key,
							'custom_options' => array(
								'blog_id' => $blog_id
							)
						)
					);

					$connector_json = self::apiCall('POST',  '/connectors?api_key='.self::$api_key, $connector_post); 
					$connector_data = json_decode( $connector_json );
					$connector_id = $connector_data -> connector -> id;
				}
				return $connector_id;
			}else{
				self::$feed_message = 'Your API key is no longer valid. Please <a href = "https://getsatisfaction.com/grabmedia" target="_blank">contact Grab support.</a>';
				return false;
			}
		}
		static $feed_message = 'Fields marked with an asterisk * are required.';
		static function create_feed(){
			if( self::validate_key() ) {
				$categories = rawurlencode($_POST[ 'channel' ]);
				$keywords_and = rawurlencode( $_POST[ 'keyword' ] );
				$json = GrabPress::get_json( 'http://catalog.'.self::$environment.'.com/catalogs/1/categories' );
				$list = json_decode( $json );
				foreach ( $list as $record ) {
			   		$category = $record -> category;
					$name = $category -> name;
					$id = $category -> id;
					if($name == $_POST['channel']){
						$cat_id = $id;
					}
				}		
				$providers = $_POST['provider'];
				$providersList = implode(',', $providers); 
				$providersListTotal = count($providers); // Total providers chosen by the user
				$providers_total = $_POST['providers_total']; // Total providers from the catalog list
				if($providersListTotal == $providers_total){
					$providersList = '';
				}
				$url = 'http://catalog.'.self::$environment.'.com/catalogs/1/videos/search.json?keywords_and='.$keywords_and.'&categories='.$categories.'&order=DESC&order_by=created_at&providers='.$providersList;
				$connector_id = self::get_connector_id();			
				$category_list = $_POST[ 'category' ];	
				$category_length = count($category_list);
				if(isset($category_list)){						
					foreach ($category_list as $cat) {
						if($category_length == 1){
							$cats = get_cat_name($cat);
						}else{
							$cats[] = get_cat_name($cat);
						}			
					}				
				}else{
					$cats = 'Uncategorized';
				}					
				$schedule = $_POST['schedule'];
				if($schedule == '12 hrs'){
					$update_frequency = 60 * $schedule;
				}else{
					$update_frequency = 60 * 24 * $schedule;
				}					
				if($_POST['click-to-play'] === null){
					$auto_play = '1';
				}else{
					$auto_play = '0';	
				}

				$author_id = (int)$_POST['author'];	

				$post_data = array(
					'feed' => array(
						'name' => $_POST[ 'channel' ],
						'posts_per_update' => $_POST[ 'limit' ],
						'url' => $url,
						'custom_options' => array(
							'category' => $cats,
							'publish' => (bool)( $_POST[ 'publish' ] ),
							'author_id' => $author_id
						),						
						'update_frequency' => $update_frequency,
						'auto_play' => $auto_play
					)
				);
				$response_json = self::apiCall('POST', '/connectors/' . $connector_id . '/feeds/?api_key='.self::$api_key, $post_data);
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
				$validate_json = self::apiCall('GET', '/user/validate?api_key='.$api_key);
				$validate_data = json_decode( $validate_json );
				if (  isset( $validate_data -> error ) ) {
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
				$feeds_json = self::apiCall('GET', '/connectors/'.$connector_id.'/feeds?api_key='.self::$api_key);
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
			$email_host =  substr( $url_array[ 2 ], 4, 13);
			$email_dir = $url_array[ 3 ];
	        $user_email = md5(uniqid(rand(), TRUE)).'@grab.press';
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
		if ( $api_key ) {
			update_option( 'grabpress_key', $api_key );//store api key
		}
		if(! isset( self::$api_key ) ){
			self::abort('Error retrieving API Key');//unless storing failed
		}
		self::$api_key = get_option( 'grabpress_key' );//retreive api key from storage
		/*
		 * Keep user up to date with API info
		 */
		$description = 'Bringing you the best media on the Web.';
		$role = 'editor';// minimum for auto-publish (author)
		if( function_exists( get_user_by ) ){
			get_user_by( 'login', $user_login );
		}else if ( function_exists( get_userbylogin ) ){
			get_userbylogin( $user_login );
		}else{
			self::abort('No get_user function.');
		}
		if ($user_data){// user exists, hash password to keep data up-to-date
			$msg = 'User Exists ('.$user_login.'): '.$user_data->ID;
			$user = array(
				"id" => $user_data -> ID,
				'user_login' => $user_login,
				"user_nicename" => $user_nicename,
				'user_url' => $user_url,
				'user_email' => $user_email,
				'display_name' => $display_name,
				'user_pass' => self::$api_key ,
				'nickname' => $nickname,
				'first_name' => $first_name,
				'last_name' => $last_name,
				'description' => $description,
				'role' => $role
			);
	    }else{// user doesnt exist, store password with new data.
			$user = array(
               	'user_login' => $user_login,
	            'user_nicename' => $user_nicename,
				'user_url' => $user_url,
				'user_email' => $user_email,
				'display_name' => $display_name,
				'user_pass' =>  self::$api_key ,
				'nickname' => $nickname,
				'first_name' => $first_name,
				'last_name' => $last_name,
				'description' => $description,
				'role' => $role
			);
		}
		$user_id = wp_insert_user($user);		
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
	static function delete_connector(){
		$connector_id = self::get_connector_id();
		$response = self::apiCall('PUT', '/connectors/' . $connector_id . '/deactivate?api_key='.GrabPress::$api_key);
		delete_option('grabpress_key');
	}
	static function outline_invalid(){
		if (self::$invalid){
			echo 'border:1px dashed red;';
		};
	}
	static function grabpress_plugin_menu() {
		add_menu_page('GrabPress', 'GrabPress', 'manage_options', 'grabpress', array( 'GrabPress', 'dispatcher' ), plugin_dir_url( __FILE__ ).'g.png', 10 );
		add_submenu_page( 'grabpress', 'AutoPoster', 'AutoPoster', 'publish_posts', 'autoposter', array( 'GrabPress', 'dispatcher' ));
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
    static function render_feed_management(){
  		//if (!current_user_can('manage_options'))  {
		// 	wp_die( __('You do not have sufficient permissions to access this page.') );
		// }
		print self::fetch('includes/gp_feed_template.php');
	}
	static function grabpress_preview_videos() {	
		/*
		if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		*/
		print self::fetch("includes/gp_preview_template.php", $_POST);
	}
	static function fetch($file = null, $data = array()) {
		if(!$file) $file = $this->file;
		extract($data); // Extract the vars to local namespace
		ob_start(); // Start output buffering
		include($file); // Include the file
		$contents = ob_get_contents(); // Get the contents of the buffer
		ob_end_clean(); // End buffering and discard
		return $contents; // Return the contents
	}
	static function dispatcher(){
		$params = $_REQUEST;
		switch ($params['action']){
			case 'update':
					if( GrabPress::validate_key() && $_POST[ 'channel' ] != '' && $_POST[ 'provider' ] != '' ) {
							GrabPress::create_feed();					
					}else {
						GrabPress::$invalid = true;
					}
					GrabPress::render_feed_management();
					break;
			case 'delete':
					$feed_id = $_POST['feed_id'];
					$connector_id = GrabPress::get_connector_id();
					GrabPress::apiCall('DELETE', '/connectors/' . $connector_id . '/feeds/'.$feed_id.'?api_key='.GrabPress::$api_key, $feed_id);
					GrabPress::render_feed_management();
					break;	
			case 'modify':
					$feed_id = $_POST['feed_id'];
					$keywords_and = htmlspecialchars($_POST['keywords_and']);
					$categories = rawurlencode($_POST[ 'channel' ]);
					$providers = $_POST['provider'];
					$providersList = implode(',', $providers);
					$providersListTotal = count($providers); // Total providers chosen by the user
					$providers_total = $_POST['providers_total']; // Total providers from the catalog list
					if($providersListTotal == $providers_total){
						$providersList = '';
					}
					$url = 'http://catalog.'.GrabPress::$environment.'.com/catalogs/1/videos/search.json?keywords_and='.$keywords_and.'&categories='.$categories.'&order=DESC&order_by=created_at&providers='.$providersList;
					$connector_id = GrabPress::get_connector_id();	
					$active	= (bool)$_POST['active'];

					$category_list = $_POST[ 'category' ];

					$category_length = count($category_list);
					if(isset($category_list)){						
						foreach ($category_list as $cat) {
							if($category_length == 1){
								$cats = get_cat_name($cat);
							}else{
								$cats[] = get_cat_name($cat);
							}			
						}				
					}else{
						$cats = 'Uncategorized';
					}
					$schedule = $_POST['schedule'];
					if($schedule == '12 hrs'){
						$update_frequency = 60 * $schedule;
					}else{
						$update_frequency = 60 * 24 * $schedule;
					}	
					if($_POST['click-to-play$'] === null){
						$auto_play = "1";
					}else{
					        $auto_play = "0";	
					}

					$author_id = (int)$_POST['author'];

					$post_data = array(
						'feed' => array(
							'active' => $active,
							'name' => $_POST[ 'channel' ],
							'posts_per_update' => $_POST[ 'limit' ],
							'url' => $url,
							'custom_options' => array(
								'category' => $cats,
								'publish' => (bool)( $_POST[ 'publish' ] ),
								'author_id' => $author_id
							),
							'auto_play' => (bool)( $_POST['auto_play'] ),
							'update_frequency' => $update_frequency,
							'auto_play' => $auto_play							
						)
					);	

					self::apiCall('PUT', '/connectors/' . $connector_id . '/feeds/' . $feed_id . '?api_key=' . GrabPress::$api_key, $post_data);	
					GrabPress::render_feed_management();
					break;
			     case 'preview-feed':
                   GrabPress::grabpress_preview_videos();
                   break;                          

			default:
				GrabPress::render_feed_management();
				break;
		}
	}
	static function print_scripts()
	{
		// Plugin url
		$plugin_url = trailingslashit( get_bloginfo('wpurl') ).PLUGINDIR.'/'. dirname( plugin_basename(__FILE__) );
	
		// jQuery files
	
		wp_enqueue_script('jquery-ui','https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/jquery-ui.min.js'); 
		wp_enqueue_script('jquery-ui-filter', $plugin_url.'/js/ui/multi/jquery.multiselect.filter.min.js');
		wp_enqueue_script('jquery-ui-multiselect', $plugin_url.'/js/ui/multi/jquery.multiselect.min.js');
	
		wp_enqueue_script('jquery-uicore', $plugin_url.'/js/ui/jquery.ui.core.js');
		wp_enqueue_script('jquery-uiwidget', $plugin_url.'/js/ui/jquery.ui.widget.js');
		wp_enqueue_script('jquery-uiposition', $plugin_url.'/js/ui/jquery.ui.position.js');
	
		wp_enqueue_script('jquery-ui-selectmenu', $plugin_url.'/js/ui/jquery.ui.selectmenu.js');
	}
	static function print_styles()
	{
		// Plugin url
		$plugin_url = trailingslashit( get_bloginfo('wpurl') ).PLUGINDIR.'/'. dirname( plugin_basename(__FILE__) );
	
		// CSS files
		
		wp_enqueue_style('jquery-css', $plugin_url.'/css/grabpress.css');
		wp_enqueue_style('jquery-ui-theme', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css');
	
	}
	}//class	
}//ifndefclass

add_action('admin_print_styles',array('GrabPress', 'print_styles') );
add_action('wp_print_scripts', array('GrabPress', 'print_scripts') );
register_activation_hook( __FILE__, array( 'GrabPress', 'setup') );
register_uninstall_hook(__FILE__, array( 'GrabPress', 'delete_connector') );
add_action('admin_menu', array('GrabPress', 'grabpress_plugin_menu' ) );
GrabPress::allow_tags();