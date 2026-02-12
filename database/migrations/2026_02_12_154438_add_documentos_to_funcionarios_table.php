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
        Schema::table('funcionarios', function (Blueprint $table) {
            $table->date('doc_ordem_servico')->nullable();
            $table->date('doc_ficha_epi')->nullable();
            $table->date('doc_nr06')->nullable();
            $table->date('doc_nr10')->nullable(); 
            $table->date('doc_nr12')->nullable();
            $table->date('doc_nr18')->nullable(); 
            $table->date('doc_nr35')->nullable();
            $table->date('doc_aso')->nullable();
            $table->date('doc_contrato_intermitente')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('funcionarios', function (Blueprint $table) {
            $table->dropColumn('doc_ordem_servico');
            $table->dropColumn('doc_ficha_epi');
            $table->dropColumn('doc_nr06');
            $table->dropColumn('doc_nr10'); 
            $table->dropColumn('doc_nr12');
            $table->dropColumn('doc_nr18'); 
            $table->dropColumn('doc_nr35');
            $table->dropColumn('doc_aso');
            $table->dropColumn('doc_contrato_intermitente');
        });
    }
};
