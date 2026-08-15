/**
 * Admin Product Create Page
 * Handles Quill editors, Dropzone file uploads, Select2, Variant Builder, and form submission
 */

// ═══════════════════════════════════════════════
// CSS IMPORTS (from node_modules)
// ═══════════════════════════════════════════════
import 'quill/dist/quill.snow.css'
import 'dropzone/dist/dropzone.css'
import 'select2/dist/css/select2.min.css'

// ═══════════════════════════════════════════════
// JS IMPORTS
// ═══════════════════════════════════════════════
import Quill from 'quill'
import Dropzone from 'dropzone'
import $ from 'jquery'
import select2 from 'select2'

select2(window, $)

// ═══════════════════════════════════════════════
// CONFIGURATION
// ═══════════════════════════════════════════════
Dropzone.autoDiscover = false

// ═══════════════════════════════════════════════
// QUILL EDITORS
// ═══════════════════════════════════════════════

let shortDescriptionQuill = null
let descriptionQuill = null

function initQuillEditors() {
    const icons = Quill.import('ui/icons')

    icons['bold'] = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 5h6a3.5 3.5 0 0 1 0 7h-6z" /><path d="M13 12h1a3.5 3.5 0 0 1 0 7h-7v-7" /></svg>'
    icons['italic'] = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M11 5l6 0" /><path d="M7 19l6 0" /><path d="M14 5l-4 14" /></svg>'
    icons['underline'] = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 5v5a5 5 0 0 0 10 0v-5" /><path d="M5 19h14" /></svg>'
    icons['strike'] = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M16 6.5a4 2 0 0 0 -4 -1.5h-1a3.5 3.5 0 0 0 0 7h2a3.5 3.5 0 0 1 0 7h-1.5a4 2 0 0 1 -4 -1.5" /></svg>'
    icons['list']['ordered'] = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M11 6h9" /><path d="M11 12h9" /><path d="M12 18h8" /><path d="M4 16a2 2 0 1 1 4 0c0 .591 -.5 1 -1 1.5l-3 2.5h4" /><path d="M6 10v-6l-2 2" /></svg>'
    icons['bullet'] = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l11 0" /><path d="M9 12l11 0" /><path d="M9 18l11 0" /><path d="M5 6l0 .01" /><path d="M5 12l0 .01" /><path d="M5 18l0 .01" /></svg>'
    icons['link'] = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 15l6 -6" /><path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464" /><path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463" /></svg>'
    icons['image'] = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 8h.01" /><path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z" /><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5" /><path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" /></svg>'
    icons['clean'] = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>'

    const toolbarConfig = [
        [{ font: [] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ color: [] }, { background: [] }],
        [{ header: [false, 1, 2, 3, 4, 5, 6] }],
        ['blockquote', 'code-block'],
        [{ list: 'ordered' }, { list: 'bullet' }, { indent: '-1' }, { indent: '+1' }],
        ['link', 'image'],
        ['clean']
    ]

    const descEditor = document.getElementById('descriptionEditor')
    if (descEditor) {
        descriptionQuill = new Quill(descEditor, {
            theme: 'snow',
            placeholder: 'Enter detailed product description...',
            modules: { toolbar: toolbarConfig }
        })
    }
}

// ═══════════════════════════════════════════════
// DROPZONE INSTANCES (product-level thumbnail + gallery)
// ═══════════════════════════════════════════════

let thumbnailDropzone = null
let galleryDropzone = null

