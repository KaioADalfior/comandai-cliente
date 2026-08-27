@extends('layouts.painel', ['titulo' => 'Painel'])

@section('conteudo')
    <div class="mx-auto max-w-2xl">
        <div class="rounded-lg border border-slate-800 bg-slate-900 p-6 shadow-xl shadow-black/20">
            <h2 class="text-lg font-semibold text-white">Olá, {{ auth()->user()->nome }}</h2>
            <p class="mt-1 text-sm text-slate-400">
                Você está autenticado como <span class="capitalize">{{ auth()->user()->papel }}</span> nesta lanchonete.
            </p>

            <dl class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-md border border-slate-800 bg-slate-950 px-4 py-3">
                    <dt class="text-xs font-medium tracking-wider text-slate-500 uppercase">E-mail</dt>
                    <dd class="mt-1 text-sm text-slate-100">{{ auth()->user()->email }}</dd>
                </div>

                <div class="rounded-md border border-slate-800 bg-slate-950 px-4 py-3">
                    <dt class="text-xs font-medium tracking-wider text-slate-500 uppercase">Tempo real</dt>
                    <dd
                        id="indicador-tempo-real"
                        class="mt-1 flex items-center gap-2 text-sm text-slate-100"
                    >
                        <span id="indicador-tempo-real-bolinha" class="inline-block h-2 w-2 rounded-full bg-slate-600"></span>
                        <span id="indicador-tempo-real-texto">Desconectado</span>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    {{--
        Fase 4, Parte B: assina o canal privado desta instalação
        (window.iniciarTempoReal, ver resources/js/echo.js) e reflete o
        estado da conexão no indicador acima - sem refresh. O
        "evento.de.teste" só existe nesta fase para provar o cano
        funcionando (ver App\Events\Instalacao\EventoDeTeste, na
        comandai-api) e some quando a Fase 5.3 (KDS) trouxer eventos
        reais para este mesmo canal.
    --}}
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            const bolinha = document.getElementById('indicador-tempo-real-bolinha');
            const texto = document.getElementById('indicador-tempo-real-texto');
            const instalacaoId = @json(auth()->user()->instalacaoId);

            if (!bolinha || !texto || !instalacaoId) {
                return;
            }

            function marcar(conectado, rotulo) {
                texto.textContent = rotulo;
                bolinha.classList.toggle('bg-emerald-500', conectado);
                bolinha.classList.toggle('bg-slate-600', !conectado);
            }

            const echo = window.iniciarTempoReal();

            echo.connector.pusher.connection.bind('connected', () => marcar(true, 'Conectado'));
            echo.connector.pusher.connection.bind('unavailable', () => marcar(false, 'Desconectado'));
            echo.connector.pusher.connection.bind('failed', () => marcar(false, 'Desconectado'));
            echo.connector.pusher.connection.bind('disconnected', () => marcar(false, 'Desconectado'));

            echo.private(`instalacao.${instalacaoId}.cozinha`)
                .listen('.evento.de.teste', (evento) => {
                    window.exibirAlerta({
                        icon: 'info',
                        title: 'Evento de teste recebido',
                        text: evento.mensagem,
                        timer: 4000,
                        showConfirmButton: false,
                    });
                });
        });
    </script>
@endsection
