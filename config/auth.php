<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | O guard "web" usa o driver customizado "operador" (registrado em
    | App\Providers\AppServiceProvider::boot), nao o driver "session"
    | padrao do Laravel. Nao existe um "provider" de usuarios aqui de
    | proposito: o comandai-cliente nao tem tabela local de usuarios -
    | quem autentica de fato e a Comandai API, contra a tabela
    | "usuarios" da propria instalacao (ver
    | App\Services\ApiCentral\ClienteApiInstalacao::login e
    | App\Auth\GuardaOperador). Mantendo o nome "web" (em vez de um
    | nome novo como "operador") os middlewares padrao "auth"/"guest"
    | do Laravel continuam funcionando sem nenhuma configuracao extra.
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'operador',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
