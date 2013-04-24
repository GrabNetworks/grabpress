var Catalog = {
    hasValidationErrors : function () {
                            if((jQuery("#channel-select :selected").length == 0) || (jQuery("#provider-select :selected").length == 0)){
				return true;
                            } else {
				return false;
                            }
                        },
                        
    doValidation : function(){
                        var errors = Catalog.hasValidationErrors();
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
    initSearchForm : function(){
        jQuery(".close-preview").click(function() {		  
	  var form = jQuery('#preview-feed');	
	  var action = jQuery('#action-preview-feed');
	  action.val(feed_action);
	  form.submit();	
  	});
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
       jQuery("#channel-select").multiselect(Catalog.multiSelectOptionsChannels, {
           uncheckAll: function(e, ui){
               Catalog.doValidation();	 	 	
           },
           checkAll: function(e, ui){
               Catalog.doValidation();	  	 	
           }
       });
       jQuery("#provider-select").multiselect(Catalog.multiSelectOptions, {
           uncheckAll: function(e, ui){
               Catalog.doValidation();
           },
           checkAll: function(e, ui){
               Catalog.doValidation();
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

      jQuery("#form-catalog-page").change(Catalog.doValidation);

      jQuery('#btn-create-feed').bind('click', function(e){
          var form = jQuery('#form-catalog-page');
          var action = jQuery('#action-catalog');

          action.val("prefill");
          form.attr("action", "admin.php?page=gp-autoposter");
          form.submit();
      });
      jQuery(".sort_by").change(function(e){
           var form = jQuery('#form-catalog-page');
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
       jQuery(".video_summary").ellipsis(2, true, "more", "less");
    }  
    
}

jQuery(window).load(function () {
	    Catalog.doValidation();
	    var action = jQuery('#action-catalog');	    
	    action.val("catalog-search");
	});

jQuery(document).ready(function(){
    Catalog.initSearchForm()
    if(!window.grabModal){
        try{
            var env = jQuery("#environment").val();
            if (env == 'grabqa') {
                modalId = '1000014775';               
            } else {
                modalId = '1720202';                
            }
            window.grabModal = new com.grabnetworks.Modal( { id : modalId , tgt: env, width: 800, height: 450 } );
            window.grabModal.hide();
          }catch(err){

          }
    }
    }
);

