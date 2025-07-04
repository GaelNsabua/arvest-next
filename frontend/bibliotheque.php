<?php
session_start();
require_once '../backend/Database.php';
$pdo = Database::connect();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
  header("Location: auth.php?redirect=bibliotheque.php");
  exit();
}

// Récupérer les filtres
$type = $_GET['type'] ?? 'conte';
$search = $_GET['search'] ?? '';
$categorie = $_GET['categorie'] ?? '';
$region = $_GET['region'] ?? '';
$langue = $_GET['langue'] ?? '';

// Construire la requête en fonction des filtres
$query = "SELECT 
            o.id, o.titre, o.contenu, o.type, o.langue, o.region, 
            u.pseudo AS auteur, 
            COUNT(l.oeuvre_id) AS likes,
            GROUP_CONCAT(DISTINCT c.nom SEPARATOR ', ') AS categories
          FROM oeuvres o
          JOIN utilisateurs u ON o.utilisateur_id = u.id
          LEFT JOIN likes l ON o.id = l.oeuvre_id
          LEFT JOIN oeuvre_categorie oc ON o.id = oc.oeuvre_id
          LEFT JOIN categories c ON oc.categorie_id = c.id
          WHERE o.statut = 'valide'";

// Appliquer les filtres
$params = [];
if (!empty($type)) {
    $query .= " AND o.type = ?";
    $params[] = $type;
}

if (!empty($search)) {
    $query .= " AND (o.titre LIKE ? OR o.contenu LIKE ? OR u.pseudo LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($categorie)) {
    $query .= " AND c.nom = ?";
    $params[] = $categorie;
}

if (!empty($region)) {
    $query .= " AND o.region = ?";
    $params[] = $region;
}

if (!empty($langue)) {
    $query .= " AND o.langue = ?";
    $params[] = $langue;
}

$query .= " GROUP BY o.id
            ORDER BY o.date_publication DESC";

// Exécuter la requête
$oeuvres = [];
try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $oeuvres = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur bibliothèque: " . $e->getMessage());
}

// Récupérer les catégories, régions et langues pour les filtres
$categories = [];
$regions = [];
$langues = [];

