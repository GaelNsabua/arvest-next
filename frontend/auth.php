<?php
session_start();
require_once '../backend/Database.php'; // Fichier contenant la classe Database

// Traitement de l'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $nom = htmlspecialchars(trim($_POST['nom']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $pseudo = htmlspecialchars(trim($_POST['pseudo']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation des données
    $errors = [];
    if (empty($nom)) $errors[] = "Le nom est requis";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide";
    if (empty($pseudo)) $errors[] = "Le pseudo est requis";
    if (strlen($password) < 8) $errors[] = "Le mot de passe doit contenir au moins 8 caractères";
    if ($password !== $confirm_password) $errors[] = "Les mots de passe ne correspondent pas";

    if (empty($errors)) {
        try {
            $pdo = Database::connect();
            
            // Vérifier si l'email ou le pseudo existe déjà
            $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ? OR pseudo = ?");
            $stmt->execute([$email, $pseudo]);
            
            if ($stmt->rowCount() > 0) {
                $errors[] = "L'email ou le pseudo est déjà utilisé";
            } else {
                // Hachage du mot de passe
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insertion dans la base de données
                $stmt = $pdo->prepare("INSERT INTO utilisateurs (email, mot_de_passe, pseudo) VALUES (?, ?, ?)");
                $stmt->execute([$email, $hashed_password, $pseudo]);
                
                // Récupération de l'utilisateur créé
                $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Création de la session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['pseudo'] = $user['pseudo'];
                $_SESSION['role'] = $user['role'];
                
                // Redirection
                header("Location: explore.php");
                exit();
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur de base de données : " . $e->getMessage();
        }
    }
}

