document.addEventListener('DOMContentLoaded', function() {
  initStarRating();
  initReviewForm();
  loadReviews();
});

function initStarRating() {
  const stars = document.querySelectorAll('.star');
  const ratingInput = document.getElementById('review-rating');
  const ratingText = document.querySelector('.rating-text');
  
  const ratingTexts = {
    0: 'Select a rating',
    1: 'Poor',
    2: 'Fair',
    3: 'Good',
    4: 'Very Good',
    5: 'Excellent'
  };
  
  stars.forEach(star => {
    star.addEventListener('mouseover', function() {
      const value = parseInt(this.dataset.value);
      
      stars.forEach(s => {
        const starValue = parseInt(s.dataset.value);
        if (starValue <= value) {
          s.classList.remove('far');
          s.classList.add('fas');
        } else {
          s.classList.remove('fas');
          s.classList.add('far');
        }
      });
      
      ratingText.textContent = ratingTexts[value];
    });
    
    star.addEventListener('click', function() {
      const value = parseInt(this.dataset.value);
      ratingInput.value = value;
    });
  });
  
  const starsContainer = document.querySelector('.stars');
  if (starsContainer) {
    starsContainer.addEventListener('mouseleave', function() {
      const currentRating = parseInt(ratingInput.value);
      
      stars.forEach(s => {
        const starValue = parseInt(s.dataset.value);
        if (starValue <= currentRating) {
          s.classList.remove('far');
          s.classList.add('fas');
        } else {
          s.classList.remove('fas');
          s.classList.add('far');
        }
      });
      
      ratingText.textContent = ratingTexts[currentRating];
    });
  }
}

function initReviewForm() {
  const reviewForm = document.getElementById('review-form');
  if (!reviewForm) return;
  
  reviewForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const reviewerName = document.getElementById('reviewer-name').value;
    const rating = document.getElementById('review-rating').value;
    const comment = document.getElementById('review-comment').value;
    const destination = document.getElementById('destination').value;
    
    if (!reviewerName || !comment || rating === '0') {
      alert('Please fill out all fields and provide a rating');
      return;
    }
    
    const reviewData = {
      destination: destination,
      reviewer_name: reviewerName,
      stars: parseInt(rating),
      comment: comment
    };
    
    submitReview(reviewData);
  });
}

function submitReview(reviewData) {
  fetch('/api/reviews', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(reviewData)
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Failed to submit review');
    }
    return response.json();
  })
  .then(data => {
    resetReviewForm();
    alert('Thank you! Your review has been submitted.');
    loadReviews();
  })
  .catch(error => {
    alert('Failed to submit review. Please try again later.');
  });
}

function resetReviewForm() {
  const reviewForm = document.getElementById('review-form');
  const stars = document.querySelectorAll('.star');
  const ratingInput = document.getElementById('review-rating');
  const ratingText = document.querySelector('.rating-text');
  
  if (reviewForm) reviewForm.reset();
  if (ratingInput) ratingInput.value = '0';
  
  if (stars) {
    stars.forEach(s => {
      s.classList.remove('fas');
      s.classList.add('far');
    });
  }
  
  if (ratingText) ratingText.textContent = 'Select a rating';
}

function loadReviews() {
  const reviewsContainer = document.getElementById('reviews-container');
  if (!reviewsContainer) return;
  
  const destination = document.getElementById('destination').value;
  if (!destination) return;
  
  const apiUrl = `/api/reviews?destination=${encodeURIComponent(destination)}`;
  
  fetch(apiUrl)
    .then(response => {
      if (!response.ok) {
        throw new Error('Failed to load reviews');
      }
      return response.json();
    })
    .then(reviews => {
      reviewsContainer.innerHTML = '';
      
      if (reviews.length === 0) {
        reviewsContainer.innerHTML = '<p class="text-center">No reviews yet. Be the first to share your experience!</p>';
        return;
      }
      
      reviews.forEach(review => {
        const reviewElement = createReviewElement(review);
        reviewsContainer.appendChild(reviewElement);
      });
    })
    .catch(error => {
      reviewsContainer.innerHTML = '<p class="text-center text-danger">Failed to load reviews. Please try again later.</p>';
    });
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

function createReviewElement(review) {
  const reviewElement = document.createElement('div');
  reviewElement.className = 'review-item';
  
  const reviewDate = new Date(review.created_at);
  const formattedDate = reviewDate.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
  
  let starsHtml = '';
  for (let i = 1; i <= 5; i++) {
    if (i <= review.stars) {
      starsHtml += '<i class="fas fa-star"></i>';
    } else {
      starsHtml += '<i class="far fa-star"></i>';
    }
  }
  
  reviewElement.innerHTML = `
    <div class="review-header">
      <div class="reviewer-info">
        <h4>${escapeHtml(review.reviewer_name)}</h4>
        <div class="review-date">${formattedDate}</div>
      </div>
      <div class="review-stars">
        ${starsHtml}
      </div>
    </div>
    <p class="review-comment">${escapeHtml(review.comment)}</p>
  `;
  
  return reviewElement;
}