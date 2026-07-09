/**
 * Admin Product Edit Page
 * Handles Quill editors, Dropzone file uploads, Select2, existing image management, and form submission
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
// QUILL EDITORS (Both with same full toolbar)
// ═══════════════════════════════════════════════

let shortDescriptionQuill = null
let descriptionQuill = null

function initQuillEditors() {
    const icons = Quill.import('ui/icons')

    // Replace Quill's built-in toolbar icons with Tabler icons (same as create)
    icons['bold'] = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 5h6a3.5 3.5 0 0 1 0 7h-6z" /><path d="M13 12h1a3.5 3.5 0 0 1 0 7h-7v-7" /></svg>'
    icons['italic'] = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M11 5l6 0" /><path d="M7 19l6 0" /><path d="M14 5l-4 14" /></svg>'
    icons['underline'] = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 5v5a5 5 0 0 0 10 0v-5" /><path d="M5 19h14" /></svg>'
    icons['strike'] = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M16 6.5a4 2 0 0 0 -4 -1.5h-1a3.5 3.5 0 0 0 0 7h2a3.5 3.5 0 0 1 0 7h-1.5a4 2 0 0 1 -4 -1.5" /></svg>'
    icons['list']['ordered'] = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M11 6h9" /><path d="M11 12h9" /><path d="M12 18h8" /><path d="M4 16a2 2 0 1 1 4 0c0 .591 -.5 1 -1 1.5l-3 2.5h4" /><path d="M6 10v-6l-2 2" /></svg>'
    icons['bullet'] = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l11 0" /><path d="M9 12l11 0" /><path d="M9 18l11 0" /><path d="M5 6l0 .01" /><path d="M5 12l0 .01" /><path d="M5 18l0 .01" /></svg>'
    icons['link'] = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 15l6 -6" /><path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464" /><path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463" /></svg>'
    icons['image'] = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 8h.01" /><path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z" /><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5" /><path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" /></svg>'
    icons['clean'] = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>'

    // ✅ SHARED TOOLBAR CONFIG (same for both editors)
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

    // Short Description Editor
    const shortDescEditor = document.getElementById('shortDescriptionEditor')
    if (shortDescEditor) {
        shortDescriptionQuill = new Quill(shortDescEditor, {
            theme: 'snow',
            placeholder: 'Enter a brief product description...',
            modules: {
                toolbar: toolbarConfig
            }
        })
    }

    // Full Description Editor (identical toolbar)
    const descEditor = document.getElementById('descriptionEditor')
    if (descEditor) {
        descriptionQuill = new Quill(descEditor, {
            theme: 'snow',
            placeholder: 'Enter detailed product description...',
            modules: {
                toolbar: toolbarConfig
            }
        })
    }
}

// ═══════════════════════════════════════════════
// DROPZONE INSTANCES
// ═══════════════════════════════════════════════

let thumbnailDropzone = null
let galleryDropzone = null

function initDropzones() {
    const thumbnailEl = document.getElementById('thumbnailDropzone')
    const galleryEl = document.getElementById('galleryDropzone')

    // Thumbnail Dropzone
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

        if (thumbnailPreviewContainer) {
            thumbnailOptions.previewsContainer = thumbnailPreviewContainer
        }

        if (thumbnailPreviewTemplate) {
            const template = document.querySelector(thumbnailPreviewTemplate)
            if (template) {
                thumbnailOptions.previewTemplate = template.innerHTML
            }
        }

        try {
            thumbnailDropzone = new Dropzone(thumbnailEl, thumbnailOptions)

            thumbnailDropzone.on('addedfile', function (file) {
                if (this.files.length > 1) {
                    this.removeFile(this.files[0])
                }
            })
        } catch (e) {
            console.error('Thumbnail Dropzone initialization failed:', e)
        }
    }

    // Gallery Dropzone
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

        if (galleryPreviewContainer) {
            galleryOptions.previewsContainer = galleryPreviewContainer
        }

        if (galleryPreviewTemplate) {
            const template = document.querySelector(galleryPreviewTemplate)
            if (template) {
                galleryOptions.previewTemplate = template.innerHTML
            }
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
// EXISTING GALLERY IMAGE MANAGEMENT (AJAX DELETE)
// ═══════════════════════════════════════════════

function initExistingImageManagement() {
    const productId = getProductIdFromUrl()

    // Delete Image buttons
    document.querySelectorAll('.delete-image-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const imageId = this.dataset.imageId
            const imageContainer = this.closest('[data-image-id]')

            if (!confirm('Are you sure you want to delete this image?')) {
                return
            }

            // AJAX delete
            fetch(`/admin/ecommerce/products/${productId}/images/${imageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        imageContainer.remove()
                        refreshLucideIcons()
                    } else {
                        alert(data.message || 'Failed to delete image')
                    }
                })
                .catch(error => {
                    console.error('Error:', error)
                    alert('An error occurred while deleting the image')
                })
        })
    })

    // Set Primary Image buttons
    document.querySelectorAll('.set-primary-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const imageId = this.dataset.imageId

            // AJAX set primary
            fetch(`/admin/ecommerce/products/${productId}/images/${imageId}/primary`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove all "Primary" badges
                        document.querySelectorAll('.badge.bg-success').forEach(badge => {
                            const btn = document.createElement('button')
                            btn.type = 'button'
                            btn.className = 'btn btn-sm btn-primary position-absolute top-0 start-0 m-1 set-primary-btn'
                            btn.dataset.imageId = badge.closest('[data-image-id]').dataset.imageId
                            btn.title = 'Set as Primary'
                            btn.innerHTML = '<i data-lucide="star" style="width:12px;height:12px;"></i>'
                            badge.replaceWith(btn)
                        })

                        // Add "Primary" badge to clicked image
                        const newBadge = document.createElement('span')
                        newBadge.className = 'badge bg-success position-absolute top-0 start-0 m-1'
                        newBadge.textContent = 'Primary'
                        this.replaceWith(newBadge)

                        refreshLucideIcons()

                        // Re-init event listeners for newly created buttons
                        initExistingImageManagement()
                    } else {
                        alert(data.message || 'Failed to set primary image')
                    }
                })
                .catch(error => {
                    console.error('Error:', error)
                    alert('An error occurred')
                })
        })
    })
}

function getProductIdFromUrl() {
    const pathSegments = window.location.pathname.split('/')
    return pathSegments[pathSegments.length - 2] // Assumes /admin/ecommerce/products/{id}/edit
}

// ═══════════════════════════════════════════════
// SELECT2 (Related Products)
// ═══════════════════════════════════════════════

function initSelect2() {
    $('#related_products').select2({
        placeholder: 'Search and select related products',
        allowClear: true,
        ajax: {
            url: '/admin/ecommerce/products/search',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    exclude: getProductIdFromUrl(),
                    page: params.page || 1
                }
            },
            processResults: function (data) {
                return {
                    results: data.map(product => ({
                        id: product.id,
                        text: `${product.name} (${product.sku})`
                    }))
                }
            },
            cache: true
        },
        minimumInputLength: 2
    })
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

        // Sync Quill content
        if (shortDescriptionQuill) {
            document.getElementById('shortDescriptionInput').value = shortDescriptionQuill.root.innerHTML
        }
        if (descriptionQuill) {
            document.getElementById('descriptionInput').value = descriptionQuill.root.innerHTML
        }

        // Convert comma-separated tags to array
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

        // Build FormData
        const formData = new FormData(form)

        // ✅ Remove empty fallback file inputs before appending real files
        formData.delete('thumbnail')
        formData.delete('images[]')

        // Thumbnail (new upload)
        if (thumbnailDropzone && thumbnailDropzone.files.length > 0) {
            formData.append('thumbnail', thumbnailDropzone.files[0])
        }

        // Gallery images (new uploads)
        if (galleryDropzone && galleryDropzone.files.length > 0) {
            galleryDropzone.files.forEach((file, index) => {
                formData.append(`images[${index}]`, file)
            })
        }

        // Disable submit button
        submitBtn.disabled = true
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Updating...'

        // Submit
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',           // ✅ Ensure Laravel returns JSON on validation errors
                'X-Requested-With': 'XMLHttpRequest'    // ✅ Helps Laravel detect AJAX requests
            }
        })
            .then(async response => {
                console.log('Response status:', response.status)
                console.log('Response redirected:', response.redirected)
                console.log('Response URL:', response.url)

                if (response.redirected) {
                    window.location.href = response.url
                    return
                }

                // ✅ Read as text first so we can see raw response if it's not JSON
                const text = await response.text()
                console.log('Raw response body:', text)

                let data
                try {
                    data = JSON.parse(text)
                } catch (parseError) {
                    console.error('Response was not valid JSON:', parseError)
                    throw new Error(`Server returned status ${response.status} with non-JSON response. Check console for raw body.`)
                }

                if (!response.ok) {
                    console.error('Server returned error:', data)
                    throw new Error(data.message || `Server error (status ${response.status})`)
                }

                return data
            })
            .catch(error => {
                console.error('Full error details:', error)
                submitBtn.disabled = false
                submitBtn.innerHTML = '<i data-lucide="save" class="me-1" style="width:16px;height:16px;"></i> Update Product'
                alert('Error: ' + error.message) // ✅ Now shows the REAL reason
            })
    })
}

// ═══════════════════════════════════════════════
// LUCIDE ICONS REFRESH
// ═══════════════════════════════════════════════

function refreshLucideIcons() {
    if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
        const iconSet = lucide.icons || (window.lucide && window.lucide.icons)
        if (iconSet) {
            lucide.createIcons({ icons: iconSet })
        }
    }
}

// ═══════════════════════════════════════════════
// INITIALIZATION
// ═══════════════════════════════════════════════

document.addEventListener('DOMContentLoaded', () => {
    initQuillEditors()
    initDropzones()
    initExistingImageManagement()
    initSelect2()
    initFormSubmission()
    refreshLucideIcons()
})