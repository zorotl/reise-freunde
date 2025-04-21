document.addEventListener('DOMContentLoaded', function () {
    const heroSection = document.querySelector('.bg-cover');

    if (heroSection) {
        window.addEventListener('scroll', function () {
            const scrollPosition = window.scrollY;
            heroSection.style.backgroundPositionY = (scrollPosition * 0.3) + 'px';
        });
    }
});