<?php

namespace Abs\OutletPkg\Api;

use Abs\BasicPkg\Traits\CrudTrait;
use App\Business;
use App\Http\Controllers\Controller;
use App\Outlet;
use DB;
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
		return $outlet->getBusiness($request->all());
	}
	public function business_outlet(Request $request) {
		$alserv = Business::where('code', 'ALSERV')->first();
		$business_outlet = DB::table('business_outlet')->where('outlet_id', $request['filter']['outlet'])->where('business_id', $alserv->id)->first();
		if (!$business_outlet) {
			return response()->json([
				'success' => false,
				'error' => 'Business Outlet not found',
			]);
		}
		return response()->json(['success' => true, 'business_outlet' => $business_outlet]);
	}
}