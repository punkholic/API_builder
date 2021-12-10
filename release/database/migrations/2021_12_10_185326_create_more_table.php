<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('more', function   ( Blueprint $table ) {
			$table->increments('id');
			$table->text('name')->nullable();
			$table->integer('age')->nullable();
			$table->decimal('price', 8, 2)->nullable();
			$table->string('password')->nullable();
			
	});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('more', function (Blueprint $table) {
            //
        });
    }
}
