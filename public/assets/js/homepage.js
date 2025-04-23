document.addEventListener('DOMContentLoaded', function() {
  const carouselEl = document.querySelector('#homeCarousel');
  const carousel = new bootstrap.Carousel(carouselEl, {
    interval: 6000,
    ride: 'carousel',
    pause: 'hover'
  });
  
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
  
  const newsletterForm = document.querySelector('.newsletter-form');
  if (newsletterForm) {
    newsletterForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const emailInput = this.querySelector('input[type="email"]');
      
      if (emailInput.value) {
        alert('Thanks for subscribing! You\'ll receive our latest travel updates soon.');
        emailInput.value = '';
      }
    });
  }
  
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
  
  const imageContainers = document.querySelectorAll('.image-container');
  imageContainers.forEach(container => {
    container.setAttribute('tabindex', '0');
    
    container.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        this.click();
      }
    });
  });
});