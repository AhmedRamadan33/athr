import './bootstrap';
import $ from 'jquery';
import select2 from 'select2';
import Swal from 'sweetalert2';

window.$ = window.jQuery = $;
select2(window, $);

const toast = Swal.mixin({
    toast: true,
    position: 'top-start',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (el) => {
        el.addEventListener('mouseenter', Swal.stopTimer);
        el.addEventListener('mouseleave', Swal.resumeTimer);
    },
});

window.showToast = (icon, title) => toast.fire({ icon, title });

window.confirmAction = (form, message = 'هل أنت متأكد؟') => {
    Swal.fire({
        title: 'تأكيد',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'نعم',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#a86c18',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });

    return false;
};

window.toggleMobileMenu = () => {
    document.getElementById('mobile-menu')?.classList.toggle('hidden');
};

document.addEventListener('DOMContentLoaded', () => {
    const flash = document.getElementById('flash-data');

    if (flash) {
        const success = flash.dataset.success;
        const error = flash.dataset.error;

        if (success) window.showToast('success', success);
        if (error) window.showToast('error', error);
    }

    $('select.js-select2').each(function () {
        $(this).select2({
            width: '100%',
            dir: 'rtl',
            placeholder: $(this).data('placeholder') || 'اختر...',
            allowClear: Boolean($(this).data('allow-clear')),
        });
    });
});
