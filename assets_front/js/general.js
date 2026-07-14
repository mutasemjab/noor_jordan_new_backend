// ── NAV SCROLL ──
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
  navbar.classList.toggle('scrolled', window.scrollY > 60);
});

// ── PARTICLES ──
const canvas = document.getElementById('particles-canvas');
const ctx = canvas.getContext('2d');
let particles = [];
let W, H;

function resizeCanvas() {
  W = canvas.width = canvas.offsetWidth;
  H = canvas.height = canvas.offsetHeight;
}
resizeCanvas();
window.addEventListener('resize', resizeCanvas);

class Particle {
  constructor() { this.reset(); }
  reset() {
    this.x = Math.random() * W;
    this.y = Math.random() * H;
    this.r = Math.random() * 1.5 + 0.3;
    this.vx = (Math.random() - 0.5) * 0.4;
    this.vy = (Math.random() - 0.5) * 0.4;
    this.alpha = Math.random() * 0.5 + 0.1;
    this.color = Math.random() > 0.6 ? '#E8192C' : '#1B4FD8';
  }
  update() {
    this.x += this.vx; this.y += this.vy;
    if (this.x < 0 || this.x > W || this.y < 0 || this.y > H) this.reset();
  }
  draw() {
    ctx.save();
    ctx.globalAlpha = this.alpha;
    ctx.fillStyle = this.color;
    ctx.beginPath();
    ctx.arc(this.x, this.y, this.r, 0, Math.PI * 2);
    ctx.fill();
    ctx.restore();
  }
}

for (let i = 0; i < 120; i++) particles.push(new Particle());

// draw lines between near particles
function connectParticles() {
  for (let i = 0; i < particles.length; i++) {
    for (let j = i + 1; j < particles.length; j++) {
      const dx = particles[i].x - particles[j].x;
      const dy = particles[i].y - particles[j].y;
      const d = Math.sqrt(dx * dx + dy * dy);
      if (d < 100) {
        ctx.save();
        ctx.globalAlpha = 0.06 * (1 - d / 100);
        ctx.strokeStyle = '#1B4FD8';
        ctx.lineWidth = 0.5;
        ctx.beginPath();
        ctx.moveTo(particles[i].x, particles[i].y);
        ctx.lineTo(particles[j].x, particles[j].y);
        ctx.stroke();
        ctx.restore();
      }
    }
  }
}

function animateParticles() {
  ctx.clearRect(0, 0, W, H);
  particles.forEach(p => { p.update(); p.draw(); });
  connectParticles();
  requestAnimationFrame(animateParticles);
}
animateParticles();

// ── GSAP SETUP ──
function initGSAP() {
  if (typeof gsap === 'undefined') { initFallback(); return; }

  try { gsap.registerPlugin(ScrollTrigger); } catch(e) {}

  // Hero timeline
  gsap.timeline({ delay: 0.3 })
    .from('.hero-badge',      { opacity: 0, y: 30, duration: 0.7, ease: 'power3.out' })
    .from('#hero-title',      { opacity: 0, y: 50, duration: 0.9, ease: 'power3.out' }, '-=0.3')
    .from('.hero-sub',        { opacity: 0, y: 30, duration: 0.7, ease: 'power3.out' }, '-=0.5')
    .from('.hero-actions',    { opacity: 0, y: 20, duration: 0.6, ease: 'power3.out' }, '-=0.4')
    .from('.hero-stats',      { opacity: 0, y: 20, duration: 0.6, ease: 'power3.out' }, '-=0.3')
    .from('.hero-card-main',  { opacity: 0, x: 60, duration: 0.9, ease: 'power3.out' }, '-=0.7')
    .from('.card-float-1',    { opacity: 0, x: -30, y: -20, duration: 0.6, ease: 'back.out(1.7)' }, '-=0.4')
    .from('.card-float-2',    { opacity: 0, x: -30, y: 20,  duration: 0.6, ease: 'back.out(1.7)' }, '-=0.3')
    .from('.scroll-indicator',{ opacity: 0, duration: 0.6 }, '-=0.2');

  if (typeof ScrollTrigger === 'undefined') return;

  // Scroll reveals
  gsap.utils.toArray('.reveal').forEach(el => {
    gsap.fromTo(el,
      { opacity: 0, y: 50 },
      { opacity: 1, y: 0, duration: 0.8, ease: 'power3.out',
        scrollTrigger: { trigger: el, start: 'top 88%', once: true } });
  });
  gsap.utils.toArray('.reveal-left').forEach(el => {
    gsap.fromTo(el,
      { opacity: 0, x: -60 },
      { opacity: 1, x: 0, duration: 0.9, ease: 'power3.out',
        scrollTrigger: { trigger: el, start: 'top 88%', once: true } });
  });
  gsap.utils.toArray('.reveal-right').forEach(el => {
    gsap.fromTo(el,
      { opacity: 0, x: 60 },
      { opacity: 1, x: 0, duration: 0.9, ease: 'power3.out',
        scrollTrigger: { trigger: el, start: 'top 88%', once: true } });
  });

  // Staggered grids
  ['teachers-grid', 'courses-grid'].forEach(cls => {
    const el = document.querySelector('.' + cls);
    if (!el) return;
    gsap.fromTo(Array.from(el.children),
      { opacity: 0, y: 60 },
      { opacity: 1, y: 0, stagger: 0.12, duration: 0.8, ease: 'power3.out',
        scrollTrigger: { trigger: el, start: 'top 88%', once: true } });
  });

  const gradesGrid = document.querySelector('.grades-grid');
  if (gradesGrid) {
    gsap.fromTo(gradesGrid.querySelectorAll('.grade-card'),
      { opacity: 0, y: 60 },
      { opacity: 1, y: 0, stagger: 0.18, duration: 0.9, ease: 'power3.out',
        scrollTrigger: { trigger: gradesGrid, start: 'top 88%', once: true } });
  }
}

