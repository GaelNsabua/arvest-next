<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>À propos - ARVEST</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="./styles/output.css" />
</head>
<body class="bg-[#f5f8fa] font-poppins">

  <!-- NAVBAR dynamique -->
  <?php include './components/navbar.php'; ?>

  <!-- Hero Section -->
  <section class="bg-white pt-12 pb-8">
    <div class="max-w-5xl mx-auto mt-16 flex flex-col md:flex-row items-center gap-8 px-6">
      <div class="flex-1">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4 leading-tight">
          Nous créons des solutions <span class="text-orange-600">pour la culture</span>
        </h1>
        <p class="text-lg text-gray-600 mb-6">
          Notre équipe s'engage à valoriser, préserver et partager le patrimoine oral africain à travers la technologie et la créativité.
        </p>
        <div class="flex gap-4">
          <a href="explore.html" class="px-6 py-3 bg-orange-500 text-white rounded-full font-semibold shadow hover:bg-orange-600 transition">Découvrir</a>
          <a href="#services" class="flex items-center gap-2 text-orange-600 font-semibold hover:underline">
            <i class='bx bx-chevron-down text-2xl'></i> En savoir plus
          </a>
        </div>
      </div>
      <div class="flex-1 flex justify-center">
        <img src="../assets/about/about-hero.jpg" alt="Illustration" class="max-w-xs md:max-w-md rounded-lg shadow-lg">
      </div>
    </div>
  </section>

  <!-- Services Section -->
  <section id="services" class="py-14 bg-[#f9fafb]">
    <div class="max-w-5xl mx-auto px-6">
      <h2 class="text-3xl font-bold text-center text-gray-900 mb-10">Nous offrons les meilleurs <span class="text-orange-600">services</span></h2>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center text-center">
          <div class="bg-orange-100 text-orange-600 rounded-full p-3 mb-4">
            <i class='bx bx-book-open text-3xl'></i>
          </div>
          <h3 class="font-semibold text-lg mb-2">Lecture libre</h3>
          <p class="text-gray-500 text-sm">Accédez gratuitement à des contes, proverbes et récits africains.</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center text-center">
          <div class="bg-blue-100 text-blue-600 rounded-full p-3 mb-4">
            <i class='bx bx-upload text-3xl'></i>
          </div>
          <h3 class="font-semibold text-lg mb-2">Contribution</h3>
          <p class="text-gray-500 text-sm">Partagez vos propres œuvres et enrichissez la bibliothèque ARVEST.</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center text-center">
          <div class="bg-green-100 text-green-600 rounded-full p-3 mb-4">
            <i class='bx bx-group text-3xl'></i>
          </div>
          <h3 class="font-semibold text-lg mb-2">Communauté</h3>
          <p class="text-gray-500 text-sm">Rejoignez une communauté de passionnés et échangez autour de la culture.</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center text-center">
          <div class="bg-purple-100 text-purple-600 rounded-full p-3 mb-4">
            <i class='bx bx-shield-quarter text-3xl'></i>
          </div>
          <h3 class="font-semibold text-lg mb-2">Préservation</h3>
          <p class="text-gray-500 text-sm">Contribuez à la sauvegarde du patrimoine oral africain pour les générations futures.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Simple Solutions Section -->
  <section class="py-14 bg-orange-50">
    <div class="max-w-5xl mx-auto flex flex-col md:flex-row items-center gap-10 px-6">
      <div class="flex-1 flex justify-center">
        <img src="../assets/about/about-solution.jpg" alt="Solutions Illustration" class="max-w-xs md:max-w-md rounded-md shadow-lg">
      </div>
      <div class="flex-1">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Des solutions <span class="text-orange-600">simples</span> et accessibles</h2>
        <ul class="space-y-4 mb-6 text-gray-700">
          <li><span class="font-bold text-orange-600">1.</span> Créez un compte gratuitement</li>
          <li><span class="font-bold text-orange-600">2.</span> Découvrez et lisez des œuvres</li>
          <li><span class="font-bold text-orange-600">3.</span> Partagez vos propres histoires</li>
          <li><span class="font-bold text-orange-600">4.</span> Interagissez avec la communauté</li>
        </ul>
        <div class="flex gap-4">
          <a href="register.html" class="px-6 py-3 bg-orange-500 text-white rounded-full font-semibold shadow hover:bg-orange-600 transition">Commencer</a>
          <a href="contact.html" class="px-6 py-3 border border-orange-500 text-orange-600 rounded-full font-semibold hover:bg-orange-50 transition">En savoir plus</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Agency Section -->
  <section class="py-14 bg-white">
    <div class="max-w-5xl mx-auto flex flex-col md:flex-row items-center gap-10 px-6">
      <div class="flex-1">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Notre <span class="text-orange-600">mission</span></h2>
        <p class="text-gray-700 mb-6">
          ARVEST vise à rendre la culture orale africaine accessible à tous, partout et à tout moment. Nous croyons en la force du partage, de la transmission et de la valorisation de nos histoires.
        </p>
        <a href="contact.html" class="px-6 py-3 bg-orange-500 text-white rounded-full font-semibold shadow hover:bg-orange-600 transition">Contactez-nous</a>
      </div>
      <div class="flex-1 flex justify-center">
        <img src="../assets/about/about-mission.jpg" alt="Mission Illustration" class="max-w-xs md:max-w-md">
      </div>
    </div>
  </section>

  <!-- Testimonials Section -->
  <section class="py-14 bg-[#f9fafb]">
    <div class="max-w-5xl mx-auto px-6">
      <h2 class="text-3xl font-bold text-center text-gray-900 mb-10">Ce que disent <span class="text-orange-600">nos membres</span></h2>
      <div class="flex flex-col md:flex-row gap-8 justify-center">
        <div class="bg-white rounded-xl shadow p-6 flex-1 max-w-md">
          <div class="flex items-center gap-4 mb-4">
            <img src="../assets/profile/profile.jpg" alt="Profil" class="w-12 h-12 rounded-full object-cover">
            <div>
              <p class="font-semibold text-gray-800">Emmanuel Nsabua</p>
              <p class="text-xs text-gray-500">Contributeur</p>
            </div>
          </div>
          <p class="text-gray-600 italic">"Grâce à ARVEST, j'ai pu partager les contes de mon enfance et découvrir ceux d'autres cultures africaines."</p>
          <div class="flex gap-1 mt-3 text-orange-500">
            <i class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star-half'></i>
          </div>
        </div>
        <div class="bg-white rounded-xl shadow p-6 flex-1 max-w-md">
          <div class="flex items-center gap-4 mb-4">
            <img src="../assets/profile/profile2.jpg" alt="Profil" class="w-12 h-12 rounded-full object-cover">
            <div>
              <p class="font-semibold text-gray-800">Sophie M.</p>
              <p class="text-xs text-gray-500">Lectrice</p>
            </div>
          </div>
          <p class="text-gray-600 italic">"Une plateforme intuitive et inspirante pour transmettre la sagesse africaine."</p>
          <div class="flex gap-1 mt-3 text-orange-500">
            <i class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i><i class='bx bxs-star'></i>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Logos partenaires / ranked -->
  <section class="py-10 bg-white">
    <div class="max-w-5xl mx-auto px-6">
      <h3 class="text-xl font-bold text-center text-gray-800 mb-6">Best Ranked By</h3>
      <div class="flex justify-center gap-8 flex-wrap">
        <img src="../assets/logo.png" alt="Logo partenaire" class="h-12 w-auto opacity-80">
        <img src="../assets/logo.png" alt="Logo partenaire" class="h-12 w-auto opacity-80">
        <img src="../assets/logo.png" alt="Logo partenaire" class="h-12 w-auto opacity-80">
        <img src="../assets/logo.png" alt="Logo partenaire" class="h-12 w-auto opacity-80">
        <img src="../assets/logo.png" alt="Logo partenaire" class="h-12 w-auto opacity-80">
      </div>
    </div>
  </section>

  <!-- Call to Action -->
  <section class="py-10 bg-orange-500">
    <div class="max-w-5xl mx-auto flex flex-col md:flex-row items-center justify-between px-6">
      <h2 class="text-2xl font-bold text-white mb-4 md:mb-0">Prêt à rejoindre l'aventure ARVEST ?</h2>
      <a href="register.html" class="px-8 py-3 bg-white text-orange-600 rounded-full font-semibold shadow hover:bg-orange-100 transition">Créer un compte</a>
    </div>
  </section>

  <!-- FOOTER dynamique -->
  <?php include './components/footer.php'; ?>
</body>
</html>
