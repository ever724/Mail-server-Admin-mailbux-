<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstimateItemsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('estimate_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('estimate_id');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('description')->nullable();
            $table->string('discount_type');
            $table->decimal('quantity', 15, 2);
            $table->decimal('discount', 15, 2)->nullable();
            $table->unsignedBigInteger('discount_val')->nullable();
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('total');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('estimate_id')->references('id')->on('estimates')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('estimate_items');
    }
}
