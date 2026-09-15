import './bootstrap';
import $ from 'jquery';
import select2 from 'select2';
import Swal from 'sweetalert2';

window.$ = window.jQuery = $;
select2(window, $);

const toast = Swal.mixin({
    toast: true,
    position: document.documentElement.dir === 'rtl' ? 'top-start' : 'top-end',
    showConfirmButton: false,
    timer: 3500,
    timerProgressBar: true,
    didOpen: (el) => {
        el.addEventListener('mouseenter', Swal.stopTimer);
        el.addEventListener('mouseleave', Swal.resumeTimer);
    },
});

window.showToast = (icon, title) => toast.fire({ icon, title });

window.toggleSidebar = () => {
    document.getElementById('sidebar')?.classList.toggle('translate-x-full');
    document.getElementById('sidebar-backdrop')?.classList.toggle('hidden');
};

window.togglePasswordVisibility = (button) => {
    const input = button.previousElementSibling;
    const isHidden = input.type === 'password';

    input.type = isHidden ? 'text' : 'password';
    button.querySelector('.pw-eye-open')?.classList.toggle('hidden', isHidden);
    button.querySelector('.pw-eye-closed')?.classList.toggle('hidden', !isHidden);
};

window.switchTab = (containerId, key) => {
    document.querySelectorAll(`#${containerId} [data-tab-panel]`).forEach((panel) => {
        panel.classList.toggle('hidden', panel.dataset.tabPanel !== key);
    });

    document.querySelectorAll(`#${containerId} [data-tab-button]`).forEach((button) => {
        const active = button.dataset.tabButton === key;
        button.classList.toggle('border-brand-600', active);
        button.classList.toggle('text-brand-700', active);
        button.classList.toggle('font-semibold', active);
        button.classList.toggle('border-transparent', !active);
        button.classList.toggle('text-zinc-500', !active);
    });
};

window.confirmDelete = (form, message = 'هل أنت متأكد من الحذف؟ لا يمكن التراجع عن هذا الإجراء.') => {
    Swal.fire({
        title: 'تأكيد الحذف',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'نعم، احذف',
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
