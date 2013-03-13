<?php
require_once dirname(__FILE__)."/GrabPressViews.php";
require_once dirname(__FILE__)."/GrabPressAPI.php";
/*
Plugin Name: GrabPress
Plugin URI: http://www.grab-media.com/publisher/grabpress
Description: Configure Grab's AutoPoster software to deliver fresh video direct to your Blog. Link a Grab Media Publisher account to get paid!
Version: 2.2.0
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
		static $version = '2.2.0';
		static $api_key;
		static $invalid = false;
		static $environment =  'grabnetworks';
		static $debug = true;
		static $message = false;
		static $error = false;
		static $grabpress_user = "grabpress";
		static $feed_message = 'items marked with an asterisk * are required.';
		static $connector;
		static $connector_user;
		static $providers;
		static $channels;
		static $player_settings;

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
		static function get_user_by($field){
			if ( function_exists( "get_user_by" ) ) {
				return get_user_by( $field, GrabPress::$grabpress_user );
			}else if ( function_exists( "get_userbylogin" ) ) {
				return get_userbylogin( GrabPress::$grabpress_user );
			}else {
				GrabPress::abort( 'No get_user function.' );
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
		
		static function get_g_icon_src(){
				return plugin_dir_url( __FILE__ ).'images/icons/g.png';
		}
		static function get_green_icon_src( $name ){
				return plugin_dir_url( __FILE__ ).'images/icons/green/'.$name.'.png';
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
			GrabPressAPI::validate_key();
			GrabPress::enable_xmlrpc();
		}
		static function delete_connector() {
			GrabPress::log();
			$connector_id = GrabPressAPI::get_connector_id();


			$response = GrabPressAPI::call( 'PUT', '/connectors/' . $connector_id . '/deactivate?api_key='.GrabPress::$api_key );
			$response_delete = GrabPressAPI::call( 'DELETE', '/connectors/' . $connector_id . '?api_key=' . GrabPress::$api_key );

			delete_option( 'grabpress_key' );
			$grab_user = GrabPress::get_user_by("login");
			$current_user = wp_get_current_user();
			wp_delete_user( $grab_user->ID, $current_user->ID );
			GrabPress::$message = 'GrabPress has been deactivated. Any posts that used to be credited to the "grabpress" user are now assigned to you. XML-RPC is still enabled, unless you are using it for anything else, we recommend you turn it off.';
		}

		static function outline_invalid() {
			GrabPress::log();
			if ( GrabPress::$invalid ) {
				echo 'border:1px dashed red;';
			};
		}

		static function grabpress_plugin_messages() {
			$feeds = GrabPressAPI::get_feeds();
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
					if ( $active_feeds > 1 || $num_feeds == 0 ) {
						$noun .= 's';
					}
					$user = GrabPressAPI::get_user();
					$linked = isset($user->email);
					$create = isset($_REQUEST[ 'page']) && $_REQUEST[ 'page'] == 'account' && isset($_REQUEST[ 'action']) &&  $_REQUEST[ 'action'] == 'create' ? 'Create' : '<a href="admin.php?page=account&action=create">Create</a>';
					$link =  isset($_REQUEST[ 'page']) && $_REQUEST[ 'page'] == 'account' && isset($_REQUEST[ 'action']) &&  $_REQUEST[ 'action'] == 'default' ? 'link an existing' : '<a href="admin.php?page=account&action=default">link an existing</a>';
					$linked_message = $linked ? '' : 'Want to earn money? ' . $create .' or '. $link . ' Grab Publisher account.';
					$environment = ( GrabPress::$environment == "grabqa" ) ? '  ENVIRONMENT = ' . GrabPress::$environment : '';
					if( $active_feeds == 0 ){
						$active_feeds = $num_feeds;
						$autoposter_status = 'OFF';
						$feeds_status = 'inactive';
					}else{
						$autoposter_status = 'ON';
						$feeds_status = 'active';
					}
					GrabPress::$message = 'Grab Autoposter is <span id="autoposter-status">'.$autoposter_status.'</span> with <span id="num-active-feeds">'.$active_feeds.'</span> <span id="feeds-status">'.$feeds_status.'</span> <span id="noun-active-feeds"> '.$noun.'</span> . '.$linked_message .$environment;						
														
				}
			}
		}

		static function grabpress_plugin_menu() {
			GrabPress::log();
			add_menu_page( 'GrabPress', 'GrabPress', 'manage_options', 'grabpress', array( 'GrabPress', 'dispatcher' ), GrabPress::get_g_icon_src(), 11 );
			add_submenu_page( 'grabpress', 'Dashboard', 'Dashboard', 'publish_posts', 'gp-dashboard', array( 'GrabPress', 'dispatcher' ) );
			add_submenu_page( 'grabpress', 'Account', 'Account', 'publish_posts', 'account', array( 'GrabPress', 'dispatcher' ) );
			add_submenu_page( 'grabpress', 'AutoPoster', 'AutoPoster', 'publish_posts', 'autoposter', array( 'GrabPress', 'dispatcher' ) );			
			add_submenu_page( 'grabpress', 'Catalog', 'Catalog', 'publish_posts', 'catalog', array( 'GrabPress', 'dispatcher' ) );
			add_submenu_page( 'grabpress', 'Template', 'Template', 'publish_posts', 'gp-template', array( 'GrabPress', 'dispatcher' ) );
			global $submenu;
			unset( $submenu['grabpress'][0] );
		}

		
		static function _escape_params_template(&$data){
			if(is_array($data)||is_object($data)){
				foreach ($data as $key => &$value) {
					GrabPress::_escape_params_template($value);
				}
			}else{
				$data = htmlentities(stripslashes($data), ENT_QUOTES);
			}
		}

		static function fetch( $file = null, $data = array() ) {
			
			GrabPress::_escape_params_template($data);
						
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
				"keywords_and" => "",
				"keywords_not" => "",
				"keywords_or" => "",
				"keywords_phrase" => "");
			foreach ( $defaults as $key => $value ) {
				if ( !array_key_exists( $key, $params ) ) {
					$params[$key] = $value;
				}
			}
			return $params;
		}
		static function _escape_params($x){
			if(is_array($x)){
				$x = serialize($x);
			}
			return rawurlencode(stripslashes(urldecode($x)));

		}
		static function generate_catalog_url($options, $unlimited = false){
			
			$defaults = array("providers" => "", "categories" => "");
			$options = array_merge($defaults, $options);
			$options = array_map(array("GrabPress", "_escape_params"), $options);
			if(isset($options["keywords_or"])){
				$options["keywords"] = $options["keywords_or"];
			}

			$url = 'http://catalog.'.GrabPress::$environment.'.com/catalogs/1/videos/search.json?'.
					'keywords_and='.$options["keywords_and"].
					'&categories='.$options["categories"].
					'&providers='.$options["providers"].
					'&keywords_not='.$options["keywords_not"].
					"&keywords=".$options["keywords"].
					"&keywords_phrase=".$options["keywords_phrase"];
			if(isset($options["sort_by"]) && $options["sort_by"] != ""){
				$url .= "&sort_by=".$options["sort_by"];
			}else{
				$url .= "&sort_by=created_at";
			}
			if(isset($options["created_after"]) && $options["created_after"] != ""){
				$url .= "&created_after=".$options["created_after"];
			}
			if(isset($options["created_before"]) && $options["created_before"] != ""){
				$url .= "&created_before=".$options["created_before"];	
			}
			if($unlimited){
				$url .= "&limit=-1";
			}
			return $url;
		}
		
		static function parse_adv_search_string($adv_search ){

			preg_match_all('/"([^"]*)"/', $adv_search, $result_exact_phrase, PREG_PATTERN_ORDER);
			for ($i = 0; $i < count($result_exact_phrase[0]); $i++) {
				$matched_exact_phrase[] = str_replace("\"","",stripslashes($result_exact_phrase[0][$i]));
			}

			$sentence = preg_replace('/\"([^\"]*)\"/', '', stripslashes($adv_search));
			
			preg_match_all('/[a-zA-Z0-9_]*\s+OR\s+[a-zA-Z0-9_]*/', $sentence, $result_or, PREG_PATTERN_ORDER);
			for ($i = 0; $i < count($result_or[0]); $i++) {
				$matched_or[] = str_replace(" OR "," ",stripslashes($result_or[0][$i]));
			}

			$sentence_without_or = preg_replace('/[a-zA-Z0-9_]*\s+OR\s+[a-zA-Z0-9_]*/', '', stripslashes($sentence));

			$keywords = preg_split("/\s+/", $sentence_without_or);

			for ($i = 0; $i < count($keywords); $i++) {
				if (preg_match("/^-/", $keywords[$i])) { 
				  $temp_not = str_replace('-', '', $keywords[$i]);
		          $keywords_not[] = $temp_not;	          
				}else{
					$keywords_and[] = $keywords[$i];
				}
			}

			$keywords_phrase = isset($matched_exact_phrase) ? implode(" ", $matched_exact_phrase) : "";
			$keywords_phrase = $keywords_phrase;
			$keywords_and = isset($keywords_and) ? implode(" ", $keywords_and) : "";
			$keywords_not = isset($keywords_not) ? implode(" ", $keywords_not) : "";
			$keywords_or = isset($matched_or) ? implode(" ", $matched_or) : "";

			return array(
				"keywords_phrase" => $keywords_phrase,
				"keywords_and" => $keywords_and,
				"keywords_not" => $keywords_not,
				"keywords_or" => $keywords_or
				);
		}
		static function generate_adv_search_string($keywords){
			$string = "";

			$string .= $keywords["keywords_and"];

			if($keywords["keywords_not"]){
				$not = preg_split("/\s+/", $keywords["keywords_not"]);
				foreach ($not as $value) {
					$string .= " -".$value;
				}
			}

			if($keywords["keywords_phrase"]){
				$string .= ' "'.trim($keywords["keywords_phrase"]).'"';
			}

			if(isset($keywords["keywords_or"])){
				$or = preg_split("/\s+/", $keywords["keywords_or"]);
				if(count($or) == 1){
					if(!$string){
						$string .= $or[0];
					}else{
						$string .= " OR ".$or[0];
					}
				}elseif(count($or) > 1){
					if(!$string){
						$string .= join(" OR ", $or);
					}else{
						$string .= " OR ".join(" OR ", $or);
					}
				}
			}
			return $string;
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
					if ( GrabPressAPI::validate_key() && $_REQUEST[ 'channel' ] != '' && $_REQUEST[ 'provider' ] != '' ) {
						GrabPressAPI::create_feed();
						GrabPressViews::feed_creation_success();
					}else {
						GrabPress::$invalid = true;
						GrabPressViews::feed_management();
					}
					break;
				case 'delete':
					$feed_id = $_REQUEST['feed_id'];
					$connector_id = GrabPressAPI::get_connector_id();
					GrabPressAPI::call( 'DELETE', '/connectors/' . $connector_id . '/feeds/'.$feed_id.'?api_key='.GrabPress::$api_key, $feed_id );
					GrabPressViews::feed_management();					
					break;
				case 'modify':
					$feed_id = $_REQUEST['feed_id'];
					$name = htmlspecialchars( $_REQUEST['name'] );
						
					//$categories = $_REQUEST[ 'channel' ];
					$channels = $_REQUEST[ 'channel' ];
					$channelsList = implode( ',', $channels );
					$channelsListTotal = count( $channels ); // Total providers chosen by the user
					$channels_total = $_REQUEST['channels_total']; // Total providers from the catalog list
					if ( $channelsListTotal == $channels_total ) {
						$channelsList = '';					}


					$providers = $_REQUEST['provider'];
					$providersList = implode( ',', $providers );
					$providersListTotal = count( $providers ); // Total providers chosen by the user
					$providers_total = $_REQUEST['providers_total']; // Total providers from the catalog list
					if ( $providersListTotal == $providers_total ) {
						$providersList = '';
					}
						$url = GrabPress::generate_catalog_url(array(
				   		"keywords_and" => $_REQUEST["keywords_and"],
				   		"keywords_not" => $_REQUEST["keywords_not"],
				   		"keywords_or" => $_REQUEST["keywords_or"],
				   		"keywords_phrase" => $_REQUEST["keywords_phrase"],
				   		"providers" => $providersList,
				   		"categories" => $channelsList
				   	));
						
					$connector_id = GrabPressAPI::get_connector_id();
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
							'name' => $name,
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

					GrabPressAPI::call( 'PUT', '/connectors/' . $connector_id . '/feeds/' . $feed_id . '?api_key=' . GrabPress::$api_key, $post_data );
					GrabPressViews::feed_creation_success();
					break;
				case 'edit-feed':			
					$feed_id = $_REQUEST['feed_id'];
					GrabPressViews::edit_feed($feed_id);
					break;	
				case 'prefill':
					GrabPressViews::prefill_feed();
				break;	
				case 'default':
				default:				
					GrabPressViews::feed_management();
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
							$user_json = GrabPressAPI::call( 'GET', '/user/validate', $credentials, true );
							$user_data = json_decode( $user_json );
							if( isset( $user_data -> user ) ){
								$user = $user_data -> user;
								$connector_data = array(
								 	'user_id' 	=> $user -> id,
									'email' 	=> $user -> email
								);
								GrabPress::log( 'PUTting to connector ' . GrabPressAPI::get_connector_id() . ':' . $user -> id );
								$result_json = GrabPressAPI::call( 'PUT', '/connectors/' . GrabPressAPI::get_connector_id() . '?api_key=' . GrabPress::$api_key, $connector_data );
								GrabPress::grabpress_plugin_messages();
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
							$user = GrabPress::get_user_by("slug");
							$connector_data = array(
							 	'user_id' 	=> null,
								'email' 	=> $user -> email
							);
							$result_json = GrabPressAPI::call( 'PUT', '/connectors/' . GrabPressAPI::get_connector_id() . '?api_key=' . GrabPress::$api_key, $connector_data );
							GrabPress::grabpress_plugin_messages();
							$_REQUEST[ 'action' ] = 'default';
						}
						break;
						case 'create-user':							
							$payment = isset( $_REQUEST['paypal_id']) ? 'paypal' : '';
							$user_data = array(
							   	'user'=>array(
							   		'email'=>trim($_REQUEST['email']),
							         'password'=>$_REQUEST['password'],
							         'first_name'=>$_REQUEST['first_name'],
							         'last_name'=>$_REQUEST['last_name'],
							         'publisher_category_id'=>$_REQUEST['publisher_category_id'],
							         'payment_detail' => array(
							         	'payee' => $_REQUEST['first_name'] . ' ' . $_REQUEST['last_name'],
							         	'company'=>$_REQUEST['company'],
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
							$result_json = GrabPressAPI::call('POST', '/register?api_key='.GrabPress::$api_key, $user_data);
							$result_data = json_decode( $result_json);

							if(!isset( $result_data->error ) ){
								$_REQUEST[ 'action' ] = 'link-user';
								return GrabPress::dispatcher();
							}else{
								GrabPress::$error = 'We already have a registered user with the email address '.$_REQUEST["email"].'. If you would like to update your account information, please login to the <a href="http://www.grab-media.com/publisherAdmin/">Grab Publisher Dashboard</a>, or contact our <a href="http://www.grab-media.com/support/">support</a> if you need assistance.';
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
					GrabPressViews::account_management();
					break;
				}

			case 'catalog':

				if(isset($_REQUEST[ 'action' ])){
					switch ( $_REQUEST[ 'action' ] ) {
						case 'update':
						if ( GrabPressAPI::validate_key() && $_REQUEST[ 'channel' ] != '' && $_REQUEST[ 'provider' ] != '' ) {
							GrabPressAPI::create_feed();
							GrabPressViews::feed_creation_success();
						}else {
							GrabPress::$invalid = true;
							GrabPressViews::feed_management();
						}
						break;
						case 'prefill':
							GrabPressViews::prefill_feed();
						break;
						case 'catalog-search':
						default:							
							GrabPressViews::catalog_management();
						break;
					}
				}

				break;			
			case 'gp-dashboard':
           		GrabPressViews::dashboard_management($_REQUEST);
				break;
			case 'gp-template':
				GrabPressViews::template_management($_REQUEST);
				break;
			}
		}
		static function grabpress_plugin_url(){
			return plugin_dir_url( __FILE__ ) ;
		}
		static function enqueue_scripts($page) {

			$handlerparts = explode("_", $page);
			if($handlerparts[0] !="grabpress" && $page != "post-new.php" && $page != "post.php"){
				return;
			}
			// Plugin url
			$plugin_url = GrabPress::grabpress_plugin_url();

			// jQuery files

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-widget' );
			wp_enqueue_script( 'jquery-ui-position' );
			wp_enqueue_script( 'jquery-ui-dialog' );
			wp_enqueue_script( 'jquery-ui-datepicker' );


			wp_enqueue_script( 'jquery-ui-filter', $plugin_url.'/js/ui/multi/jquery.multiselect.filter.min.js' , array("jquery-ui-widget"));
			wp_enqueue_script( 'jquery-ui-multiselect', $plugin_url.'/js/ui/multi/jquery.multiselect.min.js', array("jquery-ui-widget" ));
			wp_enqueue_script( 'jquery-ui-selectmenu', $plugin_url.'/js/ui/jquery.ui.selectmenu.js', array("jquery-ui-widget" ));
			wp_enqueue_script( 'jquery-simpletip', $plugin_url.'/js/jquery.simpletip.min.js' , array("jquery"));
			wp_enqueue_script( 'jquery-dotdotdot', $plugin_url.'/js/jquery.ellipsis.custom.js' , array("jquery") );
			wp_enqueue_script( 'nanoscroller', $plugin_url.'/js/nanoscroller.js' , array("jquery") );

			wp_enqueue_script( 'grab-player', 'http://player.'.GrabPress::$environment.'.com/js/Player.js' );

			$wpversion = floatval(get_bloginfo('version'));
			if ( $wpversion <= 3.1 ) {		
			    wp_enqueue_script( 'jquery-placeholder', $plugin_url.'/js/ui/jquery.placeholder.min.1.8.7.js'  , array("jquery"));
			}else{				
				wp_enqueue_script( 'jquery-placeholder', $plugin_url.'/js/ui/jquery.placeholder.min.js' , array("jquery") );
			}
			wp_enqueue_script( 'thickbox' );

			wp_enqueue_style( 'jquery-ui-theme', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css' );
			wp_enqueue_style( 'bootstrap', $plugin_url.'/css/bootstrap-sandbox.css' );
			wp_enqueue_style( 'thickbox' );
			wp_enqueue_style( 'nanoscroller', $plugin_url.'/css/nanoscroller.css');		
			wp_enqueue_style( 'grabpress-css', $plugin_url.'/css/grabpress.css' , array("jquery-ui-theme", "bootstrap", "nanoscroller"));			
			
			
			wp_enqueue_style( 'grabpresss-fonts', "http://images.grab-media.com/widgets/grab-fonts/fonts.css");
			wp_enqueue_style( 'bootstrap-responsive', $plugin_url.'/css/bootstrap-responsive.css' );
			
		}

		static function content_by_request( $content, $post )
		{
		    if ( ! empty ( $_REQUEST['pre_content'] )
		        and current_user_can( 'edit_post', $post->ID )
		        and '' === $content
		    )
		    {
			    if ( ! empty ( $_REQUEST['post_id'] )){
	              $post->ID = $_REQUEST['post_id'];
	            }		        
		        $content = str_replace('&amp;', '&', $_REQUEST['pre_content']);
		        return stripslashes($content);
		    }
		    $content = str_replace('&amp;', '&', $content);
		    return $content;
		}
		static function modified_post_title ($title) {

		  if ( ! empty ( $_REQUEST['post_title'] )){
		  	return $title = "VIDEO: ".stripslashes($_REQUEST['post_title']);
		  }
		  
		}

		static function add_my_custom_button($context){
			//path to my icon
			$img = GrabPress::get_g_icon_src();

			//our popup's title
			$title = 'Insert GrabMedia Video';
			$onclick = 'tb_show("Grab Media Catalog", "admin-ajax.php?action=gp_get_catalog&amp;width=900&amp;height=900" );';
			//append the icon
			$context .= "<a title='{$title}' href='#' onclick='{$onclick}' ><img src='{$img}' /></a>";
			return $context;
		}
		
		
		static function mce_settings($settings){
			
			if(!isset($settings["extended_valid_elements"])){
				$settings["extended_valid_elements"] = "div[*],script[*]";
			}else{
				$settings["extended_valid_elements"] .= $settings["extended_valid_elements"].",script[*],div[*]";
			}
			return $settings;
		}

	}//class
}//ifndefclass
if( is_admin() ){
	GrabPress::log( '-------------------------------------------------------' );
	add_action( 'admin_enqueue_scripts', array( 'GrabPress', 'enqueue_scripts' ) );
	register_activation_hook( __FILE__, array( 'GrabPress', 'setup' ) );
	register_uninstall_hook(__FILE__, array( 'GrabPress', 'delete_connector' ));
	add_action( 'admin_menu', array( 'GrabPress', 'grabpress_plugin_menu' ) );
	add_action( 'admin_footer', array( 'GrabPress', 'show_message' ) );
	add_action( 'wp_loaded', array( 'GrabPress', 'grabpress_plugin_messages' ) );
	add_action( 'wp_ajax_gp_toggle_feed', array( 'GrabPressViews', 'toggle_feed_callback' ));
	add_action( 'wp_ajax_gp_delete_feed', array( 'GrabPressViews', 'delete_feed_callback' ));
	add_action( 'wp_ajax_gp_feed_name_unique', array( 'GrabPressViews', 'feed_name_unique_callback' ));
	add_action( 'wp_ajax_gp_insert_video', array( 'GrabPressViews', 'insert_video_callback' ));
	add_action( 'wp_ajax_gp_get_catalog', array( 'GrabPressViews', 'get_catalog_callback' ));
	add_action( 'wp_ajax_gp_get_preview', array( 'GrabPressViews', 'get_preview_callback' ));
	add_action( 'wp_ajax_gp_toggle_watchlist', array( 'GrabPressViews', 'toggle_watchlist_callback' ));
	add_action( 'media_buttons_context',  array("GrabPress", 'add_my_custom_button'));
	add_filter( 'default_content', array( 'GrabPress', 'content_by_request' ), 10, 2 );
	add_filter( 'default_title', array( 'GrabPress', 'modified_post_title' ) );
	add_filter( 'tiny_mce_before_init', array("GrabPress", "mce_settings") );
}

GrabPress::allow_tags();
