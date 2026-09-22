<?php

namespace App\Console\Commands;

use App\Services\MicroclimaWatchdogService;
use Illuminate\Console\Command;

class MicroclimaWatchdogCommand extends Command
{
    protected $signature='microseed:watchdog';
    protected $description='Verifica la comunicación del sensor y aplica un estado seguro al microclima.';

    public function handle(MicroclimaWatchdogService $watchdog): int
    {
        $resultado=$watchdog->verificar();

        $this->info(
            ($resultado['accion']??'sin cambio').' - '.
            ($resultado['motivo']??'Sin información.')
        );

        return self::SUCCESS;
    }
}
