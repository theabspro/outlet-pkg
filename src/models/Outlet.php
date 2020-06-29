<?php

namespace Abs\OutletPkg;

use Abs\HelperPkg\Traits\SeederTrait;
use App\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Outlet extends BaseModel {
	use SeederTrait;
	use SoftDeletes;
	protected $table = 'outlets';
	protected $fillable = [
		'id',
		'company_id',
		'name',
		'code',
		'central_cashier_id',
		'region_id',
		'address',
		'pincode',
		'state_id',
	];

}
