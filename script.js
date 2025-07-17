document.addEventListener("DOMContentLoaded", function () {
    const forgotPasswordLink = document.getElementById("forgot-password-link");
    const modal = document.getElementById("forgot-password-modal");
    const closeModal = document.querySelector(".close");
    const resetButton = document.getElementById("reset-button");

    // Open Modal
    forgotPasswordLink.addEventListener("click", function (event) {
        event.preventDefault();
        modal.style.display = "flex";
    });

    // Close Modal
    closeModal.addEventListener("click", function () {
        modal.style.display = "none";
    });

    // Simulate Reset Action
    resetButton.addEventListener("click", function () {
        const email = document.getElementById("reset-email").value;
        if (email) {
            alert(Reset link sent to ${email});
            modal.style.display = "none";
        } else {
            alert("Please enter your email.");
        }
    });

    // Close modal if user clicks outside the content box
    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
});
