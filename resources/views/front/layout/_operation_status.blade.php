@if (Session::has('flash_notification.message'))
    <div id="flash_notification_div" class="alert alert-{{ Session::get('flash_notification.level') }}">
        <button type="button" class="close" style="margin-top: 0px !important;padding: 0px !important;" data-dismiss="alert" aria-hidden="true">&times;</button>

        {{ Session::get('flash_notification.message') }}
    </div>
<script type="text/javascript">
	setTimeout(function(){
	  $('#flash_notification_div').hide();
	},5000);
</script>
@endif
