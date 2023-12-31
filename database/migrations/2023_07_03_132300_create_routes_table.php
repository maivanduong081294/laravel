<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('title',255);
            $table->string('name',255)->nullable();
            $table->string('uri',255);
            $table->string('controller',255);
            $table->string('function',255);
            $table->string('method',255)->default('GET');
            $table->string('middleware',255)->nullable();
            $table->integer('parent_id')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('hidden')->default(0);
            $table->tinyInteger('super_admin')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('routes');
    }
}
