import TomSelect from 'tom-select';

// Make it globally accessible if needed by Alpine, or ensure build process handles it
window.TomSelect = TomSelect;

document.addEventListener('DOMContentLoaded', function () {
    const heroSection = document.querySelector('.bg-cover');

    if (heroSection) {
        window.addEventListener('scroll', function () {
            const scrollPosition = window.scrollY;
            heroSection.style.backgroundPositionY = (scrollPosition * 0.3) + 'px';
        });
    }
});