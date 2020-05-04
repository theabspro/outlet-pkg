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
			//Shifts
			[
				'display_order' => 99,
				'parent' => null,
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

		];
		Permission::createFromArrays($permissions);
	}
}