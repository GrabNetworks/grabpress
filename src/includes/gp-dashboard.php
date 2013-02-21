<?php
	$user = GrabPressAPI::get_user();
	$linked = isset($user->email);
	$publisher_status = $linked ? "account-linked" : "account-unlinked";
?>
<form method="post" action="" id="form-dashboard">

<div class="wrap" >
			<img src="<?php echo plugin_dir_url( __FILE__ ).'images/logo.png' ?>"/>

		<div id="t">
		  <div id="b">		    
		    <!--************************************************************-->
			<div class="container-fluid">
				<div class="row-fluid">
					<div class="span4 watchlist">
						<div class="tabbable panel">
							<ul class="nav nav-tabs">
								<li class="active">
									<a href="#watchlist-tab1" data-toggle="tab">Watchlist</a>
								</li>
								<!-- <li>
									<a href="#watchlist-tab2" data-toggle="tab">Featured Feed</a>
								</li>
								<li>
									<a href="#watchlist-tab3" data-toggle="tab">Hot Videos</a>
								</li> -->
							</ul>
							<div class="tab-content">
								<div class="tab-pane active" id="watchlist-tab1">
									<div class="accordion" id="accordion2">
										<?php $i = 1;?>
										<?php foreach($watchlist as $item){?>
										<div class="accordion-group">
											<div class="accordion-heading">
												<div class="accordion-left"></div>
												<div class="accordion-center">
													<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse<?php echo $i;?>">
													<?php echo $item->video->title;?>
													</a>
												</div>
												<div class="accordion-right"></div>
											</div>
											<div id="collapse<?php echo $i;?>" class="accordion-body collapse in" style="display:none;">
												<div class="accordion-inner">
														<script type="text/javascript" 
														src="http://player.<?php echo GrabPress::$environment; ?>.com/js/Player.js?id=<?php echo $embed_id ?>&content=v<?php echo $item->video->guid; ?>&tgt=<?php echo GrabPress::$environment; ?>">
														</script>
												</div>
											</div>
										</div>
										<?php $i++;
										}?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="span8" >
						<div class="row-fluid">
							<div class="span4">
								<div class="row-fluid">
									<div class="span12 messages">
										<div class="tabbable panel">
											<ul class="nav nav-tabs">
													<li class="active">
														<a href="#messages-tab1" data-toggle="tab">Messages</a>
													</li>
											</ul>
											<div class="tab-content">
												<div class="tab-pane active nano" id="messages-tab1">
													<div class="content">
														<?php foreach($messages as $msg){ ?>
														<p>
															<?php echo $msg->message->body; ?>
														</p>
														<?php }?>
													</div>
												</div>
											</div>
										</div>
									</div>										
								</div>
								<div class="row-fluid">
									<div class="span12 welcome">
										<div class="panel">
											<p>
												<?php echo html_entity_decode($pills[0]->message->body); ?>
											</p>
										</div>
									</div>
									
								</div>
							</div>
							<div class="span8 feeds">
								<?php
									$admin = get_admin_url();
									$admin_page = $admin.'admin.php?page=account';
								?>
								<a href="<?php echo $admin_page; ?>" id="btn-account-settings" class="button-primary">Account Settings<a>
								
								<div id="publisher-account-status" value="Publisher Account Status" class="<?php echo $publisher_status ?>" ></div>									
								<div class="panel">
								<h3>Feed Activity (Latest Auto-post)</h3>
								<table class="table table-hover">
									<thead>
										<tr>
											<th>
												Feed Name
											</th>
											<th>
												Providers
											</th>
											<th>
												Posts
											</th>
											<th>
												Watchlist
											</th>
											<th>
												&nbsp;
											</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$num_feeds = count($feeds);
											for ( $n = 0; $n < $num_feeds; $n++ ) {
												$feed = $feeds[$n]->feed;
												$url = array();
												parse_str( parse_url( $feed->url, PHP_URL_QUERY ), $url );
												GrabPress::_escape_params_template($url);
												$feedId = $feed->id;
												$providers = explode( ",", $url["providers"] ); // providers chosen by the user
												$channels = explode( ",", $url["categories"] );
										?>
										<tr id="tr-<?php echo $feedId; ?>">
											<td>
												<?php 
													echo urldecode($feed->name);
												?>	
											</td>
											<td>
												<?php								
													$providers_selected = count($providers);
													if($providers_selected == 1){
														if ( in_array( "", $providers ) ) {
															echo "All providers";
														}else{	
															foreach ( $list_providers as $record_provider ) {
																$provider = $record_provider->provider;
																$provider_name = $provider->name;
																$provider_id = $provider->id;											
																if(in_array( $provider_id, $providers )) {											
																	echo $provider_name;									
																}
															}
														}
													}else{
														echo $providers_selected." selected";
													}
												?> 
											</td>
											<?php
												switch ($feed->feed_health) {
												    case 0:
												        $feed_health = "feed-health-0";
												        break;
												    case 0.2:
												        $feed_health = "feed-health-20";
												        break;
												    case 0.4:
												        $feed_health = "feed-health-40";
												        break;
												    case 0.6:
												        $feed_health = "feed-health-60";
												        break;
												    case 0.8:
												        $feed_health = "feed-health-80";
												        break;
												    case 1:
												        $feed_health = "feed-health-100";
												        break;    
												}
												
											?>
											<td class="<?php echo $feed_health; ?>">
												<?php echo $feed->feed_health;?>
											</td>
											<td class="watch">												
												<?php
													if($feed->watchlist == '1'){
														echo '<input type="button" value="0" class=" watchlist-check watch-on" id="watchlist-check-'.$feedId.'" >';
													}else{
														echo '<input type="button" value="1" class="watchlist-check watch-off" id="watchlist-check-'.$feedId.'" >';
													}													
												?>		
											</td>
											<td>
												<?php 
												$text_edit_button = "edit";
												if(isset($_GET['action']) && ($_GET['action']=='edit-feed') && ($_GET['feed_id']==$feedId)){ 
													echo $text_edit_button;
												 }else{ ?>				
												<a href="admin.php?page=autoposter&action=edit-feed&feed_id=<?php echo $feedId; ?>" id="btn-update-<?php echo $feedId; ?>" class="<?php echo $class_edit_button; ?> btn-update-feed">						
													<?php echo $text_edit_button; ?>
												</a>
												<?php } ?>
												<i class="icon-pencil"></i>
											</td>
										</tr>
										<?php
											}
										?>
									</tbody>
								</table>
								</div>
							</div>
						</div>
						<div clas="row-fluid">
							<div class="span12 faq">
								<div class="tabbable panel">
									<ul class="nav nav-tabs">
										<li class="active">
											<a href="#faq-tab1" data-toggle="tab">Resources</a>
										</li>
									</ul>
									<div class="tab-content">
										<div class="tab-pane active" id="faq-tab1">
											<p> Placeholder </p>
											<?php foreach($resources as $msg){ ?>
											<p>
												<?php echo html_entity_decode($msg->message->body); ?>
											</p>
											<?php }?>
										</div>
									</div>
								</div>
							</div>
						</div><!--Body content-->
					</div>
				</div>
			</div>
		    <!--***********************************************************-->		   
		  </div>
		</div>
	
