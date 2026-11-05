<?php

namespace App\Console\Commands;

use App\Models\Noticia;
use Illuminate\Console\Command;

class PublicarNoticiasProgramadas extends Command
{
    protected $signature = 'noticias:publicar-programadas';

    protected $description = 'Publica las noticias cuya fecha de programación ya se cumplió';

    public function handle(): int
    {
        $publicadas = 0;

        Noticia::query()
            ->where('status', 'programado')
            ->whereNotNull('programada_para')
            ->where('programada_para', '<=', now())
            ->chunkById(100, function ($noticias) use (&$publicadas) {
                foreach ($noticias as $noticia) {
                    $noticia->status = 'publicado';
                    $noticia->save();
                    $publicadas++;
                }
            });

        $this->info("Noticias publicadas: {$publicadas}");

        return self::SUCCESS;
    }
}
