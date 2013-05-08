/* 
 *  Grabpress Dashboard javascript functionality
 */

var GrabPressDashboard = GrabPressDashboard || {
    active_video : null,
    /* Displays the first video from watchlist */
    onload_openvideo : function(embed_id) {
        if(jQuery(".accordion-warning").length == 1){
            return false;
        }
        var embed = "";
        var anchor = jQuery(jQuery(".accordion-toggle[href='#collapse1']")[0]);
        embed = '<div id="gcontainer'+embed_id+'" style="height:100%;"><div id="grabDiv'+embed_id+'"></div></div>';
        jQuery("#collapse1").find(".accordion-inner").append(embed);
        active_video = new com.grabnetworks.Player({
            "id": embed_id,
            "width": "100%",
            "height": "100%",
            "content": anchor.data("guid"),
            "autoPlay": false
        });
        active_video.showEmbed();
        jQuery("#collapse1").toggleClass("collapse");
    },
    /* Watchlist button binding to display/hide videos */
    watchlist_binding : function(embed_id){
        jQuery('.watchlist-check').bind('click', function(e){
            var id = this.id.replace('watchlist-check-','');
            var watchlist_check = jQuery(this);
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

            jQuery.post(ajaxurl, data, function(response) {	        
                  var parsedJson = jQuery.parseJSON(response);
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
                  jQuery('#accordion2').html(accordion);
                  active_video = new com.grabnetworks.Player({
                      "id": embed_id,
                      "width": "100%",
                      "height": "100%",
                      "content": parsedJson.results[0].video.guid,
                      "autoPlay": false
                  });
                  jQuery(window).resize();
                  jQuery("#gcontainer"+embed_id+" object").css("visibility","visible");
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
                  jQuery('#accordion2').html(accordion);
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
    },
    /* Watchlist accordion-like behavior */
    accordion_binding : function(env, embed_id){
        var accordion_lock = false;
        jQuery("#form-dashboard").parent().css("margin", "-10px 0 0 -18px");

        jQuery(".accordion-toggle").live("click", function(e){
            if(accordion_lock){
                e.preventDefault();
                return false;
            }

            var anchor = jQuery(this);
            var panel = jQuery(anchor.attr("href"));
            var openPanels = jQuery(".accordion-group .accordion-body").not(".collapse");
            // debugger;
            if(panel.hasClass("collapse")){
                accordion_lock = true;
                var monitor = 0;
                var slideDownCurrent = function(panel, onfinish){
                    var embed = jQuery("#gcontainer"+embed_id).detach();
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
                        //console.log("hide embed");
                        jQuery(this).toggleClass("collapse");
                        monitor++;
                    });
                }else{
                    slideDownCurrent(panel, function(){accordion_lock=false;});
                }
            }
            e.preventDefault();
            return false;
        });
    },
    /* Resize the watchlist accordion height depending on its width by keeping the same ratio */
    resize_accordion : function(){
        var width = jQuery(jQuery(".accordion-center")[0]).css("width");
        width = width.replace("px","");
        jQuery(".accordion-inner").css("height", width* 0.5625 )
    },
    /* Dashboard initializiations */
    init : function(){
         //fix for watchlist min-width and max-width for ie9 and ie10
        if (jQuery.browser.msie && jQuery.browser.version > 8.0) {
            jQuery("#t #b .watchlist").css('max-width','1392px');
            jQuery("#t #b .watchlist").css('min-width','1072px');
            jQuery("#t #b #btn-account-settings a").hover(function(){
                    jQuery(this).css('margin-left', '0');
            });
            jQuery("#t #b #btn-account-settings .accordion-center").css('filter','none');                    
            jQuery("#t #b #btn-account-settings a").css('width','auto');
            jQuery("#t #b #btn-account-settings a").css('height','auto');
            jQuery("#t #b #btn-account-settings .accordion-center").hover(function(){
                    jQuery(this).css('width','99px');
                    jQuery(this).css('padding-right','6px');
                    jQuery(this).css('margin-left','0');
                    jQuery(this).css('filter','none');
                },
                function(){                            
                    jQuery(this).css('padding-right','3px');                                                        
            });
            jQuery("#t #b #btn-account-settings .accordion-left").css('top','0');
            jQuery("#t #b #btn-account-settings .accordion-right").css('top','0');

        } else if ( jQuery.browser.version != 7.0) {
            jQuery("#t #b .watchlist .accordion-right").css("right", "-1px");
        } 
        GrabPressDashboard.watchlist_binding(jQuery("#embed_id").val());
        GrabPressDashboard.accordion_binding(jQuery("#environment").val(), jQuery("#embed_id").val());
        GrabPressDashboard.onload_openvideo(jQuery("#embed_id").val());
        jQuery(".nano").nanoScroller({"alwaysVisible":true});

        jQuery(window).resize(GrabPressDashboard.resize_accordion).resize();
        jQuery("#message").hide();//hack
        
        jQuery("#help").simpletip({
            content: 'Health displays “results/max results” per the latest feed update. <br/> Feeds in danger of not producing updates display in red or orange, feeds at risk of not producing updates display in yellow, and healthy feeds display in green.  <br /><br />', 
            fixed: true,
            position: [155, 40]
        });
                
        jQuery(".feed_title").ellipsis(0, true, "", "");
         
    }
}

jQuery(document).ready(
    GrabPressDashboard.init()
); 