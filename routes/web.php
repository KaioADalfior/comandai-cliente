<?php

use App\Http\Controllers\Auth\AutenticacaoController;
use App\Http\Controllers\Auth\TrocaSenhaController;
use App\Http\Controllers\PainelController;
use App\Http\Middleware\ExigirTrocaDeSenha;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas do comandai-cliente
|--------------------------------------------------------------------------
|
| Uso exclusivo dos operadores de UMA lanchonete - autentica contra a
| Comandai API (ver App\Services\ApiCentral\ClienteApiInstalacao), nunca
| acessa o banco da instalação diretamente.
|
| Fase 4 (fundação): só autenticação, troca de senha obrigatória e um
| painel inicial. Módulos de negócio (produtos, pedidos, mesas, PDV,
| KDS, caixa, estoque, delivery, relatórios) entram a partir da Fase 5.
|
*/

Route::redirect('/', '/entrar');

Route::middleware('guest')->group(function () {
    Route::get('entrar', [AutenticacaoController::class, 'mostrarFormulario'])->name('entrar');
    Route::post('entrar', [AutenticacaoController::class, 'entrar']);
});

Route::middleware('auth')->group(function () {
    Route::post('sair', [AutenticacaoController::class, 'sair'])->name('sair');

    // Fora do grupo com ExigirTrocaDeSenha de proposito: um operador
    // com deve_trocar_senha pendente precisa continuar alcancando
    // exatamente estas duas rotas, senao o redirecionamento do
    // middleware vira um loop.
    Route::prefix('trocar-senha')->name('trocar-senha.')->group(function () {
        Route::get('/', [TrocaSenhaController::class, 'mostrarFormulario'])->name('mostrar');
        Route::put('/', [TrocaSenhaController::class, 'atualizar'])->name('atualizar');
    });

    Route::middleware(ExigirTrocaDeSenha::class)->group(function () {
        Route::prefix('painel')->name('painel.')->group(function () {
            Route::get('/', [PainelController::class, 'dashboard'])->name('dashboard');

            // Fase 4, Parte B (Reverb): proxy de autenticacao de canal
            // privado, chamado por resources/js/echo.js. Ver o
            // docblock de PainelController::autorizarCanal - o
            // navegador nunca fala direto com a Comandai API aqui.
            Route::post('broadcasting/auth', [PainelController::class, 'autorizarCanal'])->name('broadcasting.auth');
        });
    });
});
