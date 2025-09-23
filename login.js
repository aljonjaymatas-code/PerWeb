const form = document.getElementById("loginForm");
const errorMsg = document.getElementById("errorMsg");
const togglePassword = document.getElementById("togglePassword");
const passwordInput = document.getElementById("password");

// Toggle show/hide password
togglePassword.addEventListener("click", () => {
  if (passwordInput.type === "password") {
    passwordInput.type = "text";
    togglePassword.textContent = "🙈";
  } else {
    passwordInput.type = "password";
    togglePassword.textContent = "👁";
  }
});

// Validate password on submit
form.addEventListener("submit", function(event) {
  event.preventDefault();
  const password = passwordInput.value;

  // LUDS-8 Regex
  const ludsRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

  if (!ludsRegex.test(password)) {
    errorMsg.textContent = "Password must have at least 8 characters, with uppercase, lowercase, number, and special character.";
  } else {
    errorMsg.textContent = "";
    alert("Login successful (Password meets LUDS-8)!");
  }
});
