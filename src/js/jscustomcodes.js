(function(){
    tinymce.PluginManager.requireLangPack('blist');
    tinymce.create('tinymce.plugins.blist', {
        init : function(ed, url){
            ed.addButton('blcss', {
                title: 'GrabPress',
                image: url+'/images/icons/g2.png',
                onclick : function() {
                    var postnumber = document.getElementById('post_ID').value;
                    alert(postnumber);
                    var content = tinymce.activeEditor.getContent();
                    alert(content); 
                    var form = jQuery('<form/>').attr('action','admin.php?page=catalogeditor').attr('method','post');            

                    jQuery('body').append('<form id="GrabPressForm" action="admin.php?page=catalogeditor" method="post"></form>');
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
        },
    });
    tinymce.PluginManager.add('blist', tinymce.plugins.blist);
})();