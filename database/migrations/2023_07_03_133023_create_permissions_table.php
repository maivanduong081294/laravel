<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name',255);
            $table->string('icon',255)->nullable();
            $table->string('link',255);
            $table->string('group_ids',255);
            $table->string('user_ids',255);
            $table->integer('parent_id')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('hidden')->default(0);
            $table->tinyInteger('super_admin')->default(0);
            $table->timestamps();
        });

        Schema::table('permissions',function(Blueprint $table) {
            $table->unsignedBigInteger('route_id')->nullable()->after('link');
            $table->foreign('route_id')->references('id')->on('routes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions');
    }
}
