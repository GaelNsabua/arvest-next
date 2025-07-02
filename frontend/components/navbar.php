<header class="fixed top-0 left-0 w-full z-50 bg-white/90 shadow-sm backdrop-blur transition-all">
  <div class="max-w-6xl mx-auto flex items-center justify-between px-4 md:px-8 h-16">
    <!-- Logo -->
    <div class="flex items-center">
      <a href="index.php">
        <img src="../assets/logo.png" alt="Logo Arvest" class="h-10 w-auto" />
      </a>
    </div>
    <!-- Navigation links -->
    <nav class="hidden md:flex gap-8 text-gray-700 font-medium">
      <a href="index.php" class="hover:text-orange-600 transition">Accueil</a>
      <a href="bibliotheque.php" class="hover:text-orange-600 transition">Bibliothèque</a>
      <a href="about.php" class="hover:text-orange-600 transition">À propos</a>
    </nav>
    <!-- Actions -->
    <div class="hidden md:flex items-center gap-4">
      <div class="relative">
        <input type="text" placeholder="Rechercher..." class="pl-10 pr-3 py-2 rounded-full bg-gray-100 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400 transition w-48" />
        <i class="bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
      </div>
      <form action="auth.php" method="get">
        <button class="flex items-center gap-2 px-5 py-2 rounded-full bg-orange-500 text-white font-semibold hover:bg-orange-600 transition">
          <i class="bx bx-log-in"></i> Connexion
        </button>
      </form>
    </div>
    <!-- Mobile icons -->
    <div class="flex md:hidden items-center gap-2">
      <button id="search-toggle" class="p-2 rounded-full hover:bg-gray-100 transition">
        <i class="bx bx-search text-2xl text-gray-700"></i>
      </button>
      <button id="burger-menu" class="p-2 rounded-full hover:bg-gray-100 transition">
        <i class="bx bx-menu text-3xl text-gray-700"></i>
      </button>
    </div>
  </div>
  <!-- Mobile menu (hidden by default, show with JS) -->
  <div id="mobile-menu" class="md:hidden hidden bg-white shadow-lg">
    <nav class="flex flex-col gap-2 px-6 py-4 text-gray-700 font-medium">
      <a href="index.php" class="py-2 hover:text-orange-600 transition">Accueil</a>
      <a href="bibliotheque.php" class="py-2 hover:text-orange-600 transition">Bibliothèque</a>
      <a href="about.php" class="py-2 hover:text-orange-600 transition">À propos</a>
      <form action="auth.php" method="get" class="mt-2">
        <button class="w-full flex items-center gap-2 px-5 py-2 rounded-full bg-orange-500 text-white font-semibold hover:bg-orange-600 transition">
          <i class="bx bx-log-in"></i> Connexion
        </button>
      </form>
      <div class="relative mt-2">
        <input type="text" placeholder="Rechercher..." class="pl-10 pr-3 py-2 rounded-full bg-gray-100 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400 transition w-full" />
        <i class="bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
      </div>
    </nav>
  </div>
  <script type="module" src="./scripts/navbar.js"></script>
</header>