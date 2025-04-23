document.addEventListener('DOMContentLoaded', function() {
  const navbar = document.getElementById('mainNav');
  
  function handleNavbarScroll() {
    if (window.scrollY > 50) {
      navbar.classList.add('navbar-shrink');
    } else {
      navbar.classList.remove('navbar-shrink');
    }
  }
  
  handleNavbarScroll();
  
  window.addEventListener('scroll', handleNavbarScroll);
  
  const currentPath = window.location.pathname;
  const navLinks = document.querySelectorAll('#mainNav .nav-link');
  
  navLinks.forEach(link => {
    if (link.getAttribute('href') === currentPath) {
      link.classList.add('active');
    } else {
      link.classList.remove('active');
    }
  });
  
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      const targetId = this.getAttribute('href');
      
      if (targetId !== '#' && document.querySelector(targetId)) {
        e.preventDefault();
        
        document.querySelector(targetId).scrollIntoView({
          behavior: 'smooth'
        });
      }
    });
  });
  
  const galleryItems = document.querySelectorAll('.gallery-item');
  if (galleryItems.length > 0) {
    galleryItems.forEach(item => {
      item.addEventListener('click', function() {
        const img = this.querySelector('img');
        if (img) {
        }
      });
    });
  }
});