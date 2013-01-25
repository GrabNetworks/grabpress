<?php
	$feeds = GrabPressAPI::get_feeds();
	$num_feeds = count( $feeds );
?>
<form method="post" action="" id="form-dashboard">

<div class="wrap" >
			<img src="http://grab-media.com/corpsite-static/images/grab_logo.jpg"/>
			<h2>GrabPress: Find a Video in our Catalog</h2>
			<p>Grab video content delivered fresh to your blog <a href="#" onclick='return false;' id="how-it-works">how it works</a></p>

		<div id="t">
		  <div id="b">		    
		    <!--************************************************************-->
			<div class="container-fluid">
				<div class="row-fluid">
					<div class="span4 watchlist">
						<div class="tabbable">
							<ul class="nav nav-tabs">
								<li class="active">
									<a href="#watchlist-tab1" data-toggle="tab">Watchlist</a>
								</li>
								<li>
									<a href="#watchlist-tab2" data-toggle="tab">Featured Feed</a>
								</li>
								<li>
									<a href="#watchlist-tab3" data-toggle="tab">Hot Videos</a>
								</li>
							</ul>
							<div class="tab-content">
								<div class="tab-pane active" id="watchlist-tab1">
									<div class="accordion" id="accordion2">
										<div class="accordion-group">
											<div class="accordion-heading">
												<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
												Collapsible Group Item #1
												</a>
											</div>
											<div id="collapseOne" class="accordion-body collapse in">
												<div class="accordion-inner">
												Anim pariatur cliche...
												</div>
											</div>
										</div>
										<div class="accordion-group">
											<div class="accordion-heading">
												<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">
											Collapsible Group Item #2
												</a>
											</div>
											<div id="collapseTwo" class="accordion-body collapse">
												<div class="accordion-inner">
												Anim pariatur cliche...
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane" id="watchlist-tab2">
									<p>
											Howdy, I'm in Section 2.
									</p>
								</div>
								<div class="tab-pane" id="watchlist-tab3">
									<p>
											Howdy, I'm in Section 3.
									</p>
								</div>
							</div>
						</div>
					</div>
					<div class="span8">
						<div clas="row-fluid">
							<div class="span3 messages">
								<ul class="nav nav-tabs">
										<li class="active">
											<a href="#messages-tab1" data-toggle="tab">messages</a>
										</li>
										<li>
											<a href="#messages-tab2" data-toggle="tab">read</a>
										</li>
										<li>
											<a href="#messages-tab3" data-toggle="tab">allofthem</a>
										</li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane active" id="messages-tab1">
										<p>
											I'm in Section 1.
										</p>
									</div>
									<div class="tab-pane" id="messages-tab2">
										<p>
											Howdy, I'm in Section 2.
										</p>
									</div>
									<div class="tab-pane" id="messages-tab3">
										<p>
											Howdy, I'm in Section 3.
										</p>
									</div>
								</div>
							</div>
							<div class="span5 feeds">
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
												videos
											</th>
											<th>
												watchlist
											</th>
											<th>
												edit
											</th>
										</tr>
									</thead>
									<tbody>
										<?php
											for ( $n = 0; $n < $num_feeds; $n++ ) {
												$feed = $feeds[$n]->feed;
												$url = array();
												parse_str( parse_url( $feed->url, PHP_URL_QUERY ), $url );
												GrabPress::_escape_params_template($url);
												$feedId = $feed->id;
												$providers = explode( ",", $url["providers"] ); // providers chosen by the user
												$channels = explode( ",", $url["categories"] );
												//echo "FEED: "; var_dump($feed); echo "<br/><br/>";
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
											<td>
												25
											</td>
											<td>
												<?php 												
													if(isset($_GET['action'])=='edit-feed'){
														echo $checked = ( $feed->watchlist  ) ? 'Yes' : 'No'; 
												 	}else{ 
														$checked = ( $feed->watchlist  ) ? 'checked = "checked"' : '';
														echo '<input '.$checked.' type="checkbox" value="1" name="watchlist" id="watchlist-check-'.$feedId.'" />';
													} 
													//echo "<br/>";
													/*
													if(isset($_GET['action'])=='edit-feed'){
														echo $checked = ( $feed->active  ) ? 'Yes' : 'No'; 
												 	}else{ 
														$checked = ( $feed->active  ) ? 'checked = "checked"' : '';
														echo '<input '.$checked.' type="checkbox" value="1" name="active" class="active-check" id="active-check-'.$feedId.'" />';
													} 
													*/
												?>
												<i class="icon-eye-open"></i>
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
						<div clas="row-fluid">
							<div class="span8 faq">
								<ul class="nav nav-tabs">
									<li class="active">
										<a href="#faq-tab1" data-toggle="tab">Resources</a>
									</li>
									<li>
										<a href="#faq-tab2" data-toggle="tab">Tutorials</a>
									</li>
									<li>
										<a href="#faq-tab3" data-toggle="tab">Feedback</a>
									</li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane active" id="faq-tab1">
										<p>
											Resources
										</p>
									</div>
									<div class="tab-pane" id="faq-tab2">
										<p>
											Tutorials
										</p>
									</div>
									<div class="tab-pane" id="faq-tab3">
										<p>
											Feedback
										</p>
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

		$('.watchlist-check').bind('click', function(e){

        var id = this.id.replace('watchlist-check-','');
        var watchlist_check = $(this);

        if(watchlist_check.is(':checked')) {
            var watchlist = 1;           
            $('#tr-'+id+' td').css("background-color","#FFE4C4");
        }else{
          var watchlist = 0;
          $('#tr-'+id+' td').css("background-color","#DCDCDC");         
        }       

        /*
        var data = {
	        action: 'my_action',
	        feed_id: id,
	        active: active
	    };
	    */
/*
      $.post(ajaxurl, data, function(response) {
	        //alert('Got this from the server: ' + response);
	        var substr = response.split('-');
	        var num_active_feeds = substr[0];
	        var num_feeds =  substr[1];
	        var noun = 'feed';  
	        var autoposter_status = 'ON';
	        var feeds_status = 'active';    

	        
	        //if( (num_active_feeds == 1) || (num_feeds == 1) ){
	        //  noun = 'feed';  
	        //}else 
	        if(num_active_feeds == 0){
	          var autoposter_status = 'OFF';
	          var feeds_status = 'inactive';
	          response = '';          
	          num_active_feeds = num_feeds;
	          if(num_feeds > 1){
	            noun = noun + 's';
	          }         
	        }else if( (num_active_feeds == 1) ){
	          noun = 'feed';  
	        }else{
	          noun = noun + 's';
	        }
	        
	        $('#num-active-feeds').text(num_active_feeds);  
	        $('#noun-active-feeds').text(noun);

	        $('#autoposter-status').text(autoposter_status);
	        $('#feeds-status').text(feeds_status);
	      });
		*/
      }); 

	});
</script>
