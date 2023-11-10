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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string("limit")->nullable();
            $table->string("totalGB")->nullable();
            $table->string("expiryTime")->nullable();
            $table->string("tgId")->nullable();
            $table->string("subId")->nullable();
            $table->string("flow")->nullable();
            $table->text("uuid")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn("limit");
            $table->dropColumn("totalGB");
            $table->dropColumn("expiryTime");
            $table->dropColumn("tgId");
            $table->dropColumn("subId");
            $table->dropColumn("flow");
            $table->dropColumn("uuid");
        });
    }
};
