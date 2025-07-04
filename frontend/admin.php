<?php
session_start();
require_once '../backend/Database.php';

// Vérifier si l'utilisateur est connecté et est administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: auth.php?redirect=admin.php");
    exit();
}

$pdo = Database::connect();

// Traitement des actions administratives
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Gestion des catégories
    if (isset($_POST['add_category'])) {
        $nom = htmlspecialchars(trim($_POST['category_name']));
        $type = htmlspecialchars(trim($_POST['category_type']));
        
        if (!empty($nom) && in_array($type, ['theme', 'region', 'langue'])) {
            try {
                $stmt = $pdo->prepare("INSERT INTO categories (nom, type) VALUES (?, ?)");
                $stmt->execute([$nom, $type]);
                $success = "Catégorie ajoutée avec succès!";
            } catch (PDOException $e) {
                $error = "Erreur: " . $e->getMessage();
            }
        }
    }
    
    // Gestion des utilisateurs
    if (isset($_POST['update_user'])) {
        $user_id = $_POST['user_id'];
        $role = $_POST['role'];
        
        if (in_array($role, ['visiteur', 'utilisateur', 'expert', 'admin'])) {
            try {
                $stmt = $pdo->prepare("UPDATE utilisateurs SET role = ? WHERE id = ?");
                $stmt->execute([$role, $user_id]);
                $success = "Utilisateur mis à jour avec succès!";
            } catch (PDOException $e) {
                $error = "Erreur: " . $e->getMessage();
            }
        }
    }
    
    // Gestion des œuvres
    if (isset($_POST['update_oeuvre'])) {
        $oeuvre_id = $_POST['oeuvre_id'];
        $statut = $_POST['statut'];
        
        if (in_array($statut, ['brouillon', 'en_attente', 'valide', 'rejete'])) {
            try {
                $stmt = $pdo->prepare("UPDATE oeuvres SET statut = ? WHERE id = ?");
                $stmt->execute([$statut, $oeuvre_id]);
                $success = "Œuvre mise à jour avec succès!";
            } catch (PDOException $e) {
                $error = "Erreur: " . $e->getMessage();
            }
        }
    }
}

