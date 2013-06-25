<!--[if IE]>
   
   <style type="text/css">

   .reveal-modal-bg { 
       background:transparent;
       filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#70000000,endColorstr=#70000000); 
       zoom: 1;
    } 

    </style>

<![endif]-->
<form method="post" action="" id="form-dashboard">
<input type="hidden" name="environment" value="<?php echo Grabpress::$environment;?>" id ="environment"/>
<input type="hidden" name="embed_id" value="<?php echo $embed_id;?>" id ="embed_id"/>
<div class="wrap" >		
		<div id="t">
		  <div id="b">		    
			<!--************************************************************-->
			<div class="container-fluid">
				<div class="row-fluid watchlist-wrap">                                    
					<div class="span4 watchlist">
                                            <img src="<?php echo plugin_dir_url( __FILE__ ).'images/logo.png' ?>"/>
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
													<a class="accordion-toggle feed_title" data-guid="v<?php echo $item->video->guid;?>" data-toggle="collapse" data-parent="#accordion2" href="#collapse<?php echo $i;?>">
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
										<div class="tabbable panel" id="messages-tabs">
											<ul class="nav nav-tabs">
                                                                                            <li >
                                                                                                <a href="#messages-tab1">Messages</a>
                                                                                            </li>                                                                                            
                                                                                            <?php
                                                                                            if (!empty($alerts) || !empty($errors)){
                                                                                            ?>
                                                                                            <li>
                                                                                                <a href="#messages-tab2">Alerts</a>
                                                                                            </li>
                                                                                            <?php
                                                                                            }
                                                                                            ?>                                                                                            
											</ul>
											<!--<div class="tab-content">-->
												<div class="tab-pane active nano" id="messages-tab1">
													<div class="content">
														<?php foreach($messages as $msg){ ?>
														<p>
															<?php echo html_entity_decode($msg->message->body); ?>
														</p>
														<?php }?>
													</div>
												</div>