</div>

</form>
<script type="text/javascript">
	jQuery(function($){	

		function watchlist_binding(){
			$('.watchlist-check').bind('click', function(e){

	        var id = this.id.replace('watchlist-check-','');
	        var watchlist_check = $(this);

	        if(watchlist_check.val() == 1) {
	          var watchlist = 1;
	        }else{
	          var watchlist = 0;	    
	        }  

	        var data = {
		        action: 'gp_toggle_watchlist',
		        feed_id: id,
		        watchlist: watchlist		        
		    };	    

	      $.post(ajaxurl, data, function(response) {
		        //console.log('Got this from the server: ' + response);		        
			    var parsedJson = $.parseJSON(response);
			    var accordion = '';
			    for(var i in parsedJson) {
				  if(!isNaN(i)) {
				  	accordion += '<div class="accordion-group">'
								+'<div class="accordion-heading">'
								+'	<div class="accordion-left"></div>'
								+'	<div class="accordion-center">'
								+'		<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse' + i + '">'
								+'		'+parsedJson[i].video.title+''
								+'		</a>'
								+'	</div>'
								+'	<div class="accordion-right"></div>'
								+'</div>'
								+'<div id="collapse' + i + '" class="accordion-body collapse in" style="display:none;">'
								+'	<div class="accordion-inner">'
								+'	Anim pariatur cliche...'
								+'	</div>'
								+'</div>'
								+'</div>';
				  }
				}
				$('#accordion2').html(accordion);	

				if(watchlist_check.val() == 1) {
		          watchlist_check.val('0');
		          watchlist_check.addClass('watch-on').removeClass('watch-off');
		        }else{
		          watchlist_check.val('1');
		          watchlist_check.addClass('watch-off').removeClass('watch-on');	    
		        } 

		   });
	      			
	      }); 	

		};

		function accordion_binding(){
			$(".accordion-toggle").live("click", function(e){

				if($(this).data("toggle") == "collapse"){
					var panel = $($(this).attr("href"));
					if(panel.css("display") =="none"){
						panel.slideDown();
					}else{
						panel.slideUp();
					}
				}
				e.preventDefault();
				return false;
			});

		}

		function init(){
			watchlist_binding();
			accordion_binding();
			$(".nano").nanoScroller({"alwaysVisible":true});
		}
		init();

	});
</script>
