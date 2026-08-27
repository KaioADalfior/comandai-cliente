<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Session\Session;

/**
 * Guard de autenticacao do comandai-cliente. Nao existe um provider
 * Eloquent por baixo (nao ha tabela "users" local) - quem autentica de
 * fato e a Comandai API (ver ClienteApiInstalacao::login). Este guard
 * so guarda, na sessao criptografada do proprio Laravel, os dados do
 * operador e o token Bearer que a API devolveu, e os expoe pelo
 * contrato padrao Guard para que auth()/@auth/@guest e o middleware
 * "auth"/"guest" funcionem normalmente em toda a aplicacao.
 *
 * Mesmo desenho de App\Auth\GuardaEquipeDak, do comandai-admin.
 */
class GuardaOperador implements Guard
{
    private const CHAVE_USUARIO = 'operador.usuario';

    private const CHAVE_TOKEN = 'operador.token';

    private ?OperadorAutenticado $operador = null;

    private bool $resolvido = false;

    public function __construct(
        private readonly Session $sessao,
    ) {
    }

    /**
     * Chamado pelo AutenticacaoController apos a Comandai API validar
     * o e-mail/senha e devolver o usuario + token.
     */
    public function autenticar(array $usuarioDados, string $token): void
    {
        $this->operador = OperadorAutenticado::deSessao($usuarioDados, $token);
        $this->resolvido = true;

        $this->sessao->put(self::CHAVE_USUARIO, $usuarioDados);
        $this->sessao->put(self::CHAVE_TOKEN, $token);
    }

    /**
     * Chamado pelo TrocaSenhaController apos um "PUT autenticacao/senha"
     * bem sucedido na Comandai API. Nao e login - mantem o token Bearer
     * atual (a API nao emite um novo so por causa da troca de senha) e
     * so atualiza os dados do operador guardados na sessao, pra
     * "deve_trocar_senha" virar false sem precisar de novo login.
     */
    public function atualizarUsuario(array $usuarioDados): void
    {
        $token = $this->sessao->get(self::CHAVE_TOKEN);

        if (! is_string($token)) {
            return;
        }

        $this->operador = OperadorAutenticado::deSessao($usuarioDados, $token);
        $this->resolvido = true;

        $this->sessao->put(self::CHAVE_USUARIO, $usuarioDados);
    }

    /**
     * Encerra a sessao local. Nao chama a API (ver
     * ClienteApiInstalacao::sair para o logout best-effort do lado da
     * API) - isso e responsabilidade do controller, antes de chamar
     * este metodo.
     */
    public function esquecer(): void
    {
        $this->operador = null;
        $this->resolvido = true;

        $this->sessao->forget([self::CHAVE_USUARIO, self::CHAVE_TOKEN]);
    }

    public function user(): ?OperadorAutenticado
    {
        if ($this->resolvido) {
            return $this->operador;
        }

        $this->resolvido = true;

        $usuarioDados = $this->sessao->get(self::CHAVE_USUARIO);
        $token = $this->sessao->get(self::CHAVE_TOKEN);

        if (! is_array($usuarioDados) || ! is_string($token)) {
            return $this->operador = null;
        }

        return $this->operador = OperadorAutenticado::deSessao($usuarioDados, $token);
    }

    public function check(): bool
    {
        return $this->user() !== null;
    }

    public function guest(): bool
    {
        return ! $this->check();
    }

    public function id(): int|string|null
    {
        return $this->user()?->getAuthIdentifier();
    }

    public function validate(array $credentials = []): bool
    {
        // Este guard nao valida credenciais diretamente - o login passa
        // sempre por ClienteApiInstalacao::login. Ver AutenticacaoController.
        return false;
    }

    public function hasUser(): bool
    {
        return $this->operador !== null;
    }

    public function setUser(\Illuminate\Contracts\Auth\Authenticatable $user): static
    {
        if ($user instanceof OperadorAutenticado) {
            $this->operador = $user;
            $this->resolvido = true;
        }

        return $this;
    }
}
