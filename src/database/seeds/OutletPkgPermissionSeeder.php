<?php
namespace Abs\OutletPkg\Database\Seeds;

use App\Permission;
use Illuminate\Database\Seeder;

class OutletPkgPermissionSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$permissions = [
			[
				'display_order' => 99,
				'parent' => null,
				'name' => 'outlet-masters',
				'display_name' => 'Outlet Masters',
			],

			//SHIFTS
			[
				'display_order' => 99,
				'parent' => 'outlet-masters',
				'name' => 'shifts',
				'display_name' => 'Shifts',
			],
			[
				'display_order' => 1,
				'parent' => 'shifts',
				'name' => 'add-shift',
				'display_name' => 'Add',
			],
			[
				'display_order' => 2,
				'parent' => 'shifts',
				'name' => 'edit-shift',
				'display_name' => 'Edit',
			],
			[
				'display_order' => 3,
				'parent' => 'shifts',
				'name' => 'delete-shift',
				'display_name' => 'Delete',
			],

			//OUTLET GROUPS
			[
				'display_order' => 99,
				'parent' => 'outlet-masters',
				'name' => 'outlet-groups',
				'display_name' => 'Outlet Groups',
			],
			[
				'display_order' => 1,
				'parent' => 'outlet-groups',
				'name' => 'add-outlet-group',
				'display_name' => 'Add',
			],
			[
				'display_order' => 2,
				'parent' => 'outlet-groups',
				'name' => 'edit-outlet-group',
				'display_name' => 'Edit',
			],
			[
				'display_order' => 3,
				'parent' => 'outlet-groups',
				'name' => 'delete-outlet-group',
				'display_name' => 'Delete',
			],

			//Business Outlet
			[
				'display_order' => 1,
				'parent' => 'outlet-masters',
				'name' => 'business-outlet-form',
				'display_name' => 'Business Outlet',
			],

		];
		Permission::createFromArrays($permissions);
	}
}