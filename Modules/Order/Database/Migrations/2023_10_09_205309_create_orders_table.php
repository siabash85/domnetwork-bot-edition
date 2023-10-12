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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained("users")->cascadeOnDelete();
            $table->foreignId("server_id")->constrained("servers")->cascadeOnDelete();
            $table->foreignId("package_duration_id")->constrained("package_durations")->cascadeOnDelete();

            $table->foreignId("package_id")->constrained("packages")->cascadeOnDelete();
            $table->string("reference_code");
            $table->string("status");
            $table->string("payable_price");
            $table->string("price");
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
        Schema::dropIfExists('orders');
    }
};
