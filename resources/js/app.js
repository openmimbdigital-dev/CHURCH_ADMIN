import './bootstrap';
import Swal from 'sweetalert2';

window.Swal = Swal;

window.addEventListener('swal', (e) => {
    const detail = e.detail?.[0] ?? e.detail ?? {};
    Swal.fire({
        title: detail.title ?? '',
        text: detail.text ?? '',
        icon: detail.icon ?? 'success',
        timer: detail.timer ?? 2000,
        showConfirmButton: false,
    });
});
