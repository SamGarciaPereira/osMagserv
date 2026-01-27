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
        $table->string('cep_obra', 9)->nullable();
        $table->string('cidade_obra')->nullable();
        $table->string('uf_obra', 2)->nullable(); 
        $table->string('logradouro_obra')->nullable();
        $table->string('bairro_obra')->nullable();
        $table->string('numero_obra')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orcamentos', function (Blueprint $table) {
            $table->dropColumn([
                'cep_obra',
                'cidade_obra',
                'uf_obra',
                'logradouro_obra',
                'bairro_obra',
                'numero_obra'
            ]);
        });
    }
};
