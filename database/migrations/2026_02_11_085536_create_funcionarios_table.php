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
        Schema::create('funcionarios', function (Blueprint $table) {
            $table->id();
            //dados pessoais
            $table->string('nome');
            $table->string('cpf')->unique()->nullable();
            $table->string('rg')->unique()->nullable();
            $table->date('data_nascimento')->nullable();
            $table->string('estado_nascimento')->nullable();
            $table->string('cidade_nascimento')->nullable();
            $table->string('estado_civil')->nullable();
            $table->string('sexo')->nullable(); 
            $table->integer('numero_filhos')->nullable();
            $table->string('foto_perfil')->nullable();

            //contato
            $table->string('email')->unique()->nullable();
            $table->string('telefone')->nullable();

            //endereço
            $table->string('cep')->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado')->nullable();

            //dados contratuais
            $table->string('cargo')->nullable();
            $table->enum('tipo_contrato', ['Fixo', 'Intermitente', 'PJ', 'Estagio'])->default('Fixo');
            $table->date('data_admissao')->nullable();
            $table->date('data_demissao')->nullable();

            //controle
            $table->boolean('ativo')->default(true);
            $table->text('observacoes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funcionarios');
    }
};
