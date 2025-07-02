// Navbar: gestion du scroll (ajoute un fond sur scroll)
document.addEventListener('navbarLoaded', async () => {
    const navbar = document.querySelector('header');
    window.addEventListener('scroll', () => {
      if (window.scrollY > 20) {
        navbar.classList.add('shadow', 'bg-white', 'backdrop-blur');
      } else {
        navbar.classList.remove('shadow', 'bg-white', 'backdrop-blur');
      }
    });
  
    // Burger menu mobile (affiche/masque le menu mobile)
    const burger = document.getElementById('burger-menu');
    const mobileMenu = document.getElementById('mobile-menu');
    if (burger && mobileMenu) {
      burger.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
      });
    }
  });
  
  // Carrousel horizontal (scroll avec boutons, tailwind)
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.carousel-nouveautes, .carousel-suggestions').forEach(carousel => {
      carousel.addEventListener('wheel', e => {
        if (e.deltaY !== 0) {
          e.preventDefault();
          carousel.scrollLeft += e.deltaY;
        }
      }, { passive: false });
    });
  });