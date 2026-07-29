document.addEventListener('DOMContentLoaded', () => {
    const reviewModal = document.getElementById('reviewModal')
    if (reviewModal) {
        reviewModal.addEventListener('show.bs.modal', function (e) {
            const trigger = e.relatedTarget
            if (!trigger) return

            document.getElementById('reviewOrderId').value = trigger.dataset.orderId || ''
            document.getElementById('reviewProductId').value = trigger.dataset.productId || ''
            document.getElementById('reviewProductName').textContent = trigger.dataset.productName || ''
        })
    }

    const editModal = document.getElementById('editReviewModal')
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function (e) {
            const trigger = e.relatedTarget
            if (!trigger) return

            document.getElementById('editReviewForm').action = trigger.dataset.updateUrl
            document.getElementById('editReviewRating').value = trigger.dataset.rating || ''
            document.getElementById('editReviewComment').value = trigger.dataset.comment || ''
        })
    }
})