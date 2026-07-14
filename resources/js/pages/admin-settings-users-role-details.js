document.addEventListener('DOMContentLoaded', () => {
    const selectAll = document.getElementById('selectAllPerms')
    const clearAll = document.getElementById('clearAllPerms')

    if (selectAll) {
        selectAll.addEventListener('click', () => {
            document.querySelectorAll('.perm-checkbox').forEach(cb => (cb.checked = true))
        })
    }

    if (clearAll) {
        clearAll.addEventListener('click', () => {
            document.querySelectorAll('.perm-checkbox').forEach(cb => (cb.checked = false))
        })
    }
})