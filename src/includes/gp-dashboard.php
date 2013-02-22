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
													<a class="accordion-toggle" data-guid="<?php echo $item->video->guid;?>" data-toggle="collapse" data-parent="#accordion2" href="#collapse<?php echo $i;?>">
													<?php echo $item->video->title;?>
													</a>
												</div>
												<div class="accordion-right"></div>
											</div>
											<div id="collapse<?php echo $i;?>" class="accordion-body collapse in" style="display:none;">
												<div class="accordion-inner">
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
												Schedule
											</th>
											<th>
												Health
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
										$times = array(15*60 =>  '15 mins',
													   30*60 => '30  mins',
													   45*60 => '45 mins',
													   60*60 => '01 hr',
													   120*60 => '02 hrs',
													   360*60 =>'06 hrs',
													   720*60 => '12 hrs',
													   24*60*60 => '01 day',
													   48*60*60 => '02 days',
													   72*60*60 => '03 days' );

										$num_feeds = count($feeds);
											for ( $n = 0; $n < $num_feeds; $n++ ) {
												$feed = $feeds[$n]->feed;
												$feedId = $feed->id;
												$schedule = $feed->update_frequency/60;
												
												$schedule = $times[$schedule];
										?>
										<tr id="tr-<?php echo $feedId; ?>">
											<td>
												<?php 
													echo urldecode($feed->name);
												?>	
											</td>
											<td>
												<?php echo $schedule?>
											</td>
											<?php
												if($feed->feed_health > 0.8) {
													$feed_health = "feed-health-100";
												}elseif($feed->feed_health > 0.6){
													$feed_health = "feed-health-80";
												}elseif ($feed->feed_health > 0.4) {
													$feed_health = "feed-health-60";
												}elseif ($feed->feed_health > 0.2) {
													$feed_health = "feed-health-40";
												}elseif($feed->feed_health > 0) {
													$feed_health = "feed-health-20";
												}else{
													$feed_health = "feed-health-0";
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
			        watchlist: value		        
			    };	    

		      $.post(ajaxurl, data, function(response) {	        
				    var parsedJson = $.parseJSON(response);
				    var accordion = '';
				    for(var i in parsedJson.results) {
					  if(!isNaN(i)) {
					  	accordion += '<div class="accordion-group">'
									+'<div class="accordion-heading">'
									+'	<div class="accordion-left"></div>'
									+'	<div class="accordion-center">'
									+'		<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse' + i + '">'
									+ 		parsedJson.results[i].video.title
									+'		</a>'
									+'	</div>'
									+'	<div class="accordion-right"></div>'
									+'</div>'
									+'<div id="collapse' + i + '" class="accordion-body collapse in" style="display:none;">'
									+'	<div class="accordion-inner">'
									+'	<script type="text/javascript"' 
									+'	src="http://player.'+parsedJson.environment+'.com/js/Player.js?id='+parsedJson.embed_id+'&content=v'+parsedJson.results[i].video.guid+'&tgt='+parsedJson.environment+'" />'
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

		function accordion_binding(env, embed_id){

			var active_video = null;

			$(".accordion-toggle").live("click", function(e){
				var anchor = $(this);
				var panel = $(anchor.attr("href"));
				var openPanels = $(".accordion-group .accordion-body").not(".collapse");
				// debugger;
				if(panel.hasClass("collapse")){
					var slideDownCurrent = function(panel){
						panel.slideDown(400, function(){
							var script = document.createElement( 'script' );
							script.type = 'text/javascript';
							script.src = 'http://player.'+env+'.com/js/Player.js?id='
										+embed_id+'&content=v'+anchor.data("guid")+'&width=100%&height=100%';

							panel.toggleClass("collapse");

							panel.children()[0].appendChild(script);
						});
					};
					if(openPanels.length > 0){
						openPanels.slideUp(400, function(){
							$(this).toggleClass("collapse");
							slideDownCurrent(panel);
						});
					}else{
						slideDownCurrent(panel);
					}


					
					
				}else{
					panel.slideUp(400, function(){
						panel.toggleClass("collapse");
						panel.find(".accordion-inner").empty();	
					});
				}
				
				e.preventDefault();
				return false;
			});

		}

		function init(){
			watchlist_binding();
			accordion_binding('<?php echo GrabPress::$environment; ?>', <?php echo $embed_id ?>);
			$(".nano").nanoScroller({"alwaysVisible":true});
		}
		init();

	});
</script>
