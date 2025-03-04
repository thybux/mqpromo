
function initPromoCarousel() {
  const carousel = document.querySelector('.mqpromo-carousel');
  const prevButton = document.querySelector('.mqpromo-prev');
  const nextButton = document.querySelector('.mqpromo-next');
  const items = document.querySelectorAll('.mqpromo-item');
  const dotsContainer = document.getElementById('mqpromo-dots');
  
  if (!carousel || !prevButton || !nextButton || items.length === 0) {
    return;
  }
  
  let currentIndex = 0;
  let itemWidth = 0;
  let itemsPerView = 0;
  let maxIndex = 0;
  let autoScrollTimer = null;
  let touchStartX = 0;
  let touchEndX = 0;
  
  function initDimensions() {
    itemWidth = items[0].getBoundingClientRect().width;
    itemsPerView = Math.floor(carousel.clientWidth / itemWidth);
    maxIndex = Math.max(0, items.length - itemsPerView);
    
    currentIndex = Math.min(currentIndex, maxIndex);
    console.log(currentIndex)
    scrollToCurrentIndex(false);
    updateCarouselState();
    
    generateDotIndicators();
  }
  
  function generateDotIndicators() {
    if (!dotsContainer) return;
    
    dotsContainer.innerHTML = '';
    
    const totalPages = maxIndex + 1;
    
    for (let i = 0; i < totalPages; i++) {
      const dot = document.createElement('div');
      dot.classList.add('mqpromo-dot');
      if (i === currentIndex) {
        dot.classList.add('active');
      }
      
      dot.addEventListener('click', function() {
        currentIndex = i;
        scrollToCurrentIndex();
        updateCarouselState();
        restartAutoScroll();
      });
      
      dotsContainer.appendChild(dot);
    }
  }
  
  function updateDotIndicators() {
    if (!dotsContainer) return;
    
    const dots = dotsContainer.querySelectorAll('.mqpromo-dot');
    dots.forEach((dot, index) => {
      if (index === currentIndex) {
        dot.classList.add('active');
      } else {
        dot.classList.remove('active');
      }
    });
  }
  
  function navigateCarousel(direction) {
    if (direction === 'prev') {
      currentIndex = Math.max(0, currentIndex - 1);
    } else {
      currentIndex = Math.min(maxIndex, currentIndex + 1);
    }
    
    scrollToCurrentIndex();
    updateCarouselState();
    restartAutoScroll();
  }
  
  function scrollToCurrentIndex(animated = true) {
    const scrollPosition = currentIndex * (itemWidth + parseFloat(getComputedStyle(items[0]).marginRight) * 2);
    
    if (animated) {
      carousel.scrollTo({
        left: scrollPosition,
        behavior: 'smooth'
      });
    } else {
      carousel.scrollTo({
        left: scrollPosition,
        behavior: 'auto'
      });
    }
  }
  
  function updateCarouselState() {
    prevButton.disabled = currentIndex === 0;
    nextButton.disabled = currentIndex >= maxIndex;
    
    prevButton.style.opacity = currentIndex === 0 ? '0.5' : '1';
    nextButton.style.opacity = currentIndex >= maxIndex ? '0.5' : '1';
    
    updateDotIndicators();
    
    items.forEach((item, index) => {
      const itemIndex = parseInt(item.dataset.index, 10);
      const isVisible = itemIndex >= currentIndex && itemIndex < currentIndex + itemsPerView;
      item.style.opacity = isVisible ? '1' : '0.7';
      item.style.transform = isVisible ? 'scale(1)' : 'scale(0.95)';
    });
  }
  
  function handleSwipe() {
    const swipeThreshold = 50;
    const difference = touchStartX - touchEndX;
    
    if (difference > swipeThreshold) {
      navigateCarousel('next');
    } else if (difference < -swipeThreshold) {
      navigateCarousel('prev');
    }
  }
  
  function startAutoScroll() {
    stopAutoScroll();
    autoScrollTimer = setInterval(function() {
      if (currentIndex < maxIndex) {
        navigateCarousel('next');
      } else {
        currentIndex = 0;
        scrollToCurrentIndex();
        updateCarouselState();
      }
    }, 5000);  }
  
  function stopAutoScroll() {
    if (autoScrollTimer) {
      clearInterval(autoScrollTimer);
    }
  }
  
  function restartAutoScroll() {
    stopAutoScroll();
    startAutoScroll();
  }
  
  initDimensions();
  
  prevButton.addEventListener('click', function() {
    navigateCarousel('prev');
  });
  
  nextButton.addEventListener('click', function() {
    navigateCarousel('next');
  });
  
  carousel.addEventListener('touchstart', function(e) {
    touchStartX = e.changedTouches[0].screenX;
    stopAutoScroll();
  }, { passive: true });
  
  carousel.addEventListener('touchend', function(e) {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipe();
    startAutoScroll();
  }, { passive: true });
  
  carousel.addEventListener('mouseenter', stopAutoScroll);
  carousel.addEventListener('mouseleave', startAutoScroll);
  
  let resizeTimeout;
  window.addEventListener('resize', function() {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(function() {
      initDimensions();
    }, 250);
  });
  
  startAutoScroll();
  
  setTimeout(function() {
    items.forEach(item => {
      item.style.opacity = '1';
      item.style.transform = 'translateY(0)';
    });
  }, 100);
}