<?php
 if (!empty($alerts) || !empty($errors)){
?>
                                                                                                <div class="tab-pane active nano" id="messages-tab2">
													<div class="content">
                                                                                                        <?php
                                                                                                        if (!empty($alerts)){
                                                                                                            foreach($alerts as $alrt){ ?>
														<p id="<?php echo $alrt->message->id; ?>">                                                                                                                 
                                                                                                                    <?php echo html_entity_decode($alrt->message->body); ?>
                                                                                                                    <a onclick="GrabPressDashboard.deleteAlert(<?php echo $alrt->message->id; ?>);" href="#"><span class="delete_alert">&nbsp;</span></a>
                                                                                                                    <span style="clear:both; display: block"></span>
                                                                                                                </p>
													<?php }
                                                                                                        }?>
                                                                                                         <?php
                                                                                                        if (!empty($errors)){
                                                                                                            foreach($errors as $err){ ?>
														<p id="<?php echo $err->message->id; ?>">
                                                                                                                    <?php echo html_entity_decode($err->message->body); ?>
                                                                                                                    <a onclick="GrabPressDashboard.deleteAlert(<?php echo $err->message->id; ?>);" href="#"><span class="delete_alert">&nbsp;</span></a>
                                                                                                                    <span style="clear:both; display: block"></span>
                                                                                                                </p>
													<?php }
                                                                                                        }?>
													</div>
												</div>
 <?php } ?>
											<!--</div>-->
										</div>
									</div>										
								</div>
								<div class="row-fluid">
									<div class="span12 welcome">
										<div class="panel">
											<div class="tab-content">
												<div class="tab-pane active noscroll" id="messages-tab1">
													<div class="content">                                                                                                            
													 <?php
														$num_feeds = count($feeds);
														 if($publisher_status == "account-unlinked" && GrabPress::check_permissions_for("gp-account")){
												         	$create = isset($_REQUEST[ 'page']) && $_REQUEST[ 'page'] == 'account' && isset($_REQUEST[ 'action']) &&  $_REQUEST[ 'action'] == 'create'
												         	? 'Create' : '<a href="admin.php?page=gp-account&action=create">Create</a>';
												            $link =  isset($_REQUEST[ 'page']) && $_REQUEST[ 'page'] == 'account' && isset($_REQUEST[ 'action']) &&  $_REQUEST[ 'action'] == 'default' 
												            ? 'link an existing' : '<a href="admin.php?page=gp-account&action=default">link an existing</a>';
												  			echo "Want to earn money?" . $create . " or " . $link. " Grab Publisher account.";
														}elseif($num_feeds == 0 && GrabPress::check_permissions_for("gp-autopost")){
															$admin = get_admin_url();
															$admin_page = $admin.'admin.php?page=gp-autoposter';
															$here = '<a href="'.$admin_page.'">here</a>';
															echo "Thank you for activating GrabPress. Try creating your first Autoposter feed " . $here . ".";
														}else{
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
									if(GrabPress::check_permissions_for("gp-account")){
								?>								
								<div id="btn-account-settings">
									<div class="accordion-left">&nbsp;</div>
									<div class="accordion-center">
                                                                                <a href="#" class="big-link" data-reveal-id="AccoutDetails_Modal" data-animation="fade">
                                                                                Account Settings
                                                                                </a>										
									</div>
									<div class="accordion-right">&nbsp;</div>
								</div>
								<?php } ?>
								<div id="publisher-account-status" value="Publisher Account Status" class="<?php echo $publisher_status ?>" ></div>
								<div class="panel nano">
                                                                    <div class="content">
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
														echo '<input type="button" value="0" class="watchlist-check watch-on" id="watchlist-check-'.$feedId.'" >';
													}else{
														echo '<input type="button" value="1" class="watchlist-check watch-off" id="watchlist-check-'.$feedId.'" >';
													}													
												?>		
											</td>
											<td>
												
                                                                                            <a href="#" class="big-link" data-reveal-id="FeedDetails_Modal_<?php echo $feedId; ?>" data-animation="fade">
                                                                                                details
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
<?php
for ( $n = 0; $n < $num_feeds; $n++ ) {
    $feed = $feeds[$n]->feed;
    $feedId = $feed->id;
?>
<div id="FeedDetails_Modal_<?php echo $feedId; ?>" class="reveal-modal">
    <p>Feed Details</p>
    <div class="infoBox">
        <h2 style="text-align:center;"><?php echo urldecode($feed->name); ?></h2>	
        <p style="text-align:center;">
                Created at: <?php echo $feed->created_at; ?>
        </p>
        <p>
                Search Criteria
        </p>
        <?php
        $url = array();
        parse_str( parse_url( $feed->url, PHP_URL_QUERY ), $url );
        GrabPress::_escape_params_template($url);
        ?>
        <p>
                Grab Video Categories: 
                <?php 
                if($url['amp;categories'] == ""){
                    echo "All Video Categories";
                }else {
                    echo str_replace(',', ', ', $url['amp;categories']);
                }
                ?>
                <br />
                Keywords (All):
                <?php 
                    if(isset($url['keywords_and'])){
                        echo str_replace(',', ', ', $url['keywords_and']);
                    }
                ?>
                <br />
                Excluded Keywords:
                <?php 
                    if(isset($url['amp;keywords_not'])){
                        echo str_replace(',', ', ', $url['amp;keywords_not']);
                    }
                ?>
                <br />
                Keywords (Any):
                <?php 
                    if(isset($url['amp;keywords'])){
                        echo str_replace(',', ', ', $url['amp;keywords']);
                    }
                ?>
                <br />
                Keywords (Exact Phrase):
                <?php 
                    if(isset($url['amp;keywords_phrase'])){
                        echo str_replace(',', ', ', $url['amp;keywords_phrase']);
                    }
                ?>
                <br />
                Content Providers: 
                    <?php                                                                                                            
                    $providers = explode( ',' , $url["amp;providers"] ); // providers chosen by the user
                    $providers_selected = count($providers);
                    if ($url["amp;providers"] == "") {
                         echo "All providers";
                    }
                    else{	
                            foreach ( $list_providers as $record_provider ) {
                                    $provider = $record_provider->provider;
                                    $provider_name = $provider->name;
                                    $provider_id = $provider->id;											
                                    if(in_array( $provider_id, $providers )) {											
                                            echo $provider_name.', ';									
                                    }
                            }
                    }  
                   ?>
                <br />
        </p>
        
        <p>
                Publish Settings
        </p>
        <p>
                Schedule: <?php echo isset($feed->update_frequency)?$times[$feed->update_frequency]:''; ?> (last update: <?php echo $feed->updated_at; ?>)<br />
                Maximun Posts per update: <?php echo $feed->posts_per_update; ?><br />                                                                                                        
                Post Categories:
                <?php
                    $category_list_length = count( $feed->custom_options->category );
                    if($category_list_length == 0){
                        echo "Uncategorized";
                    }else{
                        foreach ( $feed->custom_options->category as $categ ) {
                            echo $categ.', ';
                        }
                    }                                                                                                             
                ?>
                <br />
                Author: <?php  the_author_meta( 'nickname' , $feed->custom_options->author_id ); ?>
                <br />
                Player Mode: <?php echo $auto_play = $feed->auto_play ? "Auto-Play" : "Click-to-Play"; ?>
                <br />
                Delivery Mode: <?php echo $publish = $feed->custom_options->publish ? "Publish Posts Automatically" : "Draft"; ?>
                
        </p>
    </div>
    <div class="btn-modal-box">
        <div class="accordion-left">&nbsp;</div>
        <div class="accordion-center"><a class="close-reveal-modal" href="#">Back to Dashboard</a></div>
        <div class="accordion-right">&nbsp;</div>
    </div>
    <?php if(GrabPress::check_permissions_for("gp-autopost")){?>
    <div class="btn-modal-box">
        <div class="accordion-left">&nbsp;</div>
        <div class="accordion-center">
            <a href="admin.php?page=gp-autoposter&action=edit-feed&feed_id=<?php echo $feedId; ?>" id="btn-update-<?php echo $feedId; ?>" class="btn-update-feed">						
                edit
            </a>
        </div>
        <div class="accordion-right">&nbsp;</div>
    </div>												
    <?php } ?>
</div>
<?php } ?>
<div id="AccoutDetails_Modal" class="reveal-modal">
    <p>Account Details</p>
    <div class="infoBox">
    <p>Linked Account Email Adrress: <br />
    <?php
        try {
            $user = GrabPressAPI::get_user();            
        } catch (Exception $e) {
            GrabPress::log('API call exception: '.$e->getMessage());
        }
        $linked = isset( $user->email);
        if( $linked ){?>
        <?php echo $user->email;			
        }else{?>					
        <p>This installation is not linked to a Publisher account.<br/>
        Linking GrabPress to your account allows us to keep track of the video ads displayed with your Grab content and make sure you get paid.</p>
    <?php }?>
    </p>
    <p>API Key: <br /><?php echo get_option( 'grabpress_key' ); ?>
        <input type="hidden" value="<?php echo get_option( 'grabpress_key' ); ?>" id="fe_text" />
        
    </p>
    </div>
    <?php
        if(GrabPress::check_permissions_for("gp-account")){
    ?>
    <div class="btn-modal-box">
        <div class="accordion-left">&nbsp;</div>
        <div class="accordion-center"><a href="<?php echo $admin_page; ?>" >Account Settings</a></div>
        <div class="accordion-right">&nbsp;</div>
    </div>    
    <?php } ?>
    
    <div class="btn-modal-box" id="d_clip_button" data-clipboard-target="fe_text" data-clipboard-text="Default clipboard text from attribute">
        <div class="accordion-left">&nbsp;</div>
        <div class="accordion-center"><a href="#">Copy API Key</a></div>
        <div class="accordion-right">&nbsp;</div>
    </div>
    <div class="btn-modal-box">
        <div class="accordion-left">&nbsp;</div>
        <div class="accordion-center"><a class="close-reveal-modal" href="#">Back to Dashboard</a></div>
        <div class="accordion-right">&nbsp;</div>
    </div>
</div>
<!--javascript for copy to clipboard-->
<script type="text/javascript">
	jQuery(function($){               
                var clip = new ZeroClipboard($("#d_clip_button"), {
                    moviePath: "<?php echo GrabPress::grabpress_plugin_url(); ?>/js/ZeroClipboard.swf"
                });
                clip.on('complete', function (client, args) {
                  debugstr("Copied text to clipboard: " + args.text );
                });
                function debugstr(text) {
                    alert(text);
                }
	});
</script>
