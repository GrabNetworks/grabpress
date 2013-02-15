<?php

if ( ! class_exists( 'GrabPressViews' ) ) {
	class GrabPressViews {

		static function edit_feed($feed_id){
			GrabPress::log();
			$list_channels = GrabPressAPI::get_channels();
			$channels_total = count( $list_channels );				
			$list_providers = GrabPressAPI::get_providers();
			$providers_total = count( $list_providers );

			if ( GrabPressAPI::validate_key() ) {

				$feed = GrabPressAPI::get_feed($feed_id);
				
				$url = array();
				parse_str( parse_url( $feed->feed->url, PHP_URL_QUERY ), $url );
				$providers = explode( ",", $url["providers"] ); // providers chosen by the user

				$channels = explode( ",", $url["categories"] ); // Categories or channels chosen by the user
				
				$blogusers = get_users();

				if(isset($_REQUEST) && isset($_REQUEST["channel"]) != "" && isset($_REQUEST["provider"]) != ""){
					print GrabPress::fetch( "includes/gp-feed-template.php", 
					array("form" => array( "referer" => "edit",
										   "action" => "modify",
										   "feed_id" => $_REQUEST["feed_id"],
										   "name" => $_REQUEST["name"],
										   "channel" => $_REQUEST["channel"],
										   "keywords_and" => $_REQUEST["keywords_and"],
										   "keywords_not" => $_REQUEST["keywords_not"],
										   "keywords_or" => $url['keywords'],
						   				   "keywords_phrase" => $url['keywords_phrase'],										   
										   "limit" => $_REQUEST["limit"],
										   "schedule" => $_REQUEST["schedule"],
										   "active" => $_REQUEST["active"],
										   "publish" => $_REQUEST["publish"],
										   "click_to_play" => $_REQUEST["click_to_play"],
										   "author" => $_REQUEST["author"],
										   "provider" => $_REQUEST["provider"],
										   "category" => $_REQUEST["category"]								   
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
											   "channel" => $url['categories'],
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
											   "provider" => $providers,
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

		static function prefill_feed(){
			GrabPress::log();
			if ( GrabPressAPI::validate_key() ) {
				$list_providers = GrabPressAPI::get_providers();			
				$providers_total = count( $list_providers );

				$list_channels = GrabPressAPI::get_channels();
				$channels_total = count( $list_channels );

				$blogusers = get_users();

				$keywords = GrabPress::parse_adv_search_string(isset($_REQUEST["keywords"])?$_REQUEST["keywords"]:"");

				print GrabPress::fetch( "includes/gp-feed-template.php", 
					array("form" => array( "referer" => "create",
										   "action" => "update",
										   "channel" => $_REQUEST["channel"],
										   "keywords_and" => $keywords["keywords_and"],
										   "keywords_not" => $keywords["keywords_not"],
										   "keywords_or" => $keywords['keywords_or'],
						   				   "keywords_phrase" => $keywords['keywords_phrase'],
										   "provider" => $_REQUEST["provider"],
										   "publish" => $_REQUEST["publish"],
										   "click_to_play" => $_REQUEST["click_to_play"],
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

		static function account_management() {
			GrabPress::log();
			print GrabPress::fetch( 'includes/gp-account-template.php' );
		}

		static function catalog_management() {
			GrabPress::log();
			$defaults = array(
				"sort_by" => "created_at",
				"providers" => array(),
				"channels" => array());
			$request = array_merge($defaults, $_REQUEST);

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
					"channels" => $request["channels"]
					));
		}

		static function get_catalog_callback(){
			$defaults = array(
				"providers" => array(),
				"channels" => array(),
				"sort_by" => "created_at",
				"empty" => "true");
			$request = array_merge($defaults, $_REQUEST);
			
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

		static function grabpress_preview_videos() {
			GrabPress::log();
			$defaults = array(
				"sort_by" => "created_at",
				"providers" => array(),
				"channels" => array());
			$request = array_merge($defaults, $_REQUEST);

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

			$url_catalog = GrabPress::generate_catalog_url($adv_search_params);

			$json_preview = GrabPressAPI::get_json($url_catalog);

			$list_feeds = json_decode($json_preview, true);	
			
			if(empty($list_feeds["results"])){
				GrabPress::$error = 'It appears we do not have any content matching your search criteria. Please modify your settings until you see the kind of videos you want in your feed';
			}	
			
			print GrabPress::fetch( "includes/gp-preview-ajax.php", array(
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

		static function feed_management() {
			GrabPress::log();

			$list_providers = GrabPressAPI::get_providers();			
			$providers_total = count( $list_providers );

			$list_channels = GrabPressAPI::get_channels();
			$channels_total = count( $list_channels );

			$blogusers = get_users();
			print GrabPress::fetch( 'includes/gp-feed-template.php',
				array( "form" => $_REQUEST,
					"list_providers" => $list_providers,
					"providers_total" => $providers_total,
					"list_channels" => $list_channels,
					"channels_total" => $channels_total,
					"blogusers" => $blogusers ) );
		}

		static function feed_creation_success(){
			print GrabPress::fetch( "includes/gp-feed-created-template.php" );
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
			print GrabPress::fetch( 'includes/gp-dashboard.php' , array(
				"messages" => $messages,
				"pills" => $pills,
				"resources" => $resources,
				"feeds" => $feeds,
				"watchlist" => array_splice($watchlist,0,10)
				));
		}

		static function toggle_feed_callback() {
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

		static function feed_name_unique_callback() {
			$name = $_REQUEST['name'];	

			$feeds = GrabPressAPI::get_feeds();
			$num_feeds = count( $feeds );

			foreach ( $feeds as $record_feed ) {
				if($record_feed->feed->name == $name){
					$duplicated_name = "true";
					break;
				}else{
					$duplicated_name = "false";
				}
			}

			echo $duplicated_name;
			die(); // this is required to return a proper result
		}
		
		static function insert_video_callback() {	
			$video_id = $_REQUEST['video_id'];
			$format = $_REQUEST['format'];
			$id = GrabPressAPI::get_connector_id();
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
			$objXml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);
			$settings = GrabPressAPI::get_player_settings_for_embed();
			foreach ($objXml->channel->item as $item) {   
				if($format == 'post'){
					$text = "<div id=\"grabpreview\"> 
						<p><img src='".$item->mediagroup->mediathumbnail[1]->attributes()->url."' /></p> 
						</div>
						<p>".$item->description."</p> 
						<!--more-->
						<div id=\"grabembed\">
						<p><div id=\"".$item->mediagroup->grabembed->attributes()->embed_id."\"><script language=\"javascript\" type=\"text/javascript\" src=\"http://player.".GrabPress::$environment.".com/js/Player.js?id=".$item->mediagroup->grabembed->attributes()->embed_id."&content=v".$item->guid."&width=".$settings["width"]."&height=".$settings["height"]."&tgt=".GrabPress::$environment."\"></script><div id=\"overlay-adzone\" style=\"overflow:hidden; position:relative\"></div></div></p> 
						</div>
						<p>Thanks for checking us out. Please take a look at the rest of our videos and articles.</p> <br/> 
						<p><img src='".$item->grabprovider->attributes()->logo."' /></p> 
						<p>To stay in the loop, bookmark <a href=\"/\">our homepage</a>.</p>
						<style>
						div#grabpreview {
						display:none !important;
						}
						</style>
						<script type=\"text/javascript\">
						var _gaq = _gaq || [];
						_gaq.push(['_setAccount', 'UA-31934587-1']);
						_gaq.push(['_trackPageview']);
						(function() { var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true; ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s); })();
						</script>"; 
					$post_id = wp_insert_post(array(
						"post_content" => $text,
						"post_title" => "VIDEO: ".$item->title,
						"post_type" => "post",
						"post_status" => "draft",
						"tags_input" => $item->mediagroup->mediakeywords
					));				

					$upload_dir = wp_upload_dir();
					$image_url = $item->mediagroup->mediathumbnail[1]->attributes()->url;
					$image_data = file_get_contents($image_url);
					$filename = basename($image_url);
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
					require_once(ABSPATH . 'wp-admin/includes/image.php');
					$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
					wp_update_attachment_metadata( $attach_id, $attach_data );

					set_post_thumbnail( $post_id, $attach_id );

					echo json_encode(array(
						"status" => "redirect", 
						"url" => "post.php?post=".$post_id."&action=edit"));
				}elseif($format == 'embed'){
					echo json_encode(array(
						"status" => "ok",
					 	"content" => '<div id="grabDiv'.$item->mediagroup->grabembed->attributes()->embed_id.'"><script type="text/javascript" src="http://player.'.GrabPress::$environment.'.com/js/Player.js?id='.$item->mediagroup->grabembed->attributes()->embed_id.'&content=v'.$item->guid.'&width='.$settings["width"]."&height=".$settings["height"].'&tgt='.GrabPress::$environment.'"></script><div id="overlay-adzone" style="overflow:hidden; position:relative"></div></div>'));
				}		
			}	

			die(); // this is required to return a proper result
		}

		static function get_preview_callback(){
			GrabPressViews::grabpress_preview_videos();
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
						
			die(); // this is required to return a proper result

			
		}

	}
}