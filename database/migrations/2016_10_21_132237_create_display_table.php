<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDisplayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('displays', function (Blueprint $table) {
            $table->string('data_code')->index();
            $table->string('profile_stn_code')->index();

            $table->boolean('data');
            $table->boolean('collection');
            $table->timestamps();

            $table->primary(['data_code', 'profile_stn_code']);

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

        Schema::drop('displays');
    }
}
