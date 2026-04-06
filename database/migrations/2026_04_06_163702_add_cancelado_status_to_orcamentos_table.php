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
      Schema::table('orcamentos', function (Blueprint $table) {
        DB::statement("ALTER TABLE orcamentos MODIFY COLUMN status ENUM('Pendente', 'Em Andamento', 'Em Validação', 'Validado', 'Enviado', 'Aprovado', 'Cancelado') NOT NULL DEFAULT 'Pendente'");
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      Schema::table('orcamentos', function (Blueprint $table) {
        DB::statement("ALTER TABLE orcamentos MODIFY COLUMN status ENUM('Pendente', 'Em Andamento', 'Em Validação', 'Validado', 'Enviado', 'Aprovado', 'Cancelado') NOT NULL DEFAULT 'Pendente'");
      });
    }
};
