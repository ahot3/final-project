document.addEventListener('DOMContentLoaded', function() {
  console.log('Contact.js loaded');
  
  const contactForm = document.getElementById('contact-form');
  const newsletterForm = document.getElementById('newsletter-form');
  
  console.log('Contact form found:', !!contactForm);
  console.log('Newsletter form found:', !!newsletterForm);
  
  if (contactForm) {
    contactForm.addEventListener('submit', function(e) {
      e.preventDefault();
      console.log('Contact form submitted');
      
      const name = document.getElementById('name').value;
      const email = document.getElementById('email').value;
      const subject = document.getElementById('subject').value || '';
      const message = document.getElementById('message').value;
      
      console.log('Contact form data:', { name, email, subject, message });
      
      const successAlert = document.getElementById('contact-success');
      const errorAlert = document.getElementById('contact-error');
      
      successAlert.classList.add('d-none');
      errorAlert.classList.add('d-none');
      
      document.querySelectorAll('.invalid-feedback').forEach(el => {
        el.textContent = '';
      });
      
      const formData = {
        name: name,
        email: email,
        subject: subject,
        message: message
      };
      
      console.log('Sending contact request to /api/contact');
      fetch('/api/contact', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
      })
      .then(response => {
        console.log('Contact response status:', response.status);
        if (!response.ok) {
          return response.json().then(data => {
            console.error('Contact error data:', data);
            throw new Error(data.error || 'Failed to send message');
          });
        }
        return response.json();
      })
      .then(data => {
        console.log('Contact success data:', data);
        successAlert.textContent = 'Thank you for your message! I will get back to you soon.';
        successAlert.classList.remove('d-none');
        contactForm.reset();
      })
      .catch(error => {
        console.error('Contact error:', error);
        if (error.response && error.response.status === 422) {
          const errors = error.response.data.errors;
          if (errors.name) document.getElementById('error-name').textContent = escapeHtml(errors.name);
          if (errors.email) document.getElementById('error-email').textContent = escapeHtml(errors.email);
          if (errors.message) document.getElementById('error-message').textContent = escapeHtml(errors.message);
        } else {
          errorAlert.textContent = escapeHtml(error.message) || 'Something went wrong. Please try again later.';
          errorAlert.classList.remove('d-none');
        }
      });
    });
  }
  
  if (newsletterForm) {
    console.log('Adding event listener to newsletter form');
    newsletterForm.addEventListener('submit', function(e) {
      e.preventDefault();
      console.log('Newsletter form submitted');
      
      const email = document.getElementById('newsletter-email').value;
      const name = document.getElementById('newsletter-name').value || '';
      const feedback = document.getElementById('newsletter-feedback');
      
      console.log('Newsletter form data:', { email, name });
      console.log('Newsletter feedback element found:', !!feedback);
      
      const newsletterData = {
        email: email,
        name: name
      };
      
      console.log('Sending newsletter request to /api/newsletter');
      fetch('/api/newsletter', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(newsletterData)
      })
      .then(response => {
        console.log('Newsletter response status:', response.status);
        console.log('Newsletter response headers:', 
          Array.from(response.headers.entries())
            .map(([key, value]) => `${key}: ${value}`)
            .join(', ')
        );
        
        if (!response.ok) {
          return response.json().then(data => {
            console.error('Newsletter error data:', data);
            throw new Error(data.error || 'Failed to subscribe');
          });
        }
        return response.json();
      })
      .then(data => {
        console.log('Newsletter success data:', data);
        feedback.textContent = 'Thank you for subscribing to my newsletter!';
        feedback.className = 'mt-2 small text-success';
        newsletterForm.reset();
      })
      .catch(error => {
        console.error('Newsletter error:', error);
        feedback.textContent = escapeHtml(error.message) || 'Failed to subscribe. Please try again later.';
        feedback.className = 'mt-2 small text-danger';
      });
    });
  }
  
  function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }
});