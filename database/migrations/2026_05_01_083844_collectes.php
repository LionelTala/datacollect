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
        Schema::create('collectes', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['brouillon', 'active', 'fermee'])->default('brouillon');

            // Configuration des labels (exemple JSON)
            // [{"name":"age","label":"Âge","type":"number","required":true},
            //  {"name":"photo","label":"Photo","type":"file_image","required":false,"max_size_mb":5},
            //  {"name":"categorie","label":"Catégorie","type":"select","options":["A","B","C"],"required":true}]
            $table->json('config_schema');

            // Règles de prétraitement (fichiers)
            // {"image": {"max_width":800,"max_height":600,"format":"jpg"},
            //  "audio": {"sample_rate":16000,"channels":1,"format":"wav"}}
            $table->json('preprocess_rules')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collecte');

    }
};
