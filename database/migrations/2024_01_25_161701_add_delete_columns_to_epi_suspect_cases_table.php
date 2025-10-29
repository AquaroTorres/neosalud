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
        Schema::table('epi_suspect_cases', function (Blueprint $table) {
            //
            $table->string('delete_reason')->nullable()->after('id');
            $table->unsignedBigInteger('delete_user_id')->nullable()->after('delete_reason')->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('epi_suspect_cases', function (Blueprint $table) {
            //

            $table->dropColumn('delete_reason');
            $table->dropForeign(['delete_user_id']);
            $table->dropColumn('delete_user_id');
        });
    }
};
