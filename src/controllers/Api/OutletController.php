<?php

namespace Abs\OutletPkg\Api;

use Abs\BasicPkg\Traits\CrudTrait;
use App\Http\Controllers\Controller;
use App\Outlet;
use Illuminate\Http\Request;

class OutletController extends Controller {
	use CrudTrait;
	public $model = Outlet::class;
	public $successStatus = 200;

	public function getBusiness(Request $request) {
		$outlet = Outlet::find($request->outletId);
		if (!$outlet) {
			return response()->json([
				'success' => false,
				'error' => 'Outlet not found',
			]);
		}
		$business = $outlet->businesses()->filterCode($request->businessName)->first();
		if (!$business) {
			return response()->json([
				'success' => false,
				'error' => 'Business not mapped to outlet',
			]);
		}
		return response()->json([
			'success' => true,
			'business' => $business,
		]);

	}
}