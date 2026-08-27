<?php

namespace App\Http\Middleware;

use App\Auth\OperadorAutenticado;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Força o operador com "deve_trocar_senha" pendente (ver
 * AutenticacaoInstalacaoController::entrar, na Comandai API - a flag
 * vem do próprio provisionamento, no primeiro usuário "proprietario"
 * criado automaticamente) a trocar a senha antes de acessar qualquer
 * outra tela do comandai-cliente. Aplicado só ao grupo de rotas
 * autenticadas que NÃO são a própria tela de troca de senha nem o
 * logout (ver routes/web.php) - por isso nunca precisa checar a rota
 * atual aqui dentro, o que evitaria um loop de redirecionamento.
 */
class ExigirTrocaDeSenha
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var OperadorAutenticado|null $operador */
        $operador = Auth::guard('web')->user();

        if ($operador?->deveTrocarSenha) {
            return redirect()->route('trocar-senha.mostrar');
        }

        return $next($request);
    }
}
