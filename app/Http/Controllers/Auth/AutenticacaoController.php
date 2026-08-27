<?php

namespace App\Http\Controllers\Auth;

use App\Auth\GuardaOperador;
use App\Http\Controllers\Controller;
use App\Services\ApiCentral\ClienteApiInstalacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Login/logout do operador no comandai-cliente. Nunca valida
 * credenciais aqui dentro - quem faz isso é a Comandai API (ver
 * ClienteApiInstalacao::login e AutenticacaoInstalacaoController::entrar,
 * do lado da comandai-api). Este controller só guarda o resultado na
 * sessão local (ver App\Auth\GuardaOperador). Mesmo desenho de
 * App\Http\Controllers\Auth\AutenticacaoController, do comandai-admin.
 */
class AutenticacaoController extends Controller
{
    public function mostrarFormulario()
    {
        return view('auth.entrar');
    }

    public function entrar(Request $request, ClienteApiInstalacao $clienteApi)
    {
        $dados = $request->validate([
            'email' => ['required', 'email'],
            'senha' => ['required', 'string'],
        ]);

        $resposta = $clienteApi->login($dados['email'], $dados['senha'], 'cliente-web');

        /** @var GuardaOperador $guard */
        $guard = Auth::guard('web');
        $guard->autenticar($resposta['usuario'], $resposta['token']);

        $request->session()->regenerate();

        return redirect()->intended(route('painel.dashboard'));
    }

    public function sair(Request $request, ClienteApiInstalacao $clienteApi)
    {
        /** @var GuardaOperador $guard */
        $guard = Auth::guard('web');

        if ($operador = $guard->user()) {
            $clienteApi->sair($operador->token);
        }

        $guard->esquecer();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('entrar');
    }
}
