const display = document.getElementById("display");

function appendValue(value) {
  display.value += value;
}

function clearDisplay() {
  display.value = "";
}

function calculate() {
  try {
    // eslint-disable-next-line no-eval
    display.value = eval(display.value);
  } catch (e) {
    display.value = "Error";
    console.error(e);
  }
}

function deleteLast() {
  display.value = display.value.slice(0, -1);
}

function logout(target) {
  // Accept a relative path argument; default to the site root of this folder
  const destination = target || 'login.html';
  window.location.href = destination;
}

// Expose functions to global scope for inline onclick handlers
window.appendValue = appendValue;
window.clearDisplay = clearDisplay;
window.calculate = calculate;
window.deleteLast = deleteLast;
window.logout = logout;
