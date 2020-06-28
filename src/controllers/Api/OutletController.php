<?php

namespace Abs\OutletPkg\Api;

use Abs\BasicPkg\Traits\CrudTrait;
use App\Http\Controllers\Controller;
use App\Outlet;
use Illuminate\Http\Request;
use DB;
use Auth;
use Yajra\Datatables\Datatables;
use App\Outlet;

class WarrantyJobOrderRequestController extends Controller {
	use CrudTrait;
	public $model = Outlet::class;
	public $successStatus = 200;

	public function __construct() {
		$this->data['theme'] = config('custom.theme');
	}
	
	public function getOutlets(Request $r)
	{
		$key = $r->key;
        $list = Outlet::where('company_id', Auth::user()->company_id)
            ->select(
                'id',
                'name',
                'code'
            )
            ->where(function ($q) use ($key) {
                $q->where('name', 'like', $key . '%')
                    ->orWhere('code', 'like', $key . '%')
                ;
            })
            ->get();
        return response()->json($list);
		/*$this->data['outlets'] = DB::select('id','code as name')->where('company_id', Auth::user()->company_id)->get();
		return response()->json($this->data);*/
		
	}
}