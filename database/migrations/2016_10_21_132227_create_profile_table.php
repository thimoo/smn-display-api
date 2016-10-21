<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->string('stn_code')->unique()->index();
            $table->integer('altitude');
            $table->text('infos');
            $table->dateTimeTz('last_update')->nullable();
            $table->dateTimeTz('last_time_online')->nullable();
            $table->boolean('online')->default(true);
            $table->timestamps();
            $table->primary('stn_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('profiles');
    }
}
