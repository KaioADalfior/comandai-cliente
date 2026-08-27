<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Representa, dentro do comandai-cliente, o usuário da lanchonete dono
 * da sessão atual (garçom, caixa, gerente, o "proprietario" criado
 * pelo provisionamento...). Não é um Eloquent model - o comandai-cliente
 * nunca acessa o banco da instalação diretamente (ver GuardaOperador),
 * só guarda os dados que a Comandai API já validou e devolveu no login.
 */
class OperadorAutenticado implements Authenticatable
{
    public function __construct(
        public readonly int $id,
        public readonly string $nome,
        public readonly string $email,
        public readonly ?string $papel,
        public readonly bool $deveTrocarSenha,
        public readonly ?int $instalacaoId,
        public readonly string $token,
    ) {
    }

    public static function deSessao(array $usuario, string $token): self
    {
        return new self(
            id: $usuario['id'],
            nome: $usuario['nome'],
            email: $usuario['email'],
            papel: $usuario['papel'] ?? null,
            deveTrocarSenha: (bool) ($usuario['deve_trocar_senha'] ?? false),
            instalacaoId: $usuario['instalacao_id'] ?? null,
            token: $token,
        );
    }

    public function paraSessao(): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'email' => $this->email,
            'papel' => $this->papel,
            'deve_trocar_senha' => $this->deveTrocarSenha,
            'instalacao_id' => $this->instalacaoId,
        ];
    }

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthIdentifier(): int
    {
        return $this->id;
    }

    public function getAuthPasswordName(): string
    {
        return 'senha';
    }

    // Sem senha local: a autenticacao de fato acontece na Comandai API
    // (ver AutenticacaoInstalacaoController::entrar). Estes metodos
    // existem so para satisfazer o contrato Authenticatable.
    public function getAuthPassword(): string
    {
        return '';
    }

    public function getRememberToken(): ?string
    {
        return null;
    }

    public function setRememberToken($value): void
    {
        // Sem "lembrar-me": o token Bearer ja vive na sessao do
        // comandai-cliente, que segue a expiracao normal da sessao do
        // Laravel.
    }

    public function getRememberTokenName(): string
    {
        return '';
    }
}
