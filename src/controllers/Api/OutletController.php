<?php

namespace Abs\OutletPkg\Api;

use Abs\BasicPkg\Traits\CrudTrait;
use App\Http\Controllers\Controller;
use App\Outlet;

class OutletController extends Controller {
	use CrudTrait;
	public $model = Outlet::class;
	public $successStatus = 200;

}