// Traitement de la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $identifier = trim($_POST['identifier']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    $errors = [];
    if (empty($identifier)) $errors[] = "Email ou pseudo requis";
    if (empty($password)) $errors[] = "Mot de passe requis";

    if (empty($errors)) {
        try {
            $pdo = Database::connect();
            
            // Recherche par email ou pseudo
            $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ? OR pseudo = ?");
            $stmt->execute([$identifier, $identifier]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['mot_de_passe'])) {
                // Création de la session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['pseudo'] = $user['pseudo'];
                $_SESSION['role'] = $user['role'];
                
                // Cookie "Se souvenir de moi"
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $expire = time() + 60 * 60 * 24 * 30; // 30 jours
                    
                    setcookie('remember_token', $token, $expire, '/');
                    
                    // Stockage dans la base
                    $stmt = $pdo->prepare("UPDATE utilisateurs SET remember_token = ? WHERE id = ?");
                    $stmt->execute([$token, $user['id']]);
                }
                
                // Redirection
                if($user['role'] == 'admin')
                  header("Location: admin.php");
                else
                  header("Location: explore.php");
                exit();
            } else {
                $errors[] = "Identifiants incorrects";
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur de connexion : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Authentification - Arvest</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="./styles/output.css" />
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
      min-height: 100vh;
      font-family: 'Poppins', sans-serif;
      background-color: var(--gray-bg);
      background-image: 
        radial-gradient(circle at 10% 20%, rgba(255, 214, 171, 0.1) 0%, transparent 20%),
        radial-gradient(circle at 90% 80%, rgba(255, 214, 171, 0.1) 0%, transparent 20%);
      background-attachment: fixed;
    }
    
    .auth-card {
      background: var(--gray-card);
      border-radius: 20px;
      box-shadow: 0 15px 50px rgba(0, 0, 0, 0.08);
      overflow: hidden;
      border: 1px solid var(--gray-border);
    }
    
    .gradient-bg {
      background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
    }
    
    .input-group {
      position: relative;
      margin-bottom: 1.5rem;
    }
    
    .input-icon {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: var(--orange-primary);
      z-index: 10;
    }
    
    .form-input {
      width: 100%;
      padding: 0.9rem 1rem 0.9rem 3rem;
      border-radius: 12px;
      border: 2px solid var(--gray-border);
      font-size: 0.95rem;
      color: var(--gray-text);
      transition: all 0.3s ease;
      background-color: var(--gray-card);
    }
    
    .form-input:focus {
      border-color: var(--orange-primary);
      box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.15);
      outline: none;
    }
    
    .btn-primary {
      background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
      color: white;
      border: none;
      border-radius: 12px;
      padding: 1rem;
      font-weight: 600;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s ease;
      letter-spacing: 0.5px;
      box-shadow: 0 4px 10px rgba(255, 107, 0, 0.2);
    }
    
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 7px 15px rgba(255, 107, 0, 0.3);
    }
    
    .social-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      padding: 0.8rem;
      border-radius: 12px;
      border: 2px solid var(--gray-border);
      background-color: var(--gray-card);
      font-size: 0.9rem;
      font-weight: 500;
      color: var(--gray-text);
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .social-btn:hover {
      border-color: var(--orange-primary);
      transform: translateY(-2px);
    }
    
    .floating-label {
      position: absolute;
      top: 50%;
      left: 3rem;
      transform: translateY(-50%);
      color: #a0aec0;
      pointer-events: none;
      transition: all 0.3s ease;
      font-size: 0.95rem;
    }
    
    .form-input:focus + .floating-label,
    .form-input:not(:placeholder-shown) + .floating-label {
      top: 0;
      left: 3rem;
      background: var(--gray-card);
      padding: 0 0.5rem;
      font-size: 0.8rem;
      color: var(--orange-primary);
    }
    
    .error-message {
      background-color: #fef2f2;
      border: 1px solid #fecaca;
      color: #ef4444;
      padding: 1rem;
      border-radius: 12px;
      margin-bottom: 1.5rem;
      animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .tabs-container {
      display: flex;
      border-bottom: 2px solid var(--gray-border);
      margin-bottom: 1.5rem;
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
    
    .divider {
      position: relative;
      text-align: center;
      margin: 2rem 0;
    }
    
    .divider::before {
      content: "";
      position: absolute;
      top: 50%;
      left: 0;
      right: 0;
      height: 1px;
      background: linear-gradient(to right, transparent, var(--gray-border), transparent);
      z-index: 1;
    }
    
    .divider span {
      position: relative;
      background: var(--gray-card);
      padding: 0 1rem;
      z-index: 2;
      color: var(--gray-text);
      font-size: 0.9rem;
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
    
    .checkbox-orange {
      accent-color: var(--orange-primary);
    }
    
    @media (max-width: 768px) {
      .auth-card {
        border-radius: 16px;
      }
      
      .tabs-container {
        margin-bottom: 1rem;
      }
      
      .tab-button {
        padding: 0.8rem 1rem;
        font-size: 0.9rem;
      }
    }
  </style>
</head>
<body class="flex items-center justify-center p-4 min-h-screen">
  <div class="auth-card flex flex-col md:flex-row w-full max-w-4xl">
    <!-- Section visuelle -->
    <div class="gradient-bg hidden md:flex flex-col items-center justify-center p-12 text-white w-2/5">
      <div class="text-center">
        <div class="rounded-2xl mb-8 inline-block">
          <img src="https://res.cloudinary.com/dglb0uqr8/image/upload/v1751414215/Recipe_book-pana_v6gwvp.png" 
               alt="Logo Arvest" 
               class="w-auto mx-auto object-cover">
        </div>
        <h1 class="text-3xl font-bold mb-4">Arvest</h1>
        <p class="text-white/90 mb-3">Patrimoine Culturel Congolais</p>
        <p class="text-white/80 italic">"Sauvegardons ensemble notre héritage oral pour les générations futures"</p>
        <div class="flex justify-center gap-4 mt-4">
          <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center">
            <i class='bx bx-book-open text-xl'></i>
          </div>
          <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center">
            <i class='bx bx-microphone text-xl'></i>
          </div>
          <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center">
            <i class='bx bx-world text-xl'></i>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Section formulaire -->
    <div class="w-full md:w-3/5 p-8 md:p-12">
      <!-- Titre mobile -->
      <div class="md:hidden mb-8 text-center">
        <img src="https://ik.imagekit.io/melfuviii/Arvest/assets/index/logo.png?updatedAt=1751244898356" 
             alt="Logo Arvest" 
             class="w-16 h-16 mx-auto mb-4">
        <h1 class="text-2xl font-bold text-gray-800">Arvest</h1>
        <p class="text-gray-600">Patrimoine Culturel Congolais</p>
      </div>
      
      <!-- Onglets -->
      <div class="tabs-container">
        <button id="tab-register" class="tab-button active">Créer un compte</button>
        <button id="tab-login" class="tab-button">Connexion</button>
      </div>
      
      <!-- Messages d'erreur -->
      <?php if (!empty($errors)): ?>
        <div class="error-message">
          <?php foreach ($errors as $error): ?>
            <p class="flex items-center"><i class='bx bx-error-circle mr-2'></i> <?= $error ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      
      <!-- Formulaire inscription -->
      <div id="register-section">
        <form method="POST" class="space-y-4">
          <div class="input-group">
            <i class='bx bx-user input-icon'></i>
            <input 
              type="text" 
              name="nom" 
              class="form-input"
              placeholder=" "
              required
            >
            <label class="floating-label">Nom complet</label>
          </div>
          
          <div class="input-group">
            <i class='bx bx-envelope input-icon'></i>
            <input 
              type="email" 
              name="email" 
              class="form-input"
              placeholder=" "
              required
            >
            <label class="floating-label">Adresse email</label>
          </div>
          
          <div class="input-group">
            <i class='bx bx-at input-icon'></i>
            <input 
              type="text" 
              name="pseudo" 
              class="form-input"
              placeholder=" "
              required
            >
            <label class="floating-label">Nom d'utilisateur</label>
          </div>
          
          <div class="input-group">
            <i class='bx bx-lock input-icon'></i>
            <input 
              type="password" 
              name="password" 
              class="form-input"
              placeholder=" "
              required
            >
            <label class="floating-label">Mot de passe (8+ caractères)</label>
          </div>
          
          <div class="input-group">
            <i class='bx bx-lock input-icon'></i>
            <input 
              type="password" 
              name="confirm_password" 
              class="form-input"
              placeholder=" "
              required
            >
            <label class="floating-label">Confirmer le mot de passe</label>
          </div>
          
          <div class="flex items-center mb-6">
            <input 
              type="checkbox" 
              id="terms" 
              name="terms" 
              required
              class="checkbox-orange w-5 h-5"
            >
            <label for="terms" class="ml-3 text-sm text-gray-600">
              J'accepte les <a href="#" class="link-orange">conditions d'utilisation</a>
            </label>
          </div>
          
          <button 
            type="submit" 
            name="register"
            class="btn-primary w-full"
          >
            S'inscrire
          </button>
        </form>
        
        <div class="mt-6 text-center">
          <p class="text-gray-600 text-sm">
            En vous inscrivant, vous acceptez notre 
            <a href="#" class="link-orange">Politique de confidentialité</a>
          </p>
        </div>
      </div>
      
      <!-- Formulaire connexion -->
      <div id="login-section" class="hidden">
        <form method="POST" class="space-y-4">
          <div class="input-group">
            <i class='bx bx-user input-icon'></i>
            <input 
              type="text" 
              name="identifier" 
              class="form-input"
              placeholder=" "
              required
            >
            <label class="floating-label">Email ou nom d'utilisateur</label>
          </div>
          
          <div class="input-group">
            <i class='bx bx-lock input-icon'></i>
            <input 
              type="password" 
              name="password" 
              class="form-input"
              placeholder=" "
              required
            >
            <label class="floating-label">Mot de passe</label>
          </div>
          
          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
              <input 
                type="checkbox" 
                id="remember" 
                name="remember"
                class="checkbox-orange w-5 h-5"
              >
              <label for="remember" class="ml-3 text-sm text-gray-600">Se souvenir de moi</label>
            </div>
            <a href="#" class="text-sm link-orange">Mot de passe oublié ?</a>
          </div>
          
          <button 
            type="submit" 
            name="login"
            class="btn-primary w-full"
          >
            Se connecter
          </button>
        </form>
        
        <div class="divider">
          <span>Ou connectez-vous avec</span>
        </div>
        
        <div class="grid grid-cols-2 gap-3">
          <button class="social-btn">
            <i class='bx bxl-google text-lg text-red-500'></i> Google
          </button>
          <button class="social-btn">
            <i class='bx bxl-facebook text-lg text-blue-600'></i> Facebook
          </button>
        </div>
        
        <div class="mt-8 text-center">
          <p class="text-gray-600">
            Vous n'avez pas de compte ? 
            <a href="#" id="switch-to-register" class="link-orange font-semibold">S'inscrire</a>
          </p>
        </div>
      </div>
    </div>
  </div>
  
  <script>
    // Gestion des onglets
    const tabRegister = document.getElementById('tab-register');
    const tabLogin = document.getElementById('tab-login');
    const registerSection = document.getElementById('register-section');
    const loginSection = document.getElementById('login-section');
    const switchToRegister = document.getElementById('switch-to-register');
    
    // Fonction pour activer un onglet
    function activateTab(tab) {
      // Désactiver tous les onglets
      tabRegister.classList.remove('active');
      tabLogin.classList.remove('active');
      registerSection.classList.add('hidden');
      loginSection.classList.add('hidden');
      
      // Activer l'onglet sélectionné
      tab.classList.add('active');
      
      // Afficher la section correspondante
      if (tab === tabRegister) {
        registerSection.classList.remove('hidden');
      } else {
        loginSection.classList.remove('hidden');
      }
    }
    
    // Événements
    tabRegister.addEventListener('click', () => activateTab(tabRegister));
    tabLogin.addEventListener('click', () => activateTab(tabLogin));
    switchToRegister.addEventListener('click', () => activateTab(tabRegister));
    
    // Gestion des labels flottants
    document.querySelectorAll('.form-input').forEach(input => {
      // Initialiser l'état des labels
      if (input.value !== '') {
        input.nextElementSibling.classList.add('top-0', 'text-xs');
      }
      
      input.addEventListener('focus', () => {
        input.nextElementSibling.classList.add('top-0', 'text-xs');
      });
      
      input.addEventListener('blur', () => {
        if (input.value === '') {
          input.nextElementSibling.classList.remove('top-0', 'text-xs');
        }
      });
    });
  </script>
</body>
</html>