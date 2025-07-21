<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Résidence Giryad</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">
    <style>
        @font-face {
            font-family: 'Inter';
            src: url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        }
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">
    <header class="bg-white text-white shadow-lg">
        <div class="relative overflow-hidden">
            <div class="flex transition-transform duration-500 ease-in-out" id="carousel">
                <img src="{{ asset('images/residence-1.png') }}" alt="Résidence Giryad 1" class="w-full h-48 object-cover">
                <img src="{{ asset('images/residence-2.png') }}" alt="Résidence Giryad 2" class="w-full h-48 object-cover">
                <img src="{{ asset('images/residence-3.jpg') }}" alt="Résidence Giryad 3" class="w-full h-48 object-cover">
            </div>
        </div>
        <nav class="bg-giryad-medium-blue py-3">
            <div class="container mx-auto px-6 flex justify-between items-center">
                <div class="space-x-6">
                    @auth
                        @if (!in_array(Route::currentRouteName(), ['responses.create', 'forms.show']))
                            <a href="{{ route('forms.index') }}" class="text-white hover:text-giryad-green transition font-medium">Formulaires</a>
                            <a href="{{ route('dashboard') }}" class="text-white hover:text-giryad-green transition font-medium">Tableau de bord</a>
                        @endif
                    @else
                        @if (!in_array(Route::currentRouteName(), ['responses.create', 'forms.show']))
                            <a href="{{ route('login') }}" class="text-white hover:text-giryad-green transition font-medium">Connexion</a>
                        @endif
                    @endauth
                </div>
                @auth
                    @if (!in_array(Route::currentRouteName(), ['responses.create', 'forms.show']))
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-white hover:text-giryad-green transition font-medium">Déconnexion</button>
                        </form>
                    @endif
                @endauth
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-6 py-8 max-w-5xl">
        @yield('content')
        @guest
            @if (!in_array(Route::currentRouteName(), ['responses.create', 'forms.show']))
                <div class="text-center mt-8">
                    <h2 class="text-2xl font-bold text-indigo-600 mb-4">Bienvenue sur Résidence Giryad</h2>
                    <p class="text-gray-600 mb-6">Participez à notre enquête de satisfaction pour une meilleure expérience !</p>
                    <a href="{{ route('login') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition duration-300">Connexion</a>
                </div>
            @endif
        @endguest
    </main>

    <footer class="bg-giryad-dark-blue text-white py-6 mt-12">
        <div class="container mx-auto px-6 text-center">
            <p class="text-lg">Résidence Giryad - Enquêtes de satisfaction pour une vie meilleure</p>
            <p class="text-sm mt-2">© 2025 Résidence Giryad. Tous droits réservés.</p>
        </div>
    </footer>

</body>
</html>