function initDropzones() {
    const thumbnailEl = document.getElementById('thumbnailDropzone')
    const galleryEl = document.getElementById('galleryDropzone')

    if (thumbnailEl) {
        const thumbnailPreviewContainer = thumbnailEl.dataset.previewsContainer
        const thumbnailPreviewTemplate = thumbnailEl.dataset.uploadPreviewTemplate

        const thumbnailOptions = {
            url: '#',
            autoProcessQueue: false,
            uploadMultiple: false,
            maxFiles: 1,
            maxFilesize: 2,
            acceptedFiles: 'image/jpeg,image/jpg,image/png,image/webp',
            addRemoveLinks: false,
        }

        if (thumbnailPreviewContainer) thumbnailOptions.previewsContainer = thumbnailPreviewContainer
        if (thumbnailPreviewTemplate) {
            const template = document.querySelector(thumbnailPreviewTemplate)
            if (template) thumbnailOptions.previewTemplate = template.innerHTML
        }

        try {
            thumbnailDropzone = new Dropzone(thumbnailEl, thumbnailOptions)
            thumbnailDropzone.on('addedfile', function (file) {
                if (this.files.length > 1) this.removeFile(this.files[0])
            })
        } catch (e) {
            console.error('Thumbnail Dropzone initialization failed:', e)
        }
    }

    if (galleryEl) {
        const galleryPreviewContainer = galleryEl.dataset.previewsContainer
        const galleryPreviewTemplate = galleryEl.dataset.uploadPreviewTemplate

        const galleryOptions = {
            url: '#',
            autoProcessQueue: false,
            uploadMultiple: true,
            parallelUploads: 10,
            maxFiles: 10,
            maxFilesize: 2,
            acceptedFiles: 'image/jpeg,image/jpg,image/png,image/webp',
            addRemoveLinks: false,
        }

        if (galleryPreviewContainer) galleryOptions.previewsContainer = galleryPreviewContainer
        if (galleryPreviewTemplate) {
            const template = document.querySelector(galleryPreviewTemplate)
            if (template) galleryOptions.previewTemplate = template.innerHTML
        }

        try {
            galleryDropzone = new Dropzone(galleryEl, galleryOptions)
            galleryDropzone.on('maxfilesexceeded', function (file) {
                alert('Maximum 10 images allowed')
                this.removeFile(file)
            })
        } catch (e) {
            console.error('Gallery Dropzone initialization failed:', e)
        }
    }
}

// ═══════════════════════════════════════════════
// SELECT2 (Related Products)
// ═══════════════════════════════════════════════

function initSelect2() {
    const select = document.getElementById('related_products')
    const searchUrl = select?.dataset.searchUrl

    $('#related_products').select2({
        placeholder: 'Search and select related products',
        allowClear: true,
        ajax: {
            url: searchUrl,
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term, page: params.page || 1 }),
            processResults: data => ({
                results: data.map(product => ({ id: product.id, text: product.name }))
            }),
            cache: true
        },
        minimumInputLength: 2
    })
}

// ═══════════════════════════════════════════════
// VARIANT BUILDER
// ═══════════════════════════════════════════════

