<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('category_id')->references('id')->on('categories');
            $table->uuid('media_id')->references('id')->on('medias');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('divider_type')->comment("like class, grade, type");
            $table->string('unit')->comment("like kg, pcs, box");
            $table->string('slug', 255);
            $table->boolean('status')->default(false);
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
        Schema::dropIfExists('products');
    }
};
