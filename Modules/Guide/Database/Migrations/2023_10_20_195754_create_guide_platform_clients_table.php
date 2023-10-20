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
        Schema::create('guide_platform_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId("guide_platform_id")->constrained("guide_platforms")->cascadeOnDelete();
            $table->string("name");
            $table->string("link")->nullable();
            $table->text("description");
            $table->string("video")->nullable();
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
        Schema::dropIfExists('guide_platform_clients');
    }
};
