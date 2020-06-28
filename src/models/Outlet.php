<?php

namespace Abs\OutletPkg;

use Abs\HelperPkg\Traits\SeederTrait;
use App\Company;
use App\Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Outlet extends Model {
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
