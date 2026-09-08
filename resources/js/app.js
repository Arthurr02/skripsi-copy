import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const extensionFromName = (name) => name.split('.').pop()?.toLowerCase() ?? '';

const extensionsFromAccept = (accept) => {
    const mimeExtensions = {
        'application/pdf': ['pdf'],
        'image/jpeg': ['jpg', 'jpeg'],
        'image/png': ['png'],
        'application/msword': ['doc'],
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document': ['docx'],
        'application/vnd.ms-excel': ['xls'],
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': ['xlsx'],
        'application/zip': ['zip'],
        'application/x-zip-compressed': ['zip'],
    };

    return accept
        .split(',')
        .map((entry) => entry.trim().toLowerCase())
        .flatMap((entry) => {
            if (entry.startsWith('.')) return [entry.slice(1)];
            if (entry === 'image/*') return ['jpg', 'jpeg', 'png'];

            return mimeExtensions[entry] ?? [];
        });
};

const hasExpectedClientSignature = async (file, extension) => {
    const bytes = new Uint8Array(await file.slice(0, 8).arrayBuffer());
    const startsWith = (...signature) => signature.every((byte, index) => bytes[index] === byte);

    if (extension === 'pdf') return startsWith(0x25, 0x50, 0x44, 0x46, 0x2d);
    if (extension === 'png') return startsWith(0x89, 0x50, 0x4e, 0x47, 0x0d, 0x0a, 0x1a, 0x0a);
    if (extension === 'jpg' || extension === 'jpeg') return startsWith(0xff, 0xd8, 0xff);
    if (extension === 'doc' || extension === 'xls') return startsWith(0xd0, 0xcf, 0x11, 0xe0, 0xa1, 0xb1, 0x1a, 0xe1);
    if (extension === 'docx' || extension === 'xlsx') return startsWith(0x50, 0x4b, 0x03, 0x04);
    if (extension === 'zip') return startsWith(0x50, 0x4b, 0x03, 0x04) || startsWith(0x50, 0x4b, 0x05, 0x06) || startsWith(0x50, 0x4b, 0x07, 0x08);

    return false;
};

const showRejectedUpload = (message) => {
    const options = {
        icon: 'error',
        title: 'Berkas ditolak',
        text: message,
    };

    if (window.rekrutmenAlert) {
        window.rekrutmenAlert(options);
    } else {
        window.alert(message);
    }
};

const validateFilesForInput = async (input, files) => {
    const allowedExtensions = extensionsFromAccept(input.accept);
    if (allowedExtensions.length === 0) {
        return { valid: false, message: 'Jenis berkas untuk formulir ini belum dikonfigurasi dengan aman.' };
    }

    const validity = await Promise.all(Array.from(files).map(async (file) => {
        const extension = extensionFromName(file.name);

        return allowedExtensions.includes(extension)
            && hasExpectedClientSignature(file, extension);
    }));

    return validity.every(Boolean)
        ? { valid: true }
        : { valid: false, message: 'Format atau isi berkas tidak sesuai. Pilih berkas yang valid sesuai format yang diperbolehkan.' };
};

// Drop zones assign files through DataTransfer, which does not trigger the native
// file-picker `change` path. This helper gives those interactions the same early
// rejection, while SafeUploadedFile remains the authoritative server-side check.
window.rekrutmenValidateDroppedFiles = async (input, files) => {
    try {
        const result = await validateFilesForInput(input, files);
        if (result.valid) {
            input.setCustomValidity('');

            return files;
        }

        input.value = '';
        input.setCustomValidity(result.message);
        showRejectedUpload(result.message);
    } catch {
        input.value = '';
        showRejectedUpload('Berkas tidak dapat diperiksa dan tidak dipilih. Silakan gunakan berkas lain.');
    }

    return null;
};

// Client-side feedback is intentionally only a first barrier. Every file is
// validated again by SafeUploadedFile on the server before it can be stored.
document.addEventListener('change', (event) => {
    const input = event.target;
    if (!(input instanceof HTMLInputElement) || input.type !== 'file' || input.dataset.uploadValidated === 'true') {
        return;
    }

    if (input.files.length === 0) {
        return;
    }

    event.stopImmediatePropagation();

    validateFilesForInput(input, input.files).then((result) => {
        if (result.valid) {
            input.setCustomValidity('');
            input.dataset.uploadValidated = 'true';
            input.dispatchEvent(new Event('change', { bubbles: true }));
            delete input.dataset.uploadValidated;

            return;
        }

        input.value = '';
        input.setCustomValidity(result.message);
        showRejectedUpload(result.message);
    }).catch(() => {
        input.value = '';
        showRejectedUpload('Berkas tidak dapat diperiksa dan tidak dipilih. Silakan gunakan berkas lain.');
    });
}, true);
