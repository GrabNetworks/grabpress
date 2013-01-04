<script type="text/javascript">
jQuery('#message').hide();
jQuery(document).ready(function($) {
    $('#wpbody-content').append($('#message'));
});
var gp_redirect_url = "admin.php?page=gp-template";
var gp_redirect_seconds = 4;
var gp_redirect_time;
function gp_redirect() {
  document.title='Redirecting in ' + gp_redirect_seconds + ' seconds';
  gp_redirect_seconds=gp_redirect_seconds-1;
  gp_redirect_time=setTimeout("gp_redirect()",1000);
  if (gp_redirect_seconds==-1) {
    clearTimeout(gp_redirect_time);
    document.title='Redirecting ...';
    self.location= gp_redirect_url;
  }
}
jQuery(function(){
  gp_redirect();
})
</script>

<div id="message-feed-created" class="updated fade">
  <p>
    Template updated successfully.  Redirecting in 5 seconds ...  If you are not redirected automatically, please press <a href="admin.php?page=gp-template">here</a>
  </p>
</div>
