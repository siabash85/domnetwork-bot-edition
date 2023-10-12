<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained('users');
            $table->foreignId("payment_method_id")->constrained('payment_methods');
            $table->string("paymentable_type");
            $table->integer("paymentable_id");
            $table->string('invoice_id')->nullable();
            $table->string('ref_num')->nullable();
            $table->string("reference_code");
            $table->decimal('amount', $precision = 64, $scale = 4);
            $table->string("status");
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
        Schema::dropIfExists('payments');
    }
};
