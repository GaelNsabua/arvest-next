<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="./styles/output.css">
</head>
<body class="bg-gray-100 font-poppins min-h-screen">
  <?php include './components/navbar.php'; ?>

  <!-- Bandeau de couverture -->
  <div class="relative">
    <img class="w-full object-cover object-cover h-64 lg:h-[50vh]" src="../assets/profile/cover.jpg" alt="Bannière de couverture" class="h-48 w-full object-cover">
  </div>
  <!-- Contenu principal de la page de profil -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 px-4 py-6 md:translate-y-8 relative z-10 rounded-lg shadow-lg">
    <!-- Carte de profil utilisateur -->
    <aside class="md:col-span-1 bg-white p-4 shadow rounded -translate-y-48" role="profile">
      <section class="text-center mt-6">
        <!-- Photo de profil -->
         <div class="w-28 h-28 rounded-full mx-auto mb-3 aspect-ssquare">
            <img class="w-full h-full object-cover rounded-full" src="../assets/profile/profile.jpg" alt="Photo de profil">
            <button class="relative w-8 h-8 -top-5 left-6 z-20 bg-gray-200 text-red-500 text-sm p-auto border-gray-500 border-2 rounded-full shadow-lg hover:bg-gray-500 hover:text-white transiton-all duration-300"><i class='bx bx-camera text-lg'></i></button>
         </div>

         <!-- Nom et rôle -->
          <h2 class="text-xl text-gray-900 font-semibold">Emmanuel Nsabua</h2>
          <p class="text-gray-500 text-sm">124 followers | 100 suivis </p>
      </section>

      <!-- Statistique de l'utilisateur -->
      <div class="mt-6 grid grid-cols-3 text-center">
        <div>
          <p class="text-xl font-bold text-gray-900">26</p>
          <p class="text-sm text-gray-500">Projets</p>
        </div>
        <div>
          <p class="text-xl font-bold text-gray-900">88</p>
          <p class="text-sm text-gray-500">Tâches</p>
        </div>
        <div>
          <p class="text-xl font-bold text-gray-900">12</p>
          <p class="text-sm text-gray-500">Publications</p>
        </div>
      </div>

      <!-- Boutons d'action -->
      <div class="mt-6 flex justify-center gap-4">
        <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-800 transition-all duration-300"><i class='bx  bx-edit-alt'></i> Modifier le profil</button>
        <button class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-400 transition-all duration-300"><i class='bx  bx-cog'></i> Paramètres</button>
      </div>

      <!-- Menu vertical de navigation -->
      <nav class="mt-6">
        <ul class="flex flex-col mt-6 gap-2 text-gray-500">
          <li class="bg-blue-100 text-blue-500 font-semibold border-l-4 border-blue-500 pl-4 py-2 rounded">
            <i class='bx bx-home text-lg mr-2'></i><a href="#" aria-current="page">Overview</a>
          </li>
          <li class="hover:bg-gray-100 hover:text-blue-500 pl-4 py-2 rounded transition-all duration-300">
            <i class='bx bx-bar-chart text-lg mr-2'></i> <a href="#">Dashboard</a>
          </li>
          <li class="hover:bg-gray-100 hover:text-blue-500 pl-4 py-2 rounded transition-all duration-300">
            <i class='bx bx-info-circle text-lg mr-2'></i><a href="#">Help</a>
          </li>
        </ul> 
      </nav>
    </aside>

    <!-- Bloc principal des publications -->
    <main class="md:col-span-2 bg-white p-4 shadow rounded -mt-48" role="main">
      
       <section class="bg-white p-4 rounded-lg shadow mb-6">
        <div class="flex items-start gap-4">
          <!-- Avatar -->
          <img src="../assets/profile/profile.jpg" alt="avatar" class="w-10 h-10 rounded-full object-cover">

          <!-- Bloc avec What's going on? -->
          <div class="flex-1">
            <textarea placeholder="Que voulez-vous raconter ?" rows="3"
              class="w-full bg-gray-100 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>

           <!-- Bouton publier -->
            <div class="flex justify-end mt-2">
              <button class="mt-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-800 transition-all duration-300">
                <i class='bx bx-send'></i> Publier
              </button>
            </div>
          </div>
        </div>
       </section>
      <!-- Publications récentes -->
       <section class="bg-white p-4 rounded-lg shadow mt-8">
          <h2 class="text-lg font-semibold text-gray-800 mb-4">Mes projets récents</h2>

          <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <!-- Carte 1 -->
            <div class="bg-gray-100 rounded-lg overflow-hidden shadow hover:shadow-md transition">
              <img src="../assets/posts/wall-painting.jpg" alt="Wall Painting" class="w-full h-40 object-cover">
              <div class="p-3">
                <h3 class="text-sm font-semibold text-gray-800">Wall Painting</h3>
                <p class="text-xs text-gray-500">angela56</p>
              </div>
            </div>
          
            <!-- Carte 2 -->
            <div class="bg-gray-100 rounded-lg overflow-hidden shadow hover:shadow-md transition">
              <img src="../assets/posts/light-painting.jpg" alt="Light Painting" class="w-full h-40 object-cover">
              <div class="p-3">
                <h3 class="text-sm font-semibold text-gray-800">Light Painting</h3>
                <p class="text-xs text-gray-500">angela56</p>
              </div>
            </div>
          
            <!-- Carte 3 -->
            <div class="bg-gray-100 rounded-lg overflow-hidden shadow hover:shadow-md transition">
              <img src="../assets/posts/colorful-world.jpg" alt="Colorful World" class="w-full h-40 object-cover">
              <div class="p-3">
                <h3 class="text-sm font-semibold text-gray-800">Colorful World</h3>
                <p class="text-xs text-gray-500">angela56</p>
              </div>
            </div>
          
            <!-- Carte 4 -->
            <div class="bg-gray-100 rounded-lg overflow-hidden shadow hover:shadow-md transition">
              <img src="../assets/posts/face-painting.jpg" alt="Face Painting" class="w-full h-40 object-cover">
              <div class="p-3">
                <h3 class="text-sm font-semibold text-gray-800">Face Painting</h3>
                <p class="text-xs text-gray-500">angela56</p>
              </div>
            </div>
          
            <!-- Carte 5 -->
            <div class="bg-gray-100 rounded-lg overflow-hidden shadow hover:shadow-md transition">
              <img src="../assets/posts/art-of-myth.jpg" alt="Art Of Myth" class="w-full h-40 object-cover">
              <div class="p-3">
                <h3 class="text-sm font-semibold text-gray-800">Art Of Myth</h3>
                <p class="text-xs text-gray-500">angela56</p>
              </div>
            </div>
          
            <!-- Carte 6 -->
            <div class="bg-gray-100 rounded-lg overflow-hidden shadow hover:shadow-md transition">
              <img src="../assets/posts/the-mindblowing.jpg" alt="The Mindblowing" class="w-full h-40 object-cover">
              <div class="p-3">
                <h3 class="text-sm font-semibold text-gray-800">The Mindblowing</h3>
                <p class="text-xs text-gray-500">angela56</p>
              </div>
            </div>
          </div>
        
          <!-- Bouton charger plus -->
          <div class="text-center mt-6">
            <button class="px-6 py-2 bg-gray-200 text-sm text-gray-700 rounded hover:bg-gray-300 transition">
              Charger plus
            </button>
          </div>
        </section>

    </main>

    <!-- Colonne de droite : suggestions -->
    <aside class="md:col-span-1 hidden lg:block bg-white p-4 shadow rounded -mt-48" role="contacts">
      <!-- Liste des suggestions -->
       <section>
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Suggestions</h2>

        <ul class="space-y-4">
          <!-- Suggestion 1 -->
          <li class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <img src="https://randomuser.me/api/portraits/women/65.jpg" alt="Profil" class="w-10 h-10 rounded-full object-cover">
              <div>
                <p class="text-sm font-semibold text-gray-800">Sophie M.</p>
                <p class="text-xs text-gray-500">UI Designer</p>
              </div>
            </div>
            <button class="text-sm text-blue-600 hover:underline">Suivre</button>
          </li>
        
          <!-- Suggestion 2 -->
          <li class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Profil" class="w-10 h-10 rounded-full object-cover">
              <div>
                <p class="text-sm font-semibold text-gray-800">Lucas B.</p>
                <p class="text-xs text-gray-500">Développeur React</p>
              </div>
            </div>
            <button class="text-sm text-blue-600 hover:underline">Suivre</button>
          </li>
        
          <!-- Suggestion 3 -->
          <li class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Profil" class="w-10 h-10 rounded-full object-cover">
              <div>
                <p class="text-sm font-semibold text-gray-800">Nadia K.</p>
                <p class="text-xs text-gray-500">Photographe</p>
              </div>
            </div>
            <button class="text-sm text-blue-600 hover:underline">Suivre</button>
          </li>
        </ul>
      </section>

    </aside>
  </div>

  <?php include './components/footer.php'; ?>

  <script type="module" src="../js/script.js"></script>
</body>
</html