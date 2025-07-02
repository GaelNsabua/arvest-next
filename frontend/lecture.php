<?php
session_start();
require_once '../backend/Database.php';
$pdo = Database::connect();

// Récupérer l'ID de l'œuvre depuis l'URL
$oeuvre_id = $_GET['id'] ?? 0;

// Récupérer les détails de l'œuvre
$oeuvre = [];
$medias = [];
$categories = [];
$is_liked = false;
$commentaires = [];
$suggestions = [];

try {
    // Détails de l'œuvre
    $stmt = $pdo->prepare("
        SELECT o.*, u.pseudo AS auteur, u.avatar_url, 
               COUNT(l.oeuvre_id) AS likes_count
        FROM oeuvres o
        JOIN utilisateurs u ON o.utilisateur_id = u.id
        LEFT JOIN likes l ON o.id = l.oeuvre_id
        WHERE o.id = ?
        GROUP BY o.id
    ");
    $stmt->execute([$oeuvre_id]);
    $oeuvre = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($oeuvre) {
        // Médias associés
        $stmt = $pdo->prepare("SELECT * FROM medias WHERE oeuvre_id = ?");
        $stmt->execute([$oeuvre_id]);
        $medias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Catégories
        $stmt = $pdo->prepare("
            SELECT c.nom 
            FROM categories c
            JOIN oeuvre_categorie oc ON c.id = oc.categorie_id
            WHERE oc.oeuvre_id = ?
        ");
        $stmt->execute([$oeuvre_id]);
        $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Vérifier si l'utilisateur a liké cette œuvre
        if (isset($_SESSION['user_id'])) {
            $stmt = $pdo->prepare("
                SELECT 1 FROM likes 
                WHERE utilisateur_id = ? AND oeuvre_id = ?
            ");
            $stmt->execute([$_SESSION['user_id'], $oeuvre_id]);
            $is_liked = (bool)$stmt->fetch();
        }

        // Commentaires
        $stmt = $pdo->prepare("
            SELECT c.*, u.pseudo, u.avatar_url
            FROM commentaires c
            JOIN utilisateurs u ON c.utilisateur_id = u.id
            WHERE c.oeuvre_id = ? AND c.statut = 'actif'
            ORDER BY c.date_commentaire DESC
        ");
        $stmt->execute([$oeuvre_id]);
        $commentaires = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Suggestions d'œuvres similaires
        $stmt = $pdo->prepare("
            SELECT o.id, o.titre, o.type
            FROM oeuvres o
            JOIN oeuvre_categorie oc ON o.id = oc.oeuvre_id
            WHERE oc.categorie_id IN (
                SELECT categorie_id 
                FROM oeuvre_categorie 
                WHERE oeuvre_id = ?
            ) AND o.id != ? AND o.statut = 'valide'
            GROUP BY o.id
            ORDER BY RAND()
            LIMIT 3
        ");
        $stmt->execute([$oeuvre_id, $oeuvre_id]);
        $suggestions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    error_log("Erreur lecture: " . $e->getMessage());
}

// Traitement du formulaire de commentaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commentaire'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: auth.php?redirect=lecture.php?id=" . $oeuvre_id);
        exit();
    }
    
    $contenu = trim($_POST['contenu']);
    if (!empty($contenu)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO commentaires 
                (utilisateur_id, oeuvre_id, contenu) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([
                $_SESSION['user_id'],
                $oeuvre_id,
                htmlspecialchars($contenu)
            ]);
            
            // Recharger la page pour afficher le nouveau commentaire
            header("Location: lecture.php?id=" . $oeuvre_id);
            exit();
        } catch (PDOException $e) {
            $comment_error = "Erreur lors de l'ajout du commentaire";
        }
    }
}

// Traitement du like
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like_action'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: auth.php?redirect=lecture.php?id=" . $oeuvre_id);
        exit();
    }
    
    $action = $_POST['like_action'];
    if ($action === 'like') {
        $stmt = $pdo->prepare("
            INSERT INTO likes (utilisateur_id, oeuvre_id) 
            VALUES (?, ?)
        ");
        $stmt->execute([$_SESSION['user_id'], $oeuvre_id]);
    } else {
        $stmt = $pdo->prepare("
            DELETE FROM likes 
            WHERE utilisateur_id = ? AND oeuvre_id = ?
        ");
        $stmt->execute([$_SESSION['user_id'], $oeuvre_id]);
    }
    
    // Recharger la page pour mettre à jour les likes
    header("Location: lecture.php?id=" . $oeuvre_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= $oeuvre ? htmlspecialchars($oeuvre['titre']) : 'Œuvre' ?> | Arvest Congo</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="./styles/output.css" />
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
    }
    
    .hero-section {
      background: linear-gradient(135deg, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.7) 100%);
    }
    
    .content-container {
      max-width: 800px;
      line-height: 1.8;
      font-size: 1.1rem;
      color: var(--gray-700);
    }
    
    .content-container p {
      margin-bottom: 1.5rem;
    }
    
    .media-container {
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .tag {
      transition: all 0.2s ease;
    }
    
    .tag:hover {
      transform: translateY(-2px);
    }
    
    .comment-card {
      border-left: 3px solid var(--orange-500);
      transition: all 0.3s ease;
    }
    
    .comment-card:hover {
      transform: translateX(5px);
    }
    
    .suggestion-card {
      transition: all 0.3s ease;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .suggestion-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .like-btn {
      transition: all 0.3s ease;
    }
    
    .like-btn:hover {
      transform: scale(1.1);
    }
    
    .like-btn.liked {
      color: var(--orange-500);
    }
    
    .avatar {
      transition: all 0.3s ease;
    }
    
    .avatar:hover {
      transform: scale(1.1);
    }
    
    .fade-in {
      animation: fadeIn 0.6s ease-out;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .prose :where(p):not(:where([class~="not-prose"] *)) {
      margin-bottom: 1.5rem;
      line-height: 1.8;
    }.main-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 2rem;
    }
    
    @media (min-width: 1024px) {
      .main-grid {
        grid-template-columns: 1fr 300px;
      }
    }
    
    .content-section {
      background: white;
      border-radius: 1.5rem;
      box-shadow: 0 10px 30px rgba(0,0,0,0.05);
      padding: 2rem;
      margin-bottom: 2rem;
    }
    
    .author-section {
      background: white;
      border-radius: 1.5rem;
      box-shadow: 0 10px 30px rgba(0,0,0,0.05);
      padding: 1.5rem;
      position: sticky;
      top: 4.5rem;
      height: fit-content;
    }
    
    .media-gallery {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 1rem;
      margin-bottom: 2rem;
    }
    
    .interaction-bar {
      display: flex;
      justify-content: space-between;
      padding: 1.5rem 0;
      border-top: 1px solid #eee;
      border-bottom: 1px solid #eee;
      margin: 2rem 0;
    }
    
    .comment-section {
      background: #f9fafb;
      border-radius: 1rem;
      padding: 1.5rem;
      margin-top: 2rem;
    }
    
    .suggestions-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 1.5rem;
      margin-top: 2rem;
    }
  </style>
</head>
<body class="bg-gray-100 font-poppins text-gray-800">
  <!-- NAVBAR -->
  <?php include './components/navbar.php'; ?>

  <!-- HERO -->
  <section class="relative w-full flex justify-center mt-24">
    <div class="hero-section w-full h-64 md:h-96 flex items-end justify-center pb-12">
      <div class="text-center relative z-10">
        <h1 class="text-3xl md:text-5xl font-bold text-white mb-4">
          <?= $oeuvre ? htmlspecialchars($oeuvre['titre']) : 'Œuvre non trouvée' ?>
        </h1>
        <div class="flex items-center justify-center gap-3 text-white">
          <span>Par <?= $oeuvre ? htmlspecialchars($oeuvre['auteur']) : 'Auteur inconnu' ?></span>
          <span>•</span>
          <span>
            <?= $oeuvre ? date('d M Y', strtotime($oeuvre['date_publication'])) : 'Date inconnue' ?>
          </span>
        </div>
      </div>
      <div class="absolute inset-0 z-0 ">
          <img src="https://res.cloudinary.com/dglb0uqr8/image/upload/v1751423997/wood-2045380_1280_my42ue.jpg" 
               alt="<?= htmlspecialchars($oeuvre['titre']) ?>" 
               class="w-full h-full object-cover opacity-70">
        </div>
    </div>
  </section>

  <main class="w-full max-w-7xl mx-auto px-4 py-12">
    <?php if (!$oeuvre): ?>
      <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
        <div class="w-24 h-24 rounded-full bg-orange-100 flex items-center justify-center mx-auto mb-6">
          <i class="bx bx-error text-4xl text-orange-500"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-4">Œuvre introuvable</h3>
        <p class="text-gray-600 max-w-xl mx-auto mb-6">
          L'œuvre que vous cherchez n'existe pas ou a été supprimée. 
          Vous pouvez explorer notre bibliothèque pour découvrir d'autres trésors culturels.
        </p>
        <a href="bibliotheque.php" class="inline-block px-8 py-3 bg-orange-500 text-white rounded-full font-semibold shadow hover:bg-orange-600 transition">
          Explorer la bibliothèque
        </a>
      </div>
    <?php else: ?>
      <div class="main-grid">
        <div>
          <!-- Métadonnées -->
          <div class="content-section">
            <div class="flex flex-wrap gap-3 mb-6">
              <?php foreach ($categories as $cat): ?>
                <span class="tag bg-orange-100 text-orange-600 px-3 py-1 rounded-full text-sm font-medium">
                  <?= htmlspecialchars($cat) ?>
                </span>
              <?php endforeach; ?>
              
              <span class="tag bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm font-medium flex items-center gap-1">
                <i class='bx bx-map text-sm'></i>
                <?= htmlspecialchars($oeuvre['region']) ?>
              </span>
              
              <span class="tag bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-sm font-medium flex items-center gap-1">
                <i class='bx bx-globe text-sm'></i>
                <?= htmlspecialchars($oeuvre['langue']) ?>
              </span>
              
              <span class="tag bg-purple-100 text-purple-600 px-3 py-1 rounded-full text-sm font-medium flex items-center gap-1">
                <i class='bx bx-category text-sm'></i>
                <?= htmlspecialchars(ucfirst($oeuvre['type'])) ?>
              </span>
            </div>
            
            <!-- Contenu textuel -->
            <div class="content-container prose prose-lg max-w-none">
              <?= nl2br(htmlspecialchars($oeuvre['contenu'])) ?>
            </div>
          </div>
          
          <!-- Galerie de médias -->
          <?php if (count($medias) > 0): ?>
            <div class="content-section">
              <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class='bx bx-images text-orange-500'></i>
                Galerie
              </h3>
              
              <div class="media-gallery">
                <?php foreach ($medias as $media): ?>
                  <div class="media-container overflow-hidden rounded-xl">
                    <?php if ($media['type'] === 'image'): ?>
                      <img src="<?= htmlspecialchars($media['url']) ?>" 
                          alt="<?= htmlspecialchars($media['description'] ?? $oeuvre['titre']) ?>" 
                          class="w-full h-40 object-cover">
                    <?php elseif ($media['type'] === 'audio'): ?>
                      <div class="bg-gray-800 p-4 h-40 flex items-center justify-center">
                        <audio controls class="w-full">
                          <source src="<?= htmlspecialchars($media['url']) ?>" type="audio/mpeg">
                          Votre navigateur ne supporte pas l'audio.
                        </audio>
                      </div>
                    <?php elseif ($media['type'] === 'video'): ?>
                      <video controls class="w-full h-40 object-cover">
                        <source src="<?= htmlspecialchars($media['url']) ?>" type="video/mp4">
                        Votre navigateur ne supporte pas la vidéo.
                      </video>
                    <?php endif; ?>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
          
          <!-- Interactions -->
          <div class="content-section">
            <div class="interaction-bar">
              <form method="POST" class="flex items-center gap-2">
                <input type="hidden" name="like_action" value="<?= $is_liked ? 'unlike' : 'like' ?>">
                <button type="submit" class="like-btn text-2xl <?= $is_liked ? 'liked bx bxs-heart text-orange-500' : 'bx bx-heart' ?>"></button>
                <span class="text-md font-medium"><?= $oeuvre['likes_count'] ?> J'aime</span>
              </form>
              
              <button class="flex items-center gap-2 text-red-600 text-md">
                <i class="bx bx-share-alt text-2xl"></i>
                <span>Partager</span>
              </button>
              
              <button class="flex items-center gap-2 text-gray-600 text-md">
                <i class="bx bx-bookmark text-2xl"></i>
                <span>Sauvegarder</span>
              </button>
            </div>
          </div>
          
          <!-- Commentaires -->
          <div class="content-section">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
              <i class='bx bx-conversation text-orange-500'></i>
              Commentaires
              <span class="bg-orange-100 text-orange-600 px-3 py-1 rounded-full text-sm">
                <?= count($commentaires) ?>
              </span>
            </h3>
            
            <!-- Formulaire de commentaire -->
            <div class="comment-section">
              <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST">
                  <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-200">
                      <?php if (!empty($_SESSION['avatar_url'])): ?>
                        <img src="<?= htmlspecialchars($_SESSION['avatar_url']) ?>" 
                            alt="<?= htmlspecialchars($_SESSION['pseudo']) ?>" 
                            class="w-full h-full object-cover">
                      <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center bg-orange-500 text-white">
                          <?= strtoupper(substr($_SESSION['pseudo'], 0, 1)) ?>
                        </div>
                      <?php endif; ?>
                    </div>
                    <div class="flex-1">
                      <textarea 
                        name="contenu" 
                        placeholder="Ajouter un commentaire..." 
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-400 min-h-[100px]"
                        required></textarea>
                      <div class="flex justify-end mt-2">
                        <button 
                          type="submit" 
                          name="commentaire"
                          class="px-6 py-2 bg-orange-500 text-white rounded-full font-medium hover:bg-orange-600 transition">
                          Publier
                        </button>
                      </div>
                    </div>
                  </div>
                </form>
              <?php else: ?>
                <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded">
                  <p class="text-orange-700">
                    <a href="auth.php?redirect=lecture.php?id=<?= $oeuvre_id ?>" class="text-orange-600 font-medium hover:underline">
                      Connectez-vous
                    </a> 
                    pour ajouter un commentaire.
                  </p>
                </div>
              <?php endif; ?>
            </div>
            
            <!-- Liste des commentaires -->
            <div class="mt-6 space-y-6">
              <?php foreach ($commentaires as $comment): ?>
                <div class="comment-card fade-in bg-white rounded-xl p-5 shadow">
                  <div class="flex items-start gap-4 mb-4">
                    <div class="avatar w-12 h-12 rounded-full overflow-hidden bg-gray-200">
                      <?php if (!empty($comment['avatar_url'])): ?>
                        <img src="<?= htmlspecialchars($comment['avatar_url']) ?>" 
                            alt="<?= htmlspecialchars($comment['pseudo']) ?>" 
                            class="w-full h-full object-cover">
                      <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center bg-orange-500 text-white">
                          <?= strtoupper(substr($comment['pseudo'], 0, 1)) ?>
                        </div>
                      <?php endif; ?>
                    </div>
                    <div>
                      <h4 class="font-bold text-gray-900"><?= htmlspecialchars($comment['pseudo']) ?></h4>
                      <span class="text-sm text-gray-500">
                        <?= date('d M Y à H:i', strtotime($comment['date_commentaire'])) ?>
                      </span>
                    </div>
                  </div>
                  <p class="text-gray-700"><?= nl2br($comment['contenu']) ?></p>
                </div>
              <?php endforeach; ?>
              
              <?php if (empty($commentaires)): ?>
                <div class="text-center py-8 bg-gray-50 rounded-xl">
                  <div class="w-16 h-16 rounded-full bg-orange-100 flex items-center justify-center mx-auto mb-4">
                    <i class="bx bx-comment-dots text-2xl text-orange-500"></i>
                  </div>
                  <p class="text-gray-600">Soyez le premier à commenter cette œuvre</p>
                </div>
              <?php endif; ?>
            </div>
          </div>
          
          <!-- Suggestions -->
          <div class="content-section">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
              <i class='bx bx-book-open text-orange-500'></i>
              Œuvres similaires
            </h3>
            
            <?php if (!empty($suggestions)): ?>
              <div class="suggestions-grid">
                <?php foreach ($suggestions as $suggestion): ?>
                  <a href="lecture.php?id=<?= $suggestion['id'] ?>" class="suggestion-card block bg-white rounded-xl p-5 shadow hover:shadow-lg transition">
                    <div class="flex items-center gap-4">
                      <div class="w-14 h-14 rounded-xl bg-orange-100 flex items-center justify-center">
                        <?php if ($suggestion['type'] === 'proverbe'): ?>
                          <i class='bx bxs-quote-alt-left text-2xl text-orange-500'></i>
                        <?php else: ?>
                          <i class='bx bx-book text-2xl text-orange-500'></i>
                        <?php endif; ?>
                      </div>
                      <div>
                        <h4 class="font-bold text-gray-900"><?= htmlspecialchars($suggestion['titre']) ?></h4>
                        <p class="text-sm text-gray-600"><?= htmlspecialchars(ucfirst($suggestion['type'])) ?></p>
                      </div>
                    </div>
                  </a>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <div class="bg-gray-50 rounded-xl p-6 text-center">
                <p class="text-gray-600 mb-4">Explorez notre bibliothèque pour plus d'œuvres</p>
                <a href="bibliotheque.php" class="inline-block px-6 py-2 bg-orange-500 text-white rounded-full font-medium hover:bg-orange-600 transition">
                  Voir la bibliothèque
                </a>
              </div>
            <?php endif; ?>
          </div>
          <div class="bg-white rounded-xl p-6 text-center shadow hover:shadow-lg">
          <div class="mt-6 pt-6 border-t border-gray-200">
              <h3 class="text-lg font-bold text-gray-900 mb-4">Partager votre histoire</h3>
              <p class="text-sm text-gray-600 mb-3">
                Vous avez une histoire, un conte ou un proverbe que vous souhaitez partager ?
              </p>
              <a href="publication.php" class="inline-block px-6 py-2 bg-orange-500 text-white rounded-full font-medium hover:bg-orange-600 transition">
                  Je veux partager une histoire
                </a>
            </div>
          </div>
        </div>
        
        <!-- Sidebar (Auteur + Actions) -->
        <div>
          <div class="author-section">
            <h3 class="text-xl font-bold text-gray-900 mb-4">À propos de l'auteur</h3>
            <div class="flex items-center gap-4 mb-6">
              <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-200">
                <?php if (!empty($oeuvre['avatar_url'])): ?>
                  <img src="<?= htmlspecialchars($oeuvre['avatar_url']) ?>" 
                      alt="<?= htmlspecialchars($oeuvre['auteur']) ?>" 
                      class="w-full h-full object-cover">
                <?php else: ?>
                  <div class="w-full h-full flex items-center justify-center bg-orange-500 text-white">
                    <?= strtoupper(substr($oeuvre['auteur'], 0, 1)) ?>
                  </div>
                <?php endif; ?>
              </div>
              <div>
                <h4 class="font-bold text-gray-900"><?= htmlspecialchars($oeuvre['auteur']) ?></h4>
                <a href="profil.php?id=<?= $oeuvre['utilisateur_id'] ?>" class="text-sm text-orange-600 hover:underline">
                  Voir le profil
                </a>
              </div>
            </div>
            
            <div class="mt-6 pt-6 border-t border-gray-200">
              <h3 class="text-lg font-bold text-gray-900 mb-4">Partager cette œuvre</h3>
              <div class="flex gap-3">
                <button class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                  <i class="bx bxl-facebook"></i>
                </button>
                <button class="w-10 h-10 rounded-full bg-blue-400 text-white flex items-center justify-center">
                  <i class="bx bxl-twitter"></i>
                </button>
                <button class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center">
                  <i class="bx bxl-pinterest"></i>
                </button>
                <button class="w-10 h-10 rounded-full bg-gray-800 text-white flex items-center justify-center">
                  <i class="bx bx-link"></i>
                </button>
              </div>
            </div>
            
            <div class="mt-6 pt-6 border-t border-gray-200">
              <h3 class="text-lg font-bold text-gray-900 mb-4">Signaler un problème</h3>
              <p class="text-sm text-gray-600 mb-3">
                Vous avez trouvé une erreur ou un contenu inapproprié ?
              </p>
              <button class="w-full text-center px-4 py-2 bg-gray-100 text-gray-700 rounded-full font-medium hover:bg-gray-200 transition text-sm">
                Signaler cette œuvre
              </button>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </main>

  <?php include './components/footer.php'; ?>

  <script>
    // Animation au défilement
    document.addEventListener('DOMContentLoaded', () => {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('fade-in');
          }
        });
      }, { threshold: 0.1 });
      
      document.querySelectorAll('.fade-in').forEach(el => {
        observer.observe(el);
      });
    });
  </script>
</body>
</html>