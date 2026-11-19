const canvas = document.getElementById('particulas');
const ctx = canvas.getContext('2d');

function resize() {
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;
}
resize();
window.addEventListener('resize', resize);

const PARTICLE_COUNT = 45;
const colors = ['#00e5ff', '#a855f7', '#ffffff'];

class Particula {
  constructor() { this.reset(); }
  reset() {
    this.x = Math.random() * canvas.width;
    this.y = Math.random() * canvas.height;
    this.radius = Math.random() * 1.2 + 0.3;
    this.speedX = (Math.random() - 0.5) * 0.15;
    this.speedY = (Math.random() - 0.5) * 0.15;
    this.color = colors[Math.floor(Math.random() * colors.length)];
    this.alpha = Math.random() * 0.35 + 0.15;
    this.twinkleSpeed = Math.random() * 0.01 + 0.003;
    this.twinkleDir = 1;
  }
  update() {
    this.x += this.speedX;
    this.y += this.speedY;
    this.alpha += this.twinkleSpeed * this.twinkleDir;
    if (this.alpha >= 0.5 || this.alpha <= 0.1) this.twinkleDir *= -1;
    if (this.x < 0) this.x = canvas.width;
    if (this.x > canvas.width) this.x = 0;
    if (this.y < 0) this.y = canvas.height;
    if (this.y > canvas.height) this.y = 0;
  }
  draw() {
    ctx.beginPath();
    ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
    ctx.fillStyle = this.color;
    ctx.globalAlpha = this.alpha;
    ctx.fill();
    ctx.globalAlpha = 1;
  }
}

const particulas = Array.from({ length: PARTICLE_COUNT }, () => new Particula());

function animate() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  particulas.forEach(p => { p.update(); p.draw(); });
  requestAnimationFrame(animate);
}
animate();