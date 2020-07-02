<?php
Route::group(['namespace' => 'App\Http\Controllers\Api', 'middleware' => ['api', 'auth:api']], function () {

	Route::group(['prefix' => 'api'], function () {
		Route::group(['prefix' => 'outlet'], function () {

			$controller = 'Outlet';

			Route::get('options', $controller . 'Controller@options');
			Route::post('get-business', $controller . 'Controller@getBusiness');

		});
	});
});