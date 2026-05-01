<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('collecte_id')->constrained();
            $table->enum('format', ['csv', 'json', 'xls']);
            $table->enum('type', ['full', 'filtered']); // Toutes données ou filtrées
            $table->json('filters_applied')->nullable(); // {"user_id":5,"date_from":"2025-01-01"}
            $table->string('file_path')->nullable(); // Stockage du fichier exporté
            $table->integer('row_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exports');
    }
};
