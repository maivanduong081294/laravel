<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTable extends Migration
{
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('file_name');
            $table->string('disk');
            $table->string('mime_type');
            $table->string('type');
            $table->unsignedBigInteger('author_id');
            $table->timestamps();

            $table->foreign('author_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });

        Schema::create('mediables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('media_id')->index();
            $table->string('type');
            $table->tinyInteger('remove_bg')->default(0);
            $table->tinyInteger('webp')->default(0);

            $table->foreign('media_id')
                  ->references('id')
                  ->on('media')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mediables');
        Schema::dropIfExists('media');
    }
}
