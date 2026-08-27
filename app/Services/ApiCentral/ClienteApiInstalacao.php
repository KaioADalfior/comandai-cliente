<?php

namespace App\Services\ApiCentral;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Único ponto de contato do comandai-cliente com a Comandai API. O
 * comandai-cliente nunca acessa o banco da instalação diretamente (ver
 * ARQ-COMANDAI-02) - toda leitura/escrita passa por aqui.
 *
 * Diferença central em relação a ClienteApiPlataforma (comandai-admin):
 * o Host HTTP desta chamada nunca é o domínio da lanchonete (é o
 * endereço da própria API) - então toda requisição carrega
 * explicitamente "X-Comandai-Dominio" (o domínio desta instalação,
 * resolvido a partir do próprio Host da requisição que chegou no
 * comandai-cliente - ver App\Providers\AppServiceProvider) e
 * "X-Comandai-Chave-Interna" (segredo compartilhado, ver
 * config('comandai.chave_interna')) - é o que ResolvedorDeInstalacao,
 * do lado da API, exige fora de local/testing.
 */
class ClienteApiInstalacao
{
    public function __construct(
        private readonly string $urlBase,
        private readonly string $chaveInterna,
        private readonly string $dominioAtual,
    ) {
    }

    /**
     * Autentica um operador da lanchonete contra a Comandai API. Em
     * caso de credenciais inválidas, traduz o 422/401 da API para uma
     * ValidationException do próprio comandai-cliente, para o
     * formulário de login reaproveitar $errors normalmente.
     */
    public function login(string $email, string $senha, string $dispositivo): array
    {
        $resposta = $this->semToken()
            ->post('/api/v1/autenticacao/entrar', [
                'email' => $email,
                'senha' => $senha,
                'dispositivo' => $dispositivo,
            ]);

        if ($resposta->status() === 422 || $resposta->status() === 401) {
            throw ValidationException::withMessages([
                'email' => ['E-mail ou senha inválidos.'],
            ]);
        }

        if ($resposta->failed()) {
            Log::error('Falha inesperada ao autenticar operador na Comandai API.', [
                'status' => $resposta->status(),
                'dominio' => $this->dominioAtual,
            ]);

            throw ValidationException::withMessages([
                'email' => ['Não foi possível entrar agora. Tente novamente em instantes.'],
            ]);
        }

        return $resposta->json();
    }

    /**
     * Revoga o token Bearer atual na Comandai API. Best-effort de
     * propósito: o logout local (GuardaOperador::esquecer) sempre
     * acontece, mesmo que a API esteja fora do ar nesse instante.
     */
    public function sair(string $token): void
    {
        try {
            $this->autenticado($token)->post('/api/v1/autenticacao/sair');
        } catch (\Throwable $erro) {
            Log::warning('Falha ao encerrar sessao na Comandai API durante logout.', [
                'erro' => $erro->getMessage(),
            ]);
        }
    }

    /**
     * Requisição pré-configurada (base URL + domínio/chave interna +
     * Bearer do operador) para o resto da aplicação reaproveitar - a
     * partir da Fase 5, todo módulo de negócio consome a API por aqui.
     */
    public function autenticado(string $token): PendingRequest
    {
        return $this->semToken()->withToken($token);
    }

    private function semToken(): PendingRequest
    {
        return Http::baseUrl($this->urlBase)
            ->acceptJson()
            ->withHeaders([
                'X-Comandai-Dominio' => $this->dominioAtual,
                'X-Comandai-Chave-Interna' => $this->chaveInterna,
            ]);
    }
}
