<?php

if ( ! class_exists( 'GrabPressViews' ) ) {
	class GrabPressViews {

		static function edit_feed($params){
			GrabPress::log();
			$list_channels = GrabPressAPI::get_channels();
			$channels_total = count( $list_channels );				
			$list_providers = GrabPressAPI::get_providers();
			$providers_total = count( $list_providers );

			if ( GrabPressAPI::validate_key() ) {

				$feed = GrabPressAPI::get_feed($params["feed_id"]);
				
				$url = array();
				parse_str( parse_url( $feed->feed->url, PHP_URL_QUERY ), $url );
				$providers = explode( ",", $url["providers"] ); // providers chosen by the user

				$channels = explode( ",", $url["categories"] ); // Categories or channels chosen by the user
				
				$blogusers = get_users();

				if(isset($params) && isset($params["channels"]) != "" && isset($params["providers"]) != ""){
					print GrabPress::fetch( "includes/gp-feed-template.php", 
					array("form" => array( "referer" => "edit",
										   "action" => "modify",
										   "feed_id" => $params["feed_id"],
										   "name" => $params["name"],
										   "channels" => $params["channels"],
										   "keywords_and" => $params["keywords_and"],
										   "keywords_not" => $params["keywords_not"],
										   "keywords_or" => $url['keywords'],
						   				   "keywords_phrase" => $url['keywords_phrase'],										   
										   "limit" => $params["limit"],
										   "schedule" => $params["schedule"],
										   "active" => $params["active"],
										   "publish" => $params["publish"],
										   "click_to_play" => $params["click_to_play"],
										   "author" => $params["author"],
										   "providers" => $params["providers"],
										   "category" => $params["category"]								   
											),
							"list_providers" => $list_providers,
							"providers_total" => $providers_total,
							"list_channels" => $list_channels,
							"channels_total" => $channels_total,
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
											   "name" => $feed->feed->name,
											   //"channel" => $feed->feed->name,
											   "channels" => $url['categories'],
											   "keywords_and" => $url['keywords_and'],
											   "keywords_not" => $url['keywords_not'],
											   "keywords_or" => $url['keywords'],
						   				       "keywords_phrase" => $url['keywords_phrase'],	
											   "limit" => $feed->feed->posts_per_update,
											   "schedule" => $feed->feed->update_frequency,
											   "active" => $feed->feed->active,
											   "publish" => $feed->feed->custom_options->publish,
											   "click_to_play" => $feed->feed->auto_play,
											   "author" => $feed->feed->custom_options->author_id,
											   "providers" => $providers,
											   "category" => $cats
												),
								"list_providers" => $list_providers,
								"providers_total" => $providers_total,
								"list_channels" => $list_channels,
								"channels_total" => $channels_total,
								"blogusers" => $blogusers
						 ) );

				}

			}
		}

		static function do_edit_feed($params){
			GrabPressAPI::edit_feed($params);
			GrabPressViews::feed_creation_success($params);
		}
		
		static function prefill_feed($params){
			GrabPress::log();
			if ( GrabPressAPI::validate_key() ) {
				$list_providers = GrabPressAPI::get_providers();			
				$providers_total = count( $list_providers );

				$list_channels = GrabPressAPI::get_channels();
				$channels_total = count( $list_channels );

				$blogusers = get_users();

				$keywords = GrabPress::parse_adv_search_string(isset($params["keywords"])?$params["keywords"]:"");

				print GrabPress::fetch( "includes/gp-feed-template.php", 
					array("form" => array( "referer" => "create",
										   "action" => "update",
										   "channels" => $params["channels"],
										   "keywords_and" => $keywords["keywords_and"],
										   "keywords_not" => $keywords["keywords_not"],
										   "keywords_or" => $keywords['keywords_or'],
						   				   "keywords_phrase" => $keywords['keywords_phrase'],
										   "providers" => $params["providers"],
										   "publish" => $params["publish"],
										   "click_to_play" => $params["click_to_play"],
										   "category" => ""
											),
							"list_providers" => $list_providers,
							"providers_total" => $providers_total,
							"list_channels" => $list_channels,
							"channels_total" => $channels_total,
							"blogusers" => $blogusers
					 ) );
			}
		}

		static function account_management($request) {
			GrabPress::log();
			print GrabPress::fetch( 'includes/gp-account-template.php', array("request" =>$request ));
		}
		
		static function link_account($params){
			if( isset( $params[ 'email' ] ) && isset( $params[ 'password' ]) ){
				$credentials = array( 'user' => $params[ 'email' ], 'pass' => $params[ 'password' ] );
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
					GrabPress::plugin_messages();
					$params[ 'action' ] = 'default';
				}else{
					GrabPress::$error = 'No user with the supplied email and password combination exists in our system. Please try again.';
					$params[ 'action' ] = 'default';
				}
			}else {
				GrabPress::abort( 'Attempt to link user with incomplete form data.' );
			}
			GrabPressViews::account_management($params);
		}
		static function unlink_account($params){
			if( isset( $params[ 'confirm' ]) ){
				$user = GrabPress::get_user_by("slug");
				$connector_data = array(
					'user_id' 	=> null,
					'email' 	=> $user -> email
				);
				$result_json = GrabPressAPI::call( 'PUT', '/connectors/' . GrabPressAPI::get_connector_id() . '?api_key=' . GrabPress::$api_key, $connector_data );
				GrabPress::plugin_messages();
				$params[ 'action' ] = 'default';
			}
			GrabPressViews::account_management($params);
		}
		static function create_user($params){
			$payment = isset( $params['paypal_id']) ? 'paypal' : '';
			$user_data = array(
				'user'=>array(
					'email'=>trim($params['email']),
					 'password'=>$params['password'],
					 'first_name'=>$params['first_name'],
					 'last_name'=>$params['last_name'],
					 'publisher_category_id'=>$params['publisher_category_id'],
					 'payment_detail' => array(
						'payee' => $params['first_name'] . ' ' . $params['last_name'],
						'company'=>$params['company'],
						'address1'=>$params['address1'],
						'address2'=>$params['address2'],
						'city'=>$params['city'],
						'state'=>$params['state'],
						'zip'=>$params['zip'],
						'country_id' => 214,
						'preferred_payment_type'=> 'Paypal',
						'phone_number'=>$params['phone_number'],
						'paypal_id'=>$params['paypal_id']
					 )
				)
			);
			$user_json = json_encode($user_data);
			$result_json = GrabPressAPI::call('POST', '/register?api_key='.GrabPress::$api_key, $user_data);
			$result_data = json_decode( $result_json);
			

			if(isset($result_data->user)){
				$params[ 'action' ] = 'link-user';
				GrabPressViews::link_account($params);
			}else{
				GrabPress::$error = 'We already have a registered user with the email address '.$params["email"].'. If you would like to update your account information, please login to the <a href="http://www.grab-media.com/publisherAdmin/">Grab Publisher Dashboard</a>, or contact our <a href="http://www.grab-media.com/support/">support</a> if you need assistance.';
				$params['action'] = 'create';
				GrabPressViews::account_management($params);
			}
		}

		static function catalog_management($request) {
			GrabPress::log();
			$defaults = array(
				"sort_by" => "created_at",
				"providers" => array(),
				"channels" => array(),
                                "page_no" => 1
                            );
			$request = array_merge($defaults, $request);

			if(isset($request["keywords"])){
				$adv_search_params = GrabPress::parse_adv_search_string(isset($request["keywords"])?$request["keywords"]:"");
				
				if(isset($request['created_before']) && ($request['created_before'] != "")){
					$created_before_date = new DateTime( $request['created_before'] );	
					$created_before = $created_before_date->format('Ymd');
					$adv_search_params['created_before'] = $created_before;
				}
				
				if(isset($request['created_after']) && ($request['created_after'] != "")){
					$created_after_date = new DateTime( $request['created_after'] );
					$created_after = $created_after_date->format('Ymd');
					$adv_search_params['created_after'] = $created_after;
				}
				if(count($request["providers"]) != count(GrabPressAPI::get_providers())){
					$adv_search_params["providers"] =  is_array($request['providers']) ? join($request['providers'], ","): "";
				}

				if(count($request["channels"]) != count(GrabPressAPI::get_channels())){
					$adv_search_params["categories"] = is_array($request["channels"])?join($request["channels"],","):$request["channels"];
				}
				$adv_search_params["sort_by"] = $request["sort_by"];
                                $adv_search_params["page"] = $request["page_no"];
				$url_catalog = GrabPress::generate_catalog_url($adv_search_params);

				$json_preview = GrabPressAPI::get_json($url_catalog);

				$list_feeds = json_decode($json_preview, true);	
				
				if(empty($list_feeds["results"])){
					GrabPress::$error = 'It appears we do not have any content matching your search criteria. Please modify your settings until you see the kind of videos you want in your feed';
				}	
			}else{
				$list_feeds = array("results" => array());
			}

			print GrabPress::fetch( 'includes/gp-catalog-template.php' ,
				array( "form" => $request ,
					"list_channels" => GrabPressAPI::get_channels(),
					"list_providers" => GrabPressAPI::get_providers(),
					"list_feeds" => $list_feeds,
					"providers" => $request["providers"],
					"channels" => $request["channels"],                                        
					));
		}

		static function get_catalog_callback(){
			$request = Grabpress::_escape_request($_REQUEST);
			$defaults = array(
				"providers" => array(),
				"channels" => array(),
				"sort_by" => "created_at",
				"empty" => "true",
				"page" => 1);
			$request = array_merge($defaults, $request);
			
			if($request["empty"] == "true"){
				$list_feeds["results"] = array();
				$empty = "true";
			}else{
				$adv_search_params = GrabPress::parse_adv_search_string(isset($request["keywords"])?$request["keywords"]:"");

				if(isset($request['created_before']) && ($request['created_before'] != "")){
					$created_before_date = new DateTime( $request['created_before'] );	
					$created_before = $created_before_date->format('Ymd');
					$adv_search_params['created_before'] = $created_before;
				}
				
				if(isset($request['created_after']) && ($request['created_after'] != "")){
					$created_after_date = new DateTime( $request['created_after'] );
					$created_after = $created_after_date->format('Ymd');
					$adv_search_params['created_after'] = $created_after;
				}
				if(count($request["providers"]) != count(GrabPressAPI::get_providers())){
					$adv_search_params["providers"] =  isset($request['providers']) ? join($request['providers'], ","): "";
				}
				if(count($request["channels"]) != count(GrabPressAPI::get_channels())){
					$adv_search_params["categories"] = is_array($request["channels"])?join($request["channels"],","):$request["channels"];
				}
				
				$adv_search_params["sort_by"] = $request["sort_by"];
				$adv_search_params["page"] = $request["page"];
				$url_catalog = GrabPress::generate_catalog_url($adv_search_params);

				$json_preview = GrabPressAPI::get_json($url_catalog);

				$list_feeds = json_decode($json_preview, true);	

				if(empty($list_feeds["results"])){
					GrabPress::$error = 'It appears we do not have any content matching your search criteria. Please modify your settings until you see the kind of videos you want in your feed';
				}

				$empty = "false";
			}

			print GrabPress::fetch("includes/gp-catalog-ajax.php", array(
				"form" => $request,
				"list_providers" => GrabPressAPI::get_providers(),
				"list_channels" => GrabPressAPI::get_channels(),
				"list_feeds" => $list_feeds,
				"empty" => $empty,
				"providers" => $request["providers"],
				"channels" => $request["channels"]
				));
			die();
		}

		static function grabpress_preview_videos($request) {
			GrabPress::log();
			$defaults = array(
				"sort_by" => "created_at",
				"providers" => array(),
				"channels" => array(),
                                "page" => 1
                            );
			$request = array_merge($defaults, $request);

			$providers =  GrabPressAPI::get_providers();			

			$channels = GrabPressAPI::get_channels();
			if(isset($request["keywords"])){
				$adv_search_params = GrabPress::parse_adv_search_string(isset($request["keywords"])?$request["keywords"]:"");
			}elseif(isset($request["feed_id"])){
				$feed = GrabPressAPI::get_feed($request["feed_id"]);

				$url = array();
				parse_str( parse_url( $feed->feed->url, PHP_URL_QUERY ), $url );
				$adv_search_params = $url;
				$request["keywords"] = Grabpress::generate_adv_search_string($adv_search_params);
				$request["providers"] = explode(",", $url["providers"]);
				$request["channels"] = explode(",", $url["categories"]);
			}else{
				$adv_search_params = $request;
				$request["keywords"] = Grabpress::generate_adv_search_string($adv_search_params);
			}

			if(isset($request['created_before']) && ($request['created_before'] != "")){
				$created_before_date = new DateTime( $request['created_before'] );	
				$created_before = $created_before_date->format('Ymd');
				$adv_search_params['created_before'] = $created_before;
			}
			
			if(isset($request['created_after']) && ($request['created_after'] != "")){
				$created_after_date = new DateTime( $request['created_after'] );
				$created_after = $created_after_date->format('Ymd');
				$adv_search_params['created_after'] = $created_after;
			}

			if(count($request["providers"]) != count(GrabPressAPI::get_providers())){
				$adv_search_params["providers"] =  is_array($request['providers']) ? join($request['providers'], ","): "";
			}else{
				unset($adv_search_params["providers"]);
			}

			if(count($request["channels"]) != count(GrabPressAPI::get_channels())){
				$adv_search_params["categories"] = is_array($request["channels"])?join($request["channels"],","):$request["channels"];
			}else{
				unset($adv_search_params["categories"]);
				unset($adv_search_params["channels"]);
			}
                        $adv_search_params["page"] = $request["page"];
			$url_catalog = GrabPress::generate_catalog_url($adv_search_params);

			$json_preview = GrabPressAPI::get_json($url_catalog);

			$list_feeds = json_decode($json_preview, true);	

			if(empty($list_feeds["results"])){
				GrabPress::$error = 'It appears we do not have any content matching your search criteria. Please modify your settings until you see the kind of videos you want in your feed';
			}	
			
			print GrabPress::fetch( "includes/gp-catalog-ajax.php", array(
						"form" => $request ,
						"list_channels" => GrabPressAPI::get_channels(),
						"list_providers" => GrabPressAPI::get_providers(),
						"list_feeds" => $list_feeds,
						"providers" => $request["providers"],
						"channels" => $request["channels"],
						"empty" => "false"                                                
					)
				 );	
			die();
		}

		static function feed_management($params) {
			GrabPress::log();

			$list_providers = GrabPressAPI::get_providers();			
			$providers_total = count( $list_providers );

			$list_channels = GrabPressAPI::get_channels();
			$channels_total = count( $list_channels );

			$blogusers = get_users();
			print GrabPress::fetch( 'includes/gp-feed-template.php',
				array( "form" => $params,
					"list_providers" => $list_providers,
					"providers_total" => $providers_total,
					"list_channels" => $list_channels,
					"channels_total" => $channels_total,
					"blogusers" => $blogusers ) );
		}

		static function do_create_feed($params){
			if ( GrabPressAPI::validate_key() && $params[ 'channels' ] != '' && $params[ 'providers' ] != '' ) {
				GrabPressAPI::create_feed($params);
				GrabPressViews::feed_creation_success($params);
			}else {
				GrabPress::$invalid = true;
				GrabPressViews::feed_management($params);
			}
		
		}

		static function feed_creation_success($params){
			print GrabPress::fetch( "includes/gp-feed-created-template.php", array("request" => $params));
		}

		static function template_management($request){
			$defaults = array(
				"width" => 480,
				"ratio" => "widescreen",
				"playback" => "auto",
				"action" => "new"
				);

			if(isset($request["action"]) && $request["action"]!="default"){
				$ratio = $request["ratio"]=="widescreen"?"16:9":"4:3";
				$width = $request["width"];
	  			if($ratio == "16:9"){
			 		$height = (int)(($request["width"]/16)*9);
			 	}else{
					$height = (int)(($request["width"]/4)*3);
			 	}
				$result = GrabPressAPI::call( $request["action"]=="edit"?'PUT':"POST",
				 '/connectors/'.GrabPressAPI::get_connector_id().'/player_settings?api_key='.GrabPress::$api_key, array(
				 	"player_setting" => array(
					 	"ratio" => $ratio,
					 	"width" => $width,
					 	"height" => $height
				 	))
				  );

				print GrabPress::fetch("includes/gp-template-modified.php");
			}else{
				$settings = $defaults;
				$player = GrabPressAPI::get_player_settings();

				if($player){
					$settings["width"] = $player["width"];			
					$settings["height"] = $player["height"];
					
					$settings["ratio"] = $player["ratio"]=="16:9"?"widescreen":"standard";
					if($settings["ratio"] == "widescreen"){
			 			$player_height = (int)($player["width"]/16)*9;
			 			$settings["widescreen_selected"] = true;
						$settings["standard_selected"] = false;
				 	}else{
						$player_height = (int)($player["width"]/4)*3;
						$settings["widescreen_selected"] = false;
						$settings["standard_selected"] = true;
				 	}
				 	
				 	$settings["height"] = $player_height;				 	
					$settings["action"] = "edit";
				}
				/*
				if($settings["playback"] == "auto"){
					$settings["auto_selected"] = true;
					$settings["click_selected"] = false;
				}else{
					$settings["auto_selected"] = false;
					$settings["click_selected"] = true;
				}
				*/

				

			print GrabPress::fetch("includes/gp-template.php", array(
				"form" => $settings
				));
			}
		}

		static function dashboard_management($request) {
			GrabPress::log();
			$broadcast_json = GrabPressAPI::call( "GET",
				 '/messages/?api_key='.GrabPress::$api_key."&message_type_id=1");
			$pills_json = GrabPressAPI::call( "GET",
				 '/messages/?api_key='.GrabPress::$api_key."&message_type_id=2");
			$resources_json = GrabPressAPI::call( "GET",
				 '/messages/?api_key='.GrabPress::$api_key."&message_type_id=3");
			$messages = json_decode($broadcast_json);
			$pills = json_decode($pills_json);
			$resources = json_decode($resources_json);

			$watchlist = GrabpressAPI::get_watchlist();
			$feeds = GrabPressAPI::get_feeds();
			$feeds = GrabPressAPI::watchlist_activity($feeds);
			$num_feeds = count( $feeds );

			$user = GrabPressAPI::get_user();
			$linked = isset($user->email);
                        $publisher_status = $linked ? "account-linked" : "account-unlinked";  
                        
                        $list_providers = GrabPressAPI::get_providers();
                        
			print GrabPress::fetch( 'includes/gp-dashboard.php' , array(
				"messages" => $messages,
				"pills" => $pills,
				"resources" => $resources,
				"feeds" => $feeds,
				"watchlist" => array_splice($watchlist,0,10),
				"embed_id" => GrabPressAPI::get_connector()->ctp_embed_id,
				"publisher_status" => $publisher_status,
                                "list_providers" => $list_providers                            
				));
		}

		static function toggle_feed_callback($request) {
			global $wpdb; // this is how you get access to the database

			$feed_id = intval( $_REQUEST['feed_id'] );
			$active = intval( $_REQUEST['active'] );	

			$post_data = array(
				'feed' => array(
					'active' => $active
				)
			);

			GrabPressAPI::call( 'PUT', '/connectors/' . GrabPressAPI::get_connector_id() . '/feeds/' . $feed_id . '?api_key=' . GrabPress::$api_key, $post_data );

			$feeds = GrabPressAPI::get_feeds();
			$num_feeds = count( $feeds );

			$active_feeds = 0;
	
			for ( $i=0; $i < $num_feeds; $i++ ) {
				$feed = $feeds[ $i ];
				if ( $feed -> feed -> active > 0 ) {
					$active_feeds++;
				}
			}

			echo $active_feeds.'-'.$num_feeds;

			die(); // this is required to return a proper result
		}

		static function delete_feed_callback() {
			global $wpdb; // this is how you get access to the database

			$feed_id = intval( $_REQUEST['feed_id'] );	

			$connector_id = GrabPressAPI::get_connector_id();
			GrabPressAPI::call( 'DELETE', '/connectors/' . $connector_id . '/feeds/'.$feed_id.'?api_key='.GrabPress::$api_key, $feed_id );

			die(); // this is required to return a proper result
		}
		static function delete_feed($params){
			GrabPressAPI::delete_feed($params["feed_id"]);
			GrabPressViews::feed_management();
		}

		static function feed_name_unique_callback() {
			$name = $_REQUEST['name'];	

			$feeds = GrabPressAPI::get_feeds();
			$num_feeds = count( $feeds );
			$duplicated_name = "false";
			foreach ( $feeds as $record_feed ) {
				if($record_feed->feed->name == $name){
					$duplicated_name = "true";
					break;
				}
			}

			echo $duplicated_name;
			die(); // this is required to return a proper result
		}
		
		static function insert_video_callback() {
			if(!GrabPress::check_permissions_for("single-post")){
				GrabPress::abort("Insuficcient Permissions ");
			}
			$video_id = $_REQUEST['video_id'];
			$format = $_REQUEST['format'];
			$id = GrabPressAPI::get_connector_id();

			$objXml = GrabPressAPI::get_video_mrss($video_id);
			
			$img_url = GrabPressAPI::get_preview_url($objXml);
			
			$settings = GrabPressAPI::get_player_settings_for_embed();
			foreach ($objXml->channel->item as $item) {   
				if($format == 'post'){
					$text = "<div id=\"grabpreview\"> 
						<p><img src='".$img_url."' /></p> 
						</div>
						<p>".$item->description."</p> 
						<!--more-->
						<div id=\"grabembed\">
						[grabpress_video guid=\"".$item->guid."\"]
						<p>Thanks for checking us out. Please take a look at the rest of our videos and articles.</p> <br/> 
						<p><img src='".$item->grabprovider->attributes()->logo."' /></p> 
						<p>To stay in the loop, bookmark <a href=\"/\">our homepage</a>.</p>
						<style>
						div#grabpreview {
						display:none !important;
						}
						</style>"; 
					$post_id = wp_insert_post(array(
						"post_content" => $text,
						"post_title" => "VIDEO: ".$item->title,
						"post_type" => "post",
						"post_status" => "draft",
						"tags_input" => $item->mediagroup->mediakeywords
					));

					$upload_dir = wp_upload_dir();
					$image_url = $img_url;
					$image_data = file_get_contents($image_url);
					$filename = basename($image_url);

					if(validate_file($filename)){//sanitize file path
						GrabPress::error(" invalid filename ". $filename);
					}

					if(wp_mkdir_p($upload_dir['path']))
					    $file = $upload_dir['path'] . '/' . $filename;
					else
					    $file = $upload_dir['basedir'] . '/' . $filename;
					file_put_contents($file, $image_data);

					$wp_filetype = wp_check_filetype($filename, null );
					$attachment = array(
						'guid' => sanitize_file_name($filename),
						'guid' => "endworld",
					    'post_mime_type' => $wp_filetype['type'],
					    'post_title' => sanitize_file_name($filename),
					    'post_content' => '',
					    'post_status' => 'inherit'
					);
					$attach_id = wp_insert_attachment( $attachment, $file, $post_id );
					include_once(ABSPATH . 'wp-admin/includes/image.php');
					$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
					wp_update_attachment_metadata( $attach_id, $attach_data );

					set_post_thumbnail( $post_id, $attach_id );

					echo json_encode(array(
						"status" => "redirect", 
						"url" => "post.php?post=".$post_id."&action=edit"));
				}elseif($format == 'embed'){
					echo json_encode(array(
						"status" => "ok",
						"content" => '<div id="grabDiv'.GrabPressAPI::get_connector()->ctp_embed_id.'">[grabpress_video guid="'.$item->guid.'"]</div>'));
				}		
			}	

			die(); // this is required to return a proper result
		}

		static function get_preview_callback(){
			GrabPressViews::grabpress_preview_videos($_REQUEST);
			die();
		}

		static function toggle_watchlist_callback() {
			global $wpdb; // this is how you get access to the database

			$feed_id = intval( $_REQUEST['feed_id'] );
			$watchlist = intval( $_REQUEST['watchlist'] );

			$post_data = array(
				'feed' => array(
					'watchlist' => $watchlist
				)
			);

			GrabPressAPI::call( 'PUT', '/connectors/' . GrabPressAPI::get_connector_id() . '/feeds/' . $feed_id . '?api_key=' . GrabPress::$api_key, $post_data );

			$response = new stdClass();
			$response->environment = GrabPress::$environment;
			$response->embed_id = GrabPressAPI::get_connector()->ctp_embed_id;
			$response->results = array_splice(GrabpressAPI::get_watchlist(), 0 , 10);
			echo json_encode($response);
						
			die(); // this is required to return a proper result

			
		}

	}
}
