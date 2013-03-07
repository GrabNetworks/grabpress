<script type="text/javascript">
jQuery('#message').hide();
jQuery(document).ready(function($) {
    $('#wpbody-content').append($('#message'));
});
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

<div id="message-feed-created" class="updated fade">
  <p>
    Feed created successfully.  Redirecting in 5 seconds ...  If you are not redirected automatically, please press <a href="admin.php?page=autoposter">here</a>
  </p>
</div>

<?php 
    if(isset($_REQUEST[ 'page']) && $_REQUEST[ 'page'] == 'autoposter' && isset($_REQUEST[ 'action']) &&  (($_REQUEST[ 'action'] == 'update') || ($_REQUEST[ 'action'] == 'modify')) )
      {
        if ( GrabPress::$environment == 'grabqa' ) {
          $times = array( '15 mins', '30  mins', '45 mins', '01 hr', '02 hrs', '06 hrs', '12 hrs', '01 day', '02 days', '03 days' );
        }
        else {
          $times = array( '06 hrs', '12 hrs', '01 day', '02 days', '03 days' );
        } 

        if ( GrabPress::$environment == 'grabqa' ) {                        
          $values = array( 15*60,  30*60,  45*60, 60*60, 120*60, 360*60, 720*60, 1440*60, 2880*60, 4320*60 );
        }
        else {
          $values = array( 360*60, 720*60, 1440*60, 2880*60, 4320*60 );
        }

        if(isset($_REQUEST['schedule'])){
          for ( $o = 0; $o < count( $times ); $o++ ) {
            $time = $times[$o];
            $value = $values[$o];
            if($value == $_REQUEST["schedule"]){
              GrabPress::$message = 'A new draft or post will be created every '.$time.' if videos that meet your search criteria have been added to our catalog.';           
            }
          }
        }
    }
        //echo GrabPress::$message = 'A new draft or post will be created every '.$time.' if videos that meet your search criteria have been added to our catalog.'; 
?>
<div class="grabgear">
    <?php echo '<img src="'.plugin_dir_url( __FILE__ ).'images/grabgear.gif" alt="Grab">'; ?>
</div>

