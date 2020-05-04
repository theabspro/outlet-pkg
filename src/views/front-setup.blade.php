@if(config('outlet-pkg.DEV'))
    <?php $outlet_pkg_prefix = '/packages/abs/outlet-pkg/src';?>
@else
    <?php $outlet_pkg_prefix = '';?>
@endif

<script type="text/javascript">
    var shifts_voucher_list_template_url = "{{asset($outlet_pkg_prefix.'/public/themes/'.$theme.'/outlet-pkg/shift/shift.html')}}";
</script>
<script type="text/javascript" src="{{asset($outlet_pkg_prefix.'/public/themes/'.$theme.'/outlet-pkg/shift/controller.js')}}"></script>
