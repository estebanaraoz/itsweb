document.addEventListener('DOMContentLoaded', () => {
  const offset = 60;

  function easeInOutQuad(t) {
    return t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t;
  }

  function smoothScrollTo(targetY, duration) {
    const startY = window.pageYOffset;
    const diff = targetY - startY;
    let start;
    function step(timestamp) {
      if (start === undefined) start = timestamp;
      const time = timestamp - start;
      const progress = Math.min(time / duration, 1);
      const ease = easeInOutQuad(progress);
      window.scrollTo(0, startY + diff * ease);
      if (time < duration) {
        window.requestAnimationFrame(step);
      }
    }
    window.requestAnimationFrame(step);
  }

  document.querySelectorAll('a[href^="#"]:not(.back-to-top)').forEach(link => {
    link.addEventListener('click', e => {
      const id = link.getAttribute('href').slice(1);
      const target = document.getElementById(id);
      if (target) {
        e.preventDefault();
        smoothScrollTo(target.offsetTop - offset, 700);
      }
    });
  });

  const btnTop = document.querySelector('.back-to-top');
  window.addEventListener('scroll', () => {
    if (window.scrollY > 100) {
      btnTop.classList.add('visible');
    } else {
      btnTop.classList.remove('visible');
    }
  });
  btnTop.addEventListener('click', e => {
    e.preventDefault();
    smoothScrollTo(0, 700);
  });

  const testimonialContainer = document.getElementById('testimonialContainer');
  const testimonialWrapper = document.getElementById('testimonialWrapper');
  const prevBtn = document.getElementById('prevTestimonial');
  const nextBtn = document.getElementById('nextTestimonial');
  if (testimonialContainer && testimonialWrapper && prevBtn && nextBtn) {
    let index = 0;
    const cards = testimonialContainer.querySelectorAll('.testimonial-card');
    const getSlideWidth = () => {
      if (cards.length > 1) {
        return cards[1].offsetLeft - cards[0].offsetLeft;
      }
      return testimonialWrapper.clientWidth;
    };

    const updateSlider = () => {
      if (window.innerWidth >= 1024) {
        testimonialContainer.style.transform = 'none';
        return;
      }
      const width = getSlideWidth();
      testimonialContainer.style.transform = `translateX(-${index * width}px)`;
    };

    prevBtn.addEventListener('click', () => {
      index = Math.max(0, index - 1);
      updateSlider();
    });
    nextBtn.addEventListener('click', () => {
      index = Math.min(cards.length - 1, index + 1);
      updateSlider();
    });

    let startX = 0;
    let isSwiping = false;

    testimonialWrapper.addEventListener('touchstart', e => {
      startX = e.touches[0].clientX;
      isSwiping = true;
    });

    testimonialWrapper.addEventListener('touchend', e => {
      if (!isSwiping) return;
      const diff = e.changedTouches[0].clientX - startX;
      const threshold = 50;
      if (Math.abs(diff) > threshold) {
        if (diff < 0) {
          index = Math.min(cards.length - 1, index + 1);
        } else {
          index = Math.max(0, index - 1);
        }
        updateSlider();
      }
      isSwiping = false;
    });

    window.addEventListener('resize', updateSlider);
    updateSlider();
  }
});