// Récupération des statistiques
$stats = [];
try {
    // Nombre d'utilisateurs
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM utilisateurs");
    $stats['users'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Nombre d'œuvres validées
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM oeuvres WHERE statut = 'valide'");
    $stats['oeuvres'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Nombre de signalements
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM commentaires WHERE statut = 'signale'");
    $stats['signals'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Nombre de catégories
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM categories");
    $stats['categories'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Répartition des œuvres par type
    $stmt = $pdo->query("SELECT type, COUNT(*) as count FROM oeuvres GROUP BY type");
    $oeuvres_by_type = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Évolution des publications
    $stmt = $pdo->query("SELECT DATE_FORMAT(date_publication, '%Y-%m') as month, COUNT(*) as count 
                         FROM oeuvres 
                         GROUP BY month 
                         ORDER BY month DESC 
                         LIMIT 6");
    $publications_evolution = array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));
    
} catch (PDOException $e) {
    $error = "Erreur de base de données: " . $e->getMessage();
}

// Récupération des utilisateurs
$users = [];
try {
    $stmt = $pdo->query("SELECT id, pseudo, email, role, bio, date_inscription FROM utilisateurs");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Gérer l'erreur
}

// Récupération des œuvres
$oeuvres = [];
try {
    $stmt = $pdo->query("SELECT o.id, o.titre, o.type, u.pseudo as auteur, o.statut, o.date_publication
                          FROM oeuvres o 
                          JOIN utilisateurs u ON u.id = o.utilisateur_id");
    $oeuvres = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Gérer l'erreur
}

// Récupération des catégories
$categories = [];
try {
    $stmt = $pdo->query("SELECT id, nom, type FROM categories");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Gérer l'erreur
}

// Préparer les données pour les graphiques
$chart_oeuvres_type = [];
foreach ($oeuvres_by_type as $item) {
    $chart_oeuvres_type['labels'][] = ucfirst($item['type']);
    $chart_oeuvres_type['data'][] = $item['count'];
}

$chart_publications = [];
foreach ($publications_evolution as $item) {
    $chart_publications['labels'][] = date('M Y', strtotime($item['month']));
    $chart_publications['data'][] = $item['count'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Arvest Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#FF6B00',
            'primary-dark': '#E05E00',
            'primary-light': '#FF8A3D',
            secondary: '#4F46E5',
            'secondary-light': '#818CF8'
          }
        }
      }
    }
  </script>
  <style>
    .admin-tab.active {
      background-color: #FFF7F2;
      border-left: 4px solid #FF6B00;
      color: #FF6B00;
    }
    
    .chart-container {
      height: 300px;
      position: relative;
    }
    
    @media (max-width: 768px) {
      .chart-container {
        height: 250px;
      }
    }
    
    .status-badge {
      padding: 0.25rem 0.75rem;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 500;
      display: inline-flex;
      align-items: center;
    }
    
    .status-badge i {
      margin-right: 0.25rem;
      font-size: 0.875rem;
    }
    
    .status-active {
      background-color: #ECFDF5;
      color: #10B981;
    }
    
    .status-pending {
      background-color: #FFFBEB;
      color: #F59E0B;
    }
    
    .status-suspended {
      background-color: #FEF2F2;
      color: #EF4444;
    }
    
    .status-reported {
      background-color: #FEF3C7;
      color: #D97706;
    }
    
    .action-btn {
      transition: all 0.2s ease;
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
    }
    
    .table-responsive {
      overflow-x: auto;
    }
    
    .table-responsive table {
      min-width: 1000px;
    }
    
    @media (min-width: 1024px) {
      .table-responsive table {
        min-width: unset;
      }
    }
    
    .sidebar {
      width: 250px;
      transition: all 0.3s ease;
    }
    
    .content {
      margin-left: 250px;
      transition: all 0.3s ease;
    }
    
    .sidebar.collapsed {
      width: 70px;
    }
    
    .sidebar.collapsed .nav-text {
      display: none;
    }
    
    .sidebar.collapsed + .content {
      margin-left: 70px;
    }
    
    .mobile-menu-btn {
      display: none;
    }
    
    @media (max-width: 1024px) {
      .sidebar {
        position: fixed;
        z-index: 40;
        left: -250px;
      }
      
      .sidebar.active {
        left: 0;
      }
      
      .content {
        margin-left: 0;
      }
      
      .mobile-menu-btn {
        display: block;
      }
    }
  </style>
</head>
<body class="bg-gray-50 font-poppins min-h-screen flex">
  <!-- Sidebar Navigation -->
  <div class="sidebar bg-white shadow-md h-screen fixed top-0 left-0 overflow-y-auto py-6 flex flex-col">
    <div class="px-6 pb-6 border-b border-gray-100">
      <div class="flex items-center gap-3">
        <img src="https://ik.imagekit.io/melfuviii/Arvest/assets/logo.png" alt="Logo Arvest" class="h-10 w-auto"/>
        <span class="font-bold text-xl text-primary">Admin</span>
      </div>
    </div>
    
    <nav class="mt-8 flex-1">
      <ul class="space-y-1 px-3">
        <li>
          <a href="#" class="admin-tab active flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 hover:text-primary font-medium" data-tab="dashboard">
            <i class='bx bx-bar-chart-alt-2 text-xl'></i>
            <span class="nav-text">Tableau de bord</span>
          </a>
        </li>
        <li>
          <a href="#" class="admin-tab flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 hover:text-primary font-medium" data-tab="users">
            <i class='bx bx-user text-xl'></i>
            <span class="nav-text">Utilisateurs</span>
          </a>
        </li>
        <li>
          <a href="#" class="admin-tab flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 hover:text-primary font-medium" data-tab="contents">
            <i class='bx bx-book text-xl'></i>
            <span class="nav-text">Contenus</span>
          </a>
        </li>
        <li>
          <a href="#" class="admin-tab flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 hover:text-primary font-medium" data-tab="categories">
            <i class='bx bx-category text-xl'></i>
            <span class="nav-text">Catégories</span>
          </a>
        </li>
        <li>
          <a href="#" class="admin-tab flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 hover:text-primary font-medium" data-tab="settings">
            <i class='bx bx-cog text-xl'></i>
            <span class="nav-text">Paramètres</span>
          </a>
        </li>
      </ul>
    </nav>
    
    <div class="px-4 pt-4 border-t border-gray-100">
      <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50">
        <div class="bg-gray-200 border-2 border-dashed rounded-xl w-10 h-10"></div>
        <div class="flex-1">
          <p class="font-medium text-sm"><?= $_SESSION['pseudo'] ?></p>
          <p class="text-xs text-gray-500">Administrateur</p>
        </div>
      </div>
      <a href="logout.php" class="mt-4 w-full flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-primary text-white font-medium hover:bg-primary-dark transition">
        <i class="bx bx-log-out"></i>
        <span class="nav-text">Déconnexion</span>
      </a>
    </div>
  </div>

  <!-- Main Content -->
  <div class="content flex-1 min-h-screen">
    <!-- Top Bar -->
    <header class="bg-white shadow-sm py-4 px-6 flex items-center justify-between">
      <div class="flex items-center gap-4">
        <button class="mobile-menu-btn flex items-center justify-center w-10 h-10 rounded-lg text-gray-700 hover:bg-gray-100">
          <i class="bx bx-menu text-2xl"></i>
        </button>
        <h1 class="text-xl font-bold text-gray-800">Tableau de bord</h1>
      </div>
      <div class="flex items-center gap-4">
        <button class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-700 hover:bg-gray-200">
          <i class="bx bx-bell"></i>
        </button>
        <button class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-700 hover:bg-gray-200">
          <i class="bx bx-cog"></i>
        </button>
      </div>
    </header>

    <!-- Messages d'erreur/succès -->
    <div class="px-6 pt-4">
      <?php if (!empty($error)): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
          <div class="flex items-start gap-3">
            <div class="flex-shrink-0">
              <i class="bx bx-error-circle text-xl text-red-500"></i>
            </div>
            <div>
              <h3 class="font-medium text-red-800">Erreur</h3>
              <p class="text-red-700"><?= $error ?></p>
            </div>
          </div>
        </div>
      <?php endif; ?>
      
      <?php if (!empty($success)): ?>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
          <div class="flex items-start gap-3">
            <div class="flex-shrink-0">
              <i class="bx bx-check-circle text-xl text-green-500"></i>
            </div>
            <div>
              <h3 class="font-medium text-green-800">Succès</h3>
              <p class="text-green-700"><?= $success ?></p>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <!-- Tabs content -->
    <main class="p-6">
      <!-- Dashboard -->
      <section id="tab-dashboard" class="admin-section">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-5">
            <div class="w-14 h-14 rounded-lg bg-primary/10 flex items-center justify-center">
              <i class="bx bx-user text-3xl text-primary"></i>
            </div>
            <div>
              <h3 class="text-2xl font-bold"><?= $stats['users'] ?></h3>
              <p class="text-gray-600">Utilisateurs</p>
            </div>
          </div>
          
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-5">
            <div class="w-14 h-14 rounded-lg bg-secondary/10 flex items-center justify-center">
              <i class="bx bx-book text-3xl text-secondary"></i>
            </div>
            <div>
              <h3 class="text-2xl font-bold"><?= $stats['oeuvres'] ?></h3>
              <p class="text-gray-600">Œuvres validées</p>
            </div>
          </div>
          
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-5">
            <div class="w-14 h-14 rounded-lg bg-orange-100 flex items-center justify-center">
              <i class="bx bx-error text-3xl text-orange-500"></i>
            </div>
            <div>
              <h3 class="text-2xl font-bold"><?= $stats['signals'] ?></h3>
              <p class="text-gray-600">Signalements</p>
            </div>
          </div>
          
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-5">
            <div class="w-14 h-14 rounded-lg bg-green-100 flex items-center justify-center">
              <i class="bx bx-category text-3xl text-green-500"></i>
            </div>
            <div>
              <h3 class="text-2xl font-bold"><?= $stats['categories'] ?></h3>
              <p class="text-gray-600">Catégories</p>
            </div>
          </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-6">
              <h3 class="font-bold text-lg">Répartition des œuvres par type</h3>
              <div class="relative">
                <select class="text-sm border border-gray-200 rounded-lg py-1.5 pl-3 pr-8 focus:ring-2 focus:ring-primary-light focus:border-primary outline-none appearance-none bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9IiM2QjczODAiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIj48cG9seWxpbmUgcG9pbnRzPSI2IDkgMTIgMTUgMTggOSI+PC9wb2x5bGluZT48L3N2Zz4=')] bg-no-repeat bg-[center_right_0.5rem]">
                  <option>Ce mois</option>
                  <option>Ce trimestre</option>
                  <option>Cette année</option>
                </select>
              </div>
            </div>
            <div class="chart-container">
              <canvas id="chart-oeuvres-type"></canvas>
            </div>
          </div>
          
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-6">
              <h3 class="font-bold text-lg">Évolution des publications</h3>
              <div class="relative">
                <select class="text-sm border border-gray-200 rounded-lg py-1.5 pl-3 pr-8 focus:ring-2 focus:ring-primary-light focus:border-primary outline-none appearance-none bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9IiM2QjczODAiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIj48cG9seWxpbmUgcG9pbnRzPSI2IDkgMTIgMTUgMTggOSI+PC9wb2x5bGluZT48L3N2Zz4=')] bg-no-repeat bg-[center_right_0.5rem]">
                  <option>6 derniers mois</option>
                  <option>12 derniers mois</option>
                </select>
              </div>
            </div>
            <div class="chart-container">
              <canvas id="chart-publications"></canvas>
            </div>
          </div>
        </div>
        
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <h3 class="font-bold text-lg mb-6">Activité récente</h3>
          <div class="space-y-4">
            <div class="flex items-start gap-4 pb-4 border-b border-gray-100">
              <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                <i class="bx bx-user-plus text-lg text-primary"></i>
              </div>
              <div>
                <p class="font-medium">Nouvel utilisateur inscrit</p>
                <p class="text-sm text-gray-500">Jean Dupont s'est inscrit il y a 2 heures</p>
              </div>
              <div class="text-sm text-gray-500 ml-auto">11:42 AM</div>
            </div>
            
            <div class="flex items-start gap-4 pb-4 border-b border-gray-100">
              <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                <i class="bx bx-book text-lg text-green-500"></i>
              </div>
              <div>
                <p class="font-medium">Nouvelle œuvre publiée</p>
                <p class="text-sm text-gray-500">"La nuit étoilée" a été publiée par Marie Curie</p>
              </div>
              <div class="text-sm text-gray-500 ml-auto">Hier</div>
            </div>
            
            <div class="flex items-start gap-4 pb-4 border-b border-gray-100">
              <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0">
                <i class="bx bx-error text-lg text-orange-500"></i>
              </div>
              <div>
                <p class="font-medium">Signalement reçu</p>
                <p class="text-sm text-gray-500">Un commentaire a été signalé par un utilisateur</p>
              </div>
              <div class="text-sm text-gray-500 ml-auto">24/06/2023</div>
            </div>
          </div>
        </div>
      </section>

      <!-- Users -->
      <section id="tab-users" class="admin-section hidden">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold">Gestion des utilisateurs</h2>
          <div class="flex gap-3">
            <button class="px-4 py-2 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 flex items-center gap-2">
              <i class="bx bx-export"></i> Exporter
            </button>
            <button class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary-dark flex items-center gap-2">
              <i class="bx bx-plus"></i> Nouvel utilisateur
            </button>
          </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div class="relative flex-1 max-w-md">
              <input type="text" placeholder="Rechercher un utilisateur..." class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary-light focus:border-primary outline-none">
              <i class="bx bx-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            <div class="flex gap-3">
              <select class="text-sm border border-gray-200 rounded-lg py-2 pl-3 pr-8 focus:ring-2 focus:ring-primary-light focus:border-primary outline-none appearance-none bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9IiM2QjczODAiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIj48cG9seWxpbmUgcG9pbnRzPSI2IDkgMTIgMTUgMTggOSI+PC9wb2x5bGluZT48L3N2Zz4=')] bg-no-repeat bg-[center_right_0.5rem]">
                <option>Tous les rôles</option>
                <option>Admin</option>
                <option>Expert</option>
                <option>Utilisateur</option>
                <option>Visiteur</option>
              </select>
              <select class="text-sm border border-gray-200 rounded-lg py-2 pl-3 pr-8 focus:ring-2 focus:ring-primary-light focus:border-primary outline-none appearance-none bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9IiM2QjczODAiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIj48cG9seWxpbmUgcG9pbnRzPSI2IDkgMTIgMTUgMTggOSI+PC9wb2x5bGluZT48L3N2Zz4=')] bg-no-repeat bg-[center_right_0.5rem]">
                <option>Tous les statuts</option>
                <option>Actif</option>
                <option>Suspendu</option>
              </select>
            </div>
          </div>
          
          <div class="table-responsive">
            <table class="w-full">
              <thead>
                <tr class="bg-gray-50 text-gray-700 text-left">
                  <th class="py-3 px-4 font-medium">Utilisateur</th>
                  <th class="py-3 px-4 font-medium">Email</th>
                  <th class="py-3 px-4 font-medium">Rôle</th>
                  <th class="py-3 px-4 font-medium">Statut</th>
                  <th class="py-3 px-4 font-medium">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <?php foreach ($users as $user): ?>
                  <tr>
                    <td class="py-4 px-4">
                      <div class="flex items-center gap-3">
                        <div class="bg-gray-200 border-2 border-dashed rounded-xl w-10 h-10"></div>
                        <div>
                          <p class="font-medium"><?= $user['pseudo'] ?></p>
                          <p class="text-sm text-gray-500">Inscrit le <?= date('d/m/Y', strtotime($user['date_inscription'])) ?></p>
                        </div>
                      </div>
                    </td>
                    <td class="py-4 px-4"><?= $user['email'] ?></td>
                    <td class="py-4 px-4">
                      <span class="px-3 py-1 rounded-full bg-indigo-100 text-indigo-800 text-xs font-medium capitalize">
                        <?= $user['role'] ?>
                      </span>
                    </td>
                    <td class="py-4 px-4">
                      <span class="status-badge status-active">
                        <i class="bx bx-check-circle"></i> Actif
                      </span>
                    </td>
                    <td class="py-4 px-4">
                    <form method="POST" class="flex gap-2">
                      <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                      <select name="role" class="px-2 py-1 border rounded text-sm">
                        <option value="visiteur" <?= $user['role'] === 'visiteur' ? 'selected' : '' ?>>Visiteur</option>
                        <option value="utilisateur" <?= $user['role'] === 'utilisateur' ? 'selected' : '' ?>>Utilisateur</option>
                        <option value="expert" <?= $user['role'] === 'expert' ? 'selected' : '' ?>>Expert</option>
                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                      </select>
                      <button type="submit" name="update_user" class="mt-4 w-auto flex items-center justify-center gap-2 px-2 py-2 rounded-lg bg-green-500 text-white font-medium hover:bg-green-600 transition">
                        <i class="bx bx-save"></i> Mettre à jour
                      </button>
                    </form>
                      <!-- <div class="flex gap-2">
                        <button class="action-btn edit-btn text-sm text-blue-600 hover:text-blue-800">
                          <i class="bx bx-edit"></i> Modifier
                        </button>
                        <button class="action-btn delete-btn text-sm text-red-600 hover:text-red-800">
                          <i class="bx bx-trash"></i> Supprimer
                        </button>
                      </div> -->
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          
          <div class="mt-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 pt-6 border-t border-gray-100">
            <p class="text-gray-600">Affichage de 1 à 10 sur <?= $stats['users'] ?> utilisateurs</p>
            <div class="flex gap-2">
              <button class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50">
                <i class="bx bx-chevron-left"></i>
              </button>
              <button class="w-9 h-9 flex items-center justify-center rounded-lg bg-primary text-white">1</button>
              <button class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50">2</button>
              <button class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50">3</button>
              <button class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50">
                <i class="bx bx-chevron-right"></i>
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- Contents -->
      <section id="tab-contents" class="admin-section hidden">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold">Modération des contenus</h2>
          <div class="flex gap-3">
            <button class="px-4 py-2 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 flex items-center gap-2">
              <i class="bx bx-filter"></i> Filtrer
            </button>
          </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="border-b border-gray-100">
            <ul class="flex flex-wrap -mb-px">
              <li>
                <button class="px-5 py-4 font-medium text-gray-500 hover:text-primary border-b-2 border-transparent hover:border-primary">
                  Tous les contenus
                </button>
              </li>
              <li>
                <button class="px-5 py-4 font-medium text-gray-500 hover:text-primary border-b-2 border-transparent hover:border-primary">
                  En attente
                </button>
              </li>
              <li>
                <button class="px-5 py-4 font-medium text-gray-500 hover:text-primary border-b-2 border-transparent hover:border-primary">
                  Signalés
                </button>
              </li>
              <li>
                <button class="px-5 py-4 font-medium text-gray-500 hover:text-primary border-b-2 border-transparent hover:border-primary">
                  Brouillons
                </button>
              </li>
            </ul>
          </div>
          
          <div class="p-6">
            <div class="table-responsive">
              <table class="w-full">
                <thead>
                  <tr class="bg-gray-50 text-gray-700 text-left">
                    <th class="py-3 px-4 font-medium">Œuvre</th>
                    <th class="py-3 px-4 font-medium">Type</th>
                    <th class="py-3 px-4 font-medium">Auteur</th>
                    <th class="py-3 px-4 font-medium">Date</th>
                    <th class="py-3 px-4 font-medium">Statut</th>
                    <th class="py-3 px-4 font-medium">Actions</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                  <?php foreach ($oeuvres as $oeuvre): ?>
                    <tr>
                      <td class="py-4 px-4">
                        <p class="font-medium"><?= $oeuvre['titre'] ?></p>
                        <p class="text-sm text-gray-500">ID: <?= $oeuvre['id'] ?></p>
                      </td>
                      <td class="py-4 px-4">
                        <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-medium capitalize">
                          <?= $oeuvre['type'] ?>
                        </span>
                      </td>
                      <td class="py-4 px-4"><?= $oeuvre['auteur'] ?></td>
                      <td class="py-4 px-4 text-gray-500"><?= $oeuvre['date_publication'] ?></td>
                      <td class="py-4 px-4">
                        <?php
                        $status_class = 'status-pending';
                        $status_text = 'En attente';
                        $status_icon = 'bx-time';
                        
                        if ($oeuvre['statut'] === 'valide') {
                          $status_class = 'status-active';
                          $status_text = 'Validé';
                          $status_icon = 'bx-check-circle';
                        } elseif ($oeuvre['statut'] === 'rejete') {
                          $status_class = 'status-suspended';
                          $status_text = 'Rejeté';
                          $status_icon = 'bx-x-circle';
                        } elseif ($oeuvre['statut'] === 'signale') {
                          $status_class = 'status-reported';
                          $status_text = 'Signalé';
                          $status_icon = 'bx-flag';
                        }
                        ?>
                        <span class="status-badge <?= $status_class ?>">
                          <i class="bx <?= $status_icon ?>"></i> <?= $status_text ?>
                        </span>
                      </td>
                      <td class="py-4 px-4">
                        <!-- <div class="flex gap-2">
                          <button class="action-btn text-sm text-blue-600 hover:text-blue-800">
                            <i class="bx bx-show"></i>
                          </button>
                          <button class="action-btn text-sm text-green-600 hover:text-green-800">
                            <i class="bx bx-check"></i>
                          </button>
                          <button class="action-btn text-sm text-red-600 hover:text-red-800">
                            <i class="bx bx-x"></i>
                          </button>
                        </div> -->
                        <form method="POST" class="flex gap-2">
                      <input type="hidden" name="oeuvre_id" value="<?= $oeuvre['id'] ?>">
                      <select name="statut" class="px-1 py-1 border rounded text-sm">
                        <option value="brouillon" <?= $oeuvre['statut'] === 'brouillon' ? 'selected' : '' ?>>Brouillon</option>
                        <option value="en_attente" <?= $oeuvre['statut'] === 'en_attente' ? 'selected' : '' ?>>En attente</option>
                        <option value="valide" <?= $oeuvre['statut'] === 'valide' ? 'selected' : '' ?>>Validé</option>
                        <option value="rejete" <?= $oeuvre['statut'] === 'rejete' ? 'selected' : '' ?>>Rejeté</option>
                      </select>
                      <button type="submit" name="update_oeuvre" class="mt-4 w-auto flex items-center justify-center gap-2 px-2 py-2 rounded-lg bg-green-500 text-white font-medium hover:bg-green-600 transition">
                        <i class="bx bx-check"></i> Mettre à jour
                      </button>
                    </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </section>

      <!-- Categories -->
      <section id="tab-categories" class="admin-section hidden">
        <h2 class="text-2xl font-bold mb-6">Gestion des catégories et métadonnées</h2>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
              <div class="flex justify-between items-center mb-6">
                <h3 class="font-semibold text-lg">Catégories existantes</h3>
                <button class="px-3 py-1.5 rounded-lg bg-primary text-white text-sm hover:bg-primary-dark flex items-center gap-1">
                  <i class="bx bx-plus"></i> Ajouter
                </button>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="border border-gray-200 rounded-xl p-5">
                  <div class="flex items-center justify-between mb-4">
                    <h4 class="font-medium text-gray-800">Thèmes</h4>
                    <span class="bg-primary/10 text-primary text-xs font-medium px-2 py-1 rounded-full"><?= count(array_filter($categories, fn($cat) => $cat['type'] === 'theme')) ?></span>
                  </div>
                  <ul class="space-y-2">
                    <?php foreach ($categories as $category): ?>
                      <?php if ($category['type'] === 'theme'): ?>
                        <li class="flex justify-between items-center py-1.5 border-b border-gray-100">
                          <span><?= $category['nom'] ?></span>
                          <div class="flex gap-2">
                            <button class="text-gray-500 hover:text-primary">
                              <i class="bx bx-edit"></i>
                            </button>
                            <button class="text-gray-500 hover:text-red-500">
                              <i class="bx bx-trash"></i>
                            </button>
                          </div>
                        </li>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </ul>
                </div>
                
                <div class="border border-gray-200 rounded-xl p-5">
                  <div class="flex items-center justify-between mb-4">
                    <h4 class="font-medium text-gray-800">Régions</h4>
                    <span class="bg-primary/10 text-primary text-xs font-medium px-2 py-1 rounded-full"><?= count(array_filter($categories, fn($cat) => $cat['type'] === 'region')) ?></span>
                  </div>
                  <ul class="space-y-2">
                    <?php foreach ($categories as $category): ?>
                      <?php if ($category['type'] === 'region'): ?>
                        <li class="flex justify-between items-center py-1.5 border-b border-gray-100">
                          <span><?= $category['nom'] ?></span>
                          <div class="flex gap-2">
                            <button class="text-gray-500 hover:text-primary">
                              <i class="bx bx-edit"></i>
                            </button>
                            <button class="text-gray-500 hover:text-red-500">
                              <i class="bx bx-trash"></i>
                            </button>
                          </div>
                        </li>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </ul>
                </div>
                
                <div class="border border-gray-200 rounded-xl p-5">
                  <div class="flex items-center justify-between mb-4">
                    <h4 class="font-medium text-gray-800">Langues</h4>
                    <span class="bg-primary/10 text-primary text-xs font-medium px-2 py-1 rounded-full"><?= count(array_filter($categories, fn($cat) => $cat['type'] === 'langue')) ?></span>
                  </div>
                  <ul class="space-y-2">
                    <?php foreach ($categories as $category): ?>
                      <?php if ($category['type'] === 'langue'): ?>
                        <li class="flex justify-between items-center py-1.5 border-b border-gray-100">
                          <span><?= $category['nom'] ?></span>
                          <div class="flex gap-2">
                            <button class="text-gray-500 hover:text-primary">
                              <i class="bx bx-edit"></i>
                            </button>
                            <button class="text-gray-500 hover:text-red-500">
                              <i class="bx bx-trash"></i>
                            </button>
                          </div>
                        </li>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </ul>
                </div>
              </div>
            </div>
            
            <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
              <h3 class="font-semibold text-lg mb-6">Ajouter une catégorie</h3>
              <form method="POST">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Nom de la catégorie</label>
                    <input type="text" name="category_name" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-light focus:border-primary outline-none" required>
                  </div>
                  <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Type</label>
                    <select name="category_type" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-light focus:border-primary outline-none appearance-none bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9IiM2QjczODAiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIj48cG9seWxpbmUgcG9pbnRzPSI2IDkgMTIgMTUgMTggOSI+PC9wb2x5bGluZT48L3N2Zz4=')] bg-no-repeat bg-[center_right_1rem]" required>
                      <option value="theme">Thème</option>
                      <option value="region">Région</option>
                      <option value="langue">Langue</option>
                    </select>
                  </div>
                </div>
                <div class="mt-6">
                  <button type="submit" name="add_category" class="px-6 py-2.5 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark">
                    Ajouter la catégorie
                  </button>
                </div>
              </form>
            </div>
          </div>
          
          <div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
              <h3 class="font-semibold text-lg mb-6">Statistiques par catégorie</h3>
              
              <div class="mb-8">
                <h4 class="font-medium mb-3 flex items-center gap-2 text-gray-700">
                  <i class="bx bx-star text-primary"></i>
                  <span>Thèmes les plus populaires</span>
                </h4>
                <ul class="space-y-3">
                  <?php
                  $stmt = $pdo->query("
                    SELECT c.nom, COUNT(oc.oeuvre_id) as count 
                    FROM categories c
                    JOIN oeuvre_categorie oc ON oc.categorie_id = c.id
                    WHERE c.type = 'theme'
                    GROUP BY c.id
                    ORDER BY count DESC
                    LIMIT 5
                  ");
                  $popular_themes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                  
                  foreach ($popular_themes as $theme): ?>
                    <li class="flex justify-between items-center pb-3 border-b border-gray-100">
                      <span><?= $theme['nom'] ?></span>
                      <span class="font-medium bg-gray-100 px-2.5 py-1 rounded-full text-sm"><?= $theme['count'] ?> œuvres</span>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
              
              <div class="mb-8">
                <h4 class="font-medium mb-3 flex items-center gap-2 text-gray-700">
                  <i class="bx bx-map text-primary"></i>
                  <span>Régions les plus représentées</span>
                </h4>
                <ul class="space-y-3">
                  <?php
                  $stmt = $pdo->query("
                    SELECT c.nom, COUNT(oc.oeuvre_id) as count 
                    FROM categories c
                    JOIN oeuvre_categorie oc ON oc.categorie_id = c.id
                    WHERE c.type = 'region'
                    GROUP BY c.id
                    ORDER BY count DESC
                    LIMIT 5
                  ");
                  $popular_regions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                  
                  foreach ($popular_regions as $region): ?>
                    <li class="flex justify-between items-center pb-3 border-b border-gray-100">
                      <span><?= $region['nom'] ?></span>
                      <span class="font-medium bg-gray-100 px-2.5 py-1 rounded-full text-sm"><?= $region['count'] ?> œuvres</span>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
              
              <div>
                <h4 class="font-medium mb-3 flex items-center gap-2 text-gray-700">
                  <i class="bx bx-globe text-primary"></i>
                  <span>Langues les plus utilisées</span>
                </h4>
                <ul class="space-y-3">
                  <?php
                  $stmt = $pdo->query("
                    SELECT c.nom, COUNT(oc.oeuvre_id) as count 
                    FROM categories c
                    JOIN oeuvre_categorie oc ON oc.categorie_id = c.id
                    WHERE c.type = 'langue'
                    GROUP BY c.id
                    ORDER BY count DESC
                    LIMIT 5
                  ");
                  $popular_languages = $stmt->fetchAll(PDO::FETCH_ASSOC);
                  
                  foreach ($popular_languages as $language): ?>
                    <li class="flex justify-between items-center pb-3 border-b border-gray-100">
                      <span><?= $language['nom'] ?></span>
                      <span class="font-medium bg-gray-100 px-2.5 py-1 rounded-full text-sm"><?= $language['count'] ?> œuvres</span>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>

  <script>
    // --- Navigation onglets ---
    document.querySelectorAll('.admin-tab').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Mettre à jour la navigation
        document.querySelectorAll('.admin-tab').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        // Mettre à jour le contenu
        document.querySelectorAll('.admin-section').forEach(sec => sec.classList.add('hidden'));
        document.getElementById('tab-' + this.dataset.tab).classList.remove('hidden');
        
        // Mettre à jour le titre de la page
        const tabName = this.querySelector('.nav-text').textContent;
        document.querySelector('header h1').textContent = tabName;
      });
    });
    
    // Menu mobile
    document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
      document.querySelector('.sidebar').classList.toggle('active');
    });
    
    // Graphiques
    document.addEventListener('DOMContentLoaded', function() {
      // Répartition des œuvres par type
      new Chart(document.getElementById('chart-oeuvres-type'), {
        type: 'doughnut',
        data: {
          labels: <?= json_encode($chart_oeuvres_type['labels']) ?>,
          datasets: [{
            data: <?= json_encode($chart_oeuvres_type['data']) ?>,
            backgroundColor: [
              '#FF6B00', '#FF8A3D', '#FFD6B0', '#4F46E5', '#818CF8', '#C7D2FE'
            ],
            borderWidth: 0
          }]
        },
        options: { 
          responsive: true,
          maintainAspectRatio: false,
          plugins: { 
            legend: { 
              position: 'right',
              labels: {
                font: {
                  family: "'Poppins', sans-serif",
                  size: 12
                },
                padding: 20
              }
            }
          },
          cutout: '60%'
        }
      });
      
      // Évolution des publications
      new Chart(document.getElementById('chart-publications'), {
        type: 'line',
        data: {
          labels: <?= json_encode($chart_publications['labels']) ?>,
          datasets: [{
            label: 'Œuvres publiées',
            data: <?= json_encode($chart_publications['data']) ?>,
            borderColor: '#FF6B00',
            backgroundColor: 'rgba(255,107,0,0.05)',
            borderWidth: 3,
            pointBackgroundColor: '#FFF',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7,
            tension: 0.4,
            fill: true
          }]
        },
        options: { 
          responsive: true,
          maintainAspectRatio: false,
          plugins: { 
            legend: { 
              display: false 
            } 
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: 'rgba(0,0,0,0.05)'
              },
              ticks: {
                precision: 0
              }
            },
            x: {
              grid: {
                display: false
              }
            }
          }
        }
      });
    });
  </script>
</body>
</html>