<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OutletsAddGroupId extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('outlets', function (Blueprint $table) {
			$table->unsignedInteger('outlet_group_id')->nullable()->after('company_id');
			$table->foreign('outlet_group_id')->references('id')->on('outlet_groups')->onDelete('SET NULL')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('outlets', function (Blueprint $table) {
			$table->dropForeign('outlets_outlet_group_id_foreign');
			$table->dropColumn('outlet_group_id');
		});
	}
}
