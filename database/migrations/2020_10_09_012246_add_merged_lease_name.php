<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMergedLeaseName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permits', function (Blueprint $table) {
            $table->string('merged_lease_name')->nullable();
            $table->boolean('is_merged')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permits', function (Blueprint $table) {
            $table->dropColumn('merged_lease_name');
        });
    }
}
