<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test', function   ( Blueprint $table ) {
			$table->increments('id')->nullable();
			$table->text('name');
			$table->integer('age');
			$table->decimal('price', 8, 2);
			$table->string('password')->nullable();
			$table->date('created_at')->nullable();
			
	});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Test', function (Blueprint $table) {
            //
        });
    }
}