function initVariantBuilder() {
    const container = document.getElementById('variantsContainer')
    const addVariantBtn = document.getElementById('addVariantBtn')
    const generateBtn = document.getElementById('generateVariantsBtn')
    const optionNameInput = document.getElementById('optionBuilderName')
    const optionValuesInput = document.getElementById('optionBuilderValues')
    const optionSwatchesWrapper = document.getElementById('optionBuilderSwatchesWrapper')
    const optionSwatchesInput = document.getElementById('optionBuilderSwatches')
    const template = document.getElementById('variantRowTemplate')

    if (!container || !template) return

    let variantIndex = container.querySelectorAll('.variant-row').length
    let pendingOptions = [] // [{ name, values: [{value, swatch}] }]

    function nextIndex() {
        return variantIndex++
    }

    function getTemplateHtml(index) {
        return template.innerHTML.replaceAll('__INDEX__', index)
    }

    function refreshIcons() {
        if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
            const iconSet = lucide.icons || (window.lucide && window.lucide.icons)
            if (iconSet) lucide.createIcons({ icons: iconSet })
        }
    }

    function isColorOption(name) {
        return name.trim().toLowerCase() === 'color'
    }

    // Toggle swatches field visibility as the admin types the option name
    if (optionNameInput && optionSwatchesWrapper) {
        optionNameInput.addEventListener('input', function () {
            optionSwatchesWrapper.classList.toggle('d-none', !isColorOption(this.value))
        })
    }

    function updateVariantLabel(row) {
        const label = row.querySelector('.variant-label')
        const valueInputs = row.querySelectorAll('.option-value-pair input[type="text"]')
        const values = Array.from(valueInputs).map(i => i.value.trim()).filter(Boolean)
        if (label) label.textContent = values.length ? values.join(' / ') : 'Default (no options)'
    }

    function attachRowEvents(row) {
        const removeBtn = row.querySelector('.remove-variant-btn')
        if (removeBtn) {
            removeBtn.addEventListener('click', function () {
                if (container.querySelectorAll('.variant-row').length <= 1) {
                    alert('A product must have at least one variant.')
                    return
                }
                if (!confirm('Remove this variant row?')) return
                row.remove()
            })
        }

        row.querySelectorAll('.option-value-pair input[type="text"]').forEach(input => {
            input.addEventListener('input', () => updateVariantLabel(row))
        })

        const defaultCheckbox = row.querySelector('.variant-default-checkbox')
        if (defaultCheckbox) {
            defaultCheckbox.addEventListener('change', function () {
                if (this.checked) {
                    container.querySelectorAll('.variant-default-checkbox').forEach(cb => {
                        if (cb !== this) cb.checked = false
                    })
                }
            })
        }
    }

    function isRowBlank(row) {
        const hasOptionValues = row.querySelectorAll('.option-value-pair').length > 0
        const priceInput = row.querySelector('input[name$="[price]"]')
        const priceEmpty = !priceInput || priceInput.value.trim() === ''
        return !hasOptionValues && priceEmpty
    }

    function addBlankVariantRow(optionPairs = []) {
        const index = nextIndex()
        const wrapper = document.createElement('div')
        wrapper.innerHTML = getTemplateHtml(index)
        const row = wrapper.firstElementChild

        if (optionPairs.length > 0) {
            const optionContainer = row.querySelector('.variant-option-values')
            optionContainer.innerHTML = ''
            optionPairs.forEach((pair, i) => {
                const isColor = isColorOption(pair.option)
                const col = document.createElement('div')
                col.className = 'col-md-4 option-value-pair'
                col.dataset.optionName = pair.option
                col.innerHTML = `
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">${escapeHtml(pair.option)}</span>
                        <input type="hidden" name="variants[${index}][option_values][${i}][option]" value="${escapeHtml(pair.option)}">
                        <input type="text" class="form-control" name="variants[${index}][option_values][${i}][value]" value="${escapeHtml(pair.value)}">
                        ${isColor ? `<input type="color" class="form-control form-control-color p-1" style="max-width:44px;" name="variants[${index}][option_values][${i}][swatch]" value="${pair.swatch || '#000000'}" title="Pick color swatch">` : ''}
                    </div>
                `
                optionContainer.appendChild(col)
            })
        }

        container.appendChild(row)
        attachRowEvents(row)
        updateVariantLabel(row)
        refreshIcons()
        return row
    }

    if (addVariantBtn) {
        addVariantBtn.addEventListener('click', () => addBlankVariantRow())
    }

    function renderDefinedOptionsList() {
        let listEl = document.getElementById('definedOptionsList')
        if (!listEl) {
            listEl = document.createElement('div')
            listEl.id = 'definedOptionsList'
            listEl.className = 'mt-2 d-flex flex-wrap gap-1'
            document.getElementById('optionBuilderRow')?.insertAdjacentElement('afterend', listEl)
        }

        listEl.innerHTML = pendingOptions.map((opt, i) => `
            <span class="badge bg-secondary-subtle text-secondary d-inline-flex align-items-center gap-1">
                ${escapeHtml(opt.name)}: ${opt.values.map(v => escapeHtml(v.value)).join(', ')}
                <button type="button" class="btn-close btn-close-sm remove-pending-option" data-index="${i}" style="font-size:0.6rem;"></button>
            </span>
        `).join('')

        listEl.querySelectorAll('.remove-pending-option').forEach(btn => {
            btn.addEventListener('click', function () {
                pendingOptions.splice(parseInt(this.dataset.index, 10), 1)
                renderDefinedOptionsList()
            })
        })
    }

    function cartesianProduct(arrays) {
        return arrays.reduce((acc, curr) => {
            const result = []
            acc.forEach(a => curr.forEach(c => result.push([...a, c])))
            return result
        }, [[]])
    }

    if (generateBtn) {
        generateBtn.addEventListener('click', function () {
            const name = optionNameInput.value.trim()
            const valuesRaw = optionValuesInput.value.trim()
            const swatchesRaw = optionSwatchesInput ? optionSwatchesInput.value.trim() : ''

            if (name && valuesRaw) {
                const values = valuesRaw.split(',').map(v => v.trim()).filter(Boolean)
                if (values.length === 0) {
                    alert('Enter at least one value for this option.')
                    return
                }

                let swatches = []
                if (isColorOption(name) && swatchesRaw) {
                    swatches = swatchesRaw.split(',').map(s => s.trim()).filter(Boolean)
                }

                const valueObjects = values.map((v, i) => ({
                    value: v,
                    swatch: isColorOption(name) ? (swatches[i] || null) : null,
                }))

                const existingIdx = pendingOptions.findIndex(o => o.name.toLowerCase() === name.toLowerCase())
                if (existingIdx >= 0) {
                    pendingOptions[existingIdx].values = valueObjects
                } else {
                    pendingOptions.push({ name, values: valueObjects })
                }
                optionNameInput.value = ''
                optionValuesInput.value = ''
                if (optionSwatchesInput) optionSwatchesInput.value = ''
                optionSwatchesWrapper?.classList.add('d-none')
                renderDefinedOptionsList()
            }

            if (pendingOptions.length === 0) {
                alert('Define at least one option (name + values) before generating.')
                return
            }

            if (!confirm('Generate variant rows for every combination of the defined options?')) {
                return
            }

            const rows = Array.from(container.querySelectorAll('.variant-row'))
            if (rows.length === 1 && isRowBlank(rows[0])) {
                rows[0].remove()
            }

            const valueArrays = pendingOptions.map(opt =>
                opt.values.map(v => ({ option: opt.name, value: v.value, swatch: v.swatch }))
            )
            const combinations = cartesianProduct(valueArrays)

            combinations.forEach(combo => addBlankVariantRow(combo))
            refreshIcons()
        })
    }

    container.querySelectorAll('.variant-row').forEach(row => {
        attachRowEvents(row)
        updateVariantLabel(row)
    })
}

