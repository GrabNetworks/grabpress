(function(){
    tinymce.PluginManager.requireLangPack('blist');
    tinymce.create('tinymce.plugins.blist', {
        init : function(ed, url){
            ed.addButton('blcss', {
                title: 'Insert Video',
                image: url+'/images/icons/g2.png',
                onclick : function() {
                    var postnumber = document.getElementById('post_ID').value;
                    var content = tinymce.activeEditor.getContent();
                    var form = jQuery('<form/>').attr('action','admin.php?page=catalog').attr('method','post');

                    jQuery('body').append('<form id="GrabPressForm" action="admin.php?page=catalog" method="post"></form>');
                    var inputPostId = jQuery('<input/>').attr('type',"hidden").attr("name","post_id").attr("value",postnumber);
                    var inputContentId = jQuery('<input/>').attr('type',"hidden").attr("name","pre_content2").attr("value",content);
                    
                    jQuery('#GrabPressForm').append(inputPostId);
                    jQuery('#GrabPressForm').append(inputContentId);
                    jQuery('#GrabPressForm').submit();
                }
            });
        },
        createControl : function(n, cm){
            return null;
        }
    });
    tinymce.PluginManager.add('blist', tinymce.plugins.blist);
})();