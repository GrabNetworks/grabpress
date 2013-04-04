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
										<?php $i = 1;
										if(empty($watchlist)){  										
										?>
										<div class="accordion-group">
											<div class="accordion-heading">
												<div class="accordion-left"></div>
												<div class="accordion-center">
													&nbsp;
												</div>
												<div class="accordion-right"></div>
											</div>
											<div id="collapse<?php echo $i;?>" class="accordion-body" style="height:95px;" >
												<div class="accordion-inner" >
													<span class="accordion-warning">Add a feed to your watch list in the Feed Activity panel</span>
												</div>
											</div>
										</div>
										<?php	
										}else{										
										foreach($watchlist as $item){ ?>
										<div class="accordion-group">
											<div class="accordion-heading">
												<div class="accordion-left"></div>
												<div class="accordion-center">
													<a class="accordion-toggle" data-guid="v<?php echo $item->video->guid;?>" data-toggle="collapse" data-parent="#accordion2" href="#collapse<?php echo $i;?>">
													<?php echo $item->video->title;?>
													</a>
												</div>
												<div class="accordion-right"></div>
											</div>
											<div id="collapse<?php echo $i;?>" class="accordion-body collapse in" style="<?php print ($i==1)?"":"display:none;";?>">
												<div class="accordion-inner">
												</div>
											</div>
										</div>
										<?php $i++;
										 }
										} // else
										?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="span8 right-pane" >
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
															<?php echo html_entity_decode($msg->message->body); ?>
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
											<div class="tab-content">
												<div class="tab-pane active nano" id="messages-tab1">
													<div class="content">
													 <?php
														$num_feeds = count($feeds);
		 if($publisher_status == "account-unlinked"){
                 	$create = isset($_REQUEST[ 'page']) && $_REQUEST[ 'page'] == 'account' && isset($_REQUEST[ 'action']) &&  $_REQUEST[ 'action'] == 'create' ? 'Create' : '<a href="admin.php?page=gp-account&action=create">Create</a>';
                        $link =  isset($_REQUEST[ 'page']) && $_REQUEST[ 'page'] == 'account' && isset($_REQUEST[ 'action']) &&  $_REQUEST[ 'action'] == 'default' ? 'link an existing' : '<a href="admin.php?page=gp-account&action=default">link an existing</a>';                                                 echo "Want to earn money?" . $create . " or " . $link. " Grab Publisher account.";
		}
		elseif($num_feeds == 0){
			$admin = get_admin_url();
			$admin_page = $admin.'admin.php?page=gp-autoposter';
			$here = '<a href="'.$admin_page.'">here</a>';
			echo "Thank you for activating GrabPress. Try creating your first Autoposter feed " . $here . ".";										      }
														else{
																$p = count($pills);
																$p--;
																$r = rand(0, $p);
																echo html_entity_decode($pills[$r]->message->body);
														}
												?>	
													</div>
												</div>
											</div>
										</div>
									</div>
									
								</div>
							</div>
							<div class="feeds">
								<?php
									$admin = get_admin_url();
									$admin_page = $admin.'admin.php?page=gp-account';
								?>								
								<div id="btn-account-settings">
								<div class="accordion-left">&nbsp;</div>
								<div class="accordion-center">
									<a href="<?php echo $admin_page; ?>" >Account Settings</a>
								</div>
								<div class="accordion-right">&nbsp;</div>
							</div>
								<div id="publisher-account-status" value="Publisher Account Status" class="<?php echo $publisher_status ?>" ></div>
								<div class="panel">
								<h3>Feed Activity (Latest Auto-post)</h3>
								<a href="#" id="help">help</a>
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
													   60*60 => '1 hr',
													   120*60 => '2 hrs',
													   360*60 =>'6 hrs',
													   720*60 => '12 hrs',
													   24*60*60 => '1 day',
													   48*60*60 => '2 days',
													   72*60*60 => '3 days' );

										$num_feeds = count($feeds);
											for ( $n = 0; $n < $num_feeds; $n++ ) {
												$feed = $feeds[$n]->feed;
												$feedId = $feed->id;
												$schedule = $feed->update_frequency;
												$schedule = $times[$schedule];
												$rowColor = ($n % 2) == 1 ? "odd" : "even";
										?>
										<tr id="tr-<?php echo $feedId; ?>" class="<?php echo $rowColor; ?>">
											<td>
												<?php 
													echo urldecode($feed->name);
												?>	
											</td>
											<td>
												<?php echo $schedule?>
											</td>
											<?php
												$feed_health_value = $feed->submissions . "/" . $feed->posts_per_update;
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
												<?php echo $feed_health_value;?>
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
												<a href="admin.php?page=gp-autoposter&action=edit-feed&feed_id=<?php echo $feedId; ?>" id="btn-update-<?php echo $feedId; ?>" class="btn-update-feed">						
													edit
												</a>
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
											<p> Read more about GrabMedia and our GrabPress and Autoposter technology:</p>
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
		var active_video = null;
		function onload_openvideo(embed_id){
			if($(".accordion-warning").length == 1){
				return false;
			}
			var embed = "";
			var anchor = $($(".accordion-toggle[href='#collapse1']")[0]);
			embed = '<div id="gcontainer'+embed_id+'" style="height:100%;"><div id="grabDiv'+embed_id+'"></div></div>';
			$("#collapse1").find(".accordion-inner").append(embed);
			active_video = new com.grabnetworks.Player({
				"id": embed_id,
				"width": "100%",
				"height": "100%",
				"content": anchor.data("guid"),
				"autoPlay": false
			});
			active_video.showEmbed();
			$("#collapse1").toggleClass("collapse");
		}
		function watchlist_binding(embed_id){
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
					var parsedJson = $.parseJSON(response);
					var accordion = '';
					if (parsedJson.results != ''){				    	
						for(var i in parsedJson.results) {
						  if(!isNaN(i)) {
						  	var style = "";
						  	var embed = "";
						  	var collapse = "collapse";
						  	if(i != 0){
						  		style = 'style="display:none;"';
						  	}else{
						  		embed = '<div id="gcontainer'+embed_id+'" style="height:100%;"><div id="grabDiv'+embed_id+'"></div></div>';
						  		collapse = "";
						  	}

							accordion += '<div class="accordion-group">'
									+'<div class="accordion-heading">'
									+'	<div class="accordion-left"></div>'
									+'	<div class="accordion-center">'
									+'		<a class="accordion-toggle" data-guid="v'+parsedJson.results[i].video.guid+'" data-toggle="collapse" data-parent="#accordion2" href="#collapse' + (i+1) + '">'
									+ 		parsedJson.results[i].video.title
									+'		</a>'
									+'	</div>'
									+'	<div class="accordion-right"></div>'
									+'</div>'
									+'<div id="collapse' + (i+1) + '" class="accordion-body '+collapse+' in" '+style+'>'
									+'	<div class="accordion-inner">'
									+ embed
									+'	</div>'
									+'</div>'
									+'</div>';
						  }
						}
					  		$('#accordion2').html(accordion);
							active_video = new com.grabnetworks.Player({
							
								"id": embed_id,
								"width": "100%",
								"height": "100%",
								"content": parsedJson.results[0].video.guid,
								"autoPlay": false
							});
							$(window).resize();
							$("#gcontainer"+embed_id+" object").css("visibility","visible");

					}else{
						accordion += '<div class="accordion-group">'
										+'<div class="accordion-heading">'
										+'	<div class="accordion-left"></div>'
										+'	<div class="accordion-center">'										
										+'			&nbsp;'
										+'	</div>'
										+'	<div class="accordion-right"></div>'
										+'</div>'
										+'<div id="collapse1" class="accordion-body" style="height:95px;">'
										+'	<div class="accordion-inner">'
										+'		<span class="accordion-warning">Add a feed to your watch list in the Feed Activity panel</span>'
										+'	</div>'
										+'</div>'
										+'</div>';
					$('#accordion2').html(accordion);
					}
					
					

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
			var accordion_lock = false;
			$("#form-dashboard").parent().css("margin", "-10px 0 0 -18px");

			$(".accordion-toggle").live("click", function(e){
				if(accordion_lock){
					e.preventDefault();
					return false;
				}
				
				var anchor = $(this);
				var panel = $(anchor.attr("href"));
				var openPanels = $(".accordion-group .accordion-body").not(".collapse");
				// debugger;
				if(panel.hasClass("collapse")){
					accordion_lock = true;
					var monitor = 0;
					var slideDownCurrent = function(panel, onfinish){
						var embed = $("#gcontainer"+embed_id).detach();
						panel.slideDown(400,'linear', function(){
							panel.find('.accordion-inner').append( embed );
							panel.toggleClass("collapse");
							monitor++;
							onfinish(monitor);
						});
					};
					if(openPanels.length > 0){
						slideDownCurrent(panel, function(){
							setTimeout(function(){
								if(monitor == 2){
									active_video.loadNewVideo(anchor.data("guid"));
									accordion_lock = false;
							}}, 100);
						});
						openPanels.slideUp(400,'linear', function(){
							active_video.hideEmbed();
							console.log("hide embed");
							$(this).toggleClass("collapse");
							monitor++;
							
						});
					}else{
						slideDownCurrent(panel, function(){accordion_lock=false;});
					}

				}
				
				e.preventDefault();
				return false;
			});

		}

		function resize_accordion(){
			var width = jQuery(jQuery(".accordion-center")[0]).css("width");
			width = width.replace("px","");
			jQuery(".accordion-inner").css("height", width* 0.5625 )
		}

		function init(){
			watchlist_binding(<?php echo $embed_id ?>);
			accordion_binding('<?php echo GrabPress::$environment; ?>', <?php echo $embed_id ?>);
			onload_openvideo(<?php echo $embed_id ?>);
			$(".nano").nanoScroller({"alwaysVisible":true});

			$(window).resize(resize_accordion).resize();
			$("#message").hide();//hack
			
		}

		$("#help").simpletip({
		  	 content: 'Health displays “results/max results” per the latest feed update. <br/> Feeds in danger of not producing updates display in red or orange, feeds at risk of not producing updates display in yellow, and healthy feeds display in green.  <br /><br />', 
		  	 fixed: true,
		  	 position: [155, 40]
		});

		init();

	});
</script>