// Fallback: simple CSS transitions when GSAP unavailable
function initFallback() {
  document.querySelectorAll('.reveal,.reveal-left,.reveal-right').forEach(el => {
    el.style.opacity = '1';
    el.style.transform = 'none';
  });
}

// Run after DOM + scripts ready
window.addEventListener('load', initGSAP);

// ── COUNTER ANIMATION ──
function animateCounter(el, target, suffix='', prefix='') {
  let current = 0;
  const step = target / 60;
  const timer = setInterval(() => {
    current += step;
    if (current >= target) { current = target; clearInterval(timer); }
    el.innerHTML = prefix + Math.floor(current) + (suffix ? '<span>' + suffix + '</span>' : '');
  }, 16);
}

// Hero counters after load
window.addEventListener('load', () => {
  setTimeout(() => {
    document.querySelectorAll('.hero-stats .stat-number[data-count]').forEach(el => {
      const target = parseInt(el.getAttribute('data-count'));
      const hasPlus = el.textContent.includes('+') || el.innerHTML.includes('+');
      const hasPct  = el.textContent.includes('%') || el.innerHTML.includes('%');
      animateCounter(el, target, hasPct ? '%' : '', hasPlus ? '+' : '');
    });
  }, 1400);
});

// Band counters on scroll
window.addEventListener('load', () => {
  document.querySelectorAll('.stats-band-inner .stat-band-num[data-count]').forEach(el => {
    const target  = parseInt(el.getAttribute('data-count'));
    const hasPlus = el.innerHTML.includes('+');
    const hasPct  = el.innerHTML.includes('%');
    let fired = false;
    const observer = new IntersectionObserver(entries => {
      if (entries[0].isIntersecting && !fired) {
        fired = true;
        animateCounter(el, target, hasPct ? '%' : '', hasPlus ? '+' : '');
        observer.disconnect();
      }
    }, { threshold: 0.3 });
    observer.observe(el);
  });
});

// ── NAV HAMBURGER ──
document.querySelector('.hamburger').addEventListener('click', () => {
  const links = document.querySelector('.nav-links');
  links.style.display = links.style.display === 'flex' ? 'none' : 'flex';
  links.style.flexDirection = 'column';
  links.style.position = 'fixed';
  links.style.top = '80px';
  links.style.left = '0'; links.style.right = '0';
  links.style.background = 'rgba(10,22,40,0.98)';
  links.style.padding = '20px';
  links.style.gap = '4px';
  links.style.backdropFilter = 'blur(20px)';
});

// close mobile nav on link click
document.querySelectorAll('.nav-links a').forEach(a => {
  a.addEventListener('click', () => {
    const links = document.querySelector('.nav-links');
    links.style.display = 'none';
  });
});

// ── SMOOTH ACTIVE NAV ──
const sections = document.querySelectorAll('section[id]');
window.addEventListener('scroll', () => {
  let current = '';
  sections.forEach(s => {
    if (window.scrollY >= s.offsetTop - 120) current = s.id;
  });
  document.querySelectorAll('.nav-links a').forEach(a => {
    a.style.color = a.getAttribute('href') === '#' + current ? 'white' : '';
    a.style.background = a.getAttribute('href') === '#' + current ? 'rgba(255,255,255,0.08)' : '';
  });
});

// ── COURSE ENROLL BTN HOVER ──
document.querySelectorAll('.course-enroll-btn').forEach(btn => {
  btn.addEventListener('mouseenter', () => {
    btn.style.background = 'linear-gradient(135deg, #E8192C, #FF4D5E)';
    btn.style.boxShadow = '0 8px 24px rgba(232,25,44,0.4)';
  });
  btn.addEventListener('mouseleave', () => {
    btn.style.background = '';
    btn.style.boxShadow = '';
  });
});
