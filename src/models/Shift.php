<?php

namespace Abs\OutletPkg;

use Abs\HelperPkg\Traits\SeederTrait;
use App\BaseModel;
use App\Company;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shift extends BaseModel {
	use SeederTrait;
	use SoftDeletes;
	protected $table = 'shifts';
	public $timestamps = true;
	protected $fillable = [
		"id", "company_id", "name",
	];

	public function getDateOfJoinAttribute($value) {
		return empty($value) ? '' : date('d-m-Y', strtotime($value));
	}

	public function setDateOfJoinAttribute($date) {
		return $this->attributes['date_of_join'] = empty($date) ? NULL : date('Y-m-d', strtotime($date));
	}

	//issue: naming
	// public function outlet_shift() {
	// 	return $this->belongsToMany('App\Outlet', 'outlet_shift', 'shift_id', 'outlet_id');
	// }
	public function outletShifts() {
		return $this->belongsToMany('App\Outlet', 'outlet_shift', 'shift_id', 'outlet_id');
	}

	protected static $excelColumnRules = [
		'Name' => [
			'table_column_name' => 'name',
			'rules' => [
				'required' => [
				],
			],
		],
	];

	public static function saveFromObject($record_data) {
		$record = [
			'Company Code' => $record_data->company_code,
			'Name' => $record_data->name,
		];
		return static::saveFromExcelArray($record);
	}

	public static function saveFromExcelArray($record_data) {
		try {
			$errors = [];
			$company = Company::where('code', $record_data['Company Code'])->first();
			if (!$company) {
				return [
					'success' => false,
					'errors' => ['Invalid Company : ' . $record_data['Company Code']],
				];
			}

			if (!isset($record_data['created_by_id'])) {
				$admin = $company->admin();

				if (!$admin) {
					return [
						'success' => false,
						'errors' => ['Default Admin user not found'],
					];
				}
				$created_by_id = $admin->id;
			} else {
				$created_by_id = $record_data['created_by_id'];
			}

			if (count($errors) > 0) {
				return [
					'success' => false,
					'errors' => $errors,
				];
			}

			$record = Self::firstOrNew([
				'company_id' => $company->id,
				'name' => $record_data['Name'],
			]);

			$result = Self::validateAndFillExcelColumns($record_data, Static::$excelColumnRules, $record);
			if (!$result['success']) {
				return $result;
			}

			$record->company_id = $company->id;
			$record->created_by_id = $created_by_id;
			$record->save();
			return [
				'success' => true,
			];
		} catch (\Exception $e) {
			return [
				'success' => false,
				'errors' => [
					$e->getMessage(),
				],
			];
		}
	}
	/*public static function createFromObject($record_data) {

			$errors = [];
			$company = Company::where('code', $record_data->company)->first();
			if (!$company) {
				dump('Invalid Company : ' . $record_data->company);
				return;
			}

			$admin = $company->admin();
			if (!$admin) {
				dump('Default Admin user not found');
				return;
			}

			$type = Config::where('name', $record_data->type)->where('config_type_id', 89)->first();
			if (!$type) {
				$errors[] = 'Invalid Tax Type : ' . $record_data->type;
			}

			if (count($errors) > 0) {
				dump($errors);
				return;
			}

			$record = self::firstOrNew([
				'company_id' => $company->id,
				'name' => $record_data->tax_name,
			]);
			$record->type_id = $type->id;
			$record->created_by_id = $admin->id;
			$record->save();
			return $record;
		}
	*/
	public static function getList($params = [], $add_default = true, $default_text = 'Select Shift') {
		$list = Collect(Self::select([
			'id',
			'name',
		])
				->orderBy('name')
				->get());
		if ($add_default) {
			$list->prepend(['id' => '', 'name' => $default_text]);
		}
		return $list;
	}

}
