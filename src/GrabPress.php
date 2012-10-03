<?php
/*
Plugin Name: GrabPress
Plugin URI: http://www.grab-media.com/publisher/
Description: Configure Grab's AutoPoster software to deliver fresh video direct to your Blog. Create or use an existing Grab Media Publisher account to get paid!
Version: 1.0.0-6734171660
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
if ( ! class_exists( 'GrabPress' ) ) {
	class GrabPress {
		static $version = '1.0.0-6734171660';
		static $api_key;
		static $invalid = false;
		static $environment =  'grabnetworks'; // 'grabqa';
		static $debug = true;
		static $message = false;
		static $error = false;
		static $feed_message = 'items marked with an asterisk * are required.';
		static $connector_id;
		static $connector_user;
		static $providers;

		static function log( $message = false ) {
			if ( GrabPress::$debug ) {
				if ( !$message ) {
					$stack = debug_backtrace();
					$caller = $stack[1];
					@$message = 'GrabPress:<line '.$caller['line'].'>'.$caller['class'].$caller['type'].$caller['function'].'('.implode( ', ', $caller['args'] ).')';
				}
				error_log( $message );
			}
		}

		static function show_message() {
			GrabPress::log();
			$show = false;
			if ( GrabPress::$error ) {
				$show = GrabPress::$error;
				echo '<div id="message" class="error">';
			}
			else if ( GrabPress::$message ) {
					$show = GrabPress::$message;
					echo '<div id="message" class="updated fade">';
				}
			if ( $show ) {
				$icon_src = GrabPress::get_g_icon_src();
				echo '<p><img src="'.$icon_src.'" style="vertical-align:top; position:relative; top:-2px; margin-right:2px;"/>'.$show.'</p></div>';
			}
		}

		static function abort( $message ) {
			GrabPress::log( '<><><> "FATAL" ERROR. YOU SHOULD NEVER SEE THIS MESSAGE <><><>:'.$message );
			
			//TDDO: please root out the reason this is being triggered and fix the code so it doesn't get called, rather than commenting out its effects. silencing errors is not good practice.
			
			// die($message.'<br/>Please <a href = "https://getsatisfaction.com/grabmedia">contact Grab support</a><br/>Debug Info:</br>'.debug_backtrace() );
		}

		static function allow_tags() {
			GrabPress::log();
			global $allowedposttags;
			if ( ! isset( $allowedposttags[ 'div' ] ) ) {
				$allowedposttags[ 'div' ] = array();
			}
			$allowedposttags[ 'div' ][ 'id' ] = array();
			$allowedposttags[ 'div' ][ 'style' ] = array();

			if ( ! isset( $allowedposttags[ 'object' ] ) ) {
				$allowedposttags[ 'object' ] = array();
			}
			$allowedposttags[ 'object' ][ 'id' ] = array();
			$allowedposttags[ 'object' ][ 'width' ] = array();
			$allowedposttags[ 'object' ][ 'height' ] = array();
			$allowedposttags[ 'object' ][ 'type' ] = array();
			$allowedposttags[ 'object' ][ 'align' ] = array();
			$allowedposttags[ 'object' ][ 'data' ] = array();

			if ( ! isset( $allowedposttags[ 'param' ] ) ) {
				$allowedposttags[ 'param' ] = array();
			}
			$allowedposttags[ 'param' ][ 'name' ] = array();
			$allowedposttags[ 'param' ][ 'value' ] = array();

			if ( ! isset( $allowedposttags[ 'script' ] ) ) {
				$allowedposttags[ 'script' ] = array();
			}
			$allowedposttags[ 'script' ][ 'type' ] = array();
			$allowedposttags[ 'script' ][ 'language' ] = array();
			$allowedposttags[ 'script' ][ 'src' ] = array();

			if ( ! isset( $allowedposttags[ 'style' ] ) ) {
				$allowedposttags[ 'style' ] = array();
			}
		}
		static function get_api_location() {
			// GrabPress::log();
			if(GrabPress::$environment == "grabnetworks"){
				$apiLocation = 'autoposter.grabnetworks.com';
			}	
			elseif ($_SERVER['SERVER_ADDR'] == '127.0.0.1'){
				$apiLocation = '10.3.1.37';
			}else {
				$apiLocation = '74.10.95.28';
			}
			return $apiLocation;
		}

		static function get_json( $url, $optional_headers = null ) {
			GrabPress::log();
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

		static function api_call( $method, $resource, $data=array(), $auth=false ){
			GrabPress::log();
			if(isset($auth) && isset($data['user']) && isset($data['pass'])){
				GrabPress::log("HTTP AUTH <> ". $data['user'] . ":" . $data['pass']);
			}
			$json = json_encode( $data );
			$apiLocation = GrabPress::get_api_location();
			$location = 'http://'.$apiLocation.$resource;
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $location );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			//curl_setopt( $ch, CURLOPT_VERBOSE, true );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
				'Content-type: application/json'
			) );
			$params = '';
			if( isset($auth) && isset($data['user']) && isset($data['pass'])){
				curl_setopt($ch, CURLOPT_USERPWD, $data['user'] . ":" . $data['pass']);
			}else{
				$params = strstr($resource, '?') ? '&' : '?';
				foreach ($data as $key => $value) {
					$params .=$key.'='.$value.'&';
				}
				$params = substr($params, 0, -1);
			}
			switch($method){
				case 'GET':					
					curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 60 );
					$location.=$params;
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
			$status = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
			curl_close( $ch );
			GrabPress::log( 'status = ' . $status . ', response =' . $response );
			return $response;
		}

		static function get_user() {
			// if(GrabPress::$connector_user){
				// return GrabPress::$connector_user;
			// }
			GrabPress::log();
			$id = GrabPress::get_connector_id();
			$user_json = GrabPress::api_call( 'GET',  '/connectors/'.$id.'/user?api_key='.GrabPress::$api_key );
			$user_data = json_decode( $user_json );
			// GrabPress::$connector_user = $user_data;
			return $user_data;
		}

		static function get_connector_id() {
			GrabPress::log();
			if(GrabPress::$connector_id){
				return GrabPress::$connector_id;
			}
			if ( GrabPress::validate_key() ) {
				$rpc_url = get_bloginfo( 'url' ).'/xmlrpc.php';
				$connectors_json =  GrabPress::api_call( 'GET',  '/connectors?api_key='.GrabPress::$api_key );
				$connectors_data = json_decode( $connectors_json );
				for ( $n = 0; $n < count( $connectors_data ); $n++ ) {
					$connector = $connectors_data[$n]->connector;
					if ( $connector -> destination_address == $rpc_url ) {
						$connector_id = $connector -> id;
					}
				}
				if ( ! isset( $connector_id ) ) {//create connector
					$connector_types_json = GrabPress::api_call( 'GET',  '/connector_types?api_key='.GrabPress::$api_key );
					$connector_types = json_decode( $connector_types_json );
					for ( $n = 0; $n < count( $connector_types ); $n++ ) {
						$connector_type = $connector_types[$n] -> connector_type;
						if ( $connector_type -> name =='wordpress' ) {
							$connector_type_id = $connector_type -> id;
						}
					}
					if ( ! $connector_type_id ) {
						GrabPress::abort( 'Error retrieving Autoposter id for connector name "wordpress"' );
					}
					global $blog_id;
					$connector_post = array(
						'connector' => array(
							'connector_type_id' => $connector_type_id,
							'destination_name' => get_bloginfo( 'name' ),
							'destination_address' => $rpc_url,
							'username' =>'grabpress',
							'password' => GrabPress::$api_key,
							'custom_options' => array(
								'blog_id' => $blog_id
							)
						)
					);

					$connector_json = GrabPress::api_call( 'POST',  '/connectors?api_key='.GrabPress::$api_key, $connector_post );
					$connector_data = json_decode( $connector_json );
					$connector_id = $connector_data -> connector -> id;
				}
				GrabPress::$connector_id - $connector_id;
				return $connector_id;
			}else {
				GrabPress::$feed_message = 'Your API key is no longer valid. Please <a href = "https://getsatisfaction.com/grabmedia" target="_blank">contact Grab support.</a>';
				return false;
			}
		}
		static function get_g_icon_src(){
				return plugin_dir_url( __FILE__ ).'images/icons/g.png';
		}
		static function get_green_icon_src( $name ){
				return plugin_dir_url( __FILE__ ).'images/icons/green/'.$name.'.png';
		}
		static function create_feed() {
			GrabPress::log();
			if ( GrabPress::validate_key() ) {
				$categories = rawurlencode( $_REQUEST[ 'channel' ] );
				$keywords_and = rawurlencode( $_REQUEST[ 'keywords_and' ] );

				$json = GrabPress::get_json( 'http://catalog.'.GrabPress::$environment.'.com/catalogs/1/categories' );
				$list = json_decode( $json );
				foreach ( $list as $record ) {
					$category = $record -> category;
					$name = $category -> name;
					$id = $category -> id;
					if ( $name == $_REQUEST['channel'] ) {
						$cat_id = $id;
					}
				}
				$providers = $_REQUEST['provider'];
				$providersList = implode( ',', $providers );
				$providersListTotal = count( $providers ); // Total providers chosen by the user
				$providers_total = $_REQUEST['providers_total']; // Total providers from the catalog list
				if ( $providersListTotal == $providers_total ) {
					$providersList = '';
				}
				$url = 'http://catalog.'.GrabPress::$environment.'.com/catalogs/1/videos/search.json?keywords_and='.$keywords_and.'&categories='.$categories.'&order=DESC&order_by=created_at&providers='.$providersList;
				$connector_id = GrabPress::get_connector_id();
				$category_list = $_REQUEST[ 'category' ];
				$category_length = count( $category_list );
				$cats = array();
				if ( is_array( $category_list ) ) {
					foreach ( $category_list as $cat ) {
						if ( $category_length == 1 ) {
							$cats[] = get_cat_name( $cat );
						}else {
							$cats[] = get_cat_name( $cat );
						}
					}
				}else {
					$cats[] = 'Uncategorized';
				}
				$schedule = $_REQUEST['schedule'];

				if ( $_REQUEST['click_to_play'] == "1" ) {
					$auto_play = "1";
				}else {
					$auto_play = "0";
				}

				$author_id = (int)$_REQUEST['author'];

				$post_data = array(
					'feed' => array(
						'name' => $_REQUEST[ 'channel' ],
						'posts_per_update' => $_REQUEST[ 'limit' ],
						'url' => $url,
						'custom_options' => array(
							'category' => $cats,
							'publish' => (bool)( $_REQUEST[ 'publish' ] ),
							'author_id' => $author_id
						),
						'update_frequency' => $_REQUEST[ 'schedule' ] ,
						'auto_play' => $auto_play

					)
				);
				$response_json = GrabPress::api_call( 'POST', '/connectors/' . $connector_id . '/feeds/?api_key='.GrabPress::$api_key, $post_data );
				$response_data = json_decode( $response_json );
				if ( $response_data -> feed -> active == true ) {
					GrabPress::$feed_message = 'Grab yourself a coffee. Your videos are on the way!';
				}else {
					GrabPress::$feed_message = 'Something went wrong grabbing your feed. Please <a href = "https://getsatisfaction.com/grabmedia" target="_blank">contact Grab support</a>\n'.$response_data;
				}
			}else {
				GrabPress::$feed_message = 'Your API key is no longer valid. Please <a href = "https://getsatisfaction.com/grabmedia" target="_blank">contact Grab support.</a>';
			}
		}

		static function grabpress_edit_feed($feed_id){
			GrabPress::log();
			if ( GrabPress::validate_key() ) {

				$feed = GrabPress::get_feed($feed_id);
				
				$url = array();
				parse_str( parse_url( $feed->feed->url, PHP_URL_QUERY ), $url );
				$providers = explode( ",", $url["providers"] ); // providers chosen by the user
				$list_provider = GrabPress::get_providers();
				$providers_total = count( $list_provider );
				$blogusers = get_users();

				if(isset($_REQUEST) && isset($_REQUEST["channel"]) != "" && isset($_REQUEST["provider"]) != ""){
					print GrabPress::fetch( "includes/gp-feed-template.php", 
					array("form" => array( "referer" => "edit",
										   "action" => "modify",
										   "feed_id" => $_REQUEST["feed_id"],
										   "channel" => $_REQUEST["channel"],
										   "keywords_and" => $_REQUEST["keywords_and"],
										   "limit" => $_REQUEST["limit"],
										   "schedule" => $_REQUEST["schedule"],
										   "active" => $_REQUEST["active"],
										   "publish" => $_REQUEST["publish"],
										   "click_to_play" => $_REQUEST["click_to_play"],
										   "author" => $_REQUEST["author"],
										   "provider" => $_REQUEST["provider"],
										   "category" => $_REQUEST["category"]								   
											),
							"list_provider" => $list_provider,
							"providers_total" => $providers_total,
							"blogusers" => $blogusers
					 ) );
				}else{

					$cats = array();
					if ( is_array( $feed->feed->custom_options->category ) ) {
						foreach ( $feed->feed->custom_options->category as $cat ) {						
							$cats[] = get_cat_id( $cat );						
						}
					}

					print GrabPress::fetch( "includes/gp-feed-template.php", 
						array("form" => array( "referer" => "edit",
											   "action" => "modify",
											   "feed_id" => $feed_id,
											   "channel" => $feed->feed->name,
											   "keywords_and" => $url['keywords_and'],
											   "limit" => $feed->feed->posts_per_update,
											   "schedule" => $feed->feed->update_frequency,
											   "active" => $feed->feed->active,
											   "publish" => $feed->feed->custom_options->publish,
											   "click_to_play" => $feed->feed->auto_play,
											   "author" => $feed->feed->custom_options->author_id,
											   "provider" => $providers,
											   "category" => $cats
												),
								"list_provider" => $list_provider,
								"providers_total" => $providers_total,
								"blogusers" => $blogusers
						 ) );

				}

			}
		}

		static function validate_key() {
			GrabPress::log();
			$api_key = get_option( 'grabpress_key' );
			if ( $api_key != '' ) {
				$validate_json = GrabPress::api_call( 'GET', '/user/validate?api_key='.$api_key );
				$validate_data = json_decode( $validate_json );
				if (  isset( $validate_data -> error ) ) {
					return GrabPress::create_API_connection();
				}else {
					GrabPress::$api_key = $api_key;
					return true;
				}
			}else {
				return GrabPress::create_API_connection();
			}
			return false;
		}

		static function get_feeds() {
			GrabPress::log();
			if ( GrabPress::validate_key() ) {
				$connector_id = GrabPress::get_connector_id();
				$feeds_json = GrabPress::api_call( 'GET', '/connectors/'.$connector_id.'/feeds?api_key='.GrabPress::$api_key );
				$feeds_data = json_decode( $feeds_json );
				return $feeds_data;
			}else {
				GrabPress::abort( 'no valid key' );
			}
		}

		static function get_feed($feed_id) {
			GrabPress::log();
			if ( GrabPress::validate_key() ) {
				$connector_id = GrabPress::get_connector_id();					
				$feed_json = GrabPress::api_call( 'GET', '/connectors/'.$connector_id.'/feeds/'.$feed_id.'?api_key='.GrabPress::$api_key );
				$feed_data = json_decode( $feed_json );
				return $feed_data;
			}else {
				GrabPress::abort( 'no valid key' );
			}
		}

		static function create_API_connection() {
			GrabPress::log();
			$user_url = get_site_url();
			$user_nicename = 'grabpress';
			$user_login = $user_nicename;
			$url_array = explode(  '/', $user_url );
			$email_host =  substr( $url_array[ 2 ], 4, 13 );
			$email_dir = $url_array[ 3 ];
			$user_email = md5( uniqid( rand(), TRUE ) ).'@grab.press';
			$display_name = 'GrabPress';
			$nickname  = 'GrabPress';
			$first_name  = 'Grab';
			$last_name = 'Press';
			$post_data = array(
				'user' => array(
					'first_name' => $first_name,
					'last_name' => $last_name,
					'email' => $user_email
				)
			);
			$user_json = GrabPress::api_call( "POST", '/user', $post_data );
			$user_data = json_decode( $user_json );

			$api_key = $user_data -> user -> access_key;
			if ( $api_key ) {
				update_option( 'grabpress_key', $api_key );//store api key
			}
			if ( ! isset( GrabPress::$api_key ) ) {
				GrabPress::abort( 'Error retrieving API Key' );//unless storing failed
			}
			GrabPress::$api_key = get_option( 'grabpress_key' );//retreive api key from storage
			/*
		 * Keep user up to date with API info
		 */
			$description = 'Bringing you the best media on the Web.';
			$role = 'editor';// minimum for auto-publish (author)
			if ( function_exists( get_user_by ) ) {
				get_user_by( 'login', $user_login );
			}else if ( function_exists( get_userbylogin ) ) {
					get_userbylogin( $user_login );
				}else {
				GrabPress::abort( 'No get_user function.' );
			}
			if ( $user_data ) {// user exists, hash password to keep data up-to-date
				$msg = 'User Exists ('.$user_login.'): '.$user_data->ID;
				$user = array(
					"id" => $user_data -> ID,
					'user_login' => $user_login,
					"user_nicename" => $user_nicename,
					'user_url' => $user_url,
					'user_email' => $user_email,
					'display_name' => $display_name,
					'user_pass' => GrabPress::$api_key,
					'nickname' => $nickname,
					'first_name' => $first_name,
					'last_name' => $last_name,
					'description' => $description,
					'role' => $role
				);
			}else {// user doesnt exist, store password with new data.
				$user = array(
					'user_login' => $user_login,
					'user_nicename' => $user_nicename,
					'user_url' => $user_url,
					'user_email' => $user_email,
					'display_name' => $display_name,
					'user_pass' =>  GrabPress::$api_key ,
					'nickname' => $nickname,
					'first_name' => $first_name,
					'last_name' => $last_name,
					'description' => $description,
					'role' => $role
				);
			}
			$user_id = wp_insert_user( $user );
			if ( ! isset( $user_id ) ) {
				GrabPress::abort( 'Error creating user.' );
			}
			return true;
		}

		static function enable_xmlrpc() {
			GrabPress::log();
			update_option( 'enable_xmlrpc', 1 );
			if ( ! get_option( 'enable_xmlrpc' ) ) {
				GrabPress::abort( 'Error enabling XML-RPC.' );
			}
		}

		static function register_settings() {
			GrabPress::log();
			register_setting( 'grab_press', 'access_key' );
		}

		static function setup() {
			GrabPress::log();
			GrabPress::validate_key();
			GrabPress::enable_xmlrpc();
		}

		static function delete_connector() {
			GrabPress::log();
			$connector_id = GrabPress::get_connector_id();
			$response = GrabPress::api_call( 'PUT', '/connectors/' . $connector_id . '/deactivate?api_key='.GrabPress::$api_key );
			delete_option( 'grabpress_key' );
			$grab_user = get_user_by('name', 'grabpress');
			$current_user = wp_get_current_user();
			wp_delete_user( $grab_user->id, $current_user->id );
			GrabPress::$message = 'GrabPress has been deactivated. Any posts that used to be credited to the "grabpress" user are now assigned to you. XML-RPC is still enabled, unless you are using it for anything else, we recommend you turn it off.';
		}

		static function outline_invalid() {
			GrabPress::log();
			if ( GrabPress::$invalid ) {
				echo 'border:1px dashed red;';
			};
		}

		static function grabpress_plugin_menu() {
			GrabPress::log();
			add_menu_page( 'GrabPress', 'GrabPress', 'manage_options', 'grabpress', array( 'GrabPress', 'dispatcher' ), GrabPress::get_g_icon_src(), 10 );
			add_submenu_page( 'grabpress', 'AutoPoster', 'AutoPoster', 'publish_posts', 'autoposter', array( 'GrabPress', 'dispatcher' ) );
			add_submenu_page( 'grabpress', 'Account', 'Account', 'publish_posts', 'account', array( 'GrabPress', 'dispatcher' ) );
			global $submenu;
			unset( $submenu['grabpress'][0] );
			$feeds = GrabPress::get_feeds();
			$num_feeds = count( $feeds );
			$admin = get_admin_url();
			$current_page = 'http://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
			if ( $num_feeds == 0 ) {
				$admin_page = $admin.'admin.php?page=autoposter';
				if ( $current_page != $admin_page ) {
					$here = '<a href="'.$admin_page.'">here</a>';
				}else {
					$here = 'here';
				}
	
				GrabPress::$message = 'Thank you for activating GrabPress. Try creating your first Autoposter feed '.$here.'.';
			}else{
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
					$user = GrabPress::get_user();	
					$linked = isset( $user->email);
					$create =  $_REQUEST[ 'page'] == 'account' && isset($_REQUEST[ 'action']) &&  $_REQUEST[ 'action'] == 'create' ? 'Create' : '<a href="admin.php?page=account&action=create">Create</a>';
					$link =  isset($_REQUEST[ 'page']) && $_REQUEST[ 'page'] == 'account' && isset($_REQUEST[ 'action']) &&  $_REQUEST[ 'action'] == 'default' ? 'link an existing' : '<a href="admin.php?page=account&action=default">link an existing</a>';
					$linked_message = $linked ? '' : 'Want to earn money? ' . $create .' or '. $link . ' Grab Publisher account.';
					$environment = ( GrabPress::$environment == "grabqa" ) ? '  ENVIRONMENT = ' . GrabPress::$environment : '';
					GrabPress::$message = 'Grab Autoposter is ON with <span id="num-active-feeds">'.$active_feeds.'</span> <span id="noun-active-feeds"> '.$noun.'</span> active. '.$linked_message .$environment;
				}
			}
		}
		static function render_account_management() {
			GrabPress::log();
			//if (!current_user_can('manage_options'))  {
			//  wp_die( __('You do not have sufficient permissions to access this page.') );
			// }
			print GrabPress::fetch( 'includes/gp-account-template.php' );
		}

		static function _filter_out_out_providers( $x ) {
			return !$x->provider->opt_out;
		}

		static function get_providers() {
			if( isset(GrabPress::$providers) ){
				return GrabPress::$providers;
			}
			$json_provider = GrabPress::get_json( 'http://catalog.'.GrabPress::$environment.'.com/catalogs/1/providers?limit=-1' );
			$list = json_decode( $json_provider );
			$list = array_filter( $list, array( "GrabPress", "_filter_out_out_providers" ) );
			GrabPress::$providers = $list;
			return $list;
		}

		static function render_feed_management() {
			GrabPress::log();
			//if (!current_user_can('manage_options'))  {
			//  wp_die( __('You do not have sufficient permissions to access this page.') );
			// }
			/*
			if ( (isset($_REQUEST["referer"])) && ( $_REQUEST["referer"] == "edit" )) {
				$_REQUEST = GrabPress::form_default_values();				
			}
			*/

			$list_provider = GrabPress::get_providers();
			$providers_total = count( $list_provider );
			$blogusers = get_users();
			print GrabPress::fetch( 'includes/gp-feed-template.php',
				array( "form" => $_REQUEST,
					"list_provider" => $list_provider,
					"providers_total" => $providers_total,
					"blogusers" => $blogusers ) );
		}

		static function render_feed_creation_success(){
			print GrabPress::fetch( "includes/gp-feed-created-template.php" );
		}		

		static function grabpress_preview_videos() {
			GrabPress::log();
			/*
			if (!current_user_can('manage_options'))  {
				wp_die( __('You do not have sufficient permissions to access this page.') );
			}
			*/
			if(isset($_REQUEST["referer"]) && ( $_REQUEST["referer"] == "create" || $_REQUEST["referer"] == "edit" )){
				print GrabPress::fetch( "includes/gp-preview-template.php", $_REQUEST );	
			}else{
				$feed_id = $_GET['feed_id'];
				$providers_total = count(GrabPress::get_providers());
				$feed = GrabPress::get_feed($feed_id);
				
				$url = array();
				parse_str( parse_url( $feed->feed->url, PHP_URL_QUERY ), $url );
				$providers = explode( ",", $url["providers"] ); // providers chosen by the user

				if ( is_array( $feed->feed->custom_options->category ) && (!empty($feed->feed->custom_options->category)) ) {
					foreach ( $feed->feed->custom_options->category as $cat ) {
						$cats[] = get_cat_id( $cat );
					}
				}else{
					$cats[] = "";
				}

				print GrabPress::fetch( "includes/gp-preview-template.php", 
					array( "referer" => "edit",
						   "action" => "edit-feed",
						   "feed_id" => $feed_id,
						   "channel" => $feed->feed->name,
						   "keywords_and" => $url['keywords_and'],
						   "limit" => $feed->feed->posts_per_update,
						   "schedule" => $feed->feed->update_frequency,
						   "publish" => $feed->feed->custom_options->publish,
						   "click_to_play" => $feed->feed->auto_play,
						   "author" => $feed->feed->custom_options->author_id,
						   "provider" => $providers,
						   "providers_total" => $providers_total,
						   "category" => $cats
					) );
			}
			
		}

		static function fetch( $file = null, $data = array() ) {
			GrabPress::log();
			if ( !$file ) $file = $this->file;
			extract( $data ); // Extract the vars to local namespace
			ob_start(); // Start output buffering
			include $file; // Include the file
			$contents = ob_get_contents(); // Get the contents of the buffer
			ob_end_clean(); // End buffering and discard
			return $contents; // Return the contents
			GrabPress::show_message();
		}

		static function form_default_values( $params = array() ) {
			GrabPress::log();
			$defaults = array( "publish" => true,
				"click_to_play" => true,
				"category" => array(),
				"provider" => array(),
				"keywords_and" => "" );
			foreach ( $defaults as $key => $value ) {
				if ( !array_key_exists( $key, $params ) ) {
					$params[$key] = $value;
				}
			}
			return $params;
		}
		static function dispatcher() {			
			GrabPress::log();
			$_REQUEST["action"] = array_key_exists("action", $_REQUEST)?$_REQUEST["action"]:"default";
			$_REQUEST = GrabPress::form_default_values( $_REQUEST );
			$action = $_REQUEST[ 'action' ];		
			$params = $_REQUEST;			
			switch ( $_GET[ 'page' ] ) {
			case 'autoposter':
				switch ( $action ) {
				case 'update':
					if ( GrabPress::validate_key() && $_REQUEST[ 'channel' ] != '' && $_REQUEST[ 'provider' ] != '' ) {
						GrabPress::create_feed();
						GrabPress::render_feed_creation_success();
					}else {
						GrabPress::$invalid = true;
						GrabPress::render_feed_management();
					}
					break;
				case 'delete':
					$feed_id = $_REQUEST['feed_id'];
					$connector_id = GrabPress::get_connector_id();
					GrabPress::api_call( 'DELETE', '/connectors/' . $connector_id . '/feeds/'.$feed_id.'?api_key='.GrabPress::$api_key, $feed_id );
					GrabPress::render_feed_management();					
					break;
				case 'modify':

					$feed_id = $_REQUEST['feed_id'];
					$keywords_and = htmlspecialchars( $_REQUEST['keywords_and'] );
					$categories = rawurlencode( $_REQUEST[ 'channel' ] );
					$providers = $_REQUEST['provider'];

					$providersList = implode( ',', $providers );
					$providersListTotal = count( $providers ); // Total providers chosen by the user
					$providers_total = $_REQUEST['providers_total']; // Total providers from the catalog list
					if ( $providersListTotal == $providers_total ) {
						$providersList = '';
					}
					$url = 'http://catalog.'.GrabPress::$environment.'.com/catalogs/1/videos/search.json?keywords_and='.$keywords_and.'&categories='.$categories.'&order=DESC&order_by=created_at&providers='.$providersList;
					$connector_id = GrabPress::get_connector_id();
					$active = (bool)$_REQUEST['active'];

					$category_list = $_REQUEST[ 'category' ];

					$category_length = count( $category_list );

					$cats = array();
					if ( is_array( $category_list ) ) {
						foreach ( $category_list as $cat ) {
							if ( $category_length == 1 ) {
								$cats[] = get_cat_name( $cat );
							}else {
								$cats[] = get_cat_name( $cat );
							}
						}
					}else {
						$cats[] = 'Uncategorized';
					}

					if ( $_REQUEST['click_to_play'] == "1" ) {//defaults to false
						$auto_play = '1';
					}else {
						$auto_play = '0';
					}

					$author_id = (int)$_REQUEST['author'];

					$post_data = array(
						'feed' => array(
							'active' => $active,
							'name' => $_REQUEST[ 'channel' ],
							'posts_per_update' => $_REQUEST[ 'limit' ],
							'url' => $url,
							'custom_options' => array(
								'category' => $cats,
								'publish' => (bool)( $_REQUEST[ 'publish' ] ),
								'author_id' => $author_id
							),
							'update_frequency' => $_REQUEST['schedule'],
							'auto_play' => $auto_play
						)
					);

					GrabPress::api_call( 'PUT', '/connectors/' . $connector_id . '/feeds/' . $feed_id . '?api_key=' . GrabPress::$api_key, $post_data );
					GrabPress::render_feed_creation_success();
					break;
				case 'preview-feed':
					GrabPress::grabpress_preview_videos();
					break;
				case 'edit-feed':			
					$feed_id = $_REQUEST['feed_id'];
					GrabPress::grabpress_edit_feed($feed_id);
					break;	
				case 'default':
				default:
					GrabPress::render_feed_management();
					break;
				}
				break;
			case 'account':
				
			if(isset($_REQUEST[ 'action' ])){
				switch ( $_REQUEST[ 'action' ] ) {
					case 'default':
						break;
					case 'link-user' :
						if( isset( $_REQUEST[ 'email' ] ) && isset( $_REQUEST[ 'password' ]) ){
							$credentials = array( 'user' => $_REQUEST[ 'email' ], 'pass' => $_REQUEST[ 'password' ] );
							$user_json = GrabPress::api_call( 'GET', '/user/validate', $credentials, true );
							$user_data = json_decode( $user_json );
							if( isset( $user_data -> user ) ){
								$user = $user_data -> user;
								$connector_data = array(
								 	'user_id' 	=> $user -> id,
									'email' 	=> $user -> email
								);
								GrabPress::log( 'PUTting to connector ' . GrabPress::get_connector_id() . ':' . $user -> id );
								$result_json = GrabPress::api_call( 'PUT', '/connectors/' . GrabPress::get_connector_id() . '?api_key=' . GrabPress::$api_key, $connector_data );
								$_REQUEST[ 'action' ] = 'default';
							}else{
								GrabPress::$error = 'No user with the supplied email and password combination exists in our system. Please try again.';
								$_REQUEST[ 'action' ] = 'default';
							}
						}else {
							GrabPress::abort( 'Attempt to link user with incomplete form data.' );
						}
						break;
					case 'unlink-user' :
						if( isset( $_REQUEST[ 'confirm' ]) ){
							$user = get_user_by( 'slug', 'grabpress');
							$connector_data = array(
							 	'user_id' 	=> null,
								'email' 	=> $user -> email
							);
							$result_json = GrabPress::api_call( 'PUT', '/connectors/' . GrabPress::get_connector_id() . '?api_key=' . GrabPress::$api_key, $connector_data );
							$_REQUEST[ 'action' ] = 'default';
						}
						break;
						case 'create-user':							
							$payment = isset( $_REQUEST['paypal_id']) ? 'paypal' : '';
							$user_data = array(
							   	'user'=>array(
							   		'email'=>$_REQUEST['email'],
							         'password'=>$_REQUEST['password'],
							         'first_name'=>$_REQUEST['first_name'],
							         'last_name'=>$_REQUEST['last_name'],
							         'payment_detail' => array(
							         	'payee' => $_REQUEST['first_name'] . ' ' . $_REQUEST['last_name'],
								        'address1'=>$_REQUEST['address1'],
								        'address2'=>$_REQUEST['address2'],
								        'city'=>$_REQUEST['city'],
								        'state'=>$_REQUEST['state'],
								        'zip'=>$_REQUEST['zip'],
								        'country_id' => 214,
								        'preferred_payment_type'=> 'Paypal',
								        'phone_number'=>$_REQUEST['phone_number'],
								        'paypal_id'=>$_REQUEST['paypal_id']
							         )
								)
							);
							$user_json = json_encode($user_data);
							$result_json = GrabPress::api_call('POST', '/register?api_key='.GrabPress::$api_key, $user_data);
							$result_data = json_decode( $result_json);
							if(!isset( $result_data->error ) ){
								$_REQUEST[ 'action' ] = 'link-user';
								return GrabPress::dispatcher();
							}else{
								GrabPress::$error = 'Error creating user.';
								$_REQUEST['action'] = 'create';
							}
							break;
						case 'link':
						case 'unlink':
						case 'create':
						case 'switch':
							break;
						default:
							$_REQUEST[ 'action' ] = 'default';
					}
					GrabPress::render_account_management();
					break;
				}
			}
		}

		static function print_scripts() {
			GrabPress::log();
			// Plugin url
			$plugin_url = trailingslashit( get_bloginfo( 'wpurl' ) ).PLUGINDIR.'/'. dirname( plugin_basename( __FILE__ ) );

			// jQuery files

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/jquery-ui.min.js' );
			wp_enqueue_script( 'grab-player', 'http://player.grabnetworks.com/js/Player.js' );
			wp_enqueue_script( 'jquery-ui-filter', $plugin_url.'/js/ui/multi/jquery.multiselect.filter.min.js' );
			wp_enqueue_script( 'jquery-ui-multiselect', $plugin_url.'/js/ui/multi/jquery.multiselect.min.js' );

			wp_enqueue_script( 'jquery-uicore', $plugin_url.'/js/ui/jquery.ui.core.js' );
			wp_enqueue_script( 'jquery-uiwidget', $plugin_url.'/js/ui/jquery.ui.widget.js' );
			wp_enqueue_script( 'jquery-uiposition', $plugin_url.'/js/ui/jquery.ui.position.js' );

			wp_enqueue_script( 'jquery-ui-selectmenu', $plugin_url.'/js/ui/jquery.ui.selectmenu.js' );
			wp_enqueue_script( 'jquery-simpletip', $plugin_url.'/js/jquery.simpletip.min.js' );
			wp_enqueue_script( 'jquery-placeholder', $plugin_url.'/js/ui/jquery.placeholder.min.js' );
			
		}

		static function print_styles() {
			GrabPress::log();
			// Plugin url
			$plugin_url = trailingslashit( get_bloginfo( 'wpurl' ) ).PLUGINDIR.'/'. dirname( plugin_basename( __FILE__ ) );

			// CSS files

			wp_enqueue_style( 'jquery-css', $plugin_url.'/css/grabpress.css' );
			wp_enqueue_style( 'jquery-ui-theme', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css' );

		}

		static function my_action_callback() {
			global $wpdb; // this is how you get access to the database

			$feed_id = intval( $_REQUEST['feed_id'] );
			$active = intval( $_REQUEST['active'] );	

			$post_data = array(
				'feed' => array(
					'active' => $active
				)
			);

			GrabPress::api_call( 'PUT', '/connectors/' . GrabPress::get_connector_id() . '/feeds/' . $feed_id . '?api_key=' . GrabPress::$api_key, $post_data );

			$feeds = GrabPress::get_feeds();
			$num_feeds = count( $feeds );

			$active_feeds = 0;
	
			for ( $i=0; $i < $num_feeds; $i++ ) {
				$feed = $feeds[ $i ];
				if ( $feed -> feed -> active > 0 ) {
					$active_feeds++;
				}
			}

			echo $active_feeds;

			die(); // this is required to return a proper result
		}

		static function delete_action_callback() {
			global $wpdb; // this is how you get access to the database

			$feed_id = intval( $_REQUEST['feed_id'] );	

			$connector_id = GrabPress::get_connector_id();
			GrabPress::api_call( 'DELETE', '/connectors/' . $connector_id . '/feeds/'.$feed_id.'?api_key='.GrabPress::$api_key, $feed_id );

			die(); // this is required to return a proper result
		}

	}//class
}//ifndefclass
GrabPress::log( '-------------------------------------------------------' );
add_action( 'admin_print_styles', array( 'GrabPress', 'print_styles' ) );
add_action( 'admin_print_scripts', array( 'GrabPress', 'print_scripts' ) );
register_activation_hook( __FILE__, array( 'GrabPress', 'setup' ) );
register_uninstall_hook( __FILE__, array( 'GrabPress', 'delete_connector' ) );
add_action( 'admin_menu', array( 'GrabPress', 'grabpress_plugin_menu' ) );
add_action( 'admin_footer', array( 'GrabPress', 'show_message' ) );
add_action('wp_ajax_my_action', array( 'GrabPress', 'my_action_callback' ));
add_action('wp_ajax_delete_action', array( 'GrabPress', 'delete_action_callback' ));

GrabPress::allow_tags();
