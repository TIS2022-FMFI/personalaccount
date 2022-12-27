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
        Schema::create('financial_operations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->foreign('account_id')->references("id")->on("accounts")->cascadeOnDelete();
            $table->string('title');
            $table->date('date');
            $table->unsignedBigInteger('operation_type_id');
            $table->foreign('operation_type_id')->references("id")->on("operation_types")->cascadeOnDelete();
            $table->string('subject');
            $table->unsignedDecimal('sum',10,2);
            $table->string('attachment')->unique()->nullable();
            $table->boolean('checked')->nullable();
            $table->integer('sap_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('financial_operations');
    }
};
