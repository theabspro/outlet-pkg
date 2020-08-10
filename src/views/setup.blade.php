@if(config('outlet-pkg.DEV'))
    <?php $outlet_pkg_prefix = '/packages/abs/outlet-pkg/src';?>
@else
    <?php $outlet_pkg_prefix = '';?>
@endif

<script type="text/javascript">
	app.config(['$routeProvider', function($routeProvider) {

	    $routeProvider.
	    //SHIFTS
	    when('/outlet-pkg/shift/list', {
	        template: '<shift-list></shift-list>',
	        title: 'Shifts',
	    }).
	    when('/outlet-pkg/shift/add', {
	        template: '<shift-form></shift-form>',
	        title: 'Add Shift',
	    }).
	    when('/outlet-pkg/shift/edit/:id', {
	        template: '<shift-form></shift-form>',
	        title: 'Edit Shift',
	    }).
	    when('/outlet-pkg/shift/card-list', {
	        template: '<shift-card-list></shift-card-list>',
	        title: 'Shift Card List',
	    }).

	    //OUTLET GROUPS
	    when('/outlet-pkg/outlet-group/list', {
	        template: '<outlet-group-list></outlet-group-list>',
	        title: 'Outlet Groups',
	    }).
	    when('/outlet-pkg/outlet-group/add', {
	        template: '<outlet-group-form></outlet-group-form>',
	        title: 'Add Outlet Group',
	    }).
	    when('/outlet-pkg/outlet-group/edit/:id', {
	        template: '<outlet-group-form></outlet-group-form>',
	        title: 'Edit Outlet Group',
	    });
	}]);

	//SHIFTS
    var shift_list_template_url = "{{asset($outlet_pkg_prefix.'/public/themes/'.$theme.'/outlet-pkg/shift/list.html')}}";
    var shift_form_template_url = "{{asset($outlet_pkg_prefix.'/public/themes/'.$theme.'/outlet-pkg/shift/form.html')}}";
    var shift_card_list_template_url = "{{asset($outlet_pkg_prefix.'/public/themes/'.$theme.'/outlet-pkg/shift/card-list.html')}}";
    var shift_modal_form_template_url = "{{asset($outlet_pkg_prefix.'/public/themes/'.$theme.'/outlet-pkg/partials/shift-modal-form.html')}}";

    //OUTLET GROUPS
    var outlet_group_list_template_url = "{{asset($outlet_pkg_prefix.'/public/themes/'.$theme.'/outlet-pkg/outlet-group/list.html')}}";
    var outlet_group_form_template_url = "{{asset($outlet_pkg_prefix.'/public/themes/'.$theme.'/outlet-pkg/outlet-group/form.html')}}";
    
</script>

