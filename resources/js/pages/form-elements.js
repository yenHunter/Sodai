/**
 * Template Name: UBold - Admin & Dashboard Template
 * By (Author): Coderthemes
 * Module/App (File Name): Form Elements
 */

document.addEventListener("DOMContentLoaded", function () {
    const checkbox = document.getElementById("checkIndeterminate")
    checkbox.indeterminate = true
})

const passwordInput = document.getElementById("password")
const toggleBtn = document.querySelector(".password-eye")

toggleBtn.addEventListener("click", () => {
    const icons = Array.from(toggleBtn.getElementsByClassName("eye-icon"))
    const isPassword = passwordInput.type === "password"
    passwordInput.type = isPassword ? "text" : "password"

    icons.forEach((icon) => {
        icon.classList.toggle("d-none")
        icon.classList.toggle("d-block")
    })
})
