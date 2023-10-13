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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId("server_id")->constrained("servers")->cascadeOnDelete();
            $table->foreignId("package_duration_id")->constrained("package_durations")->cascadeOnDelete();
            $table->foreignId("package_id")->constrained("packages")->cascadeOnDelete();
            $table->decimal('price', $precision = 64, $scale = 4)->default(0);
            $table->text("link")->nullable();
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
        Schema::dropIfExists('services');
    }
};
