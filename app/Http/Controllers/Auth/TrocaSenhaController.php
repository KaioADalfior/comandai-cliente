<?php

namespace App\Http\Controllers\Auth;

use App\Auth\GuardaOperador;
use App\Http\Controllers\Controller;
use App\Services\ApiCentral\ClienteApiInstalacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Troca de senha do operador - sempre obrigatória no primeiro login do
 * "proprietario" criado pelo provisionamento (ver
 * App\Http\Middleware\ExigirTrocaDeSenha), mas a mesma tela serve pra
 * qualquer operador trocar a própria senha quando quiser, a qualquer
 * momento.
 */
class TrocaSenhaController extends Controller
{
    public function mostrarFormulario()
    {
        return view('auth.trocar-senha');
    }

    public function atualizar(Request $request, ClienteApiInstalacao $clienteApi)
    {
        $dados = $request->validate([
            'senha_atual' => ['required', 'string'],
            'nova_senha' => ['required', 'string', 'min:8'],
        ]);

        /** @var GuardaOperador $guard */
        $guard = Auth::guard('web');
        $operador = $guard->user();

        $resposta = $clienteApi->autenticado($operador->token)
            ->put('/api/v1/autenticacao/senha', $dados);

        if ($resposta->status() === 401) {
            $guard->esquecer();
            $request->session()->invalidate();

            return redirect()->route('entrar')->with('erro', 'Sessão expirada. Entre novamente.');
        }

        if ($resposta->status() === 422) {
            throw ValidationException::withMessages($resposta->json('errors', [
                'senha_atual' => ['Não foi possível trocar a senha. Confira os dados informados.'],
            ]));
        }

        $resposta->throw();

        $guard->atualizarUsuario($resposta->json());

        return redirect()->route('painel.dashboard')->with('sucesso', 'Senha atualizada.');
    }
}
