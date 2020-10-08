<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropLeaseIdFromPhone extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('owner_phone_numbers', function (Blueprint $table) {
            $table->dropColumn('LeaseId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('owner_phone_numbers', function (Blueprint $table) {
            $table->integer('LeaseId');
        });
    }
}
