import './bootstrap';
import Swal from 'sweetalert2';

window.Swal = Swal;

window.addEventListener('notify', (event) => {
    const data = event.detail || {};
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: data.type || 'success',
        title: data.title || 'Berhasil',
        text: data.message || '',
        showConfirmButton: false,
        timer: 2600,
        timerProgressBar: true,
    });
});

