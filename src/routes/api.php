<?php
Route::group(['namespace' => 'Abs\OutletPkg\Api', 'middleware' => ['api', 'auth:api']], function () {
	Route::group(['prefix' => 'api/outlet-pkg'], function () {
		Route::post('punch/status', 'PunchController@status');
	});
});