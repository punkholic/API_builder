<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog', function   ( Blueprint $table ) {
			$table->increments('id');
			$table->string('title');
			$table->text('description');
			$table->string('password');
			$table->string('status', 1);
			$table->string('abc')->nullable();
			
	});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blog', function (Blueprint $table) {
            //
        });
    }
}
