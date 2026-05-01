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
        Schema::create('logs_collecte', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('collecte_id')->nullable()->constrained();
            $table->string('action'); // 'saisie', 'export', 'view_stats', 'modification_data', 'suppression_data'
            $table->json('metadata')->nullable(); // {"format":"csv","row_count":150}
            $table->timestamp('created_at')->useCurrent();

            $table->index(['collecte_id', 'action', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs_collectes');
    }
};
