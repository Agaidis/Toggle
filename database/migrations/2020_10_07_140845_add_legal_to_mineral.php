<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLegalToMineral extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mineral_owners', function (Blueprint $table) {
            $table->integer('LeaseId')->index();
            $table->float('AreaAcres')->nullable();
            $table->float('Abstract')->nullable();
            $table->string('AbstractNo')->nullable();
            $table->string('Block')->nullable();
            $table->string('CountyParish')->nullable();
            $table->string('Created')->nullable();
            $table->string('Geometry')->nullable();
            $table->string('LatitudeWGS84')->nullable();
            $table->string('LongitudeWGS84')->nullable();
            $table->string('Grantee')->nullable();
            $table->string('GranteeAddress')->nullable();
            $table->string('GranteeAlias')->nullable();
            $table->string('Grantor')->nullable();
            $table->string('GrantorAddress')->nullable();
            $table->string('MaxDepth')->nullable();
            $table->string('MinDepth')->nullable();
            $table->string('Range')->nullable();
            $table->string('Section')->nullable();
            $table->string('Township')->nullable();
            $table->String('RecordDate')->nullable();
            $table->string('EffectiveDate')->nullable();
            $table->string('ExpirationofPrimaryTerm')->nullable();
            $table->string('ExtTermMonths')->nullable();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mineral_owners', function (Blueprint $table) {
            $table->dropColumn('LeaseId');
            $table->dropColumn('AreaAcres');
            $table->dropColumn('Abstract');
            $table->dropColumn('AbstractNo');
            $table->dropColumn('Block');
            $table->dropColumn('CountyParish');
            $table->dropColumn('Created');
            $table->dropColumn('Geometry');
            $table->dropColumn('LatitudeWGS84');
            $table->dropColumn('LongitudeWGS84');
            $table->dropColumn('Grantee');
            $table->dropColumn('GranteeAddress');
            $table->dropColumn('GranteeAlias');
            $table->dropColumn('Grantor');
            $table->dropColumn('GrantorAddress');
            $table->dropColumn('MaxDepth');
            $table->dropColumn('MinDepth');
            $table->dropColumn('Range');
            $table->dropColumn('Section');
            $table->dropColumn('Township');
            $table->dropColumn('RecordDate');
            $table->dropColumn('EffectiveDate')->default('n/a');
            $table->dropColumn('ExpirationofPrimaryTerm')->default('n/a');
            $table->dropColumn('ExtTermMonths')->default('n/a');
        });
    }
}
