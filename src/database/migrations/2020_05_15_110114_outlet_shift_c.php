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

		if (!Schema::hasTable('outlets')) {
			Schema::create('outlets', function (Blueprint $table) {

				$table->increments('id');
				$table->unsignedInteger('company_id');
				$table->string('code',191);
				$table->string('name',191);
				$table->unsignedInteger("created_by_id")->nullable();
				$table->unsignedInteger("updated_by_id")->nullable();
				$table->unsignedInteger("deleted_by_id")->nullable();
				$table->timestamps();
				$table->softDeletes();

				$table->foreign('company_id')->references('id')->on('companies')->onDelete('CASCADE')->onUpdate('cascade');
				$table->foreign("created_by_id")->references("id")->on("users")->onDelete("SET NULL")->onUpdate("cascade");
				$table->foreign("updated_by_id")->references("id")->on("users")->onDelete("SET NULL")->onUpdate("cascade");
				$table->foreign("deleted_by_id")->references("id")->on("users")->onDelete("SET NULL")->onUpdate("cascade");

				$table->unique(["company_id", "code"]);
				$table->unique(["company_id", "name"]);

			});
		}

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
		Schema::dropIfExists('outlets');
	}
}
