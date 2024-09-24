@if (Session::has('flash_notification.message'))
    <div class="alert alert-{{ Session::get('flash_notification.level') }}">
        <button type="button" class="close" style="margin-top: 0px !important;padding: 0px !important;" data-dismiss="alert" aria-hidden="true">&times;</button>

        {{ Session::get('flash_notification.message') }}
    </div>
@endif

@if (Session::has('stripe_account_id_not_set'))
    <div class="alert alert-danger">
        <button type="button" class="close" style="margin-top: 0px !important;padding: 0px !important;" data-dismiss="alert" aria-hidden="true">&times;</button>

        {{ Session::get('stripe_account_id_not_set') }}
    </div>
@endif
