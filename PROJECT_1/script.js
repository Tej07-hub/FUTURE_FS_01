function toggleMenu() {
  const menu = document.querySelector(".menu-links");
  const icon = document.querySelector(".hamburger-icon");
  menu.classList.toggle("open");
  icon.classList.toggle("open");
}
// Select all sections (or any elements you want to animate)
document.querySelectorAll('section, div, h1, h2, p, .project-card').forEach(el => {
  el.setAttribute('data-aos', 'fade-up');  // Change 'fade-up' to any animation you want
});

// Initialize AOS with global settings
AOS.init({
  duration: 1000,      // Animation duration (ms)
  delay: 1000,          // Default delay for all elements
  easing: 'ease-in-out',
  offset: 120,         // Trigger offset
  once: true,          // Animate only once
  mirror: false        // Don't replay on scroll up
});
