<?php

namespace Abs\OutletPkg;

use Abs\HelperPkg\Traits\SeederTrait;
use App\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Outlet extends BaseModel {
	use SeederTrait;
	use SoftDeletes;
	public static $AUTO_GENERATE_CODE = false;

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

	protected static $excelColumnRules = [
		'Outlet Group Name' => [
			'table_column_name' => 'outlet_group_id',
			'rules' => [
				'fk' => [
					'class' => 'App\OutletGroup',
					'foreign_table_column' => 'name',
				],
			],
		],
		'Code' => [
			'table_column_name' => 'code',
			'rules' => [
				'required' => [
				],
			],
		],
		'Name' => [
			'table_column_name' => 'name',
			'rules' => [
				'required' => [
				],
			],
		],
		'State Code' => [
			'table_column_name' => 'state_id',
			'rules' => [
				'required' => [
				],
				'fk' => [
					'class' => 'App\State',
					'foreign_table_column' => 'code',
				],
			],
		],
		'Region Code' => [
			'table_column_name' => 'region_id',
			'rules' => [
				'nullable' => [
				],
				'fk' => [
					'class' => 'App\Region',
					'foreign_table_column' => 'code',
				],
			],
		],
		'Address' => [
			'table_column_name' => 'address',
			'rules' => [
				'nullable' => [
				],
			],
		],
		'Pincode' => [
			'table_column_name' => 'pincode',
			'rules' => [
				'nullable' => [
				],
			],
		],
		'Central Cashier' => [
			'table_column_name' => 'central_cashier_id',
			'rules' => [
				'nullable' => [
				],
				'fk' => [
					'class' => 'App\User',
					'foreign_table_column' => 'name',
				],
			],
		],
	];

	public static function saveFromObject($record_data) {
		$record = [
			'Company Code' => $record_data->company_code,
			'Outlet Group Name' => $record_data->outlet_group_name,
			'Code' => $record_data->code,
			'Name' => $record_data->name,
			'State Code' => $record_data->state_code,
			'Region Code' => $record_data->region_code,
			'Address' => $record_data->address,
			'Pincode' => $record_data->pincode,
			'Central Cashier' => $record_data->central_cashier,
		];
		return static::saveFromExcelArray($record);
	}

	public static function saveFromExcelArray($record_data) {
		$errors = [];
		$company = Company::where('code', $record_data['Company Code'])->first();
		if (!$company) {
			return [
				'success' => false,
				'errors' => ['Invalid Company : ' . $record_data['Company Code']],
			];
		}

		if (!isset($record_data['created_by'])) {
			$admin = $company->admin();

			if (!$admin) {
				return [
					'success' => false,
					'errors' => ['Default Admin user not found'],
				];
			}
			$created_by = $admin->id;
		} else {
			$created_by = $record_data['created_by'];
		}
		if (empty($record_data['Code'])) {
			$errors[] = 'Code is empty';
		}

		if (count($errors) > 0) {
			return [
				'success' => false,
				'errors' => $errors,
			];
		}
		$record = self::firstOrNew([
			'company_id' => $company->id,
			'code' => $record_data['Code'],
		]);

		$result = Self::validateAndFillExcelColumns($record_data, Static::$excelColumnRules, $record);
		if (!$result['success']) {
			return $result;
		}
		$record->created_by = $created_by;
		$record->save();
		return [
			'success' => true,
		];
	}

	public function getBusiness($params) {
		$business = $this->businesses()->filterCode($params['businessName'])->first();
		if (!$business) {
			return [
				'success' => false,
				'error' => 'Business not mapped to outlet',
			];
		}
		return [
			'success' => true,
			'business' => $business,
		];

	}
}
