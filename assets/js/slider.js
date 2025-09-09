// File: assets/js/slider.js

document.addEventListener('DOMContentLoaded', () => {
    // Get references to the slider elements
    const sliderInner = document.querySelector('.profil-slider-inner');
    const slides = document.querySelectorAll('.profil-slide');
    const prevBtn = document.querySelector('.slider-nav-btn.prev');
    const nextBtn = document.querySelector('.slider-nav-btn.next');

    // Initialize current slide index
    let currentSlide = 0;
    // Get the total number of slides
    const totalSlides = slides.length;

    /**
     * Updates the slider's position to show the specified slide.
     * @param {number} index The index of the slide to display.
     */
    function showSlide(index) {
        // Ensure the index loops around if it goes out of bounds
        if (index >= totalSlides) {
            currentSlide = 0; // Go back to the first slide
        } else if (index < 0) {
            currentSlide = totalSlides - 1; // Go to the last slide
        } else {
            currentSlide = index; // Set to the new index
        }

        // Calculate the translation distance. Each slide takes 100% of the wrapper's width.
        const offset = -currentSlide * 100;
        // Apply the transform to move the slider inner container
        sliderInner.style.transform = `translateX(${offset}%)`;
    }

    /**
     * Moves the slider to the next slide.
     */
    function nextSlide() {
        showSlide(currentSlide + 1);
    }

    /**
     * Moves the slider to the previous slide.
     */
    function prevSlide() {
        showSlide(currentSlide - 1);
    }

    // Add event listeners to the navigation buttons
    if (prevBtn) {
        prevBtn.addEventListener('click', prevSlide);
    }
    if (nextBtn) {
        nextBtn.addEventListener('click', nextSlide);
    }

    // Initialize the slider to show the first slide
    showSlide(currentSlide);

    // Optional: Auto-slide functionality (uncomment to enable)
    // let autoSlideInterval = setInterval(nextSlide, 5000); // Change slide every 5 seconds

    // Optional: Pause auto-slide on hover
    // const sliderWrapper = document.querySelector('.profil-slider-wrapper');
    // if (sliderWrapper) {
    //     sliderWrapper.addEventListener('mouseenter', () => {
    //         clearInterval(autoSlideInterval);
    //     });
    //     sliderWrapper.addEventListener('mouseleave', () => {
    //         autoSlideInterval = setInterval(nextSlide, 5000);
    //     });
    // }
});
