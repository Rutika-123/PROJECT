
document.getElementById("loginForm").addEventListener("submit", async function(event) {
    event.preventDefault();
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;
    const response = await fetch("sign-up.php", {  // Replace "your_api_endpoint" with the actual API endpoint
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password })
    });
    const data = await response.json();
    if (response.ok) {
        document.getElementById("message").style.color = "green";
        document.getElementById("message").innerText = "Login Successful! Token: " + data.token;
    } else {
        document.getElementById("message").innerText = "Invalid Credentials";
    }
});