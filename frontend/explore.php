<?php
session_start();
require_once '../backend/Database.php';
$pdo = Database::connect();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
  header("Location: auth.php?redirect=explore.php");
  exit();
}

// Récupérer les publications (oeuvres validées) avec informations auteur
$queryPublications = "SELECT 
                        o.id, o.titre, o.contenu, o.date_publication, o.type,
                        u.id AS auteur_id, u.pseudo AS auteur, u.photo AS auteur_photo,
                        (SELECT COUNT(*) FROM likes l WHERE l.oeuvre_id = o.id) AS likes_count,
                        (SELECT COUNT(*) FROM commentaires c WHERE c.oeuvre_id = o.id) AS comment_count
                      FROM oeuvres o
                      JOIN utilisateurs u ON o.utilisateur_id = u.id
                      WHERE o.statut = 'valide'
                      ORDER BY o.date_publication DESC
                      LIMIT 10";

$publications = [];
try {
    $stmt = $pdo->prepare($queryPublications);
    $stmt->execute();
    $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur publications: " . $e->getMessage());
}

// Récupérer les commentaires pour chaque publication
foreach ($publications as &$pub) {
    $queryComments = "SELECT c.id, c.contenu, c.date_commentaire,
                             u.pseudo AS user_pseudo, u.photo AS user_photo
                      FROM commentaires c
                      JOIN utilisateurs u ON c.utilisateur_id = u.id
                      WHERE c.oeuvre_id = :oeuvre_id
                      ORDER BY c.date_commentaire DESC
                      LIMIT 3";
    
    try {
        $stmt = $pdo->prepare($queryComments);
        $stmt->bindParam(':oeuvre_id', $pub['id'], PDO::PARAM_INT);
        $stmt->execute();
        $pub['comments'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur commentaires: " . $e->getMessage());
        $pub['comments'] = [];
    }
}

// Récupérer les suggestions d'auteurs (les plus actifs)
$queryAuteurs = "SELECT u.id, u.pseudo, u.photo,
                        COUNT(o.id) AS oeuvres_count
                 FROM utilisateurs u
                 JOIN oeuvres o ON u.id = o.utilisateur_id
                 WHERE o.statut = 'valide'
                 GROUP BY u.id
                 ORDER BY oeuvres_count DESC
                 LIMIT 3";

$auteursSuggestions = [];
try {
    $stmt = $pdo->prepare($queryAuteurs);
    $stmt->execute();
    $auteursSuggestions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur auteurs: " . $e->getMessage());
}

// Récupérer les nouveautés pour le carousel
$queryNouveautes = "SELECT o.id, o.titre, o.type,
                           u.pseudo AS auteur
                    FROM oeuvres o
                    JOIN utilisateurs u ON o.utilisateur_id = u.id
                    WHERE o.statut = 'valide'
                    ORDER BY o.date_publication DESC
                    LIMIT 4";

$nouveautes = [];
try {
    $stmt = $pdo->prepare($queryNouveautes);
    $stmt->execute();
    $nouveautes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur nouveautés: " . $e->getMessage());
}
?>

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

  <!-- HERO BANNER NOUVEAUTÉS -->
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

  <!-- CAROUSEL NOUVEAUTÉS -->
  <section class="w-[85vw] max-w-7xl mx-auto mt-16 px-4">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-2xl md:text-3xl font-bold text-orange-600">Nouveautés</h2>
      <a href="bibliotheque.php" class="text-orange-600 font-semibold hover:underline">Tout voir</a>
    </div>
    <div class="flex gap-8 overflow-x-auto pb-4 snap-x snap-mandatory no-scrollbar carousel-nouveautes">
      <?php foreach ($nouveautes as $index => $nouveaute): 
        // Déterminer l'image en fonction du type
        $imageFolder = 'contes';
        $imageName = 'offer-conte.jpg';
        
        if ($nouveaute['type'] === 'proverbe') {
            $imageFolder = 'proverbes';
            $imageName = 'offer-proverbe.jpg';
        } else if ($nouveaute['type'] === 'recit') {
            $imageFolder = 'themes';
            $imageName = 'offer-theme.jpg';
        }
      ?>
      <div class="min-w-[340px] max-w-[340px] bg-white rounded-2xl shadow-lg flex flex-col overflow-hidden snap-center hover:shadow-xl transition">
        <img src="../assets/Explorer/<?= $imageName ?>" alt="Nouveauté" class="w-full h-48 object-cover">
        <div class="flex flex-col p-6 flex-1">
          <div class="font-bold text-xl text-[#1b263b] mb-1"><?= htmlspecialchars($nouveaute['titre']) ?></div>
          <div class="text-sm text-orange-600 mb-2">
            <?= htmlspecialchars(ucfirst($nouveaute['type'])) ?> • <?= htmlspecialchars($nouveaute['auteur']) ?>
          </div>
          <div class="text-gray-600 text-sm">Découvrez cette nouvelle œuvre partagée par notre communauté.</div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- PUBLICATIONS FEED -->
  <section id="feed" class="w-[85vw] max-w-7xl mx-auto mt-16 px-2 flex flex-col gap-8">
    <?php foreach ($publications as $index => $pub): 
      // Déterminer l'image en fonction du type
      $imageIndex = ($index % 9) + 1;
      $imageFolder = 'contes';
      $imageName = "conte$imageIndex.jpg";
      
      if ($pub['type'] === 'proverbe') {
          $imageFolder = 'proverbes';
          $imageName = "proverbe$imageIndex.jpg";
      } else if ($pub['type'] === 'recit') {
          $imageFolder = 'coutumes';
          $imageIndex = ($index % 3) + 1;
          $imageName = "coutume$imageIndex.jpg";
      }
    ?>
    <!-- Publication -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch publication mb-8" data-id="<?= $pub['id'] ?>">
      <!-- Bloc publication gauche -->
      <div class="bg-white rounded-2xl shadow-lg flex flex-col p-8 gap-4 relative h-full">
        <div class="flex items-center gap-4 mb-2">
          <img src="<?= htmlspecialchars($pub['auteur_photo'] ? '../uploads/avatars/'.$pub['auteur_photo'] : '../assets/profile/default.jpg') ?>" 
               alt="Auteur" class="w-16 h-16 rounded-2xl object-cover border-2 border-orange-200 shadow">
          <div>
            <div class="font-bold text-lg text-orange-600"><?= htmlspecialchars($pub['auteur']) ?></div>
            <div class="flex items-center gap-2">
              <button class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full text-xs font-semibold hover:bg-orange-200 transition">Suivre</button>
              <span class="text-xs text-gray-500 ml-2"><?= $pub['likes_count'] ?> likes</span>
            </div>
            <div class="text-xs text-gray-400 mt-1"><?= $pub['comment_count'] ?> commentaires</div>
          </div>
        </div>
        <div class="flex flex-row gap-4">
          <div class="flex-1">
            <div class="text-xl font-bold text-[#1b263b] mb-1"><?= htmlspecialchars($pub['titre']) ?></div>
            <div class="text-gray-600 text-sm mb-2 line-clamp-4">
              <?= htmlspecialchars(substr($pub['contenu'], 0, 200)) ?>...
            </div>
            <a href="lecture.php?id=<?= $pub['id'] ?>" class="text-orange-600 text-xs font-semibold hover:underline">Lire la suite</a>
          </div>
          <img src="../assets/Explorer/<?= $imageName ?>" alt="Publication" class="w-32 h-32 object-cover rounded-xl border border-orange-100">
        </div>
        <div class="flex items-center gap-8 mt-4">
          <button class="pub-like-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition" data-liked="false">
            <i class="bx bx-heart text-2xl"></i>
            <span class="like-count"><?= $pub['likes_count'] ?></span>
          </button>
          <button class="pub-comment-btn flex items-center gap-2 text-orange-500 font-semibold hover:scale-110 transition">
            <i class="bx bx-message-rounded text-2xl"></i>
            <span class="comment-count"><?= $pub['comment_count'] ?></span>
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
          <?php if (!empty($pub['comments'])): ?>
            <?php foreach ($pub['comments'] as $comment): ?>
              <div class="flex items-start gap-3">
                <img src="<?= htmlspecialchars($comment['user_photo'] ? '../uploads/avatars/'.$comment['user_photo'] : '../assets/profile/default.jpg') ?>" 
                     class="w-10 h-10 rounded-full object-cover" alt="User">
                <div class="bg-white rounded-xl px-4 py-2 text-sm text-gray-800 shadow">
                  <strong><?= htmlspecialchars($comment['user_pseudo']) ?>:</strong> 
                  <?= htmlspecialchars($comment['contenu']) ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="text-gray-500 text-sm">Aucun commentaire pour l'instant</p>
          <?php endif; ?>
        </div>
        <form class="flex items-center gap-2 mt-2 pub-comment-form absolute bottom-8 left-8 right-8" 
              action="../backend/add_comment.php" method="POST">
          <input type="hidden" name="oeuvre_id" value="<?= $pub['id'] ?>">
          <input type="text" name="contenu" placeholder="Ajouter un commentaire..." required
                 class="flex-1 px-3 py-2 rounded-full bg-gray-50 border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
          <button type="submit" class="text-orange-600 font-bold">Envoyer</button>
        </form>
      </div>
    </div>
    <?php endforeach; ?>
  </section>

  <!-- CAROUSEL SUGGESTIONS D'AUTEURS -->
  <section class="w-[85vw] max-w-7xl mx-auto mt-20 px-4">
    <h2 class="text-2xl md:text-3xl font-bold text-orange-600 mb-8">Suggestions d'auteurs à suivre</h2>
    <div class="flex gap-8 overflow-x-auto pb-4 snap-x snap-mandatory no-scrollbar carousel-suggestions">
      <?php foreach ($auteursSuggestions as $auteur): ?>
      <div class="min-w-[260px] max-w-[260px] bg-white rounded-2xl shadow-lg flex flex-col items-center p-6 hover:shadow-xl transition snap-center">
        <img src="<?= htmlspecialchars($auteur['photo'] ? '../uploads/avatars/'.$auteur['photo'] : '../assets/profile/default.jpg') ?>" 
             alt="Auteur" class="w-24 h-24 rounded-full object-cover border-2 border-orange-200 mb-3">
        <div class="font-bold text-lg text-orange-600 mb-1"><?= htmlspecialchars($auteur['pseudo']) ?></div>
        <div class="text-xs text-gray-500 mb-2"><?= $auteur['oeuvres_count'] ?> œuvres</div>
        <div class="text-gray-700 text-sm mb-2">Auteur passionné de notre communauté</div>
        <button class="px-4 py-2 bg-orange-500 text-white rounded-full font-semibold hover:bg-orange-600 transition text-sm">Suivre</button>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <script>
    // Carousel dynamique pour la bannière
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