<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Explorer | Arvest Congo</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="./styles/output.css" />
</head>
<body class="bg-[#f5f8fa] font-poppins text-gray-800">
  <!-- NAVBAR -->
  <?php include './components/navbar.php'; ?>

  <!-- HERO BANNER NOUVEAUTÉS (carousel style index) -->
  <section class="relative w-full flex justify-center mt-24">
    <div class="relative w-[85vw] max-w-6xl h-[420px] rounded-3xl overflow-hidden shadow-lg flex items-center bg-gray-900">
      <div class="absolute inset-0 z-0">
        <img id="explore-hero-bg" src="../assets/Explorer/explorer-hero.jpg" alt="Bannière Explore" class="w-full h-full object-cover object-center brightness-75 transition-all duration-700" />
      </div>
      <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/60 to-transparent z-10"></div>
      <div class="relative z-20 flex flex-col justify-center h-full px-8 py-12 max-w-xl">
        <span class="text-lg text-orange-400 font-semibold mb-2">Nouveautés</span>
        <h1 id="explore-hero-title" class="text-3xl md:text-5xl font-extrabold text-white mb-4 drop-shadow-lg leading-tight">
          Découvrez les <span class="text-orange-400">dernières publications</span>
        </h1>
        <p id="explore-hero-desc" class="text-lg text-gray-100 mb-8 max-w-2xl drop-shadow">
          Plongez dans les nouveaux contes, proverbes et récits partagés par la communauté.
        </p>
        <a href="#feed" class="inline-flex items-center gap-2 px-8 py-3 bg-orange-500 text-white rounded-full font-semibold shadow hover:bg-orange-600 transition text-lg">
          <i class="bx bx-chevron-down text-2xl"></i>
          Voir les publications
        </a>
      </div>
      <!-- Carousel dots dynamiques -->
      <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2 z-30" id="explore-hero-dots">
        <button class="explore-dot w-3 h-3 rounded-full bg-orange-400 opacity-80"></button>
        <button class="explore-dot w-3 h-3 rounded-full bg-white opacity-60"></button>
        <button class="explore-dot w-3 h-3 rounded-full bg-white opacity-60"></button>
      </div>
    </div>
  </section>

  <!-- CAROUSEL NOUVEAUTÉS (horizontal, pas de scrollbar) -->
  <section class="w-[85vw] max-w-7xl mx-auto mt-16 px-4">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-2xl md:text-3xl font-bold text-orange-600">Nouveautés</h2>
      <a href="#" class="text-orange-600 font-semibold hover:underline">Tout voir</a>
    </div>
    <div class="flex gap-8 overflow-x-auto pb-4 snap-x snap-mandatory no-scrollbar carousel-nouveautes">
      <!-- Cartes nouveauté (exemple) -->
      <div class="min-w-[340px] max-w-[340px] bg-white rounded-2xl shadow-lg flex flex-col overflow-hidden snap-center hover:shadow-xl transition">
        <img src="../assets/Explorer/offer-conte.jpg" alt="Nouveauté" class="w-full h-48 object-cover">
        <div class="flex flex-col p-6 flex-1">
          <div class="font-bold text-xl text-[#1b263b] mb-1">Le lion et le lièvre</div>
          <div class="text-sm text-orange-600 mb-2">Conte traditionnel • 4.8 ★</div>
          <div class="text-gray-600 text-sm">Un conte populaire du Katanga sur la ruse et le courage.</div>
        </div>
      </div>
      <div class="min-w-[340px] max-w-[340px] bg-white rounded-2xl shadow-lg flex flex-col overflow-hidden snap-center hover:shadow-xl transition">
        <img src="../assets/Explorer/offer-proverbe.jpg" alt="Nouveauté" class="w-full h-48 object-cover">
        <div class="flex flex-col p-6 flex-1">
          <div class="font-bold text-xl text-[#1b263b] mb-1">"La patience est une vertu"</div>
          <div class="text-sm text-orange-600 mb-2">Proverbe • 4.7 ★</div>
          <div class="text-gray-600 text-sm">Un proverbe qui enseigne la sagesse et la persévérance.</div>
        </div>
      </div>
      <div class="min-w-[340px] max-w-[340px] bg-white rounded-2xl shadow-lg flex flex-col overflow-hidden snap-center hover:shadow-xl transition">
        <img src="../assets/Explorer/offer-theme.jpg" alt="Nouveauté" class="w-full h-48 object-cover">
        <div class="flex flex-col p-6 flex-1">
          <div class="font-bold text-xl text-[#1b263b] mb-1">Thème : Animaux</div>
          <div class="text-sm text-orange-600 mb-2">+120 histoires</div>
          <div class="text-gray-600 text-sm">Découvrez les contes et proverbes mettant en scène les animaux.</div>
        </div>
      </div>
      <div class="min-w-[340px] max-w-[340px] bg-white rounded-2xl shadow-lg flex flex-col overflow-hidden snap-center hover:shadow-xl transition">
        <img src="../assets/Explorer/offer-auteur.jpg" alt="Nouveauté" class="w-full h-48 object-cover">
        <div class="flex flex-col p-6 flex-1">
          <div class="font-bold text-xl text-[#1b263b] mb-1">Auteur : M. Kalaba</div>
          <div class="text-sm text-orange-600 mb-2">Conteur • 4.9 ★</div>
          <div class="text-gray-600 text-sm">Un des plus grands conteurs du Katanga, gardien de la tradition orale.</div>
        </div>
      </div>
    </div>
  </section>

  <!-- PUBLICATIONS FEED (nouveau concept, grille pleine largeur) -->
  <section id="feed" class="w-[85vw] max-w-7xl mx-auto mt-16 px-2 flex flex-col gap-8">
    <!-- Publication 1 -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch publication mb-8" data-id="1">
      <!-- Bloc publication gauche -->
      <div class="bg-white rounded-2xl shadow-lg flex flex-col p-8 gap-4 relative h-full">
        <div class="flex items-center gap-4 mb-2">
          <img src="../assets/profile/profile.jpg" alt="Auteur" class="w-16 h-16 rounded-2xl object-cover border-2 border-orange-200 shadow">
          <div>
            <div class="font-bold text-lg text-orange-600">Emmanuel N.</div>
            <div class="flex items-center gap-2">
              <button class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full text-xs font-semibold hover:bg-orange-200 transition">Suivre</button>
              <span class="text-xs text-gray-500 ml-2">1.2k followers</span>
            </div>
            <div class="text-xs text-gray-400 mt-1">12 œuvres</div>
          </div>
        </div>
        <div class="flex flex-row gap-4">
          <div class="flex-1">
            <div class="text-xl font-bold text-[#1b263b] mb-1">Le léopard et la tortue</div>
            <div class="text-gray-600 text-sm mb-2 line-clamp-4">Un conte du Kasaï sur la ruse et la sagesse face à la force brute. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque euismod, nisi eu consectetur consectetur, nisl nisi consectetur nisi, euismod euismod nisi nisi euismod.</div>
            <a href="#" class="text-orange-600 text-xs font-semibold hover:underline">Lire la suite</a>
          </div>
          <img src="../assets/Explorer/conte1.jpg" alt="Conte" class="w-32 h-32 object-cover rounded-xl border border-orange-100">
        </div>
        <div class="flex items-center gap-8 mt-4">
          <button class="pub-like-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition" data-liked="false">
            <i class="bx bx-heart text-2xl"></i>
            <span class="like-count">12</span>
          </button>
          <button class="pub-comment-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition">
            <i class="bx bx-message-rounded text-2xl"></i>
            <span class="comment-count">3</span>
          </button>
          <button class="pub-share-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition">
            <i class="bx bx-share-alt text-2xl"></i>
            <span>Partager</span>
          </button>
        </div>
      </div>
      <!-- Bloc commentaires droite -->
      <div class="bg-orange-50 rounded-2xl shadow flex flex-col p-8 gap-4 h-full pub-comments-block relative min-h-[320px]">
        <div class="font-semibold text-orange-600 mb-2">Commentaires</div>
        <div class="flex flex-col gap-3 pub-comments flex-1">
          <div class="flex items-start gap-3">
            <img src="../assets/profile/profile2.jpg" class="w-10 h-10 rounded-full object-cover" alt="User">
            <div class="bg-white rounded-xl px-4 py-2 text-sm text-gray-800 shadow">Superbe histoire !</div>
          </div>
          <div class="flex items-start gap-3">
            <img src="../assets/profile/profile3.jpg" class="w-10 h-10 rounded-full object-cover" alt="User">
            <div class="bg-white rounded-xl px-4 py-2 text-sm text-gray-800 shadow">J'adore la morale.</div>
          </div>
        </div>
        <form class="flex items-center gap-2 mt-2 pub-comment-form absolute bottom-8 left-8 right-8">
          <input type="text" placeholder="Ajouter un commentaire..." class="flex-1 px-3 py-2 rounded-full bg-gray-50 border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
          <button type="submit" class="text-orange-600 font-bold">Envoyer</button>
        </form>
      </div>
    </div>
    <!-- Publication 2 -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch publication mb-8" data-id="2">
      <div class="bg-white rounded-2xl shadow-lg flex flex-col p-8 gap-4 relative h-full">
        <div class="flex items-center gap-4 mb-2">
          <img src="../assets/profile/profile2.jpg" alt="Auteur" class="w-16 h-16 rounded-2xl object-cover border-2 border-orange-200 shadow">
          <div>
            <div class="font-bold text-lg text-orange-600">Sophie M.</div>
            <div class="flex items-center gap-2">
              <button class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full text-xs font-semibold hover:bg-orange-200 transition">Suivre</button>
              <span class="text-xs text-gray-500 ml-2">2.1k followers</span>
            </div>
            <div class="text-xs text-gray-400 mt-1">8 œuvres</div>
          </div>
        </div>
        <div class="flex flex-row gap-4">
          <div class="flex-1">
            <div class="text-xl font-bold text-[#1b263b] mb-1">« La patience est une vertu »</div>
            <div class="text-gray-600 text-sm mb-2 line-clamp-4">Un proverbe qui enseigne la sagesse et la persévérance. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque euismod, nisi eu consectetur consectetur, nisl nisi consectetur nisi, euismod euismod nisi nisi euismod.</div>
            <a href="#" class="text-orange-600 text-xs font-semibold hover:underline">Lire la suite</a>
          </div>
          <img src="../assets/Explorer/proverbe1.jpg" alt="Proverbe" class="w-32 h-32 object-cover rounded-xl border border-orange-100">
        </div>
        <div class="flex items-center gap-8 mt-4">
          <button class="pub-like-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition" data-liked="false">
            <i class="bx bx-heart text-2xl"></i>
            <span class="like-count">8</span>
          </button>
          <button class="pub-comment-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition">
            <i class="bx bx-message-rounded text-2xl"></i>
            <span class="comment-count">1</span>
          </button>
          <button class="pub-share-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition">
            <i class="bx bx-share-alt text-2xl"></i>
            <span>Partager</span>
          </button>
        </div>
      </div>
      <div class="bg-orange-50 rounded-2xl shadow flex flex-col p-8 gap-4 h-full pub-comments-block relative min-h-[320px]">
        <div class="font-semibold text-orange-600 mb-2">Commentaires</div>
        <div class="flex flex-col gap-3 pub-comments flex-1">
          <div class="flex items-start gap-3">
            <img src="../assets/profile/profile.jpg" class="w-10 h-10 rounded-full object-cover" alt="User">
            <div class="bg-white rounded-xl px-4 py-2 text-sm text-gray-800 shadow">Très vrai !</div>
          </div>
        </div>
        <form class="flex items-center gap-2 mt-2 pub-comment-form absolute bottom-8 left-8 right-8">
          <input type="text" placeholder="Ajouter un commentaire..." class="flex-1 px-3 py-2 rounded-full bg-gray-50 border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
          <button type="submit" class="text-orange-600 font-bold">Envoyer</button>
        </form>
      </div>
    </div>
    <!-- Publication 3 -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch publication mb-8" data-id="3">
      <div class="bg-white rounded-2xl shadow-lg flex flex-col p-8 gap-4 relative h-full">
        <div class="flex items-center gap-4 mb-2">
          <img src="../assets/profile/profile3.jpg" alt="Auteur" class="w-16 h-16 rounded-2xl object-cover border-2 border-orange-200 shadow">
          <div>
            <div class="font-bold text-lg text-orange-600">Patrick K.</div>
            <div class="flex items-center gap-2">
              <button class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full text-xs font-semibold hover:bg-orange-200 transition">Suivre</button>
              <span class="text-xs text-gray-500 ml-2">1.8k followers</span>
            </div>
            <div class="text-xs text-gray-400 mt-1">10 œuvres</div>
          </div>
        </div>
        <div class="flex flex-row gap-4">
          <div class="flex-1">
            <div class="text-xl font-bold text-[#1b263b] mb-1">Les sages et le roi</div>
            <div class="text-gray-600 text-sm mb-2 line-clamp-4">Un conte inspirant sur le leadership et l'humilité. Découvrez comment un roi apprend des leçons précieuses de ses sujets. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
            <a href="#" class="text-orange-600 text-xs font-semibold hover:underline">Lire la suite</a>
          </div>
          <img src="../assets/Explorer/conte2.jpg" alt="Conte" class="w-32 h-32 object-cover rounded-xl border border-orange-100">
        </div>
        <div class="flex items-center gap-8 mt-4">
          <button class="pub-like-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition" data-liked="false">
            <i class="bx bx-heart text-2xl"></i>
            <span class="like-count">15</span>
          </button>
          <button class="pub-comment-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition">
            <i class="bx bx-message-rounded text-2xl"></i>
            <span class="comment-count">5</span>
          </button>
          <button class="pub-share-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition">
            <i class="bx bx-share-alt text-2xl"></i>
            <span>Partager</span>
          </button>
        </div>
      </div>
      <div class="bg-orange-50 rounded-2xl shadow flex flex-col p-8 gap-4 h-full pub-comments-block relative min-h-[320px]">
        <div class="font-semibold text-orange-600 mb-2">Commentaires</div>
        <div class="flex flex-col gap-3 pub-comments flex-1">
          <div class="flex items-start gap-3">
            <a href="profile-other.html"><img src="../assets/profile/profile4.jpg" class="w-10 h-10 rounded-full object-cover" alt="User"></a>
            <div class="bg-white rounded-xl px-4 py-2 text-sm text-gray-800 shadow">Une belle leçon !</div>
          </div>
          <div class="flex items-start gap-3">
            <img src="../assets/profile/profile5.jpg" class="w-10 h-10 rounded-full object-cover" alt="User">
            <div class="bg-white rounded-xl px-4 py-2 text-sm text-gray-800 shadow">À méditer...</div>
          </div>
        </div>
        <form class="flex items-center gap-2 mt-2 pub-comment-form absolute bottom-8 left-8 right-8">
          <input type="text" placeholder="Ajouter un commentaire..." class="flex-1 px-3 py-2 rounded-full bg-gray-50 border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
          <button type="submit" class="text-orange-600 font-bold">Envoyer</button>
        </form>
      </div>
    </div>
    <!-- Publication 4 -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch publication mb-8" data-id="4">
      <div class="bg-white rounded-2xl shadow-lg flex flex-col p-8 gap-4 relative h-full">
        <div class="flex items-center gap-4 mb-2">
          <img src="../assets/profile/profile4.jpg" alt="Auteur" class="w-16 h-16 rounded-2xl object-cover border-2 border-orange-200 shadow">
          <div>
            <div class="font-bold text-lg text-orange-600">Isabelle T.</div>
            <div class="flex items-center gap-2">
              <button class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full text-xs font-semibold hover:bg-orange-200 transition">Suivre</button>
              <span class="text-xs text-gray-500 ml-2">900 followers</span>
            </div>
            <div class="text-xs text-gray-400 mt-1">5 œuvres</div>
          </div>
        </div>
        <div class="flex flex-row gap-4">
          <div class="flex-1">
            <div class="text-xl font-bold text-[#1b263b] mb-1">Le serpent et la grenouille</div>
            <div class="text-gray-600 text-sm mb-2 line-clamp-4">Un conte sur l'amitié et la trahison. Découvrez comment un serpent et une grenouille apprennent à se connaître et à se méfier. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
            <a href="#" class="text-orange-600 text-xs font-semibold hover:underline">Lire la suite</a>
          </div>
          <img src="../assets/Explorer/conte3.jpg" alt="Conte" class="w-32 h-32 object-cover rounded-xl border border-orange-100">
        </div>
        <div class="flex items-center gap-8 mt-4">
          <button class="pub-like-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition" data-liked="false">
            <i class="bx bx-heart text-2xl"></i>
            <span class="like-count">10</span>
          </button>
          <button class="pub-comment-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition">
            <i class="bx bx-message-rounded text-2xl"></i>
            <span class="comment-count">2</span>
          </button>
          <button class="pub-share-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition">
            <i class="bx bx-share-alt text-2xl"></i>
            <span>Partager</span>
          </button>
        </div>
      </div>
      <div class="bg-orange-50 rounded-2xl shadow flex flex-col p-8 gap-4 h-full pub-comments-block relative min-h-[320px]">
        <div class="font-semibold text-orange-600 mb-2">Commentaires</div>
        <div class="flex flex-col gap-3 pub-comments flex-1">
          <div class="flex items-start gap-3">
            <img src="../assets/profile/profile1.jpg" class="w-10 h-10 rounded-full object-cover" alt="User">
            <div class="bg-white rounded-xl px-4 py-2 text-sm text-gray-800 shadow">Une histoire captivante !</div>
          </div>
        </div>
        <form class="flex items-center gap-2 mt-2 pub-comment-form absolute bottom-8 left-8 right-8">
          <input type="text" placeholder="Ajouter un commentaire..." class="flex-1 px-3 py-2 rounded-full bg-gray-50 border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
          <button type="submit" class="text-orange-600 font-bold">Envoyer</button>
        </form>
      </div>
    </div>
    <!-- Publication 5 -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch publication mb-8" data-id="5">
      <div class="bg-white rounded-2xl shadow-lg flex flex-col p-8 gap-4 relative h-full">
        <div class="flex items-center gap-4 mb-2">
          <img src="../assets/profile/profile5.jpg" alt="Auteur" class="w-16 h-16 rounded-2xl object-cover border-2 border-orange-200 shadow">
          <div>
            <div class="font-bold text-lg text-orange-600">Marc L.</div>
            <div class="flex items-center gap-2">
              <button class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full text-xs font-semibold hover:bg-orange-200 transition">Suivre</button>
              <span class="text-xs text-gray-500 ml-2">750 followers</span>
            </div>
            <div class="text-xs text-gray-400 mt-1">3 œuvres</div>
          </div>
        </div>
        <div class="flex flex-row gap-4">
          <div class="flex-1">
            <div class="text-xl font-bold text-[#1b263b] mb-1">Le rat et l'éléphant</div>
            <div class="text-gray-600 text-sm mb-2 line-clamp-4">Un conte sur la force de l'amitié et la solidarité. Découvrez comment un rat et un éléphant s'entraident dans les moments difficiles. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
            <a href="#" class="text-orange-600 text-xs font-semibold hover:underline">Lire la suite</a>
          </div>
          <img src="../assets/Explorer/conte4.jpg" alt="Conte" class="w-32 h-32 object-cover rounded-xl border border-orange-100">
        </div>
        <div class="flex items-center gap-8 mt-4">
          <button class="pub-like-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition" data-liked="false">
            <i class="bx bx-heart text-2xl"></i>
            <span class="like-count">20</span>
          </button>
          <button class="pub-comment-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition">
            <i class="bx bx-message-rounded text-2xl"></i>
            <span class="comment-count">8</span>
          </button>
          <button class="pub-share-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition">
            <i class="bx bx-share-alt text-2xl"></i>
            <span>Partager</span>
          </button>
        </div>
      </div>
      <div class="bg-orange-50 rounded-2xl shadow flex flex-col p-8 gap-4 h-full pub-comments-block relative min-h-[320px]">
        <div class="font-semibold text-orange-600 mb-2">Commentaires</div>
        <div class="flex flex-col gap-3 pub-comments flex-1">
          <div class="flex items-start gap-3">
            <img src="../assets/profile/profile2.jpg" class="w-10 h-10 rounded-full object-cover" alt="User">
            <div class="bg-white rounded-xl px-4 py-2 text-sm text-gray-800 shadow">Une belle histoire d'amitié.</div>
          </div>
        </div>
        <form class="flex items-center gap-2 mt-2 pub-comment-form absolute bottom-8 left-8 right-8">
          <input type="text" placeholder="Ajouter un commentaire..." class="flex-1 px-3 py-2 rounded-full bg-gray-50 border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
          <button type="submit" class="text-orange-600 font-bold">Envoyer</button>
        </form>
      </div>
    </div>
    <!-- Publication 6 -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch publication mb-8" data-id="6">
      <div class="bg-white rounded-2xl shadow-lg flex flex-col p-8 gap-4 relative h-full">
        <div class="flex items-center gap-4 mb-2">
          <img src="../assets/profile/profile6.jpg" alt="Auteur" class="w-16 h-16 rounded-2xl object-cover border-2 border-orange-200 shadow">
          <div>
            <div class="font-bold text-lg text-orange-600">Claire R.</div>
            <div class="flex items-center gap-2">
              <button class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full text-xs font-semibold hover:bg-orange-200 transition">Suivre</button>
              <span class="text-xs text-gray-500 ml-2">620 followers</span>
            </div>
            <div class="text-xs text-gray-400 mt-1">7 œuvres</div>
          </div>
        </div>
        <div class="flex flex-row gap-4">
          <div class="flex-1">
            <div class="text-xl font-bold text-[#1b263b] mb-1">Le hibou et les étoiles</div>
            <div class="text-gray-600 text-sm mb-2 line-clamp-4">Un conte poétique sur la sagesse et la connaissance. Découvrez comment un hibou transmet son savoir aux étoiles. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
            <a href="#" class="text-orange-600 text-xs font-semibold hover:underline">Lire la suite</a>
          </div>
          <img src="../assets/Explorer/conte5.jpg" alt="Conte" class="w-32 h-32 object-cover rounded-xl border border-orange-100">
        </div>
        <div class="flex items-center gap-8 mt-4">
          <button class="pub-like-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition" data-liked="false">
            <i class="bx bx-heart text-2xl"></i>
            <span class="like-count">18</span>
          </button>
          <button class="pub-comment-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition">
            <i class="bx bx-message-rounded text-2xl"></i>
            <span class="comment-count">6</span>
          </button>
          <button class="pub-share-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition">
            <i class="bx bx-share-alt text-2xl"></i>
            <span>Partager</span>
          </button>
        </div>
      </div>
      <div class="bg-orange-50 rounded-2xl shadow flex flex-col p-8 gap-4 h-full pub-comments-block relative min-h-[320px]">
        <div class="font-semibold text-orange-600 mb-2">Commentaires</div>
        <div class="flex flex-col gap-3 pub-comments flex-1">
          <div class="flex items-start gap-3">
            <img src="../assets/profile/profile3.jpg" class="w-10 h-10 rounded-full object-cover" alt="User">
            <div class="bg-white rounded-xl px-4 py-2 text-sm text-gray-800 shadow">Un conte magnifique !</div>
          </div>
        </div>
        <form class="flex items-center gap-2 mt-2 pub-comment-form absolute bottom-8 left-8 right-8">
          <input type="text" placeholder="Ajouter un commentaire..." class="flex-1 px-3 py-2 rounded-full bg-gray-50 border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
          <button type="submit" class="text-orange-600 font-bold">Envoyer</button>
        </form>
      </div>
    </div>
    <!-- Publication 7 -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch publication mb-8" data-id="7">
      <div class="bg-white rounded-2xl shadow-lg flex flex-col p-8 gap-4 relative h-full">
        <div class="flex items-center gap-4 mb-2">
          <img src="../assets/profile/profile7.jpg" alt="Auteur" class="w-16 h-16 rounded-2xl object-cover border-2 border-orange-200 shadow">
          <div>
            <div class="font-bold text-lg text-orange-600">Lucas D.</div>
            <div class="flex items-center gap-2">
              <button class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full text-xs font-semibold hover:bg-orange-200 transition">Suivre</button>
              <span class="text-xs text-gray-500 ml-2">480 followers</span>
            </div>
            <div class="text-xs text-gray-400 mt-1">6 œuvres</div>
          </div>
        </div>
        <div class="flex flex-row gap-4">
          <div class="flex-1">
            <div class="text-xl font-bold text-[#1b263b] mb-1">Le chasseur et la biche</div>
            <div class="text-gray-600 text-sm mb-2 line-clamp-4">Un conte sur la préservation de la nature et le respect des animaux. Découvrez l'histoire d'un chasseur qui change sa vision grâce à une biche. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
            <a href="#" class="text-orange-600 text-xs font-semibold hover:underline">Lire la suite</a>
          </div>
          <img src="../assets/Explorer/conte6.jpg" alt="Conte" class="w-32 h-32 object-cover rounded-xl border border-orange-100">
        </div>
        <div class="flex items-center gap-8 mt-4">
          <button class="pub-like-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition" data-liked="false">
            <i class="bx bx-heart text-2xl"></i>
            <span class="like-count">22</span>
          </button>
          <button class="pub-comment-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition">
            <i class="bx bx-message-rounded text-2xl"></i>
            <span class="comment-count">7</span>
          </button>
          <button class="pub-share-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition">
            <i class="bx bx-share-alt text-2xl"></i>
            <span>Partager</span>
          </button>
        </div>
      </div>
      <div class="bg-orange-50 rounded-2xl shadow flex flex-col p-8 gap-4 h-full pub-comments-block relative min-h-[320px]">
        <div class="font-semibold text-orange-600 mb-2">Commentaires</div>
        <div class="flex flex-col gap-3 pub-comments flex-1">
          <div class="flex items-start gap-3">
            <img src="../assets/profile/profile4.jpg" class="w-10 h-10 rounded-full object-cover" alt="User">
            <div class="bg-white rounded-xl px-4 py-2 text-sm text-gray-800 shadow">Un très beau conte.</div>
          </div>
        </div>
        <form class="flex items-center gap-2 mt-2 pub-comment-form absolute bottom-8 left-8 right-8">
          <input type="text" placeholder="Ajouter un commentaire..." class="flex-1 px-3 py-2 rounded-full bg-gray-50 border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
          <button type="submit" class="text-orange-600 font-bold">Envoyer</button>
        </form>
      </div>
    </div>
    <!-- Publication 8 -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch publication mb-8" data-id="8">
      <div class="bg-white rounded-2xl shadow-lg flex flex-col p-8 gap-4 relative h-full">
        <div class="flex items-center gap-4 mb-2">
          <img src="../assets/profile/profile8.jpg" alt="Auteur" class="w-16 h-16 rounded-2xl object-cover border-2 border-orange-200 shadow">
          <div>
            <div class="font-bold text-lg text-orange-600">Emma B.</div>
            <div class="flex items-center gap-2">
              <button class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full text-xs font-semibold hover:bg-orange-200 transition">Suivre</button>
              <span class="text-xs text-gray-500 ml-2">350 followers</span>
            </div>
            <div class="text-xs text-gray-400 mt-1">4 œuvres</div>
          </div>
        </div>
        <div class="flex flex-row gap-4">
          <div class="flex-1">
            <div class="text-xl font-bold text-[#1b263b] mb-1">La tortue et le héron</div>
            <div class="text-gray-600 text-sm mb-2 line-clamp-4">Un conte sur la persévérance et l'entraide. Découvrez comment une tortue et un héron surmontent les obstacles ensemble. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
            <a href="#" class="text-orange-600 text-xs font-semibold hover:underline">Lire la suite</a>
          </div>
          <img src="../assets/Explorer/conte7.jpg" alt="Conte" class="w-32 h-32 object-cover rounded-xl border border-orange-100">
        </div>
        <div class="flex items-center gap-8 mt-4">
          <button class="pub-like-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition" data-liked="false">
            <i class="bx bx-heart text-2xl"></i>
            <span class="like-count">17</span>
          </button>
          <button class="pub-comment-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition">
            <i class="bx bx-message-rounded text-2xl"></i>
            <span class="comment-count">4</span>
          </button>
          <button class="pub-share-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition">
            <i class="bx bx-share-alt text-2xl"></i>
            <span>Partager</span>
          </button>
        </div>
      </div>
      <div class="bg-orange-50 rounded-2xl shadow flex flex-col p-8 gap-4 h-full pub-comments-block relative min-h-[320px]">
        <div class="font-semibold text-orange-600 mb-2">Commentaires</div>
        <div class="flex flex-col gap-3 pub-comments flex-1">
          <div class="flex items-start gap-3">
            <img src="../assets/profile/profile5.jpg" class="w-10 h-10 rounded-full object-cover" alt="User">
            <div class="bg-white rounded-xl px-4 py-2 text-sm text-gray-800 shadow">Une belle morale.</div>
          </div>
        </div>
        <form class="flex items-center gap-2 mt-2 pub-comment-form absolute bottom-8 left-8 right-8">
          <input type="text" placeholder="Ajouter un commentaire..." class="flex-1 px-3 py-2 rounded-full bg-gray-50 border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
          <button type="submit" class="text-orange-600 font-bold">Envoyer</button>
        </form>
      </div>
    </div>
    <!-- Publication 9 -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch publication mb-8" data-id="9">
      <div class="bg-white rounded-2xl shadow-lg flex flex-col p-8 gap-4 relative h-full">
        <div class="flex items-center gap-4 mb-2">
          <img src="../assets/profile/profile9.jpg" alt="Auteur" class="w-16 h-16 rounded-2xl object-cover border-2 border-orange-200 shadow">
          <div>
            <div class="font-bold text-lg text-orange-600">Noah J.</div>
            <div class="flex items-center gap-2">
              <button class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full text-xs font-semibold hover:bg-orange-200 transition">Suivre</button>
              <span class="text-xs text-gray-500 ml-2">220 followers</span>
            </div>
            <div class="text-xs text-gray-400 mt-1">2 œuvres</div>
          </div>
        </div>
        <div class="flex flex-row gap-4">
          <div class="flex-1">
            <div class="text-xl font-bold text-[#1b263b] mb-1">Le loup et le chien</div>
            <div class="text-gray-600 text-sm mb-2 line-clamp-4">Un conte sur la loyauté et la trahison. Découvrez l'histoire d'un loup et d'un chien qui font face à des choix difficiles. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
            <a href="#" class="text-orange-600 text-xs font-semibold hover:underline">Lire la suite</a>
          </div>
          <img src="../assets/Explorer/conte8.jpg" alt="Conte" class="w-32 h-32 object-cover rounded-xl border border-orange-100">
        </div>
        <div class="flex items-center gap-8 mt-4">
          <button class="pub-like-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition" data-liked="false">
            <i class="bx bx-heart text-2xl"></i>
            <span class="like-count">9</span>
          </button>
          <button class="pub-comment-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition">
            <i class="bx bx-message-rounded text-2xl"></i>
            <span class="comment-count">3</span>
          </button>
          <button class="pub-share-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition">
            <i class="bx bx-share-alt text-2xl"></i>
            <span>Partager</span>
          </button>
        </div>
      </div>
      <div class="bg-orange-50 rounded-2xl shadow flex flex-col p-8 gap-4 h-full pub-comments-block relative min-h-[320px]">
        <div class="font-semibold text-orange-600 mb-2">Commentaires</div>
        <div class="flex flex-col gap-3 pub-comments flex-1">
          <div class="flex items-start gap-3">
            <img src="../assets/profile/profile6.jpg" class="w-10 h-10 rounded-full object-cover" alt="User">
            <div class="bg-white rounded-xl px-4 py-2 text-sm text-gray-800 shadow">Une histoire pleine d'enseignements.</div>
          </div>
        </div>
        <form class="flex items-center gap-2 mt-2 pub-comment-form absolute bottom-8 left-8 right-8">
          <input type="text" placeholder="Ajouter un commentaire..." class="flex-1 px-3 py-2 rounded-full bg-gray-50 border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
          <button type="submit" class="text-orange-600 font-bold">Envoyer</button>
        </form>
      </div>
    </div>
    <!-- Publication 10 (dernière, margin-bottom 16) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch publication mb-8" data-id="10">
      <div class="bg-white rounded-2xl shadow-lg flex flex-col p-8 gap-4 relative h-full">
        <div class="flex items-center gap-4 mb-2">
          <img src="../assets/profile/profile10.jpg" alt="Auteur" class="w-16 h-16 rounded-2xl object-cover border-2 border-orange-200 shadow">
          <div>
            <div class="font-bold text-lg text-orange-600">Chloé G.</div>
            <div class="flex items-center gap-2">
              <button class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full text-xs font-semibold hover:bg-orange-200 transition">Suivre</button>
              <span class="text-xs text-gray-500 ml-2">180 followers</span>
            </div>
            <div class="text-xs text-gray-400 mt-1">1 œuvre</div>
          </div>
        </div>
        <div class="flex flex-row gap-4">
          <div class="flex-1">
            <div class="text-xl font-bold text-[#1b263b] mb-1">Le voyageur et la mer</div>
            <div class="text-gray-600 text-sm mb-2 line-clamp-4">Un conte sur la découverte de soi et l'aventure. Suivez le voyage d'un homme à la recherche de sa véritable identité. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
            <a href="#" class="text-orange-600 text-xs font-semibold hover:underline">Lire la suite</a>
          </div>
          <img src="../assets/Explorer/conte9.jpg" alt="Conte" class="w-32 h-32 object-cover rounded-xl border border-orange-100">
        </div>
        <div class="flex items-center gap-8 mt-4">
          <button class="pub-like-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition" data-liked="false">
            <i class="bx bx-heart text-2xl"></i>
            <span class="like-count">5</span>
          </button>
          <button class="pub-comment-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition">
            <i class="bx bx-message-rounded text-2xl"></i>
            <span class="comment-count">1</span>
          </button>
          <button class="pub-share-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition">
            <i class="bx bx-share-alt text-2xl"></i>
            <span>Partager</span>
          </button>
        </div>
      </div>
      <div class="bg-orange-50 rounded-2xl shadow flex flex-col p-8 gap-4 h-full pub-comments-block relative min-h-[320px]">
        <div class="font-semibold text-orange-600 mb-2">Commentaires</div>
        <div class="flex flex-col gap-3 pub-comments flex-1">
          <div class="flex items-start gap-3">
            <img src="../assets/profile/profile7.jpg" class="w-10 h-10 rounded-full object-cover" alt="User">
            <div class="bg-white rounded-xl px-4 py-2 text-sm text-gray-800 shadow">Un récit inspirant.</div>
          </div>
        </div>
        <form class="flex items-center gap-2 mt-2 pub-comment-form absolute bottom-8 left-8 right-8">
          <input type="text" placeholder="Ajouter un commentaire..." class="flex-1 px-3 py-2 rounded-full bg-gray-50 border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
          <button type="submit" class="text-orange-600 font-bold">Envoyer</button>
        </form>
      </div>
    </div>
  </section>

  <!-- CAROUSEL SUGGESTIONS (auteurs à suivre, contes à lire, etc.) -->
  <section class="w-[85vw] max-w-7xl mx-auto mt-20 px-4">
    <h2 class="text-2xl md:text-3xl font-bold text-orange-600 mb-8">Suggestions d'auteurs à suivre</h2>
    <div class="flex gap-8 overflow-x-auto pb-4 snap-x snap-mandatory no-scrollbar carousel-suggestions">
      <div class="min-w-[260px] max-w-[260px] bg-white rounded-2xl shadow-lg flex flex-col items-center p-6 hover:shadow-xl transition snap-center">
        <img src="../assets/profile/profile.jpg" alt="Auteur" class="w-24 h-24 rounded-full object-cover border-2 border-orange-200 mb-3">
        <div class="font-bold text-lg text-orange-600 mb-1">Emmanuel N.</div>
        <div class="text-xs text-gray-500 mb-2">1.2k followers</div>
        <div class="text-gray-700 text-sm mb-2">Conteur, passionné de traditions orales.</div>
        <button class="px-4 py-2 bg-orange-500 text-white rounded-full font-semibold hover:bg-orange-600 transition text-sm">Suivre</button>
      </div>
      <div class="min-w-[260px] max-w-[260px] bg-white rounded-2xl shadow-lg flex flex-col items-center p-6 hover:shadow-xl transition snap-center">
        <img src="../assets/profile/profile2.jpg" alt="Auteur" class="w-24 h-24 rounded-full object-cover border-2 border-orange-200 mb-3">
        <div class="font-bold text-lg text-orange-600 mb-1">Sophie M.</div>
        <div class="text-xs text-gray-500 mb-2">2.1k followers</div>
        <div class="text-gray-700 text-sm mb-2">Collectrice de proverbes et récits populaires.</div>
        <button class="px-4 py-2 bg-orange-500 text-white rounded-full font-semibold hover:bg-orange-600 transition text-sm">Suivre</button>
      </div>
      <div class="min-w-[260px] max-w-[260px] bg-white rounded-2xl shadow-lg flex flex-col items-center p-6 hover:shadow-xl transition snap-center">
        <img src="../assets/profile/profile3.jpg" alt="Auteur" class="w-24 h-24 rounded-full object-cover border-2 border-orange-200 mb-3">
        <div class="font-bold text-lg text-orange-600 mb-1">Patrick K.</div>
        <div class="text-xs text-gray-500 mb-2">1.8k followers</div>
        <div class="text-gray-700 text-sm mb-2">Gardien des coutumes et traditions locales.</div>
        <button class="px-4 py-2 bg-orange-500 text-white rounded-full font-semibold hover:bg-orange-600 transition text-sm">Suivre</button>
      </div>
    </div>
  </section>

  <!-- Publications supplémentaires après suggestions -->
  <section class="w-[85vw] max-w-7xl mx-auto mt-16 px-2 flex flex-col gap-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch publication mb-8" data-id="11">
      <div class="bg-white rounded-2xl shadow-lg flex flex-col p-8 gap-4 relative h-full">
        <div class="flex items-center gap-4 mb-2">
          <img src="../assets/profile/profile4.jpg" alt="Auteur" class="w-16 h-16 rounded-2xl object-cover border-2 border-orange-200 shadow">
          <div>
            <div class="font-bold text-lg text-orange-600">Isabelle T.</div>
            <div class="flex items-center gap-2">
              <button class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full text-xs font-semibold hover:bg-orange-200 transition">Suivre</button>
              <span class="text-xs text-gray-500 ml-2">1.1k followers</span>
            </div>
            <div class="text-xs text-gray-400 mt-1">9 œuvres</div>
          </div>
        </div>
        <div class="flex flex-row gap-4">
          <div class="flex-1">
            <div class="text-xl font-bold text-[#1b263b] mb-1">Le baobab magique</div>
            <div class="text-gray-600 text-sm mb-2 line-clamp-4">Un conte sur la force de la nature et la magie des arbres sacrés. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
            <a href="#" class="text-orange-600 text-xs font-semibold hover:underline">Lire la suite</a>
          </div>
          <img src="../assets/Explorer/conte10.jpg" alt="Conte" class="w-32 h-32 object-cover rounded-xl border border-orange-100">
        </div>
        <div class="flex items-center gap-8 mt-4">
          <button class="pub-like-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition" data-liked="false">
            <i class="bx bx-heart text-2xl"></i>
            <span class="like-count">13</span>
          </button>
          <button class="pub-comment-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition">
            <i class="bx bx-message-rounded text-2xl"></i>
            <span class="comment-count">2</span>
          </button>
          <button class="pub-share-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition">
            <i class="bx bx-share-alt text-2xl"></i>
            <span>Partager</span>
          </button>
        </div>
      </div>
      <div class="bg-orange-50 rounded-2xl shadow flex flex-col p-8 gap-4 h-full pub-comments-block relative min-h-[320px]">
        <div class="font-semibold text-orange-600 mb-2">Commentaires</div>
        <div class="flex flex-col gap-3 pub-comments flex-1">
          <div class="flex items-start gap-3">
            <img src="../assets/profile/profile2.jpg" class="w-10 h-10 rounded-full object-cover" alt="User">
            <div class="bg-white rounded-xl px-4 py-2 text-sm text-gray-800 shadow">J'adore ce conte !</div>
          </div>
        </div>
        <form class="flex items-center gap-2 mt-2 pub-comment-form absolute bottom-8 left-8 right-8">
          <input type="text" placeholder="Ajouter un commentaire..." class="flex-1 px-3 py-2 rounded-full bg-gray-50 border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
          <button type="submit" class="text-orange-600 font-bold">Envoyer</button>
        </form>
      </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch publication mb-8" data-id="12">
      <div class="bg-white rounded-2xl shadow-lg flex flex-col p-8 gap-4 relative h-full">
        <div class="flex items-center gap-4 mb-2">
          <img src="../assets/profile/profile5.jpg" alt="Auteur" class="w-16 h-16 rounded-2xl object-cover border-2 border-orange-200 shadow">
          <div>
            <div class="font-bold text-lg text-orange-600">Marc L.</div>
            <div class="flex items-center gap-2">
              <button class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full text-xs font-semibold hover:bg-orange-200 transition">Suivre</button>
              <span class="text-xs text-gray-500 ml-2">1.4k followers</span>
            </div>
            <div class="text-xs text-gray-400 mt-1">11 œuvres</div>
          </div>
        </div>
        <div class="flex flex-row gap-4">
          <div class="flex-1">
            <div class="text-xl font-bold text-[#1b263b] mb-1">La rivière et le soleil</div>
            <div class="text-gray-600 text-sm mb-2 line-clamp-4">Un conte sur la persévérance et la lumière intérieure. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
            <a href="#" class="text-orange-600 text-xs font-semibold hover:underline">Lire la suite</a>
          </div>
          <img src="../assets/Explorer/conte11.jpg" alt="Conte" class="w-32 h-32 object-cover rounded-xl border border-orange-100">
        </div>
        <div class="flex items-center gap-8 mt-4">
          <button class="pub-like-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition" data-liked="false">
            <i class="bx bx-heart text-2xl"></i>
            <span class="like-count">7</span>
          </button>
          <button class="pub-comment-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition">
            <i class="bx bx-message-rounded text-2xl"></i>
            <span class="comment-count">1</span>
          </button>
          <button class="pub-share-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition">
            <i class="bx bx-share-alt text-2xl"></i>
            <span>Partager</span>
          </button>
        </div>
      </div>
      <div class="bg-orange-50 rounded-2xl shadow flex flex-col p-8 gap-4 h-full pub-comments-block relative min-h-[320px]">
        <div class="font-semibold text-orange-600 mb-2">Commentaires</div>
        <div class="flex flex-col gap-3 pub-comments flex-1">
          <div class="flex items-start gap-3">
            <img src="../assets/profile/profile3.jpg" class="w-10 h-10 rounded-full object-cover" alt="User">
            <div class="bg-white rounded-xl px-4 py-2 text-sm text-gray-800 shadow">Merci pour ce partage !</div>
          </div>
        </div>
        <form class="flex items-center gap-2 mt-2 pub-comment-form absolute bottom-8 left-8 right-8">
          <input type="text" placeholder="Ajouter un commentaire..." class="flex-1 px-3 py-2 rounded-full bg-gray-50 border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
          <button type="submit" class="text-orange-600 font-bold">Envoyer</button>
        </form>
      </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch publication mb-8" data-id="13">
      <div class="bg-white rounded-2xl shadow-lg flex flex-col p-8 gap-4 relative h-full">
        <div class="flex items-center gap-4 mb-2">
          <img src="../assets/profile/profile6.jpg" alt="Auteur" class="w-16 h-16 rounded-2xl object-cover border-2 border-orange-200 shadow">
          <div>
            <div class="font-bold text-lg text-orange-600">Claire R.</div>
            <div class="flex items-center gap-2">
              <button class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full text-xs font-semibold hover:bg-orange-200 transition">Suivre</button>
              <span class="text-xs text-gray-500 ml-2">1.7k followers</span>
            </div>
            <div class="text-xs text-gray-400 mt-1">14 œuvres</div>
          </div>
        </div>
        <div class="flex flex-row gap-4">
          <div class="flex-1">
            <div class="text-xl font-bold text-[#1b263b] mb-1">Le singe et la lune</div>
            <div class="text-gray-600 text-sm mb-2 line-clamp-4">Un conte sur la curiosité et l'aventure. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
            <a href="#" class="text-orange-600 text-xs font-semibold hover:underline">Lire la suite</a>
          </div>
          <img src="../assets/Explorer/conte12.jpg" alt="Conte" class="w-32 h-32 object-cover rounded-xl border border-orange-100">
        </div>
        <div class="flex items-center gap-8 mt-4">
          <button class="pub-like-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition" data-liked="false">
            <i class="bx bx-heart text-2xl"></i>
            <span class="like-count">11</span>
          </button>
          <button class="pub-comment-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition">
            <i class="bx bx-message-rounded text-2xl"></i>
            <span class="comment-count">2</span>
          </button>
          <button class="pub-share-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition">
            <i class="bx bx-share-alt text-2xl"></i>
            <span>Partager</span>
          </button>
        </div>
      </div>
      <div class="bg-orange-50 rounded-2xl shadow flex flex-col p-8 gap-4 h-full pub-comments-block relative min-h-[320px]">
        <div class="font-semibold text-orange-600 mb-2">Commentaires</div>
        <div class="flex flex-col gap-3 pub-comments flex-1">
          <div class="flex items-start gap-3">
            <img src="../assets/profile/profile4.jpg" class="w-10 h-10 rounded-full object-cover" alt="User">
            <div class="bg-white rounded-xl px-4 py-2 text-sm text-gray-800 shadow">Superbe !</div>
          </div>
        </div>
        <form class="flex items-center gap-2 mt-2 pub-comment-form absolute bottom-8 left-8 right-8">
          <input type="text" placeholder="Ajouter un commentaire..." class="flex-1 px-3 py-2 rounded-full bg-gray-50 border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
          <button type="submit" class="text-orange-600 font-bold">Envoyer</button>
        </form>
      </div>
    </div>
  </section>
  <script>
    // Carousel dynamique pour la bannière (même logique que sur index)
    const heroSlides = [
      {
        img: "../assets/Explorer/explorer-hero.jpg",
        title: "Découvrez les <span class='text-orange-400'>dernières publications</span>",
        desc: "Plongez dans les nouveaux contes, proverbes et récits partagés par la communauté."
      },
      {
        img: "../assets/Explorer/offer-conte.jpg",
        title: "Les <span class='text-orange-400'>contes populaires</span> à l'honneur",
        desc: "Des histoires inspirantes et éducatives pour petits et grands."
      },
      {
        img: "../assets/Explorer/offer-proverbe.jpg",
        title: "La <span class='text-orange-400'>sagesse africaine</span> à portée de main",
        desc: "Explorez les proverbes et dictons qui traversent les générations."
      }
    ];
    let heroCurrent = 0;
    function updateHeroBanner(idx) {
      const slide = heroSlides[idx];
      document.getElementById('explore-hero-bg').src = slide.img;
      document.getElementById('explore-hero-title').innerHTML = slide.title;
      document.getElementById('explore-hero-desc').textContent = slide.desc;
      document.querySelectorAll('#explore-hero-dots button').forEach((dot, i) => {
        dot.classList.toggle('bg-orange-400', i === idx);
        dot.classList.toggle('bg-white', i !== idx);
        dot.classList.toggle('opacity-80', i === idx);
        dot.classList.toggle('opacity-60', i !== idx);
      });
    }
    document.querySelectorAll('#explore-hero-dots button').forEach((dot, i) => {
      dot.addEventListener('click', () => {
        heroCurrent = i;
        updateHeroBanner(heroCurrent);
      });
    });
    setInterval(() => {
      heroCurrent = (heroCurrent + 1) % heroSlides.length;
      updateHeroBanner(heroCurrent);
    }, 6000);
  </script>
  <style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .line-clamp-4 {
      display: -webkit-box;
      -webkit-line-clamp: 4;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    .pub-comments-block {
      min-height: 320px;
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
    }
    .pub-comments {
      flex: 1 1 auto;
      overflow-y: auto;
      max-height: 320px;
      margin-bottom: 56px;
    }
    .pub-comment-form {
      position: absolute;
      left: 2rem;
      right: 2rem;
      bottom: 2rem;
      margin-bottom: 0 !important;
      background: transparent;
    }
    @media (max-width: 900px) {
      .w-\[85vw\] { width: 98vw !important; }
      .max-w-7xl { max-width: 100vw !important; }
    }
  </style>
</body>
</html>