/* 
 * Defining all javascript functionality for the Grabpress Autoposter tab under one namespace
 */

var GrabPressAutoposter = GrabPressAutoposter || {
    /* Checks if channels and providers are selected */
    hasValidationErrors : function () {
        if (jQuery("#message p").text() == "There was an error connecting to the API! Please try again later!") {
           return true; 
        }
        if((jQuery("#channel-select :selected").length == 0) || (jQuery("#provider-select :selected").length == 0)){
            return true;
        }       
        return false;        
    },
    /* Modal preview window definition and its closing behavior  */
    previewdialogConf : {
        modal: true,
        width:910,
        height:jQuery(window).height(),
        draggable: false,
        close: function(){
            //var and = [], or = [], phrase = [], not = [],
            //kwrds = jQuery("#keywords").val(),
            //regPhrase = /"[^"]*"/ig,                                
            //regAfterOR = /\sOR\s+[\w\S]*/ig,//regEx for keywords after OR
            //regBeforeOR = /[\w+(\?\:\-\w+)\S+]*\s+OR/;//regEx for keyword in front of OR
            /*
            phrase = regPhrase.exec(kwrds);
            if(!phrase){
                phrase = [];
            }else{
                for (var i = phrase.length - 1; i >= 0; i--) {
                    phrase[i] = phrase[i].replace(/"/g, "");
                }; 
            }
            
            kwrds = kwrds.replace(regPhrase, "");

            or = jQuery.trim(kwrds.match(regAfterOR));//match regex for all keywords after 'OR'
            beforeOr = jQuery.trim(kwrds.match(regBeforeOR));//match regex for the first keyword in front of the first 'OR'
            if(!or){
                or = [];
            }else{	                		                                        
                //split the string of keywords into an array and replace OR with ''
                or = jQuery.trim(String(or).replace(/OR\s/g,'')).split(/\,\s/);                                        
                if (beforeOr) {
                    beforeOr = beforeOr.replace(/\s+OR/,'');//replace 'OR' with so that beforeOr containd only the keyword
                    //add the keyword in front of the or array of keywords
                    or.unshift(beforeOr);
                }
            }
            //cut off the OR separated keywords from the kwrds string
            kwrds = jQuery.trim(kwrds.replace(regAfterOR, "")); 
            kwrds = kwrds.replace(new RegExp(beforeOr  + '$'), "");
            var words = kwrds.replace(/^\s+|\s+$/g, '').split(/\s+/);
            for(var i=0;i<words.length;i++){
                if(words[i][0] == "-"){
                    not.push(words[i].slice(1,words[i].length));
                }else{
                    //if (jQuery("#form-create-feed input[name=keywords_and]").val() != '') {
                        and.push(words[i]);
                    //}                                                          								
                }
            }
            jQuery("#form-create-feed input[name=keywords_and]").val(and.join(" "));
            jQuery("#form-create-feed input[name=keywords_or]").val(or.join(" "));
            jQuery("#form-create-feed input[name=keywords_not]").val(not.join(" "));
            jQuery("#form-create-feed input[name=keywords_phrase]").val(phrase.join(" "));
            jQuery("#channel-select").val(jQuery("#channel-select-preview").val());
            jQuery("#provider-select").val(jQuery("#provider-select-preview").val());
            jQuery("#channel-select").multiselect("refresh");
            jQuery("#provider-select").multiselect("refresh");
            jQuery("#channel-select-preview").multiselect("destroy");
            jQuery("#provider-select-preview").multiselect("destroy");
            
            //leave page with modified form pop-up            
            if (and || or || not|| phrase) { 
                GrabPressAutoposter.setConfirmUnload(true);
            } 
            */
           GrabPressAutoposter.doValidation();
           jQuery("#preview-modal").remove();
        }
    },
    /* Adds videos to the modal preview window by making an ajax request with the entered keywords */
    previewVideos : function () {
        var errors = GrabPressAutoposter.hasValidationErrors();
        if(!errors){
            var data = {
                "action": "gp_get_preview",
                "keywords_and": jQuery("#form-create-feed input[name=keywords_and]").val(),
                "keywords_or": jQuery("#form-create-feed input[name=keywords_or]").val(),
                "keywords_not": jQuery("#form-create-feed input[name=keywords_not]").val(),
                "keywords_phrase": jQuery("#form-create-feed input[name=keywords_phrase]").val(),
                "providers": jQuery("#provider-select").val(),
                "channels": jQuery("#channel-select").val(),
            };

            var dialog = jQuery("<div id='preview-modal'>").dialog(GrabPressAutoposter.previewdialogConf);
            // load remote content
            dialog.load(
                ajaxurl,
                data,
                function (responseText, textStatus, XMLHttpRequest) {
                    // remove the loading class
                    dialog.removeClass('loading');
                }
            );
            //prevent the browser to follow the link
            return false;
        }else{
            alert(errors);
        }
    },
    /* Delete feed from Current Feeds table */
    deleteFeed : function(id){
        var bg_color = jQuery('#tr-'+id+' td').css("background-color")
        jQuery('#tr-'+id+' td').css("background-color","red");	
        var form = jQuery('#form-'+id);
        var action = jQuery('#action-'+id);			
        var answer = confirm('Are you sure you want to delete this feed? You will no longer receive videos based on its settings. Existing video posts will not be deleted.');
        if(answer){					
            var data = {
                action: 'gp_delete_feed',
                feed_id: id
            };
            jQuery.post(ajaxurl, data, function(response) {
                    window.location = "admin.php?page=gp-autoposter";
            });
        } else{					
            jQuery('#tr-'+id+' td').css("background-color", bg_color);
            return false;
        }
    },
    /* Edit feed from Current Feeds table */
    editFeed : function(id) {
        window.location = "admin.php?page=gp-autoposter&action=edit-feed&feed_id="+id;
    },
    /* Create Feed form validation */
    doValidation : function(){
        var errors = GrabPressAutoposter.hasValidationErrors();
        if ( !errors ){
            jQuery('#btn-create-feed').removeAttr('disabled');
            jQuery('#btn-preview-feed').removeAttr('disabled');

            if( jQuery( '#btn-preview-feed' ).off ){
                    jQuery( '#btn-preview-feed' ).off('click');
            }else{
                    jQuery( '#btn-preview-feed' ).unbind('click');
            }
            jQuery('.hide').show();					
        }else{
            jQuery( '#btn-create-feed' ).attr('disabled', 'disabled');
            jQuery( '#btn-preview-feed' ).attr('disabled', 'disabled');

            if( jQuery( '#btn-preview-feed' ).off ){
                    jQuery( '#btn-preview-feed' ).off('click');
            }else{
                    jQuery( '#btn-preview-feed' ).unbind('click');
            }				
            jQuery('.hide').hide();
        }
        //add a maxlength for providers filter
        jQuery(':input').each(function(){
            if(jQuery(this).attr('placeholder') == 'Enter keywords')
                jQuery(this).attr('maxlength','32');

        });
    },
    /* Get the existing feeds keywords */
    getKeywords : function() {        
        var inpts = jQuery("#existing_keywords input");
        var kwrds =  new Object(),
            phkwrds = new Object();
        inpts.each(function(index){
           kwrds[this.name] = jQuery.trim(this.value).split(" ");
        });
        var phInpts = jQuery("#exact_keywords input");
        phInpts.each(function(index){
           phkwrds[this.name] = jQuery.trim(this.value).split("_");
           if (phkwrds[this.name][0].length == 0) {
               phkwrds[this.name].splice(0, 1);
           }
        });
        for (k in kwrds) {
            if (phkwrds[k].length != 0) {               
                jQuery.merge(kwrds[k], phkwrds[k]);
            };            
            if (kwrds[k][0].length == 0) {
               kwrds[k].splice(0, 1);
            }
        }
        
        return kwrds;
    },
    /* Check for matching keyword and alert user */
    findMatchingKeyword : function(keyword) {
        var kwrds = GrabPressAutoposter.getKeywords();
        for (feed in kwrds) {
            if (jQuery.inArray(keyword, kwrds[feed]) != -1) {
                return feed;
            }
        }  
        return false;
    },
    /* Checks if any of the keywords has already been saved in a previously created feed */
    validateKeywords : function(edit) {
        var kwrds = new Array(),
            keys = 0,
            textKwrds = "",
            text = "";            
        var andKwrds = jQuery.trim(jQuery("#keywords_and").val());
        if (andKwrds.length != 0) {
            andKwrds = andKwrds.split(" ");
            jQuery.merge(kwrds, andKwrds);
        };
        var orKwrds = jQuery.trim(jQuery("#keywords_or").val());
        if (orKwrds.length != 0) {
            orKwrds = orKwrds.split(" ");
            jQuery.merge(kwrds, orKwrds);
        };
        var exactKwrds = jQuery.trim(jQuery("#keywords_phrase").val());
        if (exactKwrds.length != 0) {                        
            var feed = GrabPressAutoposter.findMatchingKeyword(exactKwrds);
            if (feed) {
                textKwrds += ' <strong>"' + exactKwrds + '"</strong>(exact phrase), ';// already used in feed: ' + feed + ',<br/>';                
                keys++;
            }
        };
        jQuery.each(kwrds, function(index, value){            
            feed = GrabPressAutoposter.findMatchingKeyword(value);
            if (feed) {
                textKwrds += '<strong>' + value + '</strong>, ';// already used in feed: ' + feed + ',<br/>';
                keys++;
            }            
        });        
        if (keys === 0) {
            GrabPressAutoposter.validateFeedName(edit);
        } else {
            textKwrds = textKwrds.slice(0, -2);
            if (keys === 1) {
                text = "The keyword " + textKwrds + " is ";
            } else { 
                text = "The keywords " + textKwrds + " are "; 
            }
            text += "already used by previously created feeds.<br/>The videos matching a keyword will show only in the first created feed."
            jQuery("#keywords_dialog p").html(text);
            jQuery("#keywords_dialog #edit_feed").val(edit);               
            jQuery("#keywords_dialog").dialog('open');
        }        
    },
    /* Feed name validation */
    validateFeedName : function(edit){
        var feed_date = jQuery('#feed_date').val();
        var name = jQuery('#name').val();
        if(name == ""){
            jQuery('#name').val(feed_date);
            jQuery("#form-create-feed").submit();
        }
        name = jQuery.trim(jQuery('#name').val());
        var regx_name = /\s/;		
        var regx = /^[a-zA-Z0-9,\s]+$/;

        var data = {
            action: 'gp_feed_name_unique',
            name: name
        };

        // Update feed
        if(edit === "update"){ 
            if(!regx.test(name)){
                    alert("The name entered contains special characters or starts/ends with spaces. Please enter a different name");
            }else if(name.length < 6){					
                    alert("The name entered is less than 6 characters. Please enter a name between 6 and 14 characters");
            }else {
                    jQuery('#name').val(name);
                    jQuery("#form-create-feed").submit();
            }
        }else{  // Create feed
            jQuery.post(ajaxurl, data, function(response) {                
                if(response != "true"){
                    if((feed_date == name) && ((typeof edit === "undefined") || (edit===null) || (edit === ''))){
                        jQuery('#dialog-name').val(name);
                        jQuery('#dialog').dialog('open');
                    }else{
                        if(!regx.test(name)){
                            alert("The name entered contains special characters or starts/ends with spaces. Please enter a different name");
                        }else if(name.length < 6){					
                            alert("The name entered is less than 6 characters. Please enter a name between 6 and 14 characters");
                        }else {
                            jQuery('#name').val(name);
                            jQuery("#form-create-feed").submit();
                        }				
                    }
                }else{					
                    alert("The name entered is already in use. Please select a different name");
                }				
            });	
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
    /* Posts Categories multiselect definition */
    multiSelectOptionsCategories : {
        noneSelectedText:"Select categories",
        selectedText: "# of # selected"
    },
    /* Videos categories multiselect definition */
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
    /* Create Feed form initialization and bindings */
    initSearchForm : function() {
        jQuery(".btn-preview-feed").live("click", function(e){
            var id = jQuery(this).data("id");
            var data = {
                "action": "gp_get_preview",
                "feed_id": id
            };
            var dialog = jQuery("<div id='preview-modal'>").dialog(GrabPressAutoposter.previewdialogConf);
            // load remote content
            dialog.load(
                ajaxurl,
                data,
                function (responseText, textStatus, XMLHttpRequest) {
                    // remove the loading class
                    dialog.removeClass('loading');
                }
            );
            e.preventDefault();
            return false;
        });
        jQuery('#reset-form').bind('click', function(e){
            var referer = jQuery("input[name=referer]").val();
            window.onbeforeunload = null;
            if( referer == "create" ){
                window.location = "admin.php?page=gp-autoposter";
            }else{
                var id = jQuery("input[name=feed_id]").val();
                window.location = "admin.php?page=gp-autoposter&action=edit-feed&feed_id="+id;
            }            
        });
        jQuery("#form-create-feed input").keypress(function(e) {
            if(e.which == 13) {
                e.preventDefault();
                return false;
            }
        });

        if(jQuery('#provider-select option:selected').length == 0){
            jQuery('#provider-select option').attr('selected', 'selected');
        }
        if(jQuery('#channel-select option:selected').length == 0){
            jQuery('#channel-select option').attr('selected', 'selected');
        }        
        var category_options = jQuery('#cat option');
        for(var i=0;i<category_options.length; i++){
            if(jQuery.inArray(jQuery(category_options[i]).val(),GrabPressAutoposter.selectedCategories)>-1){
                jQuery(category_options[i]).attr("selected", "selected");
            }
        }
        jQuery("#provider-select").multiselect(GrabPressAutoposter.multiSelectOptions, {
            uncheckAll: function(e, ui){
                GrabPressAutoposter.doValidation();
            },
            checkAll: function(e, ui){
                /*
                if(jQuery("#provider-select :selected").length != 0){
                        jQuery('.hide').show();
                }
                */
                GrabPressAutoposter.doValidation();
            }
        }).multiselectfilter();

        jQuery(".provider-select-update").multiselect(GrabPressAutoposter.multiSelectOptions, {
            uncheckAll: function(e, ui){
                id = this.id.replace('provider-select-update-','');
            },
            checkAll: function(e, ui){
                id = this.id.replace('provider-select-update-','');
            }
        }).multiselectfilter();

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
        jQuery("#cat").multiselect(GrabPressAutoposter.multiSelectOptionsCategories,
        {
            header:false
        });

        jQuery(".postcats").multiselect(GrabPressAutoposter.multiSelectOptionsCategories, {
            header:false,
            uncheckAll: function(e, ui){
                id = this.id.replace('postcats-','');
             },
             checkAll: function(e, ui){
                id = this.id.replace('postcats-','');
             }
        }).multiselectfilter();

        //jQuery(".channel-select").selectmenu();
        jQuery(".schedule-select").selectmenu();
        jQuery(".limit-select").selectmenu();
        jQuery(".author-select").selectmenu();

        jQuery("#learn-more").simpletip({
               content: 'Please be aware that selecting a click-to-play player can negatively impact your revenue, <br />as not all users will generate an ad impression. If you are looking to optimize revenue <br />through Grabpress, all feeds should be set to autoplay. ',
               fixed: true,
               position: 'bottom'
        });
        jQuery('input, textarea').placeholder();

        jQuery('.active-check').bind('click', function(e){
              var id = this.id.replace('active-check-','');
              var active_check = jQuery(this);

              if(active_check.is(':checked')) {
                  var active = 1;		        
                  jQuery('#tr-'+id+' td').css("background-color","#FFE4C4");
              }else{
                  var active = 0;
                  jQuery('#tr-'+id+' td').css("background-color","#DCDCDC");		    	
              }		    

              var data = {
                  action: 'gp_toggle_feed',
                  feed_id: id,
                  active: active
              };

              jQuery.post(ajaxurl, data, function(response) {                  
                  var substr = response.split('-');
                  var num_active_feeds = substr[0];
                  var num_feeds =  substr[1];
                  var noun = 'feed';	
                  var autoposter_status = 'ON';
                  var feeds_status = 'active';		
                  /*
                  if( (num_active_feeds == 1) || (num_feeds == 1) ){
                          noun = 'feed';	
                  }else */
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

                  jQuery('#num-active-feeds').text(num_active_feeds);	
                  jQuery('#noun-active-feeds').text(noun);

                  jQuery('#autoposter-status').text(autoposter_status);
                  jQuery('#feeds-status').text(feeds_status);
              });
        });	  

         jQuery('#cancel-editing').bind('click', function(e){ 
              var answer = confirm('Are you sure you want to cancel editing? You will continue to receive videos based on its settings. All of your changes will be lost.');
              if(answer){				
                    window.location = "admin.php?page=gp-autoposter";
              } else{				
                    return false;
              }
        });

        jQuery(".ui-selectmenu").click(function(){
              jQuery(".ui-multiselect-menu").css("display", "none");
          });		  

        jQuery("#channel-select").multiselect(GrabPressAutoposter.multiSelectOptionsChannels, {
            uncheckAll: function(e, ui){

            },
            checkAll: function(e, ui){

            }
         });

        jQuery("#form-create-feed").change(GrabPressAutoposter.doValidation);
        jQuery('#dialog').dialog({
            autoOpen: false,
            width: 400,
            modal: true,
            resizable: false,
            buttons: {
                "Cancel": function() {
                  jQuery(this).dialog("close");
                },
                "Create Feed": function() {
                  var name = jQuery("#dialog-name").val();
                  jQuery("#name").val(name);
                  GrabPressAutoposter.validateFeedName("edit");
                }
            }
         });
         jQuery('#keywords_dialog').dialog({
            autoOpen: false,
            width: 400,
            modal: true,
            resizable: false,
            buttons: {
                "Cancel": function() {
                    jQuery(this).dialog("close");                    
                },
                "Create Feed": function() {                                                                                                        
                    var edit = jQuery("#keywords_dialog #edit_feed").val();                                        
                    GrabPressAutoposter.validateFeedName(edit);
                    jQuery(this).dialog("close");
                }
            }
          });

        jQuery(".btn-update-feed").mousedown(function(event) {
             if( event.which == 2 ) {
                 return false;
                 id = this.id.replace('btn-update-','');
                 editFeed(id); 
             }
         });
        jQuery('.btn-update-feed').bind("click",function(e){
            id = this.id.replace('btn-update-','');
            GrabPressAutoposter.editFeed(id);
            return false;
        });

        jQuery('.btn-update-feed').bind("contextmenu",function(e){
            id = this.id.replace('btn-update-','');
            GrabPressAutoposter.editFeed(id);
            return false;
        });   
        
        //if we have an API connection error disable all inputs
        if (jQuery("#message p").text() == "There was an error connecting to the API! Please try again later!") {
            jQuery(":input").attr('disabled', 'disabled');
        };
        //leave page with modified form pop-up
        jQuery(':input', 'form').bind("change", function () {
            GrabPressAutoposter.setConfirmUnload(true);
        });
        jQuery('#form-create-feed').submit(function(){window.onbeforeunload = null;});
    },
    setConfirmUnload : function(on) {
        window.onbeforeunload = (on) ? GrabPressAutoposter.unloadMessage : null;
    },
    unloadMessage : function() {
        return 'You have entered new data on this page.' +
        ' If you navigate away from this page without' +
        ' first saving your data, the changes will be' +
        ' lost.';
    }
}
//do form validation	
jQuery(window).load(function () {
    GrabPressAutoposter.doValidation();    
});
//initialize form
jQuery(document).ready(
    GrabPressAutoposter.initSearchForm()
);