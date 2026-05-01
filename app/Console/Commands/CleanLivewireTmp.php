<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanLivewireTmp extends Command
{
    protected $signature = 'livewire:clean-tmp';
    protected $description = 'Nettoie les fichiers temporaires de Livewire';

    public function handle()
    {
        $tmpPath = storage_path('app/private/livewire-tmp');

        if (File::exists($tmpPath)) {
            $files = File::files($tmpPath);
            $deleted = count($files);

            File::cleanDirectory($tmpPath);

            $this->info("✅ {$deleted} fichier(s) temporaire(s) supprimé(s).");
        } else {
            $this->info("📁 Le dossier n'existe pas.");
        }

        return Command::SUCCESS;
    }
}
