<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Trocar senha — Comandaí</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="flex min-h-screen items-center justify-center bg-slate-950 antialiased">
        <div class="w-full max-w-sm px-4">
            <div class="mb-8 text-center">
                <h1 class="text-xl font-semibold text-white">Comandaí</h1>
                <p class="text-sm text-slate-400">
                    @if (auth()->user()?->deveTrocarSenha)
                        Antes de continuar, defina uma nova senha
                    @else
                        Trocar senha
                    @endif
                </p>
            </div>

            <form method="POST" action="{{ route('trocar-senha.atualizar') }}" class="rounded-lg border border-slate-800 bg-slate-900 p-6 shadow-xl shadow-black/20">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="senha_atual" class="mb-1 block text-sm font-medium text-slate-300">Senha atual</label>
                    <input
                        id="senha_atual"
                        type="password"
                        name="senha_atual"
                        required
                        autofocus
                        class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-indigo-400 focus:ring focus:ring-indigo-900/50 focus:outline-none"
                    >
                </div>

                <div class="mb-2">
                    <label for="nova_senha" class="mb-1 block text-sm font-medium text-slate-300">Nova senha</label>
                    <input
                        id="nova_senha"
                        type="password"
                        name="nova_senha"
                        required
                        minlength="8"
                        class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-indigo-400 focus:ring focus:ring-indigo-900/50 focus:outline-none"
                    >
                </div>
                <p class="mb-6 text-xs text-slate-500">Mínimo de 8 caracteres.</p>

                <button
                    type="submit"
                    class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-indigo-500"
                >
                    Salvar nova senha
                </button>

                @unless (auth()->user()?->deveTrocarSenha)
                    <a
                        href="{{ route('painel.dashboard') }}"
                        class="mt-3 block text-center text-sm text-slate-400 transition-colors hover:text-slate-200"
                    >
                        Cancelar
                    </a>
                @endunless
            </form>
        </div>

        @if ($errors->any())
            <script>
                window.addEventListener('DOMContentLoaded', function () {
                    window.exibirAlerta({
                        icon: 'error',
                        title: 'Não foi possível trocar a senha',
                        text: @json($errors->first()),
                    });
                });
            </script>
        @endif
    </body>
</html>
