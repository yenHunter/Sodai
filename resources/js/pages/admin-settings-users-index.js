function waitForJQuery(cb) {
    if (typeof window.jQuery !== 'undefined') cb(window.jQuery)
    else setTimeout(() => waitForJQuery(cb), 50)
}

waitForJQuery(function ($) {
    $(document).ready(function () {
        initDataTable()
        initModal()
        initDelete()
        initToggleConfirm()
    })

    function refreshIcons() {
        if (typeof lucide !== 'undefined') lucide.createIcons({ icons: lucide.icons })
    }

    function initDataTable() {
        if (!document.getElementById('adminTable')) return
        $('#adminTable').DataTable({
            pageLength: 10,
            dom:
                '<"row mb-3"<"col-md-6"l><"col-md-6"f>>' +
                '<"row"<"col-12"tr>>' +
                '<"row mt-3"<"col-md-5"i><"col-md-7"p>>',
            drawCallback: refreshIcons,
        })
    }

    function initModal() {
        const modal = document.getElementById('adminModal')
        const addBtn = document.getElementById('addAdminBtn')
        const form = document.getElementById('adminForm')
        if (!modal) return

        if (addBtn) addBtn.addEventListener('click', resetForAdd)

        modal.addEventListener('show.bs.modal', function (e) {
            const trigger = e.relatedTarget
            if (trigger?.dataset.mode === 'edit') populateForEdit(trigger)
            else resetForAdd()
        })

        form.addEventListener('submit', function (e) {
            e.preventDefault()
            const btn = document.getElementById('adminSubmitBtn')
            btn.disabled = true
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...'
            this.submit()
        })
    }

    function resetForAdd() {
        const form = document.getElementById('adminForm')
        form.reset()
        form.action = form.dataset.storeUrl
        document.getElementById('adminModalLabel').textContent = 'Add New Admin'
        document.getElementById('adminSubmitBtn').innerHTML = '<i data-lucide="plus" class="fs-sm me-1"></i> Add Admin'
        document.getElementById('adminPassword').required = true
        document.getElementById('passwordHint').style.display = 'none'
        document.getElementById('avatarPreviewContainer').classList.add('d-none')
        refreshIcons()
    }

    function populateForEdit(trigger) {
        const { name, email, phone, roleId, status, image, updateUrl } = trigger.dataset
        const form = document.getElementById('adminForm')

        form.action = updateUrl
        document.getElementById('adminName').value = name || ''
        document.getElementById('adminEmail').value = email || ''
        document.getElementById('adminPhone').value = phone || ''
        document.getElementById('adminRole').value = roleId || ''
        document.getElementById('adminStatus').value = status || 'active'
        document.getElementById('adminPassword').value = ''
        document.getElementById('adminPassword').required = false
        document.getElementById('passwordHint').style.display = 'block'

        const previewContainer = document.getElementById('avatarPreviewContainer')
        if (image) {
            document.getElementById('avatarPreview').src = image
            previewContainer.classList.remove('d-none')
        } else {
            previewContainer.classList.add('d-none')
        }

        document.getElementById('adminModalLabel').textContent = 'Edit Admin'
        document.getElementById('adminSubmitBtn').innerHTML = '<i data-lucide="save" class="fs-sm me-1"></i> Update Admin'
        refreshIcons()
    }

    function initDelete() {
        const modal = document.getElementById('deleteAdminModal')
        if (!modal) return

        modal.addEventListener('show.bs.modal', function (e) {
            const trigger = e.relatedTarget
            document.getElementById('deleteSingleForm').action = trigger.dataset.deleteUrl
            document.getElementById('deleteModalBody').innerHTML =
                `<p>Are you sure you want to delete <strong>${trigger.dataset.name}</strong>?</p>
                 <p class="text-danger small mb-0">This action cannot be undone.</p>`
        })
    }

    function initToggleConfirm() {
        document.querySelectorAll('.toggle-status-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                const status = this.querySelector('button')?.textContent?.trim()
                const action = status === 'Active' ? 'deactivate' : 'activate'
                if (!confirm(`${action.charAt(0).toUpperCase() + action.slice(1)} this admin?`)) e.preventDefault()
            })
        })
    }
})