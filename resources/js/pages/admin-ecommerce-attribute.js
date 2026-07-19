document.addEventListener('DOMContentLoaded', () => {
    initModal()
    initToggleConfirm()
})

function initModal() {
    const modal = document.getElementById('attributeModal')
    if (!modal) return

    modal.addEventListener('show.bs.modal', function (e) {
        const trigger = e.relatedTarget
        if (!trigger) return

        document.getElementById('attributeForm').action = trigger.dataset.updateUrl
        document.getElementById('attributeLabel').value = trigger.dataset.label || ''
        document.getElementById('attributeStatus').value = trigger.dataset.status || 'active'
    })
}

function initToggleConfirm() {
    document.querySelectorAll('.toggle-status-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            const status = this.querySelector('button')?.textContent?.trim()
            const action = status === 'Active' ? 'disable' : 'enable'
            if (!confirm(`${action.charAt(0).toUpperCase() + action.slice(1)} this attribute?`)) e.preventDefault()
        })
    })
}