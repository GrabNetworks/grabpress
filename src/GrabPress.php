<?php
/*
Plugin Name: GrabPress
Plugin URI: http://www.grab-media.com/publisher/solutions/autoposter
Description: Configure Grab's AutoPoster software to deliver fresh video direct to your Blog. Create or use an existing Grab Media Publisher account to get paid!
Version: 0.4.1b30
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

if( ! class_exists( 'GrabPress' ) ) {
	class GrabPress{
		static $api_key;
		static $invalid = false;
		static $environment = 'grabqa'; // or 'grabqa'
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
						"connector" => array(
							"connector_type_id" => $connector_type_id,
							"destination_name" => get_bloginfo( 'name' ),
							"destination_address" => $rpc_url,
							"username" =>'grabpress',
							"password" => self::$api_key,
							"custom_options" => array(
								"blog_id" => $blog_id
							)
						)
					);

					$connector_json = self::apiCall("POST",  '/connectors?api_key='.self::$api_key, $connector_post); 
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
				$providersList = implode(",", $providers); 
				$providersListTotal = count($providers); // Total providers chosen by the user
				$providers_total = $_POST['providers_total']; // Total providers from the catalog list
				if($providersListTotal == $providers_total){
					$providersList = "";
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
					$cats = "Uncategorized";
				}					
				$schedule = $_POST['schedule'];
				if($schedule == "12 hrs"){
					$update_frequency = 60 * $schedule;
				}else{
					$update_frequency = 60 * 24 * $schedule;
				}					
				if($_POST['click-to-play'] === null){
					$auto_play = "1";
				}else{
					$auto_play = "0";	
				}

				$author_id = (int)$_POST['author'];	

				$post_data = array(
					"feed" => array(
						"name" => $_POST[ 'channel' ],
						"posts_per_update" => $_POST[ 'limit' ],
						"url" => $url,
						"custom_options" => array(
							"category" => $cats,
							"publish" => (bool)( $_POST[ 'publish' ] ),
							"author_id" => $author_id
						),						
						"update_frequency" => $update_frequency,
						"auto_play" => $auto_play
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
		$response = self::apiCall("PUT", "/connectors/" . $connector_id . "/deactivate?api_key=".GrabPress::$api_key);
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
			<?php 				
				// List of all providers
				$json_provider = GrabPress::get_json('http://catalog.'.GrabPress::$environment.'.com/catalogs/1/providers?limit=-1');
				$list_provider = json_decode($json_provider);
				$providers_total = count($list_provider);
				$blogusers = get_users();
			?>
			<script language = "JavaScript" type = "text/javascript">
				function validateRequiredFields() {					
					var category =  jQuery('#channel-select').val();
					if(category == ''){						
						alert("Please select at least one video channel");					  
						e.preventDefault();
					}else if(jQuery("#provider-select :selected").length == 0){						
						alert("Please select at least one provider");					  
						e.preventDefault();
					}else {
						return true;
					}				
				}
				( function ( global, $ ) {
					global.previewVideos = function () {
						var keywords =  $( '#keyword-input' ).val();
						var category =  $( '#channel-select').val();
						var limit =  $( '#limit-select').val();
						var isValid = validateRequiredFields();
						var environment = "<?php echo GrabPress::$environment; ?>";
						if(isValid){							
							window.open( 'http://catalog.'+environment+'.com/catalogs/1/videos/search.mrss?keywords_and=' + keywords + '&categories=' + category );						
						}						
					}	
				} )( window, jQuery );	

			    function toggleButton(feedId) {
					jQuery('#btn-update-' + feedId).css({"visibility":"visible"});
				}

				function deleteFeed(id){
					var form = jQuery('#form-'+id);
					var action = jQuery('#action-'+id);
					var answer = confirm('Are you sure you want to delete the feed? You will no longer receive automatic posts with the specified settings.');
  					if(answer){
  						action.val("delete");
						form.submit();
  					} else{
  						return false;
  					}
				}
				
				function previewFeed(id) {
					var keywords =  jQuery( '#keywords_and_'+id ).val();
					var category =  jQuery( '#channel-select-'+id).val();	
					var environment = "<?php echo GrabPress::$environment; ?>";
					window.open( 'http://catalog.'+environment+'.com/catalogs/1/videos/search.mrss?keywords_and=' + keywords + '&categories=' + category );																				
				}

				var multiSelectOptions = {
				  	 noneSelectedText:"Select providers",
				  	 selectedText:function(selectedCount, totalCount){
						if (totalCount==selectedCount){
				  	 		return "All providers selected";
				  	 	}else{
				  	 		return selectedCount + " providers selected of " + totalCount;
				  	 	}
				  	 }
				};

				var multiSelectOptionsCategories = {
				  	 noneSelectedText:"Select categories",
				  	 selectedText: "# of # selected"
				};			

				function showButtons() {
					var isValid = validateRequiredFields();
					if(isValid){
						jQuery('.hide').show();	
					}								
				}

				jQuery(function(){
				  jQuery('#provider-select option').attr('selected', 'selected');

				  jQuery("#provider-select").multiselect(multiSelectOptions, {
					 checkAll: function(e, ui){
				  	 	showButtons();      
					 }
				  }).multiselectfilter();	  		  

				  jQuery(".provider-select-update").multiselect(multiSelectOptions, {
				  	 uncheckAll: function(e, ui){
				  	 	id = this.id.replace('provider-select-update-',''); 	 	
				  	 	toggleButton(id);      
					 },
					 checkAll: function(e, ui){
				  	 	id = this.id.replace('provider-select-update-','');	
				  	 	toggleButton(id);      
					 }
				   }).multiselectfilter();

				  jQuery('#create-feed-btn').bind('click', function(e){
				  	var isValid = validateRequiredFields();
				  	var form = jQuery('#form-create-feed');
					if(isValid){				
						form.submit();
					}else{
						e.preventDefault();
					}
				  });

				  jQuery('.btn-update').bind('click', function(e){
				    id = jQuery(this).attr('name');		  
					var form = jQuery('#form-'+id);
					var action = jQuery('#action-'+id);
					if(jQuery("#provider-select-update-" + id + " :selected").length == 0){						
						alert("Please select at least one provider");					  
						e.preventDefault();
					}else{
						action.val("modify");
						form.submit();
					}
				  });

				  jQuery("#cat").multiselect(multiSelectOptionsCategories,
				  {
				  	header:false
				  });

				  jQuery(".postcats").multiselect(multiSelectOptionsCategories, {
				  	header:false,
				  	uncheckAll: function(e, ui){
				  	 	id = this.id.replace('postcats-','');	
				  	 	toggleButton(id);      
					 },
					 checkAll: function(e, ui){
				  	 	id = this.id.replace('postcats-','');
				  	 	toggleButton(id);      
					 }
				  }).multiselectfilter();
				  
				  jQuery(".channel-select").selectmenu();
				  jQuery(".schedule-select").selectmenu();
				  jQuery(".limit-select").selectmenu();
				  jQuery(".author-select").selectmenu();			
				
				});
				
			</script>
			<?php 
				$rpc_url = get_bloginfo('url').'/xmlrpc.php';
				$connector_id = GrabPress::get_connector_id();		
			?>
			<form method="post" action="" id="form-create-feed">
	            		<?php settings_fields('grab_press');//XXX: Do we need this? ?>
	            		<?php $options = get_option('grab_press'); //XXX: Do we need this? ?>
	            		<table class="form-table grabpress-table">
	                		<tr valign="top">
						<th scope="row">API Key</th>
			                	<td>
							<?php echo get_option( 'grabpress_key' ); ?>
						</td>
					</tr>
						<tr>
							<th scope="row">Video Channel</th>
							<td>
								<select  style="<?php GrabPress::outline_invalid() ?>" name="channel" id="channel-select" class="channel-select" onchange="showButtons()">
									<option selected = "selected" value = "">Choose One</option>
									<?php 	
										$json = GrabPress::get_json('http://catalog.'.self::$environment.'.com/catalogs/1/categories');
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
								<input type="text" name="keyword" id="keyword-input" class="ui-autocomplete-input" /> 
								<span class="description">Enter search keywords (e.g. <b>celebrity gossip</b>)</span>
							</td>
		        		        </tr>
		        		        <tr valign="top">
							<th scope="row">Max Results</th>
		        		           	<td>
								<select name="limit" id="limit-select" class="limit-select" style="width:60px;" >
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
								<select name="schedule" id="schedule-select" class="schedule-select" style="width:90px;" >
									<?php $times = array( '12 hrs', '01 day', '02 days', '03 days');
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
						<tr valign="top">
						<th scope="row">Click-to-Play Video</th>
						<td>
							<input type="checkbox" value="1" name="click-to-play" id="click-to-play" />
							<span class="description">Check this to wait for the reader to click to start the video (this is likely to result in fewer ad impressions) <a href="#">learn more</a></span>
						</td>
					</tr>
		        		<tr valign="top">
						<th scope="row">Post Category</th>
						<td>
							<?php 							
								$select_cats = wp_dropdown_categories( array( 'echo' => 0, 'taxonomy' => 'category', 'hide_empty' => 0 ) );
								$select_cats = str_replace( "name='cat' id=", "name='category[]' multiple='multiple' id=", $select_cats );
								echo $select_cats; 							
							?>
							<span class="description">Select a category for your autoposts</span>
						</td>
					</tr>
					</tr>
		        		<tr valign="top">
						<th scope="row">Post Author</th>
						<td>
							<select name="author" id="author_id" class="author-select" >
								<!--<option selected="selected" value = "">Choose One</option>-->
								<?php
									foreach ($blogusers as $user) {
										$author_name = $user->display_name;
										$author_id = $user->ID;										
								   		echo '<option value = "'.$author_id.'">'.$author_name.'</option>\n';
									} 
								?>
							</select>
							<span class="description">Select the default Wordpress user to credit as author of the posts from this feed</span>
						</td>
					</tr>
					</tr>
		        		<tr valign="top">
						<th scope="row">Providers</th>
						<td>
							<input type="hidden" name="providers_total" value="<?php echo $providers_total; ?>" />	
							<select name="provider[]" id="provider-select" class="multiselect" multiple="multiple" style="<?php GrabPress::outline_invalid() ?>" onchange="showButtons()" >
								<!--<option selected="selected" value = "">Choose One</option>-->
								<?php
									foreach ($list_provider as $record_provider) {
								   		$provider = $record_provider->provider;
										$provider_name = $provider->name;
										$provider_id = $provider->id;
								   		echo '<option value = "'.$provider_id.'">'.$provider_name.'</option>\n';
									} 
								?>
							</select> *
							<span class="description">Select providers for your autoposts</span>
						</td>
					</tr>
					<tr valign="top">
						<td>
							<input type="button" onclick="previewVideos()" class="button-secondary hide" value="<?php _e('Preview Feed') ?>" id="btn-preview-feed" />
						</td>
						<td>
							<span class="description">Click to preview which videos will be autoposted from this feed</span>
						</td>
					</tr>
					<tr valign="top">
						<td>
							<input type="submit" class="button-primary hide" value="<?php _e('Create Feed') ?>" id="create-feed-btn" />
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
									$times = array( '12 hrs', '01 day', '02 days', '03 days' );
									$values = array(  720, 1440, 2880, 4320 );
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

<?php			}
/* End HTML */
		}//if
	}//class
}
function dispatcher($params){
	if( count($_POST) > 0 ) {
		switch ($params['action']){
			case 'update':
					if( GrabPress::validate_key() && $_POST[ 'channel' ] != '' && $_POST[ 'provider' ] != '' ) {
							GrabPress::create_feed();					
					}else {
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
					$categories = rawurlencode($_POST[ 'channel' ]);
					$providers = $_POST['provider'];
					$providersList = implode(",", $providers);
					$providersListTotal = count($providers); // Total providers chosen by the user
					$providers_total = $_POST['providers_total']; // Total providers from the catalog list
					if($providersListTotal == $providers_total){
						$providersList = "";
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
						$cats = "Uncategorized";
					}
					$schedule = $_POST['schedule'];
					if($schedule == "12 hrs"){
						$update_frequency = 60 * $schedule;
					}else{
						$update_frequency = 60 * 24 * $schedule;
					}	
					if($_POST['click-to-play'] === null){
						$auto_play = "1";
					}else{
					        $auto_play = "0";	
					}

					$author_id = (int)$_POST['author'];

					$post_data = array(
						"feed" => array(
							"active" => $active,
							"name" => $_POST[ 'channel' ],
							"posts_per_update" => $_POST[ 'limit' ],
							"url" => $url,
							"custom_options" => array(
								"category" => $cats,
								"publish" => (bool)( $_POST[ 'publish' ] ),
								"author_id" => $author_id
							),
							"auto_play" => (bool)( $_POST['auto_play'] ),
							"update_frequency" => $update_frequency,
							"auto_play" => $auto_play							
						)
					);	

					GrabPress::apiCall("PUT", "/connectors/" . $connector_id . "/feeds/".$feed_id."?api_key=".GrabPress::$api_key, $post_data);	
					break;				
		}
	}
}

dispatcher($_REQUEST);

add_action('admin_print_styles', 'WPWall_StylesAction');
function WPWall_StylesAction()
{
	// Plugin url
	$wp_wall_plugin_url = trailingslashit( get_bloginfo('wpurl') ).PLUGINDIR.'/'. dirname( plugin_basename(__FILE__) );

	// CSS files
	
	wp_enqueue_style('jquery-css', $wp_wall_plugin_url.'/grabpress.css');
	wp_enqueue_style('jquery-ui-theme', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css" ');

}

add_action('wp_print_scripts', 'WPWall_ScriptsAction');
function WPWall_ScriptsAction()
{
	// Plugin url
	$wp_wall_plugin_url = trailingslashit( get_bloginfo('wpurl') ).PLUGINDIR.'/'. dirname( plugin_basename(__FILE__) );

	// jQuery files

	wp_enqueue_script("jquery-ui","https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/jquery-ui.min.js"); 
	wp_enqueue_script('jquery-ui-filter', $wp_wall_plugin_url.'/src/jquery.multiselect.filter.min.js');	
	wp_enqueue_script('jquery-prettify', $wp_wall_plugin_url.'/src/assets/prettify.js');
	wp_enqueue_script('jquery-ui-multiselect', $wp_wall_plugin_url.'/src/jquery.multiselect.min.js');

	wp_enqueue_script('jquery-uicore', $wp_wall_plugin_url.'/ui/jquery.ui.core.js');
	wp_enqueue_script('jquery-uiwidget', $wp_wall_plugin_url.'/ui/jquery.ui.widget.js');
	wp_enqueue_script('jquery-uiposition', $wp_wall_plugin_url.'/ui/jquery.ui.position.js');

	wp_enqueue_script('jquery-ui-selectmenu', $wp_wall_plugin_url.'/ui/jquery.ui.selectmenu.js');
}

register_activation_hook( __FILE__, array( 'GrabPress', 'setup') );
register_uninstall_hook(__FILE__, array( 'GrabPress', 'delete_connector') );
if(! function_exists( 'grabpress_plugin_menu')){
	function grabpress_plugin_menu() {
		add_menu_page('GrabPress', 'GrabPress', 'manage_options', 'grabpress', array( 'GrabPress', 'grabpress_plugin_options' ), plugin_dir_url( __FILE__ ).'g.png', 10 );
		add_submenu_page( 'grabpress', 'AutoPoster', 'AutoPoster', 'publish_posts', 'autoposter', array( 'GrabPress', 'grabpress_plugin_options' ));
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
