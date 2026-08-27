import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

// Tema escuro (slate/indigo) igual ao resto da plataforma - definido uma
// vez aqui pra qualquer <script> inline das views só chamar
// window.exibirAlerta/window.confirmarAcao sem repetir configuração.
// Mesmo padrão do comandai-admin.
const temaSwal = {
    background: '#0f172a',
    color: '#e2e8f0',
    confirmButtonColor: '#4f46e5',
    cancelButtonColor: '#334155',
};

window.exibirAlerta = (opcoes) => Swal.fire({ ...temaSwal, ...opcoes });

window.confirmarAcao = (opcoes) => Swal.fire({
    ...temaSwal,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Confirmar',
    cancelButtonText: 'Cancelar',
    ...opcoes,
});

// Registra window.iniciarTempoReal(...) globalmente, mas NAO conecta a
// nada sozinho - so a pagina do painel chama essa funcao (ver
// resources/views/painel/dashboard.blade.php). Login/troca de senha
// nunca abrem WebSocket.
import './echo.js';
