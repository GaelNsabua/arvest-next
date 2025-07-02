<?php
session_start();
require_once '../backend/Database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php?redirect=publication.php");
    exit();
}

$pdo = Database::connect();
$error = '';
$success = '';

// Récupérer les catégories, langues et régions
$categories = [];
$langues = [];
$regions = [];

try {
    // Catégories (thèmes)
    $stmt = $pdo->prepare("SELECT id, nom FROM categories WHERE type = 'theme'");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Langues
    $stmt = $pdo->prepare("SELECT DISTINCT langue FROM oeuvres WHERE langue IS NOT NULL");
    $stmt->execute();
    $langues = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Régions
    $stmt = $pdo->prepare("SELECT DISTINCT region FROM oeuvres WHERE region IS NOT NULL");
    $stmt->execute();
    $regions = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $error = "Erreur lors du chargement des données: " . $e->getMessage();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données
    $titre = trim($_POST['titre']);
    $type = $_POST['type'];
    $langue = $_POST['langue'];
    $region = $_POST['region'];
    $contenu = trim($_POST['contenu']);
    $custom_langue = trim($_POST['custom_langue']);
    $custom_region = trim($_POST['custom_region']);
    $selected_categories = $_POST['categories'] ?? [];
    
    // Validation
    if (empty($titre) || empty($contenu)) {
        $error = "Le titre et le contenu sont obligatoires";
    } else {
        // Utiliser la langue/region personnalisée si fournie
        $final_langue = !empty($custom_langue) ? $custom_langue : $langue;
        $final_region = !empty($custom_region) ? $custom_region : $region;
        
        try {
            // Commencer la transaction
            $pdo->beginTransaction();
            
            // Insérer l'œuvre
            $stmt = $pdo->prepare("
                INSERT INTO oeuvres 
                (utilisateur_id, titre, type, contenu, langue, region, statut) 
                VALUES (?, ?, ?, ?, ?, ?, 'en_attente')
            ");
            $stmt->execute([
                $_SESSION['user_id'],
                $titre,
                $type,
                $contenu,
                $final_langue,
                $final_region
            ]);
            $oeuvre_id = $pdo->lastInsertId();
            
            // Ajouter les catégories
            foreach ($selected_categories as $categorie_id) {
                $stmt = $pdo->prepare("
                    INSERT INTO oeuvre_categorie (oeuvre_id, categorie_id)
                    VALUES (?, ?)
                ");
                $stmt->execute([$oeuvre_id, $categorie_id]);
            }
            
            // Ajouter les médias (simulé pour l'instant)
            $medias = $_POST['medias'] ?? [];
            foreach ($medias as $media) {
                if (!empty($media['url'])) {
                    $stmt = $pdo->prepare("
                        INSERT INTO medias (oeuvre_id, type, url, description)
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $oeuvre_id,
                        $media['type'],
                        $media['url'],
                        $media['description'] ?? ''
                    ]);
                }
            }
            
            // Valider la transaction
            $pdo->commit();
            
            $success = "Votre œuvre a été soumise avec succès ! Elle sera examinée par nos experts avant publication.";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Erreur lors de la publication: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Publier une œuvre | Arvest Congo</title>
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
    
    .publish-container {
      max-width: 900px;
      background: white;
      border-radius: 1.5rem;
      box-shadow: 0 20px 50px rgba(0,0,0,0.1);
      overflow: hidden;
    }
    
    .publish-header {
      background: linear-gradient(135deg, var(--orange-500) 0%, var(--orange-600) 100%);
    }
    
    .form-step {
      display: none;
      padding: 2rem;
      animation: fadeIn 0.5s ease-out;
    }
    
    .form-step.active {
      display: block;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .form-card {
      background: var(--gray-100);
      border-radius: 1rem;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      border: 1px solid var(--gray-200);
    }
    
    .form-group {
      margin-bottom: 1.5rem;
    }
    
    .form-label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: var(--gray-800);
    }
    
    .form-input, .form-textarea, .form-select {
      width: 100%;
      padding: 0.875rem 1rem;
      border: 2px solid var(--gray-200);
      border-radius: 0.75rem;
      font-size: 1rem;
      transition: all 0.3s ease;
      background: white;
    }
    
    .form-input:focus, .form-textarea:focus, .form-select:focus {
      border-color: var(--orange-500);
      box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.15);
      outline: none;
    }
    
    .form-textarea {
      min-height: 150px;
      resize: vertical;
    }
    
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.875rem 1.75rem;
      border-radius: 0.75rem;
      font-weight: 600;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s ease;
      border: none;
    }
    
    .btn-primary {
      background: linear-gradient(135deg, var(--orange-500) 0%, var(--orange-600) 100%);
      color: white;
      box-shadow: 0 4px 10px rgba(255, 107, 0, 0.3);
    }
    
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 7px 15px rgba(255, 107, 0, 0.4);
    }
    
    .btn-secondary {
      background: white;
      color: var(--gray-700);
      border: 2px solid var(--gray-200);
    }
    
    .btn-secondary:hover {
      background: var(--gray-100);
    }
    
    .step-indicator {
      display: flex;
      justify-content: center;
      gap: 1rem;
      margin-bottom: 2rem;
    }
    
    .step {
      width: 2.5rem;
      height: 2.5rem;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--gray-200);
      color: var(--gray-700);
      font-weight: 600;
      position: relative;
      z-index: 2;
    }
    
    .step.active {
      background: var(--orange-500);
      color: white;
    }
    
    .step.completed {
      background: var(--orange-400);
      color: white;
    }
    
    .step-line {
      position: absolute;
      top: 50%;
      left: 50%;
      width: 120%;
      height: 2px;
      background: var(--gray-200);
      z-index: 1;
      transform: translate(-50%, -50%);
    }
    
    .step-label {
      position: absolute;
      top: calc(100% + 0.5rem);
      left: 50%;
      transform: translateX(-50%);
      font-size: 0.875rem;
      color: var(--gray-700);
      white-space: nowrap;
    }
    
    .step.active .step-label {
      color: var(--orange-500);
      font-weight: 600;
    }
    
    .media-card {
      background: white;
      border-radius: 0.75rem;
      padding: 1rem;
      border: 2px dashed var(--gray-200);
      transition: all 0.3s ease;
      margin-bottom: 1rem;
    }
    
    .media-card:hover {
      border-color: var(--orange-500);
    }
    
    .add-media-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      width: 100%;
      padding: 1.5rem;
      border: 2px dashed var(--gray-300);
      border-radius: 0.75rem;
      background: var(--gray-50);
      color: var(--gray-600);
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .add-media-btn:hover {
      border-color: var(--orange-500);
      color: var(--orange-500);
      background: var(--orange-50);
    }
    
    .category-card {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.75rem 1rem;
      background: white;
      border: 2px solid var(--gray-200);
      border-radius: 0.75rem;
      margin-bottom: 0.75rem;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    
    .category-card:hover {
      border-color: var(--orange-400);
    }
    
    .category-card.selected {
      background: var(--orange-50);
      border-color: var(--orange-500);
    }
    
    .category-checkbox {
      width: 1.25rem;
      height: 1.25rem;
      border: 2px solid var(--gray-300);
      border-radius: 0.375rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .category-card.selected .category-checkbox {
      background: var(--orange-500);
      border-color: var(--orange-500);
    }
    
    .category-card.selected .category-checkbox::after {
      content: '✓';
      color: white;
      font-size: 0.75rem;
    }
    
    .preview-card {
      background: white;
      border-radius: 1rem;
      overflow: hidden;
      box-shadow: 0 10px 25px rgba(0,0,0,0.05);
      border: 1px solid var(--gray-200);
    }
    
    .success-container {
      text-align: center;
      padding: 3rem;
    }
    
    .success-icon {
      width: 6rem;
      height: 6rem;
      border-radius: 50%;
      background: var(--orange-100);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 2rem;
    }
  </style>
</head>
<body class="bg-gray-100 font-poppins">
  <!-- NAVBAR -->
  <?php include './components/navbar.php'; ?>

  <div class="min-h-screen flex items-center justify-center md:mt-[3rem] py-12 px-4">
    <div class="publish-container w-full">
      <!-- En-tête -->
      <div class="publish-header text-white text-center py-8 px-6">
        <h1 class="text-3xl md:text-4xl font-bold mb-4">Partagez votre patrimoine culturel</h1>
        <p class="text-orange-100 max-w-2xl mx-auto">
          Contribuez à la préservation des traditions congolaises en publiant un conte, un proverbe ou une coutume
        </p>
      </div>
      
      <!-- Indicateurs d'étapes -->
      <div class="step-indicator space-x-6 py-6">
        <div class="step active" data-step="1">
          <span>1</span>
          <div class="step-line"></div>
          <div class="step-label">Information</div>
        </div>
        <div class="step" data-step="2">
          <span>2</span>
          <div class="step-line"></div>
          <div class="step-label">Contenu</div>
        </div>
        <div class="step" data-step="3">
          <span>3</span>
          <div class="step-line"></div>
          <div class="step-label">Médias</div>
        </div>
        <div class="step" data-step="4">
          <span>4</span>
          <div class="step-label">Catégories</div>
        </div>
      </div>
      
      <!-- Messages d'erreur/succès -->
      <?php if ($error): ?>
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mx-6 mb-6 rounded">
          <p class="text-red-700 flex items-center">
            <i class='bx bx-error-circle mr-2'></i> <?= $error ?>
          </p>
        </div>
      <?php endif; ?>
      
      <?php if ($success): ?>
        <div class="success-container">
          <div class="success-icon">
            <i class='bx bx-check text-4xl text-orange-500'></i>
          </div>
          <h2 class="text-2xl font-bold text-gray-900 mb-4">Publication réussie !</h2>
          <p class="text-gray-600 mb-8 max-w-md mx-auto"><?= $success ?></p>
          <div class="flex justify-center gap-4">
            <a href="profil.php" class="btn btn-primary">
              <i class='bx bx-user mr-2'></i>Voir mon profil
            </a>
            <a href="publication.php" class="btn btn-secondary">
              <i class='bx bx-plus mr-2'></i>Publier une autre œuvre
            </a>
          </div>
        </div>
      <?php else: ?>
        <!-- Formulaire de publication -->
        <form method="POST" id="publication-form">
          <!-- Étape 1: Information de base -->
          <div class="form-step active" id="step-1">
            <div class="form-card">
              <h2 class="text-xl font-bold text-gray-900 mb-4">Informations de base</h2>
              
              <div class="form-group">
                <label class="form-label" for="titre">Titre de l'œuvre *</label>
                <input 
                  type="text" 
                  id="titre" 
                  name="titre" 
                  class="form-input" 
                  placeholder="Ex: Le lièvre et la tortue"
                  required
                >
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-group">
                  <label class="form-label" for="type">Type d'œuvre *</label>
                  <select id="type" name="type" class="form-select" required>
                    <option value="">Sélectionnez un type</option>
                    <option value="conte">Conte</option>
                    <option value="proverbe">Proverbe</option>
                    <option value="recit">Récit ou coutume</option>
                    <option value="chanson">Chanson traditionnelle</option>
                    <option value="poeme">Poème</option>
                  </select>
                </div>
                
                <div class="form-group">
                  <label class="form-label" for="langue">Langue *</label>
                  <select id="langue" name="langue" class="form-select" required>
                    <option value="">Sélectionnez une langue</option>
                    <?php foreach ($langues as $lang): ?>
                      <option value="<?= htmlspecialchars($lang) ?>"><?= htmlspecialchars($lang) ?></option>
                    <?php endforeach; ?>
                    <option value="other">Autre langue</option>
                  </select>
                  <input 
                    type="text" 
                    id="custom_langue" 
                    name="custom_langue" 
                    class="form-input mt-2 hidden" 
                    placeholder="Précisez la langue"
                  >
                </div>
                
                <div class="form-group">
                  <label class="form-label" for="region">Région d'origine</label>
                  <select id="region" name="region" class="form-select">
                    <option value="">Sélectionnez une région</option>
                    <?php foreach ($regions as $reg): ?>
                      <option value="<?= htmlspecialchars($reg) ?>"><?= htmlspecialchars($reg) ?></option>
                    <?php endforeach; ?>
                    <option value="other">Autre région</option>
                  </select>
                  <input 
                    type="text" 
                    id="custom_region" 
                    name="custom_region" 
                    class="form-input mt-2 hidden" 
                    placeholder="Précisez la région"
                  >
                </div>
              </div>
            </div>
            
            <div class="flex justify-between">
              <button type="button" class="btn btn-secondary" disabled>
                <i class='bx bx-chevron-left mr-2'></i>Précédent
              </button>
              <button type="button" class="btn btn-primary next-step" data-next="2">
                Suivant <i class='bx bx-chevron-right ml-2'></i>
              </button>
            </div>
          </div>
          
          <!-- Étape 2: Contenu -->
          <div class="form-step" id="step-2">
            <div class="form-card">
              <h2 class="text-xl font-bold text-gray-900 mb-4">Contenu de l'œuvre</h2>
              
              <div class="form-group">
                <label class="form-label" for="contenu">Votre œuvre *</label>
                <textarea 
                  id="contenu" 
                  name="contenu" 
                  class="form-textarea" 
                  placeholder="Écrivez votre conte, proverbe ou récit ici..."
                  required
                ></textarea>
                <p class="text-sm text-gray-500 mt-2">
                  * Pour les proverbes: écrivez le proverbe puis son explication
                </p>
              </div>
            </div>
            
            <div class="flex justify-between">
              <button type="button" class="btn btn-secondary prev-step" data-prev="1">
                <i class='bx bx-chevron-left mr-2'></i>Précédent
              </button>
              <button type="button" class="btn btn-primary next-step" data-next="3">
                Suivant <i class='bx bx-chevron-right ml-2'></i>
              </button>
            </div>
          </div>
          
          <!-- Étape 3: Médias -->
          <div class="form-step" id="step-3">
            <div class="form-card">
              <h2 class="text-xl font-bold text-gray-900 mb-4">Ajouter des médias</h2>
              <p class="text-gray-600 mb-6">
                Illustrez votre œuvre avec des images, des enregistrements audio ou des vidéos (optionnel)
              </p>
              
              <div id="media-container">
                <!-- Les médias seront ajoutés ici dynamiquement -->
              </div>
              
              <button type="button" id="add-media" class="add-media-btn">
                <i class='bx bx-plus text-xl'></i> Ajouter un média
              </button>
            </div>
            
            <div class="flex justify-between">
              <button type="button" class="btn btn-secondary prev-step" data-prev="2">
                <i class='bx bx-chevron-left mr-2'></i>Précédent
              </button>
              <button type="button" class="btn btn-primary next-step" data-next="4">
                Suivant <i class='bx bx-chevron-right ml-2'></i>
              </button>
            </div>
          </div>
          
          <!-- Étape 4: Catégories -->
          <div class="form-step" id="step-4">
            <div class="form-card">
              <h2 class="text-xl font-bold text-gray-900 mb-4">Catégorisation</h2>
              <p class="text-gray-600 mb-6">
                Sélectionnez les thèmes qui correspondent à votre œuvre (1 minimum)
              </p>
              
              <div id="categories-container">
                <?php foreach ($categories as $categorie): ?>
                  <label class="category-card">
                    <div class="category-checkbox"></div>
                    <input 
                      type="checkbox" 
                      name="categories[]" 
                      value="<?= $categorie['id'] ?>" 
                      class="hidden"
                    >
                    <span><?= htmlspecialchars($categorie['nom']) ?></span>
                  </label>
                <?php endforeach; ?>
              </div>
            </div>
            
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg mb-6">
              <div class="flex items-start gap-3">
                <i class='bx bx-info-circle text-blue-500 text-xl mt-0.5'></i>
                <div>
                  <h4 class="font-bold text-blue-900">Processus de validation</h4>
                  <p class="text-blue-700">
                    Votre œuvre sera examinée par nos experts avant publication. 
                    Vous serez notifié par email une fois validée.
                  </p>
                </div>
              </div>
            </div>
            
            <div class="flex justify-between">
              <button type="button" class="btn btn-secondary prev-step" data-prev="3">
                <i class='bx bx-chevron-left mr-2'></i>Précédent
              </button>
              <button type="submit" class="btn btn-primary">
                <i class='bx bx-send mr-2'></i>Soumettre pour validation
              </button>
            </div>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Gestion des étapes
      const steps = document.querySelectorAll('.form-step');
      const stepIndicators = document.querySelectorAll('.step');
      
      function showStep(stepNumber) {
        // Masquer toutes les étapes
        steps.forEach(step => step.classList.remove('active'));
        // Afficher l'étape courante
        document.getElementById(`step-${stepNumber}`).classList.add('active');
        
        // Mettre à jour les indicateurs
        stepIndicators.forEach(indicator => {
          const step = parseInt(indicator.dataset.step);
          indicator.classList.remove('active', 'completed');
          
          if (step === stepNumber) {
            indicator.classList.add('active');
          } else if (step < stepNumber) {
            indicator.classList.add('completed');
          }
        });
      }
      
      // Navigation entre les étapes
      document.querySelectorAll('.next-step').forEach(button => {
        button.addEventListener('click', function() {
          const nextStep = this.dataset.next;
          showStep(nextStep);
        });
      });
      
      document.querySelectorAll('.prev-step').forEach(button => {
        button.addEventListener('click', function() {
          const prevStep = this.dataset.prev;
          showStep(prevStep);
        });
      });
      
      // Langue personnalisée
      const langueSelect = document.getElementById('langue');
      const customLangueInput = document.getElementById('custom_langue');
      
      langueSelect.addEventListener('change', function() {
        if (this.value === 'other') {
          customLangueInput.classList.remove('hidden');
          customLangueInput.required = true;
        } else {
          customLangueInput.classList.add('hidden');
          customLangueInput.required = false;
        }
      });
      
      // Région personnalisée
      const regionSelect = document.getElementById('region');
      const customRegionInput = document.getElementById('custom_region');
      
      regionSelect.addEventListener('change', function() {
        if (this.value === 'other') {
          customRegionInput.classList.remove('hidden');
        } else {
          customRegionInput.classList.add('hidden');
        }
      });
      
      // Gestion des catégories
      document.querySelectorAll('.category-card').forEach(card => {
        const checkbox = card.querySelector('input[type="checkbox"]');
        
        card.addEventListener('click', function() {
          checkbox.checked = !checkbox.checked;
          card.classList.toggle('selected', checkbox.checked);
        });
      });
      
      // Gestion des médias
      const mediaContainer = document.getElementById('media-container');
      const mediaCounter = { image: 1, audio: 1, video: 1 };
      
      document.getElementById('add-media').addEventListener('click', function() {
        const mediaId = Date.now(); // ID unique
        
        const mediaCard = document.createElement('div');
        mediaCard.className = 'media-card';
        mediaCard.innerHTML = `
          <div class="flex justify-between items-start mb-3">
            <h3 class="font-medium text-gray-900">Nouveau média</h3>
            <button type="button" class="remove-media text-gray-400 hover:text-red-500">
              <i class='bx bx-x text-xl'></i>
            </button>
          </div>
          
          <div class="form-group">
            <label class="form-label">Type de média</label>
            <select name="medias[${mediaId}][type]" class="form-select" required>
              <option value="">Sélectionnez un type</option>
              <option value="image">Image</option>
              <option value="audio">Audio</option>
              <option value="video">Vidéo</option>
            </select>
          </div>
          
          <div class="form-group">
            <label class="form-label">URL du média *</label>
            <input 
              type="url" 
              name="medias[${mediaId}][url]" 
              class="form-input" 
              placeholder="https://..."
              required
            >
          </div>
          
          <div class="form-group">
            <label class="form-label">Description (optionnel)</label>
            <input 
              type="text" 
              name="medias[${mediaId}][description]" 
              class="form-input" 
              placeholder="Description du média"
            >
          </div>
        `;
        
        mediaContainer.appendChild(mediaCard);
        
        // Bouton de suppression
        mediaCard.querySelector('.remove-media').addEventListener('click', function() {
          mediaCard.remove();
        });
      });
      
      // Validation du formulaire à la soumission
      document.getElementById('publication-form').addEventListener('submit', function(e) {
        // Vérifier qu'au moins une catégorie est sélectionnée
        const categoriesSelected = document.querySelectorAll('input[name="categories[]"]:checked').length > 0;
        
        if (!categoriesSelected) {
          e.preventDefault();
          alert('Veuillez sélectionner au moins une catégorie pour votre œuvre');
          showStep(4);
        }
      });
    });
  </script>
</body>
</html>