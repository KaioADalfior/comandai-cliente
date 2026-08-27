<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sessoes do comandai-cliente (SESSION_DRIVER=database) - a unica
 * tabela "de framework" que este app precisa. Nao existe tabela
 * "users" local: o operador autentica contra a Comandai API, que
 * guarda os dados reais em usuarios (banco da instalacao) - ver
 * App\Services\ApiCentral\ClienteApiInstalacao e App\Auth\GuardaOperador.
 * Esta migration substitui a "create_users_table" padrao do skeleton
 * "laravel/laravel", que criava "users"/"password_reset_tokens" junto -
 * nenhuma das duas se aplica aqui. Mesmo raciocinio do comandai-admin.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
