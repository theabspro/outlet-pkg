<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OutletShiftC extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {

		if (!Schema::hasTable('outlet_shift')) {
			Schema::create('outlet_shift', function (Blueprint $table) {

				$table->unsignedInteger('outlet_id');
				$table->unsignedInteger('shift_id');
				$table->time('start_time');
				$table->time('end_time');

				$table->foreign('outlet_id')->references('id')->on('outlets')->onDelete('CASCADE')->onUpdate('cascade');
				$table->foreign('shift_id')->references('id')->on('shifts')->onDelete('CASCADE')->onUpdate('cascade');

				$table->unique(["outlet_id", "shift_id"]);

			});
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('outlet_shift');
	}
}
