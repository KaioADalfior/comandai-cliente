<?php

namespace App\Providers;

use App\Auth\GuardaOperador;
use App\Services\ApiCentral\ClienteApiInstalacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ClienteApiInstalacao precisa da URL base, do segredo
        // compartilhado (config/comandai.php) e do dominio desta
        // instalacao - resolvido a partir do Host da propria
        // requisicao que chegou no comandai-cliente (nao e resolvido
        // automaticamente pelo container por depender de um valor em
        // runtime, nao de configuracao estatica).
        $this->app->bind(ClienteApiInstalacao::class, function ($app) {
            /** @var Request $requisicao */
            $requisicao = $app->make('request');

            return new ClienteApiInstalacao(
                config('comandai.api_url'),
                (string) config('comandai.chave_interna'),
                strtolower((string) $requisicao->getHost()),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Guard proprio do comandai-cliente: nao ha tabela "users"
        // local, quem autentica de fato e a Comandai API (ver
        // App\Services\ApiCentral\ClienteApiInstalacao e
        // App\Auth\GuardaOperador). Configurado como driver do guard
        // "web" em config/auth.php, entao "auth"/"guest" (middlewares
        // padrao do Laravel) funcionam sem configuracao extra.
        Auth::extend('operador', function ($app) {
            return new GuardaOperador($app->make('session.store'));
        });
    }
}
