document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('addressModal')
    const addBtn = document.getElementById('addAddressBtn')
    if (!modal) return

    const storeUrl = document.getElementById('addressForm').action

    if (addBtn) addBtn.addEventListener('click', resetForAdd)

    modal.addEventListener('show.bs.modal', function (e) {
        const trigger = e.relatedTarget
        if (trigger?.dataset.mode === 'edit') populateForEdit(trigger)
        else resetForAdd()
    })

    function resetForAdd() {
        const form = document.getElementById('addressForm')
        form.reset()
        form.action = storeUrl
        document.getElementById('addressModalLabel').textContent = 'Add New Address'
    }

    function populateForEdit(trigger) {
        const d = trigger.dataset
        const form = document.getElementById('addressForm')
        form.action = d.updateUrl

        document.getElementById('addr_label').value = d.label || ''
        document.getElementById('addr_recipient_name').value = d.recipientName || ''
        document.getElementById('addr_recipient_phone').value = d.recipientPhone || ''
        document.getElementById('addr_address_line_1').value = d.addressLine1 || ''
        document.getElementById('addr_address_line_2').value = d.addressLine2 || ''
        document.getElementById('addr_city').value = d.city || ''
        document.getElementById('addr_state').value = d.state || ''
        document.getElementById('addr_zip_code').value = d.zipCode || ''
        document.getElementById('addr_country').value = d.country || ''

        document.getElementById('addressModalLabel').textContent = 'Edit Address'
    }
})