const canvas = document.getElementById("network");
const ctx = canvas?.getContext("2d");

function resizeCanvas() {
  if (!canvas) return;
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;
}

resizeCanvas();
window.addEventListener("resize", resizeCanvas);

const dots = [];
const numDots = 150;

for (let i = 0; i < numDots; i++) {
  dots.push({
    x: Math.random() * (canvas?.width || window.innerWidth),
    y: Math.random() * (canvas?.height || window.innerHeight),
    vx: (Math.random() - 0.5) * (1 + Math.random() * 2),
    vy: (Math.random() - 0.5) * (1 + Math.random() * 2),
    radius: 3
  });
}

function drawDots() {
  if (!ctx) return;
  ctx.fillStyle = "#4fc3f7";
  dots.forEach(dot => {
    ctx.beginPath();
    ctx.arc(dot.x, dot.y, dot.radius, 0, Math.PI * 2);
    ctx.fill();
  });
}

function drawLines() {
  if (!ctx) return;
  for (let i = 0; i < dots.length; i++) {
    for (let j = i + 1; j < dots.length; j++) {
      const dx = dots[i].x - dots[j].x;
      const dy = dots[i].y - dots[j].y;
      const dist = Math.sqrt(dx * dx + dy * dy);

      if (dist < 150) {
        ctx.strokeStyle = `rgba(79, 195, 247, ${1 - dist / 150})`;
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.moveTo(dots[i].x, dots[i].y);
        ctx.lineTo(dots[j].x, dots[j].y);
        ctx.stroke();
      }
    }
  }
}

function updateDots() {
  dots.forEach(dot => {
    dot.x += dot.vx;
    dot.y += dot.vy;

    if (!canvas) return;
    if (dot.x < 0 || dot.x > canvas.width) dot.vx *= -1;
    if (dot.y < 0 || dot.y > canvas.height) dot.vy *= -1;
  });
}

function animate() {
  if (!ctx) return;
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  drawDots();
  drawLines();
  updateDots();
  requestAnimationFrame(animate);
}

animate();

const hamburger = document.querySelector('.hamburger');
const navLinks = document.querySelector('.nav-links');

if (hamburger && navLinks) {
  hamburger.addEventListener('click', () => {
    navLinks.classList.toggle('active');
  });
}

window.addEventListener("scroll", function () {
  const navbar = document.querySelector(".navbar");
  if (!navbar) return;
  if (window.scrollY > 50) {
    navbar.classList.add("scrolled");
  } else {
    navbar.classList.remove("scrolled");
  }
});

document.getElementById('project').addEventListener('click', function(event) {
    // Prevent the default immediate link jump
    event.preventDefault(); 
    
    // Display a confirmation dialog asking the user where they want to go
    // Clicking "OK" goes to Admin, clicking "Cancel" goes to Student
    const choice = confirm("Where would you like to go?\n\nClick 'OK' for Student Portal.\nClick 'Cancel' for Admin Portal.");
    
    if (choice) {
        // User clicked OK -> Redirect to Admin side
        alert("Redirecting to Student Portal...");
        window.location.href = "The-Main/STIRS-Project/student/index.html"; // Update this path if your admin folder name is different
    } else {
        // User clicked Cancel -> Redirect to Student side
        alert("Redirecting to Admin Portal...");
        window.location.href = "The-Main/STIRS-Project/admin/adminLogin.html";
    }
});

//The-Main/STIRS-Project/student/index.html