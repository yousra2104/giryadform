<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réponse #{{ $index + 1 }} pour {{ $form->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Réponse #{{ $index + 1 }} pour "{{ $form->title }}"</h1>
        <div class="bg-white shadow-md rounded-lg p-6">
            <form class="space-y-4">
                @foreach($form->questions as $question)
                    <div>
                        <label class="block text-gray-700 font-semibold">{{ $question['title'] }}</label>
                        <input type="text" value="{{ $response->answers[$question['id']] ?? 'Pas de réponse' }}"
                               class="w-full p-2 border rounded bg-gray-100" readonly>
                    </div>
                @endforeach
            </form>
            <div class="mt-6 flex justify-between">
                @if($index > 0)
                    <a href="{{ route('forms.response', [$form->slug, $index - 1]) }}"
                       class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Précédent</a>
                @else
                    <span></span>
                @endif
                @if($index < $totalResponses - 1)
                    <a href="{{ route('forms.response', [$form->slug, $index + 1]) }}"
                       class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Suivant</a>
                @endif
            </div>
        </div>
        <a href="{{ route('forms.responses', $form->slug) }}" class="mt-4 inline-block text-blue-600 hover:underline">
            Retour à la liste des réponses
        </a>
    </div>
</body>
</html>
