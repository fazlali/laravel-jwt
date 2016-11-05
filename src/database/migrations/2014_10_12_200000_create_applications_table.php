<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('secret',32);
            $table->string('hook',128);
            $table->integer('user_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('applications', function(Blueprint $table){
           $table->foreign('user_id')
               ->references('id')->on('users')
               ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('applications', function(Blueprint $table){
           $table->dropForeign('applications_user_id_foreign');
        });
        Schema::drop('applications');
    }
}
