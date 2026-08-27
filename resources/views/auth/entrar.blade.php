<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Entrar — Comandaí</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="flex min-h-screen items-center justify-center bg-slate-950 antialiased">
        <div class="w-full max-w-sm px-4">
            <div class="mb-8 text-center">
                <h1 class="text-xl font-semibold text-white">Comandaí</h1>
                <p class="text-sm text-slate-400">Acesso da equipe da lanchonete</p>
            </div>

            <form method="POST" action="{{ route('entrar') }}" class="rounded-lg border border-slate-800 bg-slate-900 p-6 shadow-xl shadow-black/20">
                @csrf

                <div class="mb-4">
                    <label for="email" class="mb-1 block text-sm font-medium text-slate-300">E-mail</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-indigo-400 focus:ring focus:ring-indigo-900/50 focus:outline-none"
                    >
                </div>

                <div class="mb-6">
                    <label for="senha" class="mb-1 block text-sm font-medium text-slate-300">Senha</label>
                    <input
                        id="senha"
                        type="password"
                        name="senha"
                        required
                        class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-indigo-400 focus:ring focus:ring-indigo-900/50 focus:outline-none"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-indigo-500"
                >
                    Entrar
                </button>
            </form>
        </div>

        @if ($errors->any())
            <script>
                window.addEventListener('DOMContentLoaded', function () {
                    window.exibirAlerta({
                        icon: 'error',
                        title: 'Não foi possível entrar',
                        text: @json($errors->first()),
                    });
                });
            </script>
        @endif
    </body>
</html>
