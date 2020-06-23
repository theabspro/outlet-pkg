<?php

Route::group(['namespace' => 'App\Http\Controllers','middleware' => ['web', 'auth'], 'prefix' => 'outlet-pkg'], function () {

	//SHIFTS 
	Route::get('/shift/get-list', 'ShiftController@getShiftList')->name('getShiftList');
	Route::get('/shift/get-form-data', 'ShiftController@getShiftFormData')->name('getShiftFormData');
	Route::post('/shift/save', 'ShiftController@saveShift')->name('saveShift');
	Route::get('/shift/delete', 'ShiftController@deleteShift')->name('deleteShift');
	Route::get('/shift/get-filter-data', 'ShiftController@getShiftFilter')->name('getShiftFilter');

	//OUTLET GROUPS 
	Route::get('/outlet-group/get-list', 'OutletGroupController@getOutletGroupList')->name('getOutletGroupList');
	Route::get('/outlet-group/get-form-data', 'OutletGroupController@getOutletGroupFormData')->name('getOutletGroupFormData');
	Route::post('/outlet-group/save', 'OutletGroupController@saveOutletGroup')->name('saveOutletGroup');
	Route::get('/outlet-group/delete', 'OutletGroupController@deleteOutletGroup')->name('deleteOutletGroup');
	Route::get('/outlet-group/get-filter-data', 'OutletGroupController@getOutletGroupFilter')->name('getOutletGroupFilter');
});