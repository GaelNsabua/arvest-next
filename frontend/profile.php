<?php
session_start();
require_once '../backend/Database.php'; // Fichier contenant la classe Database

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user = [];
$stats = [
    'oeuvres' => 0,
    'likes' => 0,
    'abonnes' => 0,
    'abonnements' => 0
];
$oeuvres = [];
$commentaires = [];
$abonnes = [];
$abonnements = [];

try {
    $pdo = Database::connect();
    
    // Récupérer les informations de l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Récupérer les statistiques
    // Nombre d'oeuvres
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM oeuvres WHERE utilisateur_id = ?");
    $stmt->execute([$user_id]);
    $stats['oeuvres'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Nombre de likes totaux
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM likes WHERE utilisateur_id = ?");
    $stmt->execute([$user_id]);
    $stats['likes'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Nombre d'abonnés
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM abonnements WHERE auteur_id = ?");
    $stmt->execute([$user_id]);
    $stats['abonnes'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Nombre d'abonnements
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM abonnements WHERE abonne_id = ?");
    $stmt->execute([$user_id]);
    $stats['abonnements'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Récupérer les dernières oeuvres
    $stmt = $pdo->prepare("SELECT * FROM oeuvres WHERE utilisateur_id = ? ORDER BY date_publication DESC LIMIT 5");
    $stmt->execute([$user_id]);
    $oeuvres = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les derniers commentaires
    $stmt = $pdo->prepare("
        SELECT c.*, o.titre as oeuvre_titre 
        FROM commentaires c
        JOIN oeuvres o ON o.id = c.oeuvre_id
        WHERE c.utilisateur_id = ?
        ORDER BY c.date_commentaire DESC 
        LIMIT 5
    ");
    $stmt->execute([$user_id]);
    $commentaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les derniers abonnés
    $stmt = $pdo->prepare("
        SELECT u.id, u.pseudo, u.avatar_url, a.date_abonnement 
        FROM abonnements a
        JOIN utilisateurs u ON u.id = a.abonne_id
        WHERE a.auteur_id = ?
        ORDER BY a.date_abonnement DESC 
        LIMIT 5
    ");
    $stmt->execute([$user_id]);
    $abonnes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les derniers abonnements
    $stmt = $pdo->prepare("
        SELECT u.id, u.pseudo, u.avatar_url, a.date_abonnement 
        FROM abonnements a
        JOIN utilisateurs u ON u.id = a.auteur_id
        WHERE a.abonne_id = ?
        ORDER BY a.date_abonnement DESC 
        LIMIT 5
    ");
    $stmt->execute([$user_id]);
    $abonnements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error = "Erreur de base de données : " . $e->getMessage();
}

// Traitement de la mise à jour du profil
$update_success = false;
$update_errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $pseudo = htmlspecialchars(trim($_POST['pseudo']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $bio = htmlspecialchars(trim($_POST['bio']));
    
    // Validation
    if (empty($pseudo)) $update_errors[] = "Le pseudo est requis";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $update_errors[] = "Email invalide";
    
    if (empty($update_errors)) {
        try {
            $pdo = Database::connect();
            
            // Vérifier si le pseudo ou email est déjà utilisé
            $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE (email = ? OR pseudo = ?) AND id != ?");
            $stmt->execute([$email, $pseudo, $user_id]);
            
            if ($stmt->rowCount() > 0) {
                $update_errors[] = "L'email ou le pseudo est déjà utilisé par un autre compte";
            } else {
                // Mise à jour des informations
                $stmt = $pdo->prepare("UPDATE utilisateurs SET pseudo = ?, email = ?, bio = ? WHERE id = ?");
                $stmt->execute([$pseudo, $email, $bio, $user_id]);
                
                // Mettre à jour la session
                $_SESSION['pseudo'] = $pseudo;
                $user['pseudo'] = $pseudo;
                $user['email'] = $email;
                $user['bio'] = $bio;
                
                $update_success = true;
            }
        } catch (PDOException $e) {
            $update_errors[] = "Erreur de mise à jour : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Profil - Arvest</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    :root {
      --orange-primary: #FF6B00;
      --orange-dark: #E05E00;
      --orange-light: #FF8A3D;
      --orange-accent: #FFD166;
      --gray-bg: #f5f7fa;
      --gray-card: #ffffff;
      --gray-border: #e2e8f0;
      --gray-text: #4a5568;
    }
    
    body {
      font-family: 'Poppins', sans-serif;
      background-color: var(--gray-bg);
      background-image: 
        radial-gradient(circle at 10% 20%, rgba(255, 214, 171, 0.1) 0%, transparent 20%),
        radial-gradient(circle at 90% 80%, rgba(255, 214, 171, 0.1) 0%, transparent 20%);
      background-attachment: fixed;
    }
    
    .profile-card {
      background: var(--gray-card);
      border-radius: 20px;
      box-shadow: 0 15px 50px rgba(0, 0, 0, 0.08);
      border: 1px solid var(--gray-border);
    }
    
    .gradient-bg {
      background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
    }
    
    .stat-card {
      transition: all 0.3s ease;
      border: 2px solid transparent;
    }
    
    .stat-card:hover {
      transform: translateY(-5px);
      border-color: var(--orange-primary);
      box-shadow: 0 10px 25px rgba(255, 107, 0, 0.15);
    }
    
    .btn-primary {
      background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
      color: white;
      border: none;
      border-radius: 12px;
      padding: 0.8rem 1.5rem;
      font-weight: 600;
      font-size: 0.95rem;
      cursor: pointer;
      transition: all 0.3s ease;
      letter-spacing: 0.5px;
      box-shadow: 0 4px 10px rgba(255, 107, 0, 0.2);
    }
    
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 7px 15px rgba(255, 107, 0, 0.3);
    }
    
    .btn-outline {
      background: transparent;
      color: var(--orange-primary);
      border: 2px solid var(--orange-primary);
      border-radius: 12px;
      padding: 0.8rem 1.5rem;
      font-weight: 600;
      font-size: 0.95rem;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .btn-outline:hover {
      background: rgba(255, 107, 0, 0.1);
      transform: translateY(-2px);
    }
    
    .tab-button {
      padding: 1rem 1.5rem;
      font-weight: 600;
      font-size: 1rem;
      background: none;
      border: none;
      cursor: pointer;
      position: relative;
      color: var(--gray-text);
      transition: all 0.3s ease;
    }
    
    .tab-button.active {
      color: var(--orange-primary);
    }
    
    .tab-button.active::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      right: 0;
      height: 3px;
      background: var(--orange-primary);
      border-radius: 3px 3px 0 0;
    }
    
    .oeuvre-card {
      transition: all 0.3s ease;
      border: 2px solid transparent;
    }
    
    .oeuvre-card:hover {
      transform: translateY(-3px);
      border-color: var(--orange-primary);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
    }
    
    .link-orange {
      color: var(--orange-primary);
      text-decoration: none;
      transition: all 0.2s ease;
    }
    
    .link-orange:hover {
      color: var(--orange-dark);
      text-decoration: underline;
    }
    
    .avatar-edit {
      position: absolute;
      bottom: 10px;
      right: 10px;
      background: white;
      border-radius: 50%;
      width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .avatar-edit:hover {
      transform: scale(1.1);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    }
    
    .type-badge {
      position: absolute;
      top: 15px;
      right: 15px;
      padding: 0.3rem 0.8rem;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .tab-content {
      display: none;
    }
    
    .tab-content.active {
      display: block;
      animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body class="min-h-screen bg-gray-50">
  <!-- Barre de navigation -->
  <nav class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex items-center">
          <a href="index.html" class="flex items-center">
            <img class="h-10 w-auto" src="https://ik.imagekit.io/melfuviii/Arvest/assets/index/logo.png" alt="Logo Arvest">
            <span class="ml-2 text-xl font-bold text-orange-600">Arvest</span>
          </a>
        </div>
        <div class="flex items-center">
          <a href="explore.html" class="px-3 py-2 text-gray-700 hover:text-orange-600 font-medium">Explorer</a>
          <a href="bibliotheque.html" class="px-3 py-2 text-gray-700 hover:text-orange-600 font-medium">Bibliothèque</a>
          <a href="publication.html" class="px-3 py-2 text-gray-700 hover:text-orange-600 font-medium">Publier</a>
          <div class="ml-4 relative">
            <div class="relative">
              <img class="h-10 w-10 rounded-full object-cover" src="<?= $user['avatar_url'] ?: 'https://ik.imagekit.io/melfuviii/Arvest/assets/avatars/default.jpg' ?>" alt="Avatar">
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- En-tête du profil -->
    <div class="profile-card overflow-hidden mb-8">
      <div class="relative h-48 bg-gradient-to-r from-orange-400 to-orange-600">
        <!-- Bannière de profil (optionnel) -->
      </div>
      
      <div class="px-6 pb-6">
        <div class="flex flex-col md:flex-row md:items-end -mt-20">
          <div class="relative">
            <img class="h-32 w-32 rounded-full border-4 border-white object-cover shadow-lg" 
                 src="<?= $user['avatar_url'] ?: 'https://ik.imagekit.io/melfuviii/Arvest/assets/avatars/default.jpg' ?>" 
                 alt="Avatar de <?= $user['pseudo'] ?>">
            <div class="avatar-edit">
              <i class='bx bx-camera text-orange-600 text-xl'></i>
            </div>
          </div>
          
          <div class="mt-4 md:mt-0 md:ml-6 md:flex-1">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
              <div>
                <h1 class="text-2xl font-bold text-gray-800"><?= $user['pseudo'] ?></h1>
                <?php if ($user['role'] !== 'utilisateur'): ?>
                  <span class="inline-block mt-1 px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-sm font-medium">
                    <?= ucfirst($user['role']) ?>
                  </span>
                <?php endif; ?>
                <p class="mt-2 text-gray-600 flex items-center">
                  <i class='bx bx-envelope mr-2'></i> <?= $user['email'] ?>
                </p>
              </div>
              
              <div class="mt-4 md:mt-0 flex space-x-3">
                <button id="edit-profile-btn" class="btn-outline">
                  <i class='bx bx-edit mr-2'></i> Modifier le profil
                </button>
                <button class="btn-primary">
                  <i class='bx bx-share-alt mr-2'></i> Partager
                </button>
              </div>
            </div>
            
            <?php if (!empty($user['bio'])): ?>
              <div class="mt-4">
                <p class="text-gray-700"><?= $user['bio'] ?></p>
              </div>
            <?php endif; ?>
            
            <div class="mt-6 flex flex-wrap gap-4">
              <?php if (!empty($user['region'])): ?>
                <div class="flex items-center text-gray-600">
                  <i class='bx bx-map mr-2'></i>
                  <span><?= $user['region'] ?></span>
                </div>
              <?php endif; ?>
              
              <div class="flex items-center text-gray-600">
                <i class='bx bx-calendar mr-2'></i>
                <span>Membre depuis <?= date('d/m/Y', strtotime($user['date_inscription'])) ?></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Cartes de statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="stat-card bg-white rounded-xl p-6 shadow-sm">
        <div class="flex items-center">
          <div class="p-3 bg-orange-100 rounded-lg mr-4">
            <i class='bx bx-book text-orange-600 text-2xl'></i>
          </div>
          <div>
            <p class="text-gray-500">Œuvres</p>
            <p class="text-2xl font-bold text-gray-800"><?= $stats['oeuvres'] ?></p>
          </div>
        </div>
      </div>
      
      <div class="stat-card bg-white rounded-xl p-6 shadow-sm">
        <div class="flex items-center">
          <div class="p-3 bg-orange-100 rounded-lg mr-4">
            <i class='bx bx-heart text-orange-600 text-2xl'></i>
          </div>
          <div>
            <p class="text-gray-500">Appréciations</p>
            <p class="text-2xl font-bold text-gray-800"><?= $stats['likes'] ?></p>
          </div>
        </div>
      </div>
      
      <div class="stat-card bg-white rounded-xl p-6 shadow-sm">
        <div class="flex items-center">
          <div class="p-3 bg-orange-100 rounded-lg mr-4">
            <i class='bx bx-user-plus text-orange-600 text-2xl'></i>
          </div>
          <div>
            <p class="text-gray-500">Abonnés</p>
            <p class="text-2xl font-bold text-gray-800"><?= $stats['abonnes'] ?></p>
          </div>
        </div>
      </div>
      
      <div class="stat-card bg-white rounded-xl p-6 shadow-sm">
        <div class="flex items-center">
          <div class="p-3 bg-orange-100 rounded-lg mr-4">
            <i class='bx bx-user-check text-orange-600 text-2xl'></i>
          </div>
          <div>
            <p class="text-gray-500">Abonnements</p>
            <p class="text-2xl font-bold text-gray-800"><?= $stats['abonnements'] ?></p>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Section principale -->
    <div class="flex flex-col lg:flex-row gap-8">
      <!-- Formulaire de modification (caché par défaut) -->
      <div id="edit-profile-section" class="profile-card w-full lg:w-1/3 p-6 hidden">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Modifier le profil</h2>
        
        <?php if (!empty($update_errors)): ?>
          <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            <?php foreach ($update_errors as $error): ?>
              <p class="flex items-center"><i class='bx bx-error-circle mr-2'></i> <?= $error ?></p>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        
        <?php if ($update_success): ?>
          <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
            <p class="flex items-center"><i class='bx bx-check-circle mr-2'></i> Profil mis à jour avec succès!</p>
          </div>
        <?php endif; ?>
        
        <form method="POST">
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-medium mb-2" for="pseudo">
              Pseudo
            </label>
            <input 
              type="text" 
              id="pseudo" 
              name="pseudo" 
              value="<?= $user['pseudo'] ?>" 
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
              required
            >
          </div>
          
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-medium mb-2" for="email">
              Email
            </label>
            <input 
              type="email" 
              id="email" 
              name="email" 
              value="<?= $user['email'] ?>" 
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
              required
            >
          </div>
          
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-medium mb-2" for="bio">
              Biographie
            </label>
            <textarea 
              id="bio" 
              name="bio" 
              rows="3" 
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
            ><?= $user['bio'] ?></textarea>
          </div>
          
          <div class="mb-6">
            <label class="block text-gray-700 text-sm font-medium mb-2">
              Avatar
            </label>
            <div class="flex items-center">
              <img class="h-16 w-16 rounded-full object-cover mr-4" 
                   src="<?= $user['avatar_url'] ?: 'https://ik.imagekit.io/melfuviii/Arvest/assets/avatars/default.jpg' ?>" 
                   alt="Avatar">
              <button type="button" class="btn-outline">
                <i class='bx bx-cloud-upload mr-2'></i> Changer
              </button>
            </div>
          </div>
          
          <div class="flex justify-end space-x-3">
            <button 
              type="button" 
              id="cancel-edit-btn" 
              class="btn-outline"
            >
              Annuler
            </button>
            <button 
              type="submit" 
              name="update_profile"
              class="btn-primary"
            >
              Enregistrer
            </button>
          </div>
        </form>
      </div>
      
      <!-- Contenu principal -->
      <div class="flex-1">
        <!-- Onglets -->
        <div class="tabs-container border-b border-gray-200 mb-6">
          <button data-tab="oeuvres" class="tab-button active">Œuvres</button>
          <button data-tab="commentaires" class="tab-button">Commentaires</button>
          <button data-tab="abonnes" class="tab-button">Abonnés</button>
          <button data-tab="abonnements" class="tab-button">Abonnements</button>
        </div>
        
        <!-- Section Œuvres (active par défaut) -->
        <div id="oeuvres-section" class="tab-content active">
          <?php if (empty($oeuvres)): ?>
            <div class="text-center py-12">
              <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto">
                <i class='bx bx-book-open text-3xl text-gray-400'></i>
              </div>
              <h3 class="mt-4 text-lg font-medium text-gray-900">Aucune œuvre publiée</h3>
              <p class="mt-1 text-gray-500">Vous n'avez pas encore publié d'œuvre sur Arvest.</p>
              <div class="mt-6">
                <a href="publication.html" class="btn-primary inline-flex items-center">
                  <i class='bx bx-plus mr-2'></i> Publier votre première œuvre
                </a>
              </div>
            </div>
          <?php else: ?>
            <div class="space-y-6">
              <?php foreach ($oeuvres as $oeuvre): ?>
                <div class="oeuvre-card bg-white rounded-xl p-5 shadow-sm relative">
                  <?php
                    $type_colors = [
                      'conte' => 'bg-orange-100 text-orange-800',
                      'proverbe' => 'bg-blue-100 text-blue-800',
                      'recit' => 'bg-purple-100 text-purple-800',
                      'chanson' => 'bg-red-100 text-red-800',
                      'poeme' => 'bg-green-100 text-green-800'
                    ];
                  ?>
                  <div class="type-badge <?= $type_colors[$oeuvre['type']] ?>">
                    <?= $oeuvre['type'] ?>
                  </div>
                  
                  <div class="flex">
                    <div class="flex-shrink-0 mr-4">
                      <div class="bg-gray-200 border-2 border-dashed rounded-xl w-16 h-16" />
                    </div>
                    <div class="min-w-0 flex-1">
                      <h3 class="text-lg font-medium text-gray-900 truncate">
                        <a href="lecture.html?id=<?= $oeuvre['id'] ?>" class="hover:text-orange-600"><?= $oeuvre['titre'] ?></a>
                      </h3>
                      <p class="text-sm text-gray-500 mt-1">
                        <?= date('d/m/Y', strtotime($oeuvre['date_publication'])) ?> 
                        • <?= $oeuvre['langue'] ?> 
                        <?= !empty($oeuvre['region']) ? '• ' . $oeuvre['region'] : '' ?>
                      </p>
                      <div class="mt-2 flex items-center text-sm text-gray-500">
                        <span class="flex items-center mr-4">
                          <i class='bx bx-heart mr-1'></i> 
                          <?php
                            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM likes WHERE oeuvre_id = ?");
                            $stmt->execute([$oeuvre['id']]);
                            $like_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                            echo $like_count;
                          ?>
                        </span>
                        <span class="flex items-center mr-4">
                          <i class='bx bx-message-rounded mr-1'></i> 
                          <?php
                            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM commentaires WHERE oeuvre_id = ? AND statut = 'actif'");
                            $stmt->execute([$oeuvre['id']]);
                            $comment_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                            echo $comment_count;
                          ?>
                        </span>
                        <span class="flex items-center">
                          <i class='bx bx-show mr-1'></i> 
                          <?= rand(50, 500) // Statistiques fictives pour l'exemple ?>
                        </span>
                      </div>
                    </div>
                  </div>
                  
                  <div class="mt-4 flex justify-end space-x-2">
                    <a href="publication.html?edit=<?= $oeuvre['id'] ?>" class="text-sm btn-outline py-1 px-3">
                      <i class='bx bx-edit mr-1'></i> Modifier
                    </a>
                    <button class="text-sm btn-outline py-1 px-3">
                      <i class='bx bx-trash mr-1'></i> Supprimer
                    </button>
                  </div>
                </div>
              <?php endforeach; ?>
              
              <div class="mt-6 text-center">
                <a href="mes-oeuvres.html" class="btn-outline inline-flex items-center">
                  <i class='bx bx-list-ul mr-2'></i> Voir toutes mes œuvres
                </a>
              </div>
            </div>
          <?php endif; ?>
        </div>
        
        <!-- Section Commentaires (cachée par défaut) -->
        <div id="commentaires-section" class="tab-content">
          <?php if (empty($commentaires)): ?>
            <div class="text-center py-12">
              <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto">
                <i class='bx bx-comment text-3xl text-gray-400'></i>
              </div>
              <h3 class="mt-4 text-lg font-medium text-gray-900">Aucun commentaire</h3>
              <p class="mt-1 text-gray-500">Vous n'avez pas encore posté de commentaire.</p>
            </div>
          <?php else: ?>
            <div class="space-y-4">
              <?php foreach ($commentaires as $commentaire): ?>
                <div class="bg-white rounded-xl p-5 shadow-sm">
                  <div class="flex">
                    <div class="flex-shrink-0 mr-4">
                      <img class="h-10 w-10 rounded-full object-cover" 
                           src="<?= $user['avatar_url'] ?: 'https://ik.imagekit.io/melfuviii/Arvest/assets/avatars/default.jpg' ?>" 
                           alt="Avatar">
                    </div>
                    <div class="min-w-0 flex-1">
                      <h4 class="text-sm font-medium text-gray-900">
                        Sur : <a href="lecture.html?id=<?= $commentaire['oeuvre_id'] ?>" class="link-orange"><?= $commentaire['oeuvre_titre'] ?></a>
                      </h4>
                      <p class="text-sm text-gray-500 mt-1"><?= date('d/m/Y H:i', strtotime($commentaire['date_commentaire'])) ?></p>
                      <div class="mt-2 text-gray-700">
                        <?= $commentaire['contenu'] ?>
                      </div>
                      <div class="mt-4 flex items-center">
                        <span class="flex items-center text-sm text-gray-500 mr-4">
                          <i class='bx bx-like mr-1'></i> <?= rand(0, 10) ?>
                        </span>
                        <button class="text-sm text-orange-600 hover:text-orange-800">
                          <i class='bx bx-trash mr-1'></i> Supprimer
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
        
        <!-- Section Abonnés (cachée par défaut) -->
        <div id="abonnes-section" class="tab-content">
          <?php if (empty($abonnes)): ?>
            <div class="text-center py-12">
              <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto">
                <i class='bx bx-user-plus text-3xl text-gray-400'></i>
              </div>
              <h3 class="mt-4 text-lg font-medium text-gray-900">Aucun abonné</h3>
              <p class="mt-1 text-gray-500">Vous n'avez pas encore d'abonnés.</p>
            </div>
          <?php else: ?>
            <div class="space-y-4">
              <?php foreach ($abonnes as $abonne): ?>
                <div class="bg-white rounded-xl p-5 shadow-sm">
                  <div class="flex items-center">
                    <img class="h-12 w-12 rounded-full object-cover" 
                         src="<?= $abonne['avatar_url'] ?: 'https://ik.imagekit.io/melfuviii/Arvest/assets/avatars/default.jpg' ?>" 
                         alt="<?= $abonne['pseudo'] ?>">
                    <div class="ml-4 min-w-0 flex-1">
                      <h4 class="text-sm font-medium text-gray-900 truncate"><?= $abonne['pseudo'] ?></h4>
                      <p class="text-sm text-gray-500">Abonné depuis <?= date('d/m/Y', strtotime($abonne['date_abonnement'])) ?></p>
                    </div>
                    <a href="profil.html?id=<?= $abonne['id'] ?>" class="btn-outline text-sm py-1 px-3">
                      Voir le profil
                    </a>
                  </div>
                </div>
              <?php endforeach; ?>
              
              <div class="mt-6 text-center">
                <a href="abonnes.html" class="btn-outline inline-flex items-center">
                  <i class='bx bx-list-ul mr-2'></i> Voir tous les abonnés
                </a>
              </div>
            </div>
          <?php endif; ?>
        </div>
        
        <!-- Section Abonnements (cachée par défaut) -->
        <div id="abonnements-section" class="tab-content">
          <?php if (empty($abonnements)): ?>
            <div class="text-center py-12">
              <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto">
                <i class='bx bx-user-check text-3xl text-gray-400'></i>
              </div>
              <h3 class="mt-4 text-lg font-medium text-gray-900">Aucun abonnement</h3>
              <p class="mt-1 text-gray-500">Vous ne suivez personne pour le moment.</p>
              <div class="mt-6">
                <a href="explore.html" class="btn-primary inline-flex items-center">
                  <i class='bx bx-search mr-2'></i> Découvrir des conteurs
                </a>
              </div>
            </div>
          <?php else: ?>
            <div class="space-y-4">
              <?php foreach ($abonnements as $abonnement): ?>
                <div class="bg-white rounded-xl p-5 shadow-sm">
                  <div class="flex items-center">
                    <img class="h-12 w-12 rounded-full object-cover" 
                         src="<?= $abonnement['avatar_url'] ?: 'https://ik.imagekit.io/melfuviii/Arvest/assets/avatars/default.jpg' ?>" 
                         alt="<?= $abonnement['pseudo'] ?>">
                    <div class="ml-4 min-w-0 flex-1">
                      <h4 class="text-sm font-medium text-gray-900 truncate"><?= $abonnement['pseudo'] ?></h4>
                      <p class="text-sm text-gray-500">Suivi depuis <?= date('d/m/Y', strtotime($abonnement['date_abonnement'])) ?></p>
                    </div>
                    <a href="profil.html?id=<?= $abonnement['id'] ?>" class="btn-outline text-sm py-1 px-3">
                      Voir le profil
                    </a>
                  </div>
                </div>
              <?php endforeach; ?>
              
              <div class="mt-6 text-center">
                <a href="abonnements.html" class="btn-outline inline-flex items-center">
                  <i class='bx bx-list-ul mr-2'></i> Voir tous mes abonnements
                </a>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
      
      <!-- Barre latérale -->
      <div class="w-full lg:w-1/3 space-y-6">
        <!-- Section Abonnés -->
        <div class="profile-card p-6">
          <h2 class="text-lg font-bold text-gray-800 mb-4">Abonnés récents</h2>
          
          <div class="space-y-4">
            <?php if (empty($abonnes)): ?>
              <p class="text-gray-500 text-sm">Vous n'avez pas encore d'abonnés</p>
            <?php else: ?>
              <?php foreach ($abonnes as $abonne): ?>
                <div class="flex items-center">
                  <img class="h-10 w-10 rounded-full object-cover" 
                       src="<?= $abonne['avatar_url'] ?: 'https://ik.imagekit.io/melfuviii/Arvest/assets/avatars/default.jpg' ?>" 
                       alt="<?= $abonne['pseudo'] ?>">
                  <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900"><?= $abonne['pseudo'] ?></p>
                    <p class="text-xs text-gray-500">Abonné depuis <?= date('d/m/Y', strtotime($abonne['date_abonnement'])) ?></p>
                  </div>
                  <a href="profil.html?id=<?= $abonne['id'] ?>" class="ml-auto text-xs btn-outline py-1 px-3">
                    Voir
                  </a>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
            
            <div class="mt-4 text-center">
              <a href="abonnes.html" class="link-orange text-sm font-medium">
                Voir tous les abonnés (<?= $stats['abonnes'] ?>)
              </a>
            </div>
          </div>
        </div>
        
        <!-- Section Suggestions -->
        <div class="profile-card p-6">
          <h2 class="text-lg font-bold text-gray-800 mb-4">Conteurs recommandés</h2>
          
          <div class="space-y-4">
            <?php
            // Récupération de conteurs populaires (exemple fictif)
            $conteurs = [
              [
                'id' => 2,
                'pseudo' => 'Lucie N.',
                'avatar_url' => 'https://ik.imagekit.io/melfuviii/Arvest/assets/avatars/avatar4.jpg',
                'oeuvres_count' => 42,
                'abonnes_count' => 1250
              ],
              [
                'id' => 3,
                'pseudo' => 'Thomas D.',
                'avatar_url' => 'https://ik.imagekit.io/melfuviii/Arvest/assets/avatars/avatar5.jpg',
                'oeuvres_count' => 28,
                'abonnes_count' => 842
              ],
              [
                'id' => 4,
                'pseudo' => 'Sophie K.',
                'avatar_url' => 'https://ik.imagekit.io/melfuviii/Arvest/assets/avatars/avatar6.jpg',
                'oeuvres_count' => 35,
                'abonnes_count' => 967
              ]
            ];
            ?>
            
            <?php foreach ($conteurs as $conteur): ?>
              <div class="flex items-center">
                <img class="h-10 w-10 rounded-full object-cover" 
                     src="<?= $conteur['avatar_url'] ?>" 
                     alt="<?= $conteur['pseudo'] ?>">
                <div class="ml-3 flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900 truncate"><?= $conteur['pseudo'] ?></p>
                  <div class="flex items-center text-xs text-gray-500">
                    <span class="flex items-center mr-3">
                      <i class='bx bx-book mr-1'></i> <?= $conteur['oeuvres_count'] ?>
                    </span>
                    <span class="flex items-center">
                      <i class='bx bx-user-plus mr-1'></i> <?= $conteur['abonnes_count'] ?>
                    </span>
                  </div>
                </div>
                <button class="ml-3 text-xs btn-outline py-1 px-3">
                  Suivre
                </button>
              </div>
            <?php endforeach; ?>
            
            <div class="mt-4 text-center">
              <a href="explore.html" class="link-orange text-sm font-medium">
                Découvrir plus de conteurs
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <script>
    // Gestion de l'affichage du formulaire d'édition
    const editProfileBtn = document.getElementById('edit-profile-btn');
    const cancelEditBtn = document.getElementById('cancel-edit-btn');
    const editProfileSection = document.getElementById('edit-profile-section');
    
    editProfileBtn.addEventListener('click', () => {
      editProfileSection.classList.remove('hidden');
      editProfileSection.classList.add('block');
    });
    
    cancelEditBtn.addEventListener('click', () => {
      editProfileSection.classList.add('hidden');
    });
    
    // Gestion des onglets
    const tabButtons = document.querySelectorAll('.tabs-container .tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
      button.addEventListener('click', () => {
        // Désactiver tous les boutons
        tabButtons.forEach(btn => btn.classList.remove('active'));
        // Cacher tous les contenus
        tabContents.forEach(content => content.classList.remove('active'));
        
        // Activer le bouton cliqué
        button.classList.add('active');
        
        // Afficher le contenu correspondant
        const tabId = button.getAttribute('data-tab') + '-section';
        document.getElementById(tabId).classList.add('active');
      });
    });
  </script>
</body>
</html>