try {
    // Catégories
    $stmt = $pdo->prepare("SELECT DISTINCT nom FROM categories WHERE type = 'theme'");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Régions
    $stmt = $pdo->prepare("SELECT DISTINCT region FROM oeuvres WHERE region IS NOT NULL AND region != ''");
    $stmt->execute();
    $regions = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Langues
    $stmt = $pdo->prepare("SELECT DISTINCT langue FROM oeuvres WHERE langue IS NOT NULL AND langue != ''");
    $stmt->execute();
    $langues = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    error_log("Erreur filtres: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Bibliothèque | Arvest Congo</title>
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
      background: linear-gradient(135deg, var(--orange-500) 0%, var(--orange-600) 100%);
      box-shadow: 0 10px 30px rgba(255, 107, 0, 0.2);
      border-radius: 24px;
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
    
    .tab-button {
      transition: all 0.3s ease;
      box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    
    .tab-button:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 12px rgba(0,0,0,0.08);
    }
    
    .active-tab {
      background: var(--orange-500);
      color: white;
    }
    
    .filter-input {
      transition: all 0.3s ease;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    
    .filter-input:focus {
      border-color: var(--orange-500);
      box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.15);
    }
    
    .tag {
      transition: all 0.2s ease;
    }
    
    .tag:hover {
      transform: translateY(-2px);
    }
    
    .fade-in {
      animation: fadeIn 0.6s ease-out;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .quote-icon {
      animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); }
    }
    
    .active-filter {
      background: var(--orange-500);
      color: white;
      border-color: var(--orange-500);
    }
  </style>
</head>
<body class="bg-gray-100 font-poppins text-gray-800">
  <!-- NAVBAR -->
  <?php include './components/navbar.php'; ?>

  <!-- HERO -->
  <section class="w-full flex justify-center mt-24 px-4">
    <div class="hero-section w-full max-w-6xl rounded-3xl overflow-hidden flex flex-col items-center py-12 px-6">
      <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-4 text-center">Bibliothèque du Patrimoine Congolais</h1>
      <p class="text-lg text-orange-100 mb-6 text-center max-w-2xl">
        Explorez la richesse des contes, proverbes et coutumes transmis de génération en génération
      </p>
      <div class="flex flex-wrap gap-4 justify-center">
        <a href="?type=conte" class="tab-button px-6 py-3 rounded-full <?= $type === 'conte' ? 'active-tab bg-white text-orange-600' : 'bg-orange-100 text-orange-600' ?> font-semibold shadow transition">
          <i class="bx bx-book-open mr-2"></i>Contes
        </a>
        <a href="?type=proverbe" class="tab-button px-6 py-3 rounded-full <?= $type === 'proverbe' ? 'active-tab bg-white text-orange-600' : 'bg-orange-100 text-orange-600' ?> font-semibold shadow transition">
          <i class="bx bx-chat mr-2"></i>Proverbes
        </a>
        <a href="?type=recit" class="tab-button px-6 py-3 rounded-full <?= $type === 'recit' ? 'active-tab bg-white text-orange-600' : 'bg-orange-100 text-orange-600' ?> font-semibold shadow transition">
          <i class="bx bx-palette mr-2"></i>Coutumes & Traditions
        </a>
      </div>
    </div>
  </section>

  <!-- FILTRES -->
  <section class="w-full max-w-7xl mx-auto mt-10 px-4">
    <form method="GET" class="bg-white rounded-2xl shadow-lg p-6 mb-8">
      <div class="flex flex-wrap gap-4 items-center">
        <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
        
        <div class="relative flex-1 min-w-[250px]">
          <i class='bx bx-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400'></i>
          <input 
            type="text" 
            name="search" 
            value="<?= htmlspecialchars($search) ?>"
            placeholder="Rechercher un titre, un auteur..." 
            class="filter-input w-full pl-12 pr-4 py-3 rounded-full border border-gray-200 focus:outline-none text-sm"
          />
        </div>
        
        <select name="categorie" class="filter-input px-4 py-3 rounded-full border border-gray-200 bg-white text-sm">
          <option value="">Toutes les catégories</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= htmlspecialchars($cat) ?>" <?= $categorie === $cat ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat) ?>
            </option>
          <?php endforeach; ?>
        </select>
        
        <select name="region" class="filter-input px-4 py-3 rounded-full border border-gray-200 bg-white text-sm">
          <option value="">Toutes les régions</option>
          <?php foreach ($regions as $reg): ?>
            <option value="<?= htmlspecialchars($reg) ?>" <?= $region === $reg ? 'selected' : '' ?>>
              <?= htmlspecialchars($reg) ?>
            </option>
          <?php endforeach; ?>
        </select>
        
        <select name="langue" class="filter-input px-4 py-3 rounded-full border border-gray-200 bg-white text-sm">
          <option value="">Toutes les langues</option>
          <?php foreach ($langues as $lang): ?>
            <option value="<?= htmlspecialchars($lang) ?>" <?= $langue === $lang ? 'selected' : '' ?>>
              <?= htmlspecialchars($lang) ?>
            </option>
          <?php endforeach; ?>
        </select>
        
        <button type="submit" class="px-6 py-3 bg-orange-500 text-white rounded-full font-semibold shadow hover:bg-orange-600 transition text-sm">
          Appliquer
        </button>
        <a href="bibliotheque.php?type=<?= $type ?>" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-full font-semibold shadow hover:bg-gray-300 transition text-sm">
          Réinitialiser
        </a>
      </div>
      
      <!-- Filtres actifs -->
      <?php if (!empty($search) || !empty($categorie) || !empty($region) || !empty($langue)): ?>
        <div class="mt-6 flex flex-wrap gap-2">
          <span class="text-gray-700 font-medium">Filtres :</span>
          <?php if (!empty($search)): ?>
            <span class="bg-orange-100 text-orange-600 px-3 py-1 rounded-full text-sm flex items-center">
              "<?= htmlspecialchars($search) ?>"
              <a href="?<?= http_build_query(array_merge($_GET, ['search' => ''])) ?>" class="ml-2">
                <i class='bx bx-x text-lg'></i>
              </a>
            </span>
          <?php endif; ?>
          <?php if (!empty($categorie)): ?>
            <span class="bg-orange-100 text-orange-600 px-3 py-1 rounded-full text-sm flex items-center">
              Catégorie: <?= htmlspecialchars($categorie) ?>
              <a href="?<?= http_build_query(array_merge($_GET, ['categorie' => ''])) ?>" class="ml-2">
                <i class='bx bx-x text-lg'></i>
              </a>
            </span>
          <?php endif; ?>
          <?php if (!empty($region)): ?>
            <span class="bg-orange-100 text-orange-600 px-3 py-1 rounded-full text-sm flex items-center">
              Région: <?= htmlspecialchars($region) ?>
              <a href="?<?= http_build_query(array_merge($_GET, ['region' => ''])) ?>" class="ml-2">
                <i class='bx bx-x text-lg'></i>
              </a>
            </span>
          <?php endif; ?>
          <?php if (!empty($langue)): ?>
            <span class="bg-orange-100 text-orange-600 px-3 py-1 rounded-full text-sm flex items-center">
              Langue: <?= htmlspecialchars($langue) ?>
              <a href="?<?= http_build_query(array_merge($_GET, ['langue' => ''])) ?>" class="ml-2">
                <i class='bx bx-x text-lg'></i>
              </a>
            </span>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </form>
  </section>

  <!-- CONTENU -->
  <section class="w-full max-w-7xl mx-auto px-4 pb-16">
    <?php if (empty($oeuvres)): ?>
      <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
        <div class="w-24 h-24 rounded-full bg-orange-100 flex items-center justify-center mx-auto mb-6">
          <i class="bx bx-book-open text-4xl text-orange-500"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-4">Aucune œuvre trouvée</h3>
        <p class="text-gray-600 max-w-xl mx-auto mb-6">
          Aucune œuvre ne correspond à vos critères de recherche. Essayez de modifier vos filtres ou explorez d'autres catégories.
        </p>
        <a href="bibliotheque.php" class="inline-block px-8 py-3 bg-orange-500 text-white rounded-full font-semibold shadow hover:bg-orange-600 transition">
          Réinitialiser les filtres
        </a>
      </div>
    <?php else: ?>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
        <?php foreach ($oeuvres as $index => $oeuvre): 
          // Déterminer l'image en fonction du type
          $imageIndex = ($index % 6) + 1;
          $imageFolder = $oeuvre['type'] === 'proverbe' ? 'proverbes' : 'contes';
          
          // Pour les coutumes (récits)
          if ($oeuvre['type'] === 'recit') {
            $imageFolder = 'coutumes';
            $imageIndex = ($index % 3) + 1;
          }
        ?>
          <div class="card fade-in" style="animation-delay: <?= $index * 0.05 ?>s">
            <?php if ($oeuvre['type'] !== 'proverbe'): ?>
              <div class="overflow-hidden">
                     <img src="https://res.cloudinary.com/dglb0uqr8/image/upload/v1751414215/Recipe_book-pana_v6gwvp.png" 
                     alt="<?= htmlspecialchars($oeuvre['titre']) ?>" 
                     class="card-img">
              </div>
            <?php endif; ?>
            
            <div class="p-6 flex flex-col gap-3">
              <?php if ($oeuvre['type'] === 'proverbe'): ?>
                <div class="w-16 h-16 rounded-full bg-orange-100 flex items-center justify-center mb-4 quote-icon">
                  <i class="bx bxs-quote-alt-left text-3xl text-orange-500"></i>
                </div>
              <?php endif; ?>
              
              <h3 class="font-bold text-lg text-gray-900">
                <?= htmlspecialchars($oeuvre['titre']) ?>
              </h3>
              
              <p class="text-gray-600 text-sm line-clamp-3">
                <?= htmlspecialchars($oeuvre['contenu']) ?>
              </p>
              
              <div class="flex items-center justify-between mt-4">
                <span class="text-sm text-gray-700">Par <?= htmlspecialchars($oeuvre['auteur']) ?></span>
                <div class="flex items-center gap-1 text-orange-400">
                  <i class="bx bxs-star"></i>
                  <span class="text-gray-700"><?= min(5, ceil($oeuvre['likes'] / 2)) ?>.0</span>
                </div>
              </div>
              
              <?php if (!empty($oeuvre['categories']) || !empty($oeuvre['region']) || !empty($oeuvre['langue'])): ?>
                <div class="flex flex-wrap gap-2 mt-4">
                  <?php 
                    $categories = explode(', ', $oeuvre['categories']);
                    foreach ($categories as $cat): 
                  ?>
                    <span class="tag bg-orange-100 text-orange-600 px-2 py-1 rounded-full text-xs">
                      <?= htmlspecialchars($cat) ?>
                    </span>
                  <?php endforeach; ?>
                  
                  <?php if (!empty($oeuvre['region'])): ?>
                    <span class="tag bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs">
                      <?= htmlspecialchars($oeuvre['region']) ?>
                    </span>
                  <?php endif; ?>
                  
                  <?php if (!empty($oeuvre['langue'])): ?>
                    <span class="tag bg-blue-100 text-blue-600 px-2 py-1 rounded-full text-xs">
                      <?= htmlspecialchars($oeuvre['langue']) ?>
                    </span>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
              
              <a href="lecture.php?id=<?= $oeuvre['id'] ?>" class="mt-4 inline-block text-center px-4 py-2 bg-orange-500 text-white rounded-full text-sm font-medium hover:bg-orange-600 transition">
                Lire la suite
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

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
      
      document.querySelectorAll('.card').forEach(card => {
        observer.observe(card);
      });
    });
  </script>
</body>
</html>