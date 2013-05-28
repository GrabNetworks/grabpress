/* 
 * Defining all javascript functionality for the Grabpress Autoposter tab under one namespace
 */

var GrabPressTemplate = GrabPressTemplate || {
    /* Update the height value and the preview screen */
    updateHeightValue : function(){
        var player_width = jQuery("form input[name=width]").val();
        jQuery(".template-preview").width(player_width); 
        if(jQuery("form input[name=ratio]:checked").val() == "widescreen"){
                jQuery(".template-preview .widescreen").css("display", "block");
                jQuery(".template-preview .standard").css("display", "none");
                var height = parseInt((jQuery("form input[name=width]").val()/16),10)*9;
                var widescreen_width = (player_width * 3) / 4;
                var margin_left = (player_width - widescreen_width) / 2;
                jQuery(".widescreen").width(widescreen_width);
                jQuery(".widescreen").height(height);
                jQuery(".widescreen").css({"border-top": "none", "border-bottom" : "none", "margin-left" : margin_left});
        }else{
                jQuery(".template-preview .standard").css("display", "block");
                jQuery(".template-preview .widescreen").css("display", "none");
                var height = parseInt((jQuery("form input[name=width]").val()/4),10)*3;
                var standard_height = (height * 3) / 4;
                var margin_top = (height - standard_height) / 2;
                jQuery(".standard").width(player_width);
                jQuery(".standard").height(standard_height);
                jQuery(".standard").css({"border-left": "none", "border-right" : "none", "margin-top" : margin_top });
        }
        jQuery(".height").text(height);
        jQuery(".template-preview").height(height);
    },
    validateWidthValue : function(){                    
        var player_width = jQuery("form input[name=width]").val();
        if(isNaN(player_width))
        {         
            alert("Please enter a numeric value !");
            document.getElementById("player_width").value = jQuery("form input[name=width_orig]").val();
            GrabPressTemplate.updateHeightValue();
            return false;
        }
        if (player_width<300){            
            var text = "The minimum width for a Grab Video Player is 300 px wide.";
            document.getElementById("player_width").value = 300;
            jQuery("#dialog_300 p").html(text);
            jQuery("#dialog_300").dialog("open");
        }
        if (player_width>640){
            if(jQuery("form input[name=ratio]:checked").val() == "widescreen"){
                var text = "Creating an embed larger than the video's native size (640x360 px) may result in pixelated video.";
            }
            else{
                var text = "Creating an embed larger than the video's native size (640x480 px) may result in pixelated video.";
            }                        
            jQuery("#dialog_640 p").html(text);
            jQuery("#dialog_640").dialog("open");
        }
        GrabPressTemplate.updateHeightValue();
    },
    preserveNativeSize : function(){
        document.getElementById("player_width").value = 640;
        GrabPressTemplate.updateHeightValue();
    },
    setConfirmUnload : function(on) {
        window.onbeforeunload = (on) ? GrabPressTemplate.unloadMessage : null;
    },
    unloadMessage : function() {
        return 'You have entered new data on this page.' +
        ' If you navigate away from this page without' +
        ' first saving your data, the changes will be' +
        ' lost.';
    },
    /* initialization */
    init : function() {
        if (jQuery("#message p").text() == "There was an error connecting to the API! Please try again later!") {
            jQuery(":input").attr('disabled', 'disabled');
        }
        jQuery('#dialog_300').dialog({
            autoOpen: false,
            width: 400,
            modal: true,
            resizable: false,
            buttons: {
                "Continue": function() {
                    jQuery(this).dialog("close");                    
                }
            }
         });
         jQuery('#dialog_640').dialog({
            autoOpen: false,
            width: 400,
            modal: true,
            resizable: false,
            buttons: {
                "Continue": function() {
                    jQuery(this).dialog("close");                    
                },
                "Preserve Native Size": function() {
                    GrabPressTemplate.preserveNativeSize();
                    jQuery(this).dialog("close");
                }
            }
          });
          GrabPressTemplate.updateHeightValue();
          jQuery("form input[name=width]").change(GrabPressTemplate.validateWidthValue);
 	  jQuery("form input[name=ratio]").change(GrabPressTemplate.validateWidthValue); 
          //leave page with modified form pop-up
          jQuery(':input', 'form').bind("change", function () {
              GrabPressTemplate.setConfirmUnload(true);
          });    
          jQuery('.template-form').submit(function(){window.onbeforeunload = null;});
    }
}

//initialize
jQuery(document).ready(
    GrabPressTemplate.init()
);


