import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Cliente Echo/Reverb (Fase 4, Parte B). So variaveis PUBLICAS aqui -
// VITE_REVERB_APP_KEY/HOST/PORT/SCHEME - nunca o REVERB_APP_SECRET, que
// fica exclusivamente no .env da comandai-api.
//
// A conexao Echo NAO se conecta sozinha ao importar este modulo: quem
// decide quando conectar e a propria pagina (hoje so
// painel/dashboard.blade.php), via window.iniciarTempoReal(...) -
// assim a pagina de login/troca de senha nunca abre um WebSocket a toa.
window.Pusher = Pusher;

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

/**
 * Autorizacao de canal privado: NUNCA fala direto com a Comandai API a
 * partir do navegador (o token Sanctum do operador so existe no lado do
 * servidor - ver App\Auth\GuardaOperador). Em vez disso, chama a rota
 * local /painel/broadcasting/auth (mesma origem, sem CORS), que o
 * PainelController::autorizarCanal repassa pra API autenticado com o
 * token guardado na sessao.
 */
function autorizarCanal(channel, socketId, callback) {
    fetch('/painel/broadcasting/auth', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
        },
        credentials: 'same-origin',
        body: JSON.stringify({ socket_id: socketId, channel_name: channel.name }),
    })
        .then((resposta) => {
            if (!resposta.ok) {
                throw new Error(`Falha ao autorizar canal (HTTP ${resposta.status})`);
            }

            return resposta.json();
        })
        .then((dados) => callback(false, dados))
        .catch((erro) => callback(true, erro));
}

let echo = null;

/**
 * Cria (uma unica vez) e devolve a conexao Echo/Reverb. Chamado pela
 * pagina que precisa de tempo real - hoje so o painel.
 */
window.iniciarTempoReal = function iniciarTempoReal() {
    if (echo) {
        return echo;
    }

    echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
        authorizer: (channel) => ({
            authorize: (socketId, callback) => autorizarCanal(channel, socketId, callback),
        }),
    });

    window.Echo = echo;

    return echo;
};
