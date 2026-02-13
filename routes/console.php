<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('financeiro:atualizar-atrasados')->daily();

Schedule::command('contratos:verificar-validade')->daily();

Schedule::command('financeiro:renovar-fixas')->yearlyOn(1, 1, '01:00');

Schedule::command('rh:verificar-documentos')->dailyAt('01:00');
