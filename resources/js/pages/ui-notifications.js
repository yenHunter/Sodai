/**
 * Template Name: UBold - Admin & Dashboard Template
 * By (Author): Coderthemes
 * Module/App (File Name): UI Notifications
 */

const toastTrigger = document.getElementById("liveToastBtn")
const toastLiveExample = document.getElementById("liveToast")

if (toastTrigger) {
    const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample)
    toastTrigger.addEventListener("click", () => {
        toastBootstrap.show()
    })
}

const toastPlacement = document.getElementById("toastPlacement")
if (toastPlacement) {
    document.getElementById("selectToastPlacement").addEventListener("change", function () {
        if (!toastPlacement.dataset.originalClass) {
            toastPlacement.dataset.originalClass = toastPlacement.className
        }
        toastPlacement.className = toastPlacement.dataset.originalClass + " " + this.value
    })
}
