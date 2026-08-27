<?php

namespace App\Http\Controllers;

use App\Auth\OperadorAutenticado;
use App\Services\ApiCentral\ClienteApiInstalacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Painel do comandai-cliente. Por enquanto só a página inicial, com os
 * dados do operador logado e (Fase 4, Parte B) o status da conexão em
 * tempo real com a Comandai API. Nenhum módulo de negócio aqui ainda -
 * produtos, pedidos, mesas, comandas, caixa, estoque e delivery entram
 * a partir da Fase 5, um de cada vez.
 */
class PainelController extends Controller
{
    public function dashboard()
    {
        return view('painel.dashboard');
    }

    /**
     * Proxy da autenticação de canal privado do Reverb
     * (POST /api/v1/broadcasting/auth na Comandai API - ver
     * routes/channels.php e routes/api.php lá). O navegador nunca fala
     * direto com a API para isto: o token Sanctum do operador vive só
     * no lado do servidor (sessão do comandai-cliente, ver
     * App\Auth\GuardaOperador), nunca é exposto ao JavaScript. Este
     * endpoint é o único ponto onde resources/js/echo.js pede
     * autorização de canal - mesmo-origem, sem CORS, sem token no
     * navegador.
     */
    public function autorizarCanal(Request $request, ClienteApiInstalacao $clienteApi)
    {
        $dados = $request->validate([
            'socket_id' => ['required', 'string'],
            'channel_name' => ['required', 'string'],
        ]);

        /** @var OperadorAutenticado $operador */
        $operador = Auth::guard('web')->user();

        $resposta = $clienteApi->autenticado($operador->token)
            ->asForm()
            ->post('/api/v1/broadcasting/auth', $dados);

        return response($resposta->body(), $resposta->status())
            ->header('Content-Type', 'application/json');
    }
}
