import './bootstrap';
import Quill from 'quill';

window.Quill = Quill;

const emptyQuillHtml = '<p><br></p>';

const syncEditorInput = (quill, input) => {
    const html = quill.root.innerHTML.trim();
    input.value = html === emptyQuillHtml ? '' : html;
};

const uploadEditorImage = async (file, uploadUrl) => {
    const formData = new FormData();
    formData.append('image', file);

    const response = await fetch(uploadUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            'Accept': 'application/json',
        },
        body: formData,
    });

    if (!response.ok) {
        throw new Error('Image upload failed');
    }

    return response.json();
};

const initRichTextEditors = () => {
    document.querySelectorAll('[data-rich-text-editor]').forEach((wrapper) => {
        if (wrapper.dataset.initialized === 'true') {
            return;
        }

        const input = wrapper.querySelector('[data-rich-text-input]');
        const area = wrapper.querySelector('[data-rich-text-area]');

        if (!input || !area) {
            return;
        }

        wrapper.dataset.initialized = 'true';

        const quill = new Quill(area, {
            theme: 'snow',
            placeholder: wrapper.dataset.placeholder || '',
            modules: {
                toolbar: [
                    [{ header: [false, 2, 3, 4] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ color: [] }, { background: [] }],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['blockquote', 'link', 'image'],
                    ['clean'],
                ],
            },
        });

        if (input.value.trim() !== '') {
            quill.clipboard.dangerouslyPasteHTML(input.value);
            syncEditorInput(quill, input);
        }

        const toolbar = quill.getModule('toolbar');
        toolbar.addHandler('image', () => {
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = 'image/jpeg,image/png,image/webp,image/gif';
            fileInput.click();

            fileInput.addEventListener('change', async () => {
                const file = fileInput.files?.[0];

                if (!file || !wrapper.dataset.uploadUrl) {
                    return;
                }

                try {
                    const { url } = await uploadEditorImage(file, wrapper.dataset.uploadUrl);
                    const range = quill.getSelection(true);
                    quill.insertEmbed(range.index, 'image', url, 'user');
                    quill.setSelection(range.index + 1, 0, 'silent');
                    syncEditorInput(quill, input);
                } catch (error) {
                    window.Swal?.fire({
                        icon: 'error',
                        title: document.documentElement.dir === 'rtl' ? 'تعذر رفع الصورة' : 'Image upload failed',
                        timer: 2200,
                        showConfirmButton: false,
                    });
                }
            }, { once: true });
        });

        quill.on('text-change', () => syncEditorInput(quill, input));
        input.form?.addEventListener('submit', () => syncEditorInput(quill, input));
    });
};

document.addEventListener('DOMContentLoaded', initRichTextEditors);
