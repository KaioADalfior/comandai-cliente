<?php

return [

    /*
    |--------------------------------------------------------------------------
    | URL base da Comandai API
    |--------------------------------------------------------------------------
    |
    | O comandai-cliente nao acessa o banco da instalacao diretamente -
    | fala sempre com a Comandai API (ver
    | App\Services\ApiCentral\ClienteApiInstalacao). Em desenvolvimento
    | local, normalmente e onde o "php artisan serve" da pasta
    | comandai-api esta rodando.
    |
    */

    'api_url' => env('COMANDAI_API_URL', 'http://127.0.0.1:8000'),

    /*
    |--------------------------------------------------------------------------
    | Segredo compartilhado com a Comandai API
    |--------------------------------------------------------------------------
    |
    | Toda chamada do comandai-cliente pra API e servidor-a-servidor -
    | o Host HTTP dela nunca e o dominio da lanchonete. Esse segredo,
    | enviado no cabecalho X-Comandai-Chave-Interna junto de
    | X-Comandai-Dominio, e o que permite a API confiar em qual
    | instalacao esta sendo servida (ver ResolvedorDeInstalacao, do
    | lado da comandai-api). Precisa ser o MESMO valor configurado la
    | (config('comandai.chave_interna_cliente')). Nunca commitado - so
    | em .env.
    |
    */

    'chave_interna' => env('COMANDAI_CHAVE_INTERNA'),

];
