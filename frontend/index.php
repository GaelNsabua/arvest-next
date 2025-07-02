<?php
session_start();
require_once '../backend/Database.php';
$pdo = Database::connect();

// Récupérer les contes populaires
$contes = [];
try {
    $stmt = $pdo->prepare("
        SELECT o.id, o.titre, o.contenu, u.pseudo AS auteur, COUNT(l.oeuvre_id) AS likes
        FROM oeuvres o
        JOIN utilisateurs u ON o.utilisateur_id = u.id
        LEFT JOIN likes l ON o.id = l.oeuvre_id
        WHERE o.type = 'conte' AND o.statut = 'valide'
        GROUP BY o.id
        ORDER BY likes DESC, o.date_publication DESC
        LIMIT 4
    ");
    $stmt->execute();
    $contes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur contes: " . $e->getMessage());
}

// Récupérer les proverbes
$proverbes = [];
try {
    $stmt = $pdo->prepare("
        SELECT o.titre, o.contenu, o.langue
        FROM oeuvres o
        WHERE o.type = 'proverbe' AND o.statut = 'valide'
        ORDER BY RAND()
        LIMIT 3
    ");
    $stmt->execute();
    $proverbes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur proverbes: " . $e->getMessage());
}

// Récupérer les auteurs populaires
$auteurs = [];
try {
    $stmt = $pdo->prepare("
        SELECT u.id, u.pseudo, u.bio, COUNT(o.id) AS oeuvres_count
        FROM utilisateurs u
        JOIN oeuvres o ON u.id = o.utilisateur_id
        WHERE u.role IN ('expert', 'utilisateur') AND o.statut = 'valide'
        GROUP BY u.id
        ORDER BY oeuvres_count DESC
        LIMIT 3
    ");
    $stmt->execute();
    $auteurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur auteurs: " . $e->getMessage());
}

// Récupérer les coutumes
$coutumes = [];
try {
    $stmt = $pdo->prepare("
        SELECT o.id, o.titre, o.contenu, o.region
        FROM oeuvres o
        JOIN oeuvre_categorie oc ON o.id = oc.oeuvre_id
        JOIN categories c ON oc.categorie_id = c.id
        WHERE c.nom = 'Coutumes' AND o.statut = 'valide'
        ORDER BY RAND()
        LIMIT 3
    ");
    $stmt->execute();
    $coutumes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur coutumes: " . $e->getMessage());
}

// Récupérer le nombre d'utilisateurs
$user_count = 0;
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM utilisateurs WHERE role != 'visiteur'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_count = $result['count'] ?? 0;
} catch (PDOException $e) {
    error_log("Erreur user count: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Arvest Congo - Accueil</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="./styles/output.css"/>
  <style>
    :root {
      --orange-500: #FF6B00;
      --orange-600: #E05E00;
      --orange-400: #FF8A3D;
      --orange-100: #FFF5E6;
      --gray-900: #1a202c;
      --gray-800: #2d3748;
      --gray-700: #4a5568;
      --gray-200: #edf2f7;
      --gray-100: #f7fafc;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: var(--gray-100);
      color: var(--gray-800);
      scroll-behavior: smooth;
    }

    .hero-section {
      background: linear-gradient(135deg, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.6) 100%);
    }

    .hero-overlay {
      background: linear-gradient(90deg, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.7) 50%, rgba(0,0,0,0.4) 100%);
    }

    .card {
      background: white;
      border-radius: 16px;
      overflow: hidden;
      transition: all 0.3s ease;
      box-shadow: 0 10px 25px rgba(0,0,0,0.05);
      border: 1px solid rgba(0,0,0,0.03);
    }

    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    }

    .card-img {
      height: 200px;
      width: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
    }

    .card:hover .card-img {
      transform: scale(1.05);
    }

    .category-btn {
      transition: all 0.2s ease;
      box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }

    .category-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 12px rgba(0,0,0,0.08);
    }

    .active-category {
      background: var(--orange-500);
      color: white;
    }

    .floating-icon {
      animation: float 4s ease-in-out infinite;
    }

    @keyframes float {
      0% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
      100% { transform: translateY(0px); }
    }

    .fade-in {
      animation: fadeIn 0.8s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .section-title {
      position: relative;
      display: inline-block;
      margin-bottom: 2rem;
    }

    .section-title::after {
      content: '';
      position: absolute;
      bottom: -8px;
      left: 0;
      width: 50%;
      height: 4px;
      background: var(--orange-500);
      border-radius: 2px;
    }

    .quote-card {
      background: linear-gradient(135deg,rgb(247, 245, 242) 0%,rgb(248, 242, 237) 100%);
      border-left: 4px solid var(--orange-500);
      border-right: 4px solid var(--orange-500);
    }

    .community-avatar {
      transition: all 0.3s ease;
      border: 2px solid var(--orange-400);
      box-shadow: 0 4px 10px rgba(255, 107, 0, 0.15);
    }

    .community-avatar:hover {
      transform: scale(1.1);
      z-index: 10;
    }

    .cta-section {
      background: linear-gradient(135deg, var(--orange-100) 0%, #ffefe0 100%);
      box-shadow: 0 10px 30px rgba(255, 107, 0, 0.1);
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--orange-500) 0%, var(--orange-600) 100%);
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(255, 107, 0, 0.3);
    }

    .btn-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 7px 20px rgba(255, 107, 0, 0.4);
    }

    .carousel-card {
      transition: all 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55);
    }
  </style>