function escapeHtml(text) {
    const div = document.createElement('div')
    div.appendChild(document.createTextNode(text ?? ''))
    return div.innerHTML
}

// ═══════════════════════════════════════════════
// FORM SUBMISSION
// ═══════════════════════════════════════════════

function initFormSubmission() {
    const form = document.getElementById('productForm')
    const submitBtn = document.getElementById('submitBtn')

    if (!form) return

    form.addEventListener('submit', function (e) {
        e.preventDefault()

        if (descriptionQuill) {
            document.getElementById('descriptionInput').value = descriptionQuill.root.innerHTML
        }

        const tagsInput = document.querySelector('input[name="tags_input"]')
        if (tagsInput && tagsInput.value.trim()) {
            const tagsArray = tagsInput.value.split(',').map(tag => tag.trim()).filter(tag => tag)
            form.querySelectorAll('input[name="tags[]"]').forEach(el => el.remove())
            tagsArray.forEach(tag => {
                const hiddenInput = document.createElement('input')
                hiddenInput.type = 'hidden'
                hiddenInput.name = 'tags[]'
                hiddenInput.value = tag
                form.appendChild(hiddenInput)
            })
        }

        // Variant rows (thumbnail / images[] file inputs) are already part of
        // the form DOM, so FormData(form) below picks them up automatically —
        // no special handling needed here, only the product-level dropzones.
        const formData = new FormData(form)

        formData.delete('thumbnail')
        formData.delete('images[]')

        if (thumbnailDropzone && thumbnailDropzone.files.length > 0) {
            formData.append('thumbnail', thumbnailDropzone.files[0])
        }

        if (galleryDropzone && galleryDropzone.files.length > 0) {
            galleryDropzone.files.forEach((file, index) => {
                formData.append(`images[${index}]`, file)
            })
        }

        submitBtn.disabled = true
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Publishing...'

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url
                } else {
                    return response.json()
                }
            })
            .catch(error => {
                console.error('Error:', error)
                submitBtn.disabled = false
                submitBtn.innerHTML = '<i data-lucide="save" class="me-1" style="width:16px;height:16px;"></i> Publish Product'
                alert('An error occurred. Please try again.')
            })
    })
}

// ═══════════════════════════════════════════════
// INITIALIZATION
// ═══════════════════════════════════════════════

function refreshLucideIcons() {
    if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
        const iconSet = lucide.icons || (window.lucide && window.lucide.icons)
        if (iconSet) lucide.createIcons({ icons: iconSet })
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initQuillEditors()
    initDropzones()
    initSelect2()
    initVariantBuilder()
    initFormSubmission()
    refreshLucideIcons()
})