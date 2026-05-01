<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('collectes', function (Blueprint $table) {
            $table->integer('category_field_index')->nullable();
        });
    }

    public function down()
    {
        Schema::table('collectes', function (Blueprint $table) {
            $table->dropColumn('category_field_index');
        });
    }
};
