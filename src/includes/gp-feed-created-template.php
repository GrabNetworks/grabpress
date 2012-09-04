<script type="text/javascript">
var gp_redirect_url = "admin.php?page=autoposter";
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

<div id="message" class="updated fade">
  <p>
    Feed created successfully.  Redirecting in 5 seconds ...  If you are not redirected automatically, please press <a href="admin.php?page=autoposter">here</a>
  </p>
</div>
