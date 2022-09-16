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
        Schema::create('child_products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id')->references('id')->on('products');
            $table->string('title');
            $table->decimal('price');
            $table->text('description')->nullable();
            $table->longText('details')->comment("array values");
            $table->string('location')->nullable();
            $table->integer('minimum')->nullable();
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('child_products');
    }
};
