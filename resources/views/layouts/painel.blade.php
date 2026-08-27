<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $titulo ?? 'Painel' }} — Comandaí</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
        <div class="flex min-h-screen">
            <aside class="flex w-64 shrink-0 flex-col border-r border-slate-800 bg-slate-900">
                <div class="border-b border-slate-800 px-6 py-5">
                    <span class="text-lg font-semibold text-white">Comandaí</span>
                    <span class="block text-xs font-medium tracking-wider text-slate-500 uppercase">Operação</span>
                </div>

                {{--
                    Fase 4: só o painel inicial. Os módulos de negócio
                    (produtos, pedidos, mesas, PDV, KDS, caixa, estoque,
                    delivery, relatórios) entram na Fase 5+, um de cada vez -
                    cada um ganha sua própria entrada aqui quando existir.
                --}}
                <nav class="flex flex-col gap-1 p-3">
                    <a
                        href="{{ route('painel.dashboard') }}"
                        class="rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('painel.dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' }}"
                    >
                        Painel
                    </a>
                </nav>
            </aside>

            <div class="flex min-w-0 flex-1 flex-col">
                <header class="flex h-16 shrink-0 items-center justify-between gap-4 border-b border-slate-800 bg-slate-900 px-6">
                    <h1 class="truncate text-lg font-semibold text-white">{{ $titulo ?? 'Painel' }}</h1>

                    <div class="flex shrink-0 items-center gap-4">
                        <div class="text-right text-sm leading-tight">
                            <div class="font-medium text-slate-100">{{ auth()->user()->nome }}</div>
                            <div class="text-xs text-slate-500 capitalize">{{ auth()->user()->papel }}</div>
                        </div>

                        <a
                            href="{{ route('trocar-senha.mostrar') }}"
                            class="rounded-md border border-slate-700 px-3 py-1.5 text-sm text-slate-300 transition-colors hover:border-slate-600 hover:bg-slate-800"
                        >
                            Trocar senha
                        </a>

                        <form method="POST" action="{{ route('sair') }}">
                            @csrf
                            <button type="submit" class="rounded-md border border-slate-700 px-3 py-1.5 text-sm text-slate-300 transition-colors hover:border-slate-600 hover:bg-slate-800">
                                Sair
                            </button>
                        </form>
                    </div>
                </header>

                <main class="flex-1 overflow-y-auto p-6">
                    @yield('conteudo')
                </main>
            </div>
        </div>

        @if (session('sucesso'))
            <script>
                window.addEventListener('DOMContentLoaded', function () {
                    window.exibirAlerta({
                        icon: 'success',
                        text: @json(session('sucesso')),
                        timer: 1800,
                        showConfirmButton: false,
                    });
                });
            </script>
        @elseif (session('erro'))
            <script>
                window.addEventListener('DOMContentLoaded', function () {
                    window.exibirAlerta({
                        icon: 'error',
                        text: @json(session('erro')),
                    });
                });
            </script>
        @endif
    </body>
</html>
