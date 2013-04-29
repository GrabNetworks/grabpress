var GrabPressCatalog = {
    /* Checks for channels and providers selection */
    hasValidationErrors : function () {
                            if((jQuery("#channel-select :selected").length == 0) || (jQuery("#provider-select :selected").length == 0)){
				return true;
                            } else {
				return false;
                            }
                        },
    /* Validation for search form inputs */                    
    doValidation : function(){
                        var errors = GrabPressCatalog.hasValidationErrors();
			if ( !errors ){
				jQuery('#btn-create-feed').removeAttr('disabled');
				jQuery('#update-search').removeAttr('disabled');

				if( jQuery( '#update-search' ).off ){
					jQuery( '#update-search' ).off('click');
				}else{
					jQuery( '#update-search' ).unbind('click');
				}
				jQuery('.hide').show();					
			}else{
				jQuery( '#btn-create-feed' ).attr('disabled', 'disabled');
				jQuery( '#update-search' ).attr('disabled', 'disabled');
			
				if( jQuery( '#update-search' ).off ){
					jQuery( '#update-search' ).off('click');
				}else{
					jQuery( '#update-search' ).unbind('click');
				}
				jQuery('.hide').hide();
			}			
		},
    /* Channels multiselect definition */
    multiSelectOptionsChannels : {
        noneSelectedText:"Select Video Categories",
        selectedText:function(selectedCount, totalCount){
            if (totalCount==selectedCount){
            	return "All Video Categories";
            }else{
 		return selectedCount + " of " + totalCount + " Video Categories";
            }
        }
    },
    /* Providers multiselect definition */
    multiSelectOptions : {
        noneSelectedText:"Select providers",
        selectedText:function(selectedCount, totalCount){
            if (totalCount==selectedCount){
                return "All providers selected";
            }else{
                return selectedCount + " providers selected of " + totalCount;
            }
        }
    },
    /* Initialization specific to Catalog tab template page */
    tabSearchForm : function(){
        jQuery(".close-preview").click(function() {		  
	  var form = jQuery('#preview-feed');	
	  var action = jQuery('#action-preview-feed');
	  action.val(feed_action);
	  form.submit();	
  	});
        jQuery('#btn-create-feed').bind('click', function(e){
          var form = jQuery('#form-catalog-page');
          var action = jQuery('#action-catalog');

          action.val("prefill");
          form.attr("action", "admin.php?page=gp-autoposter");
          form.submit();
        });
         jQuery('.btn-create-feed-single').bind('click', function(e){
          var v_id = this.id.replace('btn-create-feed-single-','');

          var data = {
               action: 'gp_insert_video', 
               format : 'post',
               video_id: v_id
          };
          jQuery.post(ajaxurl, data, function(response) {
              if(response.status == "redirect"){
                  window.location = response.url;
              }
          }, "json");	
       });
       jQuery('#clear-search').bind('click', function(e){
           window.location = "admin.php?page=gp-catalog";		    
       });
       
       if(!window.grabModal){
           try{
                var env = jQuery("#environment").val();
                if (env == 'grabqa') {
                    modalId = '1000014775';
                } else {
                    modalId = '1720202';
                }
                window.grabModal = new com.grabnetworks.Modal({id: modalId, tgt: env, width: 800, height: 450});
                window.grabModal.hide();
           }catch(err){

           }
       }
    },
    /* Insert in post ajax search form initializations and bindings */
    postSearchForm : function(){
        jQuery(window).scroll(function () {
            var channelHeight = jQuery('.ui-multiselect').position().top;
            jQuery('.ui-multiselect-menu').css('top', channelHeight + 61);
            jQuery('.ui-multiselect-menu').css('position', 'fixed');
        });

        jQuery('#TB_ajaxContent').scroll(function () {
            var channelHeight = jQuery('.ui-multiselect').position().top;
            jQuery('.ui-multiselect-menu').css('z-index', 103);
            jQuery('.ui-multiselect-menu').css('overflow', 'auto');
            jQuery('.ui-multiselect-menu').css('top', channelHeight + 61);		
        });
        var submitSearch = function() {
            var data = {"action": "gp_get_catalog",
                "empty": false,
                "keywords": jQuery("#keywords").val(),
                "providers": jQuery("#provider-select").val(),
                "channels": jQuery("#channel-select").val(),
                "sort_by": jQuery('.sort_by:checked').val(),
                "created_before": jQuery("#created_before").val(),
                "created_after": jQuery("#created_after").val()};
            jQuery.post(ajaxurl, data, function(response) {
                jQuery("#gp-catalog-container").replaceWith(response);
            });
        };
        var submitClear = function() {
            var data = {"action": "gp_get_catalog",
                "empty": true,
                "keywords": jQuery("#keywords").val(),
                "providers": jQuery("#provider-select").val(),
                "channels": jQuery("#channel-select").val(),
                "sort_by": jQuery('.sort_by:checked').val(),
                "created_before": jQuery("#created_before").val(),
                "created_after": jQuery("#created_after").val()};
            jQuery.post(ajaxurl, data, function(response) {
                jQuery("#gp-catalog-container").replaceWith(response);
            });
        };
        jQuery("#form-catalog-page").submit(function(e) {
            e.preventDefault();
            submitSearch();
            return false;
        });
        jQuery(".sort_by").change(function(e) {
            submitSearch();
        });
        jQuery('.insert_into_post').bind('click', function(e) {
            var v_id = this.id.replace('btn-create-feed-single-', '');
            var data = {
                action: 'gp_insert_video',
                format: 'embed',
                video_id: v_id
            };

            jQuery.post(ajaxurl, data, function(response) {
                if (response.status == "ok") {
                    window.send_to_editor(response.content);
                }
                tb_position = backup_tb_position
                return false;
            }, "json");
        });

        jQuery('#clear-search').bind('click', function(e) {
            jQuery("#keywords").val("");
            jQuery('#provider-select option').attr('selected', 'selected');
            jQuery("#provider-select").multiselect("refresh");
            jQuery("#provider-select").multiselect({
                selectedText: "All providers selected"
            });
            jQuery('#channel-select option').attr('selected', 'selected');
            jQuery("#channel-select").multiselect("refresh");
            jQuery("#channel-select").multiselect({
                selectedText: "All Video Categories"
            });
            jQuery('.sort_by[value=relevance]').removeAttr("checked");
            jQuery('.sort_by[value=created_at]').attr("checked", "checked");
            jQuery("#created_before").val("");
            jQuery("#created_after").val("");
            submitClear();
        });
        
        if(!window.grabModal){
            try{
                var env = jQuery("#environment").val();
                if (env == 'grabqa') {
                    modalId = '1000014775';               
                } else {
                    modalId = '1720202';                
                }
                window.grabModal = new com.grabnetworks.Modal( { id : modalId , tgt: env, width: 1100, height: 450 } );
                window.grabModal.hide();
            }catch(err){

            }
        }
    },
    /* Common initializations for the catalog search forms */
    initSearchForm : function(){
        jQuery("#how-it-works").simpletip({
            content: 'The Grabpress plugin gives your editors the power of our constantly updating video catalog from the dashboard of your Wordpress CMS. Leveraging automated delivery, along with keyword feed curation, the Grabpress plugin delivers article templates featuring video articles that compliment the organic content creation your site offers.<br /><br /> As an administrator, you may use Grabpress to set up as many feeds as you desire, delivering content based on intervals you specify. You may also assign these feeds to various owners, if your site has multiple editors, and the articles will wait in your drafts folder until you see a need to publish. Additionally, for smaller sites, you can automate the entire process, publishing automatically and extending the reach of your site without adding work to your busy day. <br /><br /> To get started, select a channel from our catalog, hone your feed by adding keywords, set your posting interval, and check the posting options (post interval, player style, save as draft or publish) for that feed to make sure the specifications meet your needs. Click the preview feed button to see make sure your feed will generate enough content and that the content is what you are looking for. If the feed seems to be right for you, save the feed and you will start getting new articles delivered to your site at the interval you specified. <br /><br />', 
            fixed: true, 
            position: 'bottom'
       });

       jQuery("#help").simpletip({
            content: "This search input supports Google syntax for advanced search:<br/><b>Every</b> term separated only by a space will be required in your results.<br/>At least one of any terms separated by an ' OR ' will be included in your results.<br/>Add a '-' before any term that must be <b>excluded</b>.<br/> Add quotes around any \"exact phrase\" to look for.<br /><br />", 
            fixed: true,
            position: 'bottom'
       });

       if(jQuery('#provider-select option:selected').length == 0){
           jQuery('#provider-select option').attr('selected', 'selected');
       }

       if(jQuery('#channel-select option:selected').length == 0){
           jQuery('#channel-select option').attr('selected', 'selected');
       }
       jQuery("#channel-select").multiselect(GrabPressCatalog.multiSelectOptionsChannels, {
           uncheckAll: function(e, ui){
               GrabPressCatalog.doValidation();	 	 	
           },
           checkAll: function(e, ui){
               GrabPressCatalog.doValidation();	  	 	
           }
       });
       jQuery("#provider-select").multiselect(GrabPressCatalog.multiSelectOptions, {
           uncheckAll: function(e, ui){
               GrabPressCatalog.doValidation();
           },
           checkAll: function(e, ui){
               GrabPressCatalog.doValidation();
           }
      }).multiselectfilter();

      jQuery(".datepicker").datepicker({
          showOn: 'both',
          buttonImage: 'http://'+window.location.host+'/wp-content/plugins/grabpress/images/icon-calendar.gif',
          buttonImageOnly: true,
          changeMonth: true,
          changeYear: true,
          showAnim: 'slideDown',
          duration: 'fast'
      });
      jQuery("#form-catalog-page").change(GrabPressCatalog.doValidation);      
      
      jQuery(".sort_by").change(function(e){
           var form = jQuery('#form-catalog-page');
           form.submit();
      });
     
      jQuery(".video_summary").ellipsis(2, true, "more", "less");
    } ,
    TB_Position : function(){        
        var SpartaPaymentWidth			= 930;
        var TB_newWidth			= jQuery(window).width() < (SpartaPaymentWidth + 40) ? jQuery(window).width() - 40 : SpartaPaymentWidth;
        var TB_newHeight		= jQuery(window).height() - 70;
        var TB_newMargin		= (jQuery(window).width() - SpartaPaymentWidth) / 2;

        jQuery('#TB_window').css({'marginLeft': -(TB_newWidth / 2), "marginTop": -(TB_newHeight / 2)});
        jQuery('#TB_window, #TB_iframeContent').width(TB_newWidth).height(TB_newHeight);
        jQuery('#TB_ajaxContent').height(TB_newHeight - 29);
        jQuery('#TB_ajaxContent').width(TB_newWidth - 33);			
    }    
}