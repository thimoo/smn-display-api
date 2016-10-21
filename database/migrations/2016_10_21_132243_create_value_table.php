<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateValueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('values', function (Blueprint $table) {
            $table->string('data_code')->index();
            $table->string('profile_stn_code')->index();
            $table->dateTimeTz('date')->index();

            $table->float('value');
            $table->enum('tag', ['original', 'smoothed', 'substituted', 'no-data']);
            $table->timestamps();

            $table->primary(['data_code', 'profile_stn_code', 'date']);

            $table->foreign('data_code')
                ->references('code')
                ->on('data');
            
            $table->foreign('profile_stn_code')
                ->references('stn_code')
                ->on('profiles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();

        Schema::drop('values');
    }
}
