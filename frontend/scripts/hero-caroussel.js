const slides = [
    {
      img: "https://ik.imagekit.io/melfuviii/Arvest/assets/index/IMG-20240608-WA0032.jpg?updatedAt=1751245163105",
      thumb: "https://ik.imagekit.io/melfuviii/Arvest/assets/index/IMG-20240608-WA0032.jpg?updatedAt=1751245163105",
      title: "Découvrez la richesse<br><span class='text-orange-400'>des contes congolais</span>",
      mobileTitle: "Découvrez<br><span class='text-orange-400'>les contes congolais</span>",
      desc: "Plongez dans l’univers fascinant des histoires, proverbes et coutumes qui font la fierté de la culture congolaise.",
      mobileDesc: "Plongez dans les histoires, proverbes et coutumes du Congo.",
      label: "Contes du Congo",
      sublabel: "Culture & Sagesse"
    },
    {
      img: "https://ik.imagekit.io/melfuviii/Arvest/assets/index/Carnival-of-Lubumbashi.jpg?updatedAt=1751245166268",
      thumb: "https://ik.imagekit.io/melfuviii/Arvest/assets/index/Carnival-of-Lubumbashi.jpg?updatedAt=1751245166268",
      title: "La magie du <span class='text-orange-400'>Carnaval de Lubumbashi</span>",
      mobileTitle: "Carnaval de Lubumbashi",
      desc: "Un événement haut en couleurs qui rassemble toutes les générations autour de la fête et de la tradition.",
      mobileDesc: "Un événement haut en couleurs pour tous.",
      label: "Carnaval Lubumbashi",
      sublabel: "Fête & Tradition"
    },
    {
      img: "https://ik.imagekit.io/melfuviii/Arvest/assets/index/Les%20Bamil%C3%A9k%C3%A9s,%20groupe%20ethnique%20du%20Cameroun.jpg?updatedAt=1751245163505",
      thumb: "https://ik.imagekit.io/melfuviii/Arvest/assets/index/Les%20Bamil%C3%A9k%C3%A9s,%20groupe%20ethnique%20du%20Cameroun.jpg?updatedAt=1751245163505",
      title: "Les <span class='text-orange-400'>Bamilékés</span> et la force du groupe",
      mobileTitle: "Les Bamilékés",
      desc: "Découvrez la solidarité et la richesse culturelle de ce peuple emblématique.",
      mobileDesc: "Solidarité et richesse culturelle.",
      label: "Bamilékés",
      sublabel: "Solidarité"
    },
    {
      img: "https://ik.imagekit.io/melfuviii/Arvest/assets/index/Culture-of-Congo-People.jpg?updatedAt=1751245163495",
      thumb: "https://ik.imagekit.io/melfuviii/Arvest/assets/index/Culture-of-Congo-People.jpg?updatedAt=1751245163495",
      title: "La <span class='text-orange-400'>culture congolaise</span> à l’honneur",
      mobileTitle: "Culture congolaise",
      desc: "Partagez et préservez les récits et savoirs transmis de génération en génération.",
      mobileDesc: "Préservez les récits et savoirs du Congo.",
      label: "Culture",
      sublabel: "Transmission"
    }
  ];
  
  let current = 0;
  let autoInterval = null;
  
  function updateHero() {
    // Desktop
    const heroBg = document.getElementById('hero-bg');
    const heroTitle = document.getElementById('hero-title');
    const heroDesc = document.getElementById('hero-desc');
    // Mobile
    const mobileTitle = document.querySelector('.md\\:hidden #hero-title, .md\\:hidden h1');
    const mobileDesc = document.querySelector('.md\\:hidden #hero-desc, .md\\:hidden p');
    // Carousel cards
    const carousel = document.querySelector('.flex-1 .flex.items-end');
    const leftBtn = document.querySelector('.bx-chevron-left')?.closest('button');
    const rightBtn = document.querySelector('.bx-chevron-right')?.closest('button');
  
    // Fond principal avec transition fluide
    if (heroBg) {
      heroBg.style.transition = "opacity 0.7s cubic-bezier(.4,2,.6,1)";
      heroBg.style.opacity = 0;
      setTimeout(() => {
        heroBg.src = slides[current].img;
        heroBg.onload = () => {
          heroBg.style.opacity = 1;
        };
      }, 350);
    }
  
    // Texte desktop
    if (heroTitle) {
      heroTitle.style.transition = "opacity 0.5s";
      heroTitle.style.opacity = 0;
      setTimeout(() => {
        heroTitle.innerHTML = slides[current].title;
        heroTitle.style.opacity = 1;
      }, 250);
    }
    if (heroDesc) {
      heroDesc.style.transition = "opacity 0.5s";
      heroDesc.style.opacity = 0;
      setTimeout(() => {
        heroDesc.textContent = slides[current].desc;
        heroDesc.style.opacity = 1;
      }, 250);
    }
  
    // Texte mobile
    if (mobileTitle) {
      mobileTitle.style.transition = "opacity 0.5s";
      mobileTitle.style.opacity = 0;
      setTimeout(() => {
        mobileTitle.innerHTML = slides[current].mobileTitle;
        mobileTitle.style.opacity = 1;
      }, 250);
    }
    if (mobileDesc) {
      mobileDesc.style.transition = "opacity 0.5s";
      mobileDesc.style.opacity = 0;
      setTimeout(() => {
        mobileDesc.textContent = slides[current].mobileDesc;
        mobileDesc.style.opacity = 1;
      }, 250);
    }
  
    // Carousel desktop avec transition fluide
    if (carousel) {
      carousel.style.transition = "opacity 0.5s";
      carousel.style.opacity = 0;
      setTimeout(() => {
        carousel.innerHTML = slides.map((slide, i) => {
          let z = 30 - i;
          let scale = i === current ? 'scale-110 z-30 ring-4 ring-orange-500 shadow-2xl' :
                      i === current - 1 ? 'scale-95 z-20 shadow-lg -ml-8' :
                      i === current + 1 ? 'scale-95 z-20 shadow-lg -ml-8' :
                      'scale-90 z-10 shadow -ml-8';
          let h = i === current ? 'h-[320px] w-[220px]' :
                  i === current - 1 || i === current + 1 ? 'h-[260px] w-[170px]' :
                  'h-[180px] w-[110px]';
          return `
            <div class="relative bg-white rounded-3xl overflow-hidden ${h} ${scale} transition-all duration-700">
              <img src="${slide.thumb}" alt="${slide.label}" class="w-full h-full object-cover transition-all duration-700" />
              <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-black/80 via-black/30 to-transparent px-5 py-4">
                <h3 class="text-white font-bold text-xl mb-1">${slide.label}</h3>
                <span class="text-sm text-orange-200">${slide.sublabel}</span>
              </div>
            </div>
          `;
        }).join('');
        carousel.style.opacity = 1;
      }, 250);
    }
  }
  
  function goTo(idx) {
    current = (idx + slides.length) % slides.length;
    updateHero();
    resetAutoCarousel();
  }
  
  function next() {
    goTo(current + 1);
  }
  function prev() {
    goTo(current - 1);
  }
  
  function startAutoCarousel() {
    if (autoInterval) clearInterval(autoInterval);
    autoInterval = setInterval(() => {
      next();
    }, 5000);
  }
  
  function resetAutoCarousel() {
    if (autoInterval) clearInterval(autoInterval);
    startAutoCarousel();
  }
  
  document.addEventListener('DOMContentLoaded', () => {
    updateHero();
    startAutoCarousel();
  
    // Flèches desktop
    const leftBtn = document.querySelector('.bx-chevron-left')?.closest('button');
    const rightBtn = document.querySelector('.bx-chevron-right')?.closest('button');
    if (leftBtn) leftBtn.addEventListener('click', prev);
    if (rightBtn) rightBtn.addEventListener('click', next);
  
    // Swipe mobile
    let startX = null;
    const heroSection = document.querySelector('.hero, .relative.w-full.h-screen');
    if (heroSection) {
      heroSection.addEventListener('touchstart', e => {
        startX = e.touches[0].clientX;
      });
      heroSection.addEventListener('touchend', e => {
        if (startX === null) return;
        let dx = e.changedTouches[0].clientX - startX;
        if (dx > 40) prev();
        else if (dx < -40) next();
        startX = null;
      });
    }
  });
  