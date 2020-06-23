<?php

namespace Abs\OutletPkg;

use Abs\HelperPkg\Traits\SeederTrait;
use App\Company;
use App\Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutletGroup extends Model {
	use SeederTrait;
	use SoftDeletes;
	protected $table = 'outlet_groups';
	public $timestamps = true;
	protected $fillable = [
		"id", "company_id", "name", "code",
	];

	public function getDateOfJoinAttribute($value) {
		return empty($value) ? '' : date('d-m-Y', strtotime($value));
	}

	public function setDateOfJoinAttribute($date) {
		return $this->attributes['date_of_join'] = empty($date) ? NULL : date('Y-m-d', strtotime($date));
	}

}
