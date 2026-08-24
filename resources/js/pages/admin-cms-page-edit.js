/**
 * Admin CMS Page Edit
 * Initializes a single Quill editor for the page content field and
 * syncs its HTML into the hidden textarea before submit.
 */

import 'quill/dist/quill.snow.css'
import Quill from 'quill'

let contentQuill = null

function initQuillEditor() {
    const editorEl = document.getElementById('pageContentEditor')
    if (!editorEl) return

    const toolbarConfig = [
        [{ header: [false, 1, 2, 3] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ list: 'ordered' }, { list: 'bullet' }, { indent: '-1' }, { indent: '+1' }],
        ['blockquote', 'link'],
        ['clean'],
    ]

    contentQuill = new Quill(editorEl, {
        theme: 'snow',
        placeholder: 'Write the page content here...',
        modules: { toolbar: toolbarConfig },
    })
}

function initFormSubmission() {
    const form = document.getElementById('cmsPageForm')
    const submitBtn = document.getElementById('cmsPageSubmitBtn')

    if (!form) return

    form.addEventListener('submit', function () {
        if (contentQuill) {
            document.getElementById('pageContentInput').value = contentQuill.root.innerHTML
        }

        if (submitBtn) {
            submitBtn.disabled = true
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...'
        }
    })
}

document.addEventListener('DOMContentLoaded', () => {
    initQuillEditor()
    initFormSubmission()
})