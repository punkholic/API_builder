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
			$table->increments('id')->nullable();
			$table->string('title');
			$table->text('description');
			$table->string('status', 1);
			$table->string('xyz')->nullable();
			$table->date('created_at')->nullable();
			$table->dateTime('updated_at')->nullable();
			
	});


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Blog', function (Blueprint $table) {
            //
        });
    }
}
