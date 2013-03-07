<?php

if ( ! class_exists( 'GrabPressAPI' ) ) {
	class GrabPressAPI {
		static function get_location() {
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

		static function call( $method, $resource, $data=array(), $auth=false ){
			GrabPress::log();
			if(isset($auth) && isset($data['user']) && isset($data['pass'])){
				GrabPress::log("HTTP AUTH <> ". $data['user'] . ":" . $data['pass']);
			}
			$json = json_encode( $data );
			$apiLocation = GrabPressAPI::get_location();
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
				$params = http_build_query($data);
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
			GrabPress::log();
			$id = GrabPressAPI::get_connector_id();
			$user_json = GrabPressAPI::call( 'GET',  '/connectors/'.$id.'/user?api_key='.GrabPress::$api_key );
			$user_data = json_decode( $user_json );
			// GrabPress::$connector_user = $user_data;
			return $user_data;
		}

		static function get_player_settings(){
			if(!GrabPress::$player_settings){
				$settings_json =  GrabPressAPI::call( 'GET',  '/connectors/'.GrabPressAPI::get_connector_id().'/player_settings?api_key='.GrabPress::$api_key );
				$settings = json_decode( $settings_json );

				if( empty($settings) || (isset($settings->error) && $settings->error->status_code == 404)){//nonexistent. set defaults.
					GrabPress::$player_settings = array();
				}else{
					GrabPress::$player_settings = array(
						"width" => $settings->player_setting->width,
					 	"height" => $settings->player_setting->height,
					 	"ratio" => $settings->player_setting->ratio
					 	);
				}
				
			}
			
			return GrabPress::$player_settings;
		}
		static function get_player_settings_for_embed(){
			$sett = GrabPressAPI::get_player_settings();
			$defaults = array("width" => 600, "height"=> 270, "ratio" => "16:9");

			return array_merge($defaults, $sett);
		}

		static function get_connector() {
			GrabPress::log();
			if(GrabPress::$connector){
				return GrabPress::$connector;
			}
			if ( GrabPressAPI::validate_key() ) {
				$rpc_url = get_bloginfo( 'url' ).'/xmlrpc.php';
				$connectors_json =  GrabPressAPI::call( 'GET',  '/connectors?api_key='.GrabPress::$api_key );
				$connectors_data = json_decode( $connectors_json );
				for ( $n = 0; $n < count( $connectors_data ); $n++ ) {
					$connector = $connectors_data[$n]->connector;
					if ( $connector -> destination_address == $rpc_url ) {
						$connector_id = $connector -> id;
						GrabPressAPI::report_versions($connector);
						GrabPress::$connector = $connector;	
					}
				}

				if ( ! isset( $connector_id ) ) {//create connector
					$connector_types_json = GrabPressAPI::call( 'GET',  '/connector_types?api_key='.GrabPress::$api_key );
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

					$connector_json = GrabPressAPI::call( 'POST',  '/connectors?api_key='.GrabPress::$api_key, $connector_post );
					$connector_data = json_decode( $connector_json );
					GrabPress::$connector = $connector_data -> connector;	
				}
				
				return GrabPress::$connector;
			}else {
				GrabPress::$feed_message = 'Your API key is no longer valid. Please <a href = "https://getsatisfaction.com/grabmedia" target="_blank">contact Grab support.</a>';
				return false;
			}
		}
		static function get_connector_id(){
			return GrabPressAPI::get_connector()->id;
		}
		
		static function create_feed() {
			GrabPress::log();
			if ( GrabPressAPI::validate_key() ) {
				$channels = $_REQUEST[ 'channel' ];
				$channelsList = implode( ',', $channels );
				$channelsListTotal = count( $channels ); // Total providers chosen by the user
				$channels_total = $_REQUEST['channels_total']; // Total providers from the catalog list
				if ( $channelsListTotal == $channels_total ) {
					$channelsList = '';
				}

				$name = rawurlencode( $_REQUEST[ 'name' ] );

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
						'name' => $name,
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
				$response_json = GrabPressAPI::call( 'POST', '/connectors/' . $connector_id . '/feeds/?api_key='.GrabPress::$api_key, $post_data );
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

		static function validate_key() {
			GrabPress::log();
			$api_key = get_option( 'grabpress_key' );
			if ( $api_key != '' ) {
				$validate_json = GrabPressAPI::call( 'GET', '/user/validate?api_key='.$api_key );
				$validate_data = json_decode( $validate_json );
				if (  isset( $validate_data -> error ) ) {
					return GrabPressAPI::create_connection();
				}else {
					GrabPress::$api_key = $api_key;
					return true;
				}
			}else {
				return GrabPressAPI::create_connection();
			}
			return false;
		}

		static function report_versions($connector){
			$gpv = GrabPress::$version;
 			$wpv = get_bloginfo("version");

			if(GrabPressAPI::_needs_version_update($connector, $gpv, $wpv)){

				//$connector_json = GrabPressAPI::call( 'PUT',  '/connectors?api_key='.GrabPress::$api_key, $connector_post );
				GrabPressAPI::call("PUT", "/connectors/".$connector->id."?api_key=".GrabPress::$api_key, 
					 array(
						"wordpress_version" => $wpv,
						"grabpress_version" => $gpv
					));
			}
		}
		static function _needs_version_update($connector, $currentGP, $currentWP){
			return (!$connector->grabpress_version  || !$connector->wordpress_version) //connector does not have a version for either one
				|| ($connector->grabpress_version != $currentGP || $connector->wordpress_version != $currentWP); //outdated
		}

		static function get_feeds() {
			GrabPress::log();
			if ( GrabPressAPI::validate_key() ) {
				$connector_id = GrabPressAPI::get_connector_id();
				$feeds_json = GrabPressAPI::call( 'GET', '/connectors/'.$connector_id.'/feeds?api_key='.GrabPress::$api_key );
				$feeds_data = json_decode( $feeds_json );
				return $feeds_data;
			}else {
				GrabPress::abort( 'no valid key' );
			}
		}

		static function get_feed($feed_id) {
			GrabPress::log();
			if ( GrabPressAPI::validate_key() ) {
				$connector_id = GrabPressAPI::get_connector_id();					
				$feed_json = GrabPressAPI::call( 'GET', '/connectors/'.$connector_id.'/feeds/'.$feed_id.'?api_key='.GrabPress::$api_key );
				$feed_data = json_decode( $feed_json );
				return $feed_data;
			}else {
				GrabPress::abort( 'no valid key' );
			}
		}

		static function create_connection() {
			GrabPress::log();
			$user_url = get_site_url();
			$user_nicename = GrabPress::$grabpress_user;
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
			$user_json = GrabPressAPI::call( "POST", '/user', $post_data );
			$user_data = json_decode( $user_json );

			$api_key = $user_data -> user -> access_key;
			if ( $api_key ) {
				update_option( 'grabpress_key', $api_key );//store api key
			}

			GrabPress::$api_key = get_option( 'grabpress_key' );//retreive api key from storage
			/*
		 * Keep user up to date with API info
		 */
			$description = 'Bringing you the best media on the Web.';
			$role = 'editor';// minimum for auto-publish (author)
			GrabPress::get_user_by("login");
			if ( isset($user_data) ) {// user exists, hash password to keep data up-to-date
				$msg = 'User Exists ('.$user_login.'): '.$user_data->user->id;
				$user = array(
					"id" => $user_data->user->id,
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
		
		static function _filter_out_out_providers( $x ) {
			return !$x->provider->opt_out;
		}

		static function _sort_providers($a, $b){
			return strcasecmp($a->provider->name, $b->provider->name);
		}
		// returns cached results after 1rst call
		static function get_providers() {
			if( isset(GrabPress::$providers) ){
				return GrabPress::$providers;
			}
			$json_provider = GrabPressAPI::get_json( 'http://catalog.'.GrabPress::$environment.'.com/catalogs/1/providers?limit=-1' );
			$list = json_decode( $json_provider );
			$list = array_filter( $list, array( "GrabPressAPI", "_filter_out_out_providers" ) );
			uasort($list, array("GrabPressAPI", "_sort_providers"));
			GrabPress::$providers = $list;
			return $list;
		}
		///Alphabetically
		static function _sort_channels($a, $b){
			return strcasecmp($a->category->name, $b->category->name);
		}
		static function get_channels() {
			if( isset(GrabPress::$channels) ){
				return GrabPress::$channels;
			}
			$json_channel = GrabPressAPI::get_json( 'http://catalog.'.GrabPress::$environment.'.com/catalogs/1/categories' );			
			$list = json_decode( $json_channel );
			uasort($list, array("GrabPressAPI", "_sort_channels"));
			GrabPress::$channels = $list;
			return $list;
		}
		static function _sort_watchlist($a, $b){
			$at = new DateTime($a->video->created_at);
			$bt = new DateTime($b->video->created_at);
			return $at->format("YmdHis") < $bt->format("YmdHis");
		}
		static function get_watchlist(){
			if( isset(GrabPress::$watchlist) ){
				return GrabPress::$watchlist;
			}
			$feeds = GrabPressAPI::get_feeds();
			$watched = array();

			foreach ($feeds as $feed) {
				if($feed->feed->watchlist == true){
					$json = GrabPressAPI::get_json($feed->feed->url);
					$watched = array_merge($watched, json_decode($json)->results);
				}
			}
			uasort($watched, array("GrabPressAPI", "_sort_watchlist"));
			return $watched;
		}
		static function watchlist_activity($feeds){
			foreach ($feeds as $feed) {
				$submissions = GrabPressAPI::get_items_from_last_submission($feed);
				$feed->feed->feed_health = $submissions/$feed->feed->posts_per_update;
				$feed->feed->submissions = $submissions;
			}
			return $feeds;
		}
		static function get_items_from_last_submission($feed){
			$submissions = GrabPressAPI::call("GET", "/connectors/".GrabPressAPI::get_connector_id()."/feeds/".$feed->feed->id."/submissions?api_key=".GrabPress::$api_key);
			$submissions = json_decode($submissions);
			$count = 0;
			if(count($submissions)){
				foreach ($submissions as $sub) {
					$last_submission = new DateTime($submissions[0]->submission->created_at);
					if(
						new DateTime($sub->submission->created_at) > 
						$last_submission->sub(date_interval_create_from_date_string($feed->feed->update_frequency." seconds"))
					){

						$count++;
					}
				}
			}
			return $count;
		}
		static function get_video_mrss($video_id){
			$url= 'http://catalog.'.GrabPress::$environment.'.com/catalogs/1/videos/'.$video_id.'.mrss';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$xml = curl_exec($ch);
			
			curl_close($ch);

			$search = array('grab:', 'media:', 'type="flash"');
			$replace = array('grab', 'media', '');

			$xmlString = str_replace( $search, $replace, $xml);
			return  simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);
		}
	}
}