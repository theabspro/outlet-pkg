<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'outlet-pkg'], function () {

	//Shift
	Route::get('/shift/get-list', 'ShiftController@getShiftList')->name('getShiftList');
	Route::get('/shift/get-form-data', 'ShiftController@getShiftFormData')->name('getShiftFormData');
	Route::post('/shift/save', 'ShiftController@saveShift')->name('saveShift');
	Route::get('/shift/delete', 'ShiftController@deleteShift')->name('deleteShift');
	Route::get('/shift/get-filter-data', 'ShiftController@getShiftFilterData')->name('getShiftFilterData');

});