</head>
<body class="bg-gray-100 font-poppins text-gray-800">
  <!-- NAVBAR -->
  <?php include './components/navbar.php'; ?>

  <main>
    <!-- HERO SECTION -->
    <section class="relative w-full min-h-screen flex items-center hero-section overflow-hidden">
      <div class="absolute inset-0 z-0">
        <img id="hero-bg" src="" alt="Bannière" class="w-full h-full object-cover object-center brightness-75 transition-all duration-1000" />
      </div>
      
      <div class="absolute inset-0 hero-overlay z-10"></div>

      <div class="relative z-20 w-full max-w-7xl mx-auto flex items-center h-full px-4 md:px-8 flex-col md:flex-row">
        <div class="flex flex-col justify-center h-full max-w-xl mr-12 md:items-start items-center text-center md:text-left
          md:py-0 py-16">
          <span id="hero-label" class="text-lg text-orange-400 font-semibold mb-3 tracking-wider"></span>
          <h1 id="hero-title" class="text-4xl md:text-6xl font-extrabold text-white mb-6 leading-tight drop-shadow-lg"></h1>
          <p id="hero-desc" class="text-xl text-gray-100 mb-8 max-w-md opacity-90"></p>
          <a href="#contes" class="btn-primary inline-flex items-center gap-3 px-8 py-4 text-white rounded-full font-semibold text-lg w-max">
            <i class="bx bx-book-open text-2xl"></i>
            Explorer notre patrimoine
          </a>
        </div>
        
        <div class="flex-1 flex flex-col items-center justify-center md:flex hidden mt-12 md:mt-0">
          <div id="carousel-container" class="flex items-end justify-center gap-0 relative h-[340px] w-full transition-all duration-700">
            <!-- Les cartes du carrousel seront générées dynamiquement -->
          </div>
          
          <div class="flex items-center justify-center gap-4 mt-8">
            <button id="prev-btn" class="carousel-prev w-12 h-12 rounded-full bg-white/90 hover:bg-orange-500 flex items-center justify-center shadow-lg transition">
              <i class="bx bx-chevron-left text-2xl text-gray-700 hover:text-white"></i>
            </button>
            <button id="next-btn" class="carousel-next w-12 h-12 rounded-full bg-white/90 hover:bg-orange-500 flex items-center justify-center shadow-lg transition">
              <i class="bx bx-chevron-right text-2xl text-gray-700 hover:text-white"></i>
            </button>
          </div>
        </div>
        
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white opacity-80 text-sm md:hidden">
          <i class="bx bx-down-arrow-alt animate-bounce"></i>
        </div>
      </div>
    </section>

    <!-- CATEGORIES/FILTRES -->
    <section class="max-w-4xl mx-auto mt-12 px-4">
      <h2 class="text-2xl font-bold text-center mb-2">Explorez par catégorie</h2>
      <p class="text-gray-600 text-center mb-8 max-w-xl mx-auto">Découvrez notre patrimoine à travers différentes thématiques</p>
      
      <div class="flex flex-wrap gap-4 justify-center">
        <button class="category-btn px-6 py-3 rounded-full bg-orange-500 text-white font-semibold shadow active-category">Tous</button>
        <button onclick="window.location.href='bibliotheque.php?type=conte'" class="category-btn px-6 py-3 rounded-full bg-white text-gray-700 font-semibold shadow hover:bg-orange-100 hover:text-orange-600 transition">Contes</button>
        <button onclick="window.location.href='bibliotheque.php?type=proverbe'" class="category-btn px-6 py-3 rounded-full bg-white text-gray-700 font-semibold shadow hover:bg-orange-100 hover:text-orange-600 transition">Proverbes</button>
        <button onclick="window.location.href='bibliotheque.php?type=couteume'" class="category-btn px-6 py-3 rounded-full bg-white text-gray-700 font-semibold shadow hover:bg-orange-100 hover:text-orange-600 transition">Coutumes</button>
        <button onclick="window.location.href='bibliotheque.php?type=sage'" class="category-btn px-6 py-3 rounded-full bg-white text-gray-700 font-semibold shadow hover:bg-orange-100 hover:text-orange-600 transition">Sagesse</button>
        <button onclick="window.location.href='bibliotheque.php?type=langue'" class="category-btn px-6 py-3 rounded-full bg-white text-gray-700 font-semibold shadow hover:bg-orange-100 hover:text-orange-600 transition">Langues</button>
      </div>
    </section>

    <!-- SECTION CONTES -->
    <section id="contes" class="max-w-6xl mx-auto mt-20 px-4">
      <div class="flex items-center justify-between mb-10">
        <h2 class="section-title text-3xl font-bold text-gray-900">Contes populaires</h2>
        <a href="bibliotheque.php?type=conte" class="text-orange-600 font-semibold hover:underline flex items-center gap-2">
          Voir tous les contes
          <i class='bx bx-chevron-right'></i>
        </a>
      </div>
      
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
        <?php foreach ($contes as $index => $conte): ?>
          <div class="card fade-in" style="animation-delay: <?= $index * 0.1 ?>s">
            <div class="overflow-hidden">
              <img src="https://res.cloudinary.com/dglb0uqr8/image/upload/v1751418628/Reading_glasses-cuate_dnpnkw.png" 
                   alt="<?= htmlspecialchars($conte['titre']) ?>" 
                   class="card-img">
            </div>
            <div class="p-6 flex flex-col gap-3">
              <h3 class="font-bold text-xl text-gray-900"><?= htmlspecialchars($conte['titre']) ?></h3>
              <p class="text-gray-600 text-sm line-clamp-2"><?= htmlspecialchars($conte['contenu']) ?></p>
              <div class="flex items-center justify-between mt-4">
                <span class="text-sm text-gray-700">Par <?= htmlspecialchars($conte['auteur']) ?></span>
                <div class="flex items-center gap-1 text-orange-400">
                  <i class="bx bxs-star"></i>
                  <span class="text-gray-700"><?= min(5, ceil($conte['likes'] / 2)) ?>.0</span>
                </div>
              </div>
              <a href="bibliotheque.php?type=conte" class="btn-primary inline-flex items-center justify-center gap-2 px-6 py-2 text-white rounded-full font-semibold text-lg">
                Découvrir
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- SECTION PROVERBES & SAGESSES -->
    <section id="proverbes" class="max-w-6xl mx-auto mt-20 px-4">
      <h2 class="section-title text-3xl font-bold text-gray-900 mb-12">Perles de sagesse</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($proverbes as $index => $proverbe): ?>
          <div class="quote-card rounded-2xl p-8 flex flex-col items-center text-center fade-in" style="animation-delay: <?= $index * 0.1 ?>s">
            <div class="w-16 h-16 rounded-full bg-orange-100 flex items-center justify-center mb-6 floating-icon">
              <i class="bx bxs-quote-alt-left text-3xl text-orange-500"></i>
            </div>
            <p class="text-xl italic text-gray-900 mb-4 font-thin"><?= htmlspecialchars($proverbe['contenu']) ?></p>
            <span class="text-sm text-orange-600 font-semibold">Proverbe <?= htmlspecialchars($proverbe['langue']) ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- SECTION COUTUMES -->
    <section id="coutumes" class="max-w-6xl mx-auto mt-20 px-4">
      <h2 class="section-title text-3xl font-bold text-gray-900 mb-12">Coutumes & traditions</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($coutumes as $index => $coutume): ?>
          <div class="card fade-in" style="animation-delay: <?= $index * 0.1 ?>s">
            <div class="h-56 w-full bg-gray-200 flex items-center justify-center overflow-hidden">
              <img src="https://ik.imagekit.io/melfuviii/Arvest/assets/index/coutume<?= ($index % 3) + 1 ?>.jpg" 
                   alt="<?= htmlspecialchars($coutume['titre']) ?>" 
                   class="w-full h-full object-cover">
            </div>
            <div class="p-6 flex flex-col flex-1">
              <h3 class="font-bold text-xl text-gray-900 mb-3"><?= htmlspecialchars($coutume['titre']) ?></h3>
              <p class="text-gray-600 text-sm mb-4 line-clamp-3"><?= htmlspecialchars($coutume['contenu']) ?></p>
              <span class="mt-auto text-sm text-orange-600 font-semibold">Région: <?= htmlspecialchars($coutume['region']) ?></span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- SECTION AUTEURS -->
    <section id="auteurs" class="max-w-6xl mx-auto mt-20 px-4">
      <h2 class="section-title text-3xl font-bold text-gray-900 mb-12">Nos gardiens du patrimoine</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <?php foreach ($auteurs as $index => $auteur): ?>
          <div class="card p-8 flex flex-col items-center text-center fade-in" style="animation-delay: <?= $index * 0.1 ?>s">
            <div class="w-24 h-24 rounded-full bg-gray-200 mb-6 overflow-hidden border-4 border-orange-100">
              <img src="https://res.cloudinary.com/dglb0uqr8/image/upload/v1751406677/generated-9115001_1280_bfd8z2.png" 
                   alt="<?= htmlspecialchars($auteur['pseudo']) ?>" 
                   class="w-full h-full object-cover">
            </div>
            <h3 class="font-bold text-xl text-gray-900 mb-1"><?= htmlspecialchars($auteur['pseudo']) ?></h3>
            <span class="text-sm text-orange-600 font-semibold mb-4"><?= $auteur['oeuvres_count'] ?> œuvres</span>
            <p class="text-gray-600 text-sm italic">"<?= htmlspecialchars($auteur['bio'] ?: 'Transmettre notre héritage aux générations futures') ?>"</p>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- SECTION COMMUNAUTÉ -->
    <section id="communaute" class="max-w-6xl mx-auto mt-20 px-4">
      <h2 class="section-title text-3xl font-bold text-gray-900 mb-12">Notre communauté</h2>
      <div class="bg-white rounded-2xl shadow-xl p-10 flex flex-col md:flex-row items-center gap-10 border border-orange-100">
        <div class="flex-1 flex flex-col gap-6">
          <h3 class="text-2xl font-bold text-orange-600 mb-2">Une communauté passionnée</h3>
          <p class="text-gray-700 text-lg mb-4">
            Rejoignez <span class="text-orange-600 font-bold"><?= $user_count ?> membres</span> qui préservent et célèbrent la richesse du patrimoine oral congolais.
          </p>
          <ul class="flex flex-col gap-3 text-gray-600">
            <li class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center">
                <i class="bx bx-book text-orange-500"></i>
              </div>
              Publiez vos propres récits et proverbes
            </li>
            <li class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center">
                <i class="bx bx-globe text-orange-500"></i>
              </div>
              Découvrez les traditions de toutes les régions
            </li>
            <li class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center">
                <i class="bx bx-chat text-orange-500"></i>
              </div>
              Échangez avec des passionnés et des experts
            </li>
            <li class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center">
                <i class="bx bx-calendar-event text-orange-500"></i>
              </div>
              Participez à des événements culturels
            </li>
          </ul>
        </div>
        <div class="flex-1 flex flex-row flex-wrap justify-center gap-4">
          <?php for ($i = 1; $i <= 12; $i++): ?>
            <div class="community-avatar w-16 h-16 rounded-full bg-gray-200 overflow-hidden">
              <img src="https://res.cloudinary.com/dglb0uqr8/image/upload/v1751415784/ai-generated-8208809_1280_hxkhxd.jpg" 
                   alt="Membre" 
                   class="w-full h-full object-cover">
            </div>
          <?php endfor; ?>
        </div>
      </div>
    </section>

    <!-- CALL TO ACTION -->
    <section class="py-16 mt-20 cta-section rounded-3xl max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between px-8 shadow-lg">
      <div class="text-center md:text-left mb-8 md:mb-0">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3">Prêt à contribuer à notre patrimoine ?</h2>
        <p class="text-gray-700 max-w-xl">Rejoignez notre communauté et partagez vos connaissances avec les générations futures</p>
      </div>
      <a href="auth.php" class="btn-primary px-10 py-4 text-white rounded-full font-semibold text-lg">
        Rejoindre Arvest
      </a>
    </section>

    <!-- FOOTER -->
    <?php include './components/footer.php'; ?>
  </main>

  <script>
    // Données du carrousel (adaptées de votre fichier original)
    const slides = [
      {
        img: "https://ik.imagekit.io/melfuviii/Arvest/assets/index/IMG-20240608-WA0032.jpg?updatedAt=1751245163105",
        thumb: "https://ik.imagekit.io/melfuviii/Arvest/assets/index/IMG-20240608-WA0032.jpg?updatedAt=1751245163105",
        title: "Découvrez la richesse<br><span class='text-orange-400'>des contes congolais</span>",
        mobileTitle: "Découvrez<br><span class='text-orange-400'>les contes congolais</span>",
        desc: "Plongez dans l'univers fascinant des histoires, proverbes et coutumes qui font la fierté de la culture congolaise.",
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
        title: "La <span class='text-orange-400'>culture congolaise</span> à l'honneur",
        mobileTitle: "Culture congolaise",
        desc: "Partagez et préservez les récits et savoirs transmis de génération en génération.",
        mobileDesc: "Préservez les récits et savoirs du Congo.",
        label: "Culture",
        sublabel: "Transmission"
      }
    ];

    let current = 0;
    let autoInterval = null;

    // Fonction principale de mise à jour du carrousel
    function updateHero() {
      // Desktop
      const heroBg = document.getElementById('hero-bg');
      const heroTitle = document.getElementById('hero-title');
      const heroDesc = document.getElementById('hero-desc');
      const heroLabel = document.getElementById('hero-label');
      
      // Carousel container
      const carouselContainer = document.getElementById('carousel-container');

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
      
      if (heroLabel) {
        heroLabel.style.transition = "opacity 0.5s";
        heroLabel.style.opacity = 0;
        setTimeout(() => {
          heroLabel.textContent = "CULTURE CONGOLAISE";
          heroLabel.style.opacity = 1;
        }, 250);
      }

      // Carousel desktop avec transition fluide
      if (carouselContainer) {
        carouselContainer.style.transition = "opacity 0.5s";
        carouselContainer.style.opacity = 0;
        setTimeout(() => {
          carouselContainer.innerHTML = slides.map((slide, i) => {
            let scale = i === current ? 'scale-110 z-30 ring-4 ring-orange-500 shadow-2xl' :
                        i === current - 1 ? 'scale-95 z-20 shadow-lg -ml-8' :
                        i === current + 1 ? 'scale-95 z-20 shadow-lg -ml-8' :
                        'scale-90 z-10 shadow -ml-8';
            let h = i === current ? 'h-[320px] w-[220px]' :
                    i === current - 1 || i === current + 1 ? 'h-[260px] w-[170px]' :
                    'h-[180px] w-[110px]';
                    
            return `
              <div class="relative bg-white rounded-3xl overflow-hidden ${h} ${scale} transition-all duration-700 cursor-pointer">
                <img src="${slide.thumb}" alt="${slide.label}" class="w-full h-full object-cover" />
                <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-black/80 via-black/30 to-transparent px-5 py-4">
                  <h3 class="text-white font-bold text-xl mb-1">${slide.label}</h3>
                  <span class="text-sm text-orange-200">${slide.sublabel}</span>
                </div>
              </div>
            `;
          }).join('');
          carouselContainer.style.opacity = 1;
          
          // Ajouter les événements de clic sur les cartes
          const cards = carouselContainer.querySelectorAll('.relative');
          cards.forEach((card, index) => {
            card.addEventListener('click', () => {
              goTo(index);
            });
          });
        }, 250);
      }
    }

    // Navigation dans le carrousel
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

    // Gestion du défilement automatique
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

    // Initialisation au chargement de la page
    document.addEventListener('DOMContentLoaded', () => {
      updateHero();
      startAutoCarousel();

      // Boutons de navigation
      document.getElementById('prev-btn').addEventListener('click', () => {
        prev();
      });

      document.getElementById('next-btn').addEventListener('click', () => {
        next();
      });

      // Swipe mobile
      let startX = null;
      const heroSection = document.querySelector('.hero-section');
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

      // Animation au défilement pour les autres éléments
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('fade-in');
          }
        });
      }, { threshold: 0.1 });
      
      document.querySelectorAll('.card, .quote-card').forEach(card => {
        observer.observe(card);
      });
    });
  </script>
</body>
</html>