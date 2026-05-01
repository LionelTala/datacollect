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
        Schema::create('donnees_collectes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collecte_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Qui a saisi
            $table->json('data'); // {"age":34,"categorie":"A","photo":"storage/processed/xxx.jpg"}
            $table->json('fichiers_processes')->nullable(); // Chemins versions traitées
            $table->ipAddress()->nullable(); // Pour audit
            $table->timestamps();

            $table->index(['collecte_id', 'user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donnees_collectes');
    }
};
