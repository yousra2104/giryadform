<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enquête de satisfaction - {{ $form->title }} - Résidence Giryad</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/heroicons@2.0.13/dist/heroicons.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans antialiased">
    <header class="bg-[var(--color-giryad-dark-blue)] text-white py-4 shadow-lg">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold flex items-center">
                <svg class="w-8 h-8 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Résidence Giryad
            </h1>
        </div>
    </header>
    <div class="container mx-auto px-6 py-8 max-w-5xl">
        <h2 class="text-3xl font-semibold text-[var(--color-giryad-dark-blue)] mb-6">{{ $form->title }}</h2>
        @if($form->description)
            <p class="text-gray-600 mb-6 text-lg">{{ $form->description }}</p>
        @endif
        <div class="bg-white shadow-lg rounded-lg p-6">
            <form method="POST" action="{{ route('responses.store', $form->slug) }}" class="space-y-6">
                @csrf
                @foreach($form->questions as $question)
                    <div class="border-l-4 border-[var(--color-giryad-medium-blue)] pl-4">
                        <label class="block text-[var(--color-giryad-dark-blue)] font-semibold text-lg mb-2">
                            {{ $question->text }} @if($question->is_required)<span class="text-red-500">*</span>@endif
                        </label>
                        @if($question->questionType->name === 'short answer')
                            <input type="text" name="answers[{{ $question->id }}]"
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--color-giryad-green)] focus:border-[var(--color-giryad-green)]"
                                   @if($question->is_required) required @endif>
                        @elseif($question->questionType->name === 'paragraph')
                            <textarea name="answers[{{ $question->id }}]"
                                      class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--color-giryad-green)] focus:border-[var(--color-giryad-green)]"
                                      rows="5" @if($question->is_required) required @endif></textarea>
                        @elseif($question->questionType->name === 'multiple choice')
                            @foreach($question->options ?? [] as $option)
                                <div class="flex items-center mb-2">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}"
                                           class="h-4 w-4 text-[var(--color-giryad-green)] focus:ring-[var(--color-giryad-green)] border-gray-300"
                                           @if($question->is_required) required @endif>
                                    <label class="ml-2 text-gray-700">{{ $option }}</label>
                                </div>
                            @endforeach
                        @elseif($question->questionType->name === 'checkboxes')
                            @foreach($question->options ?? [] as $option)
                                <div class="flex items-center mb-2">
                                    <input type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $option }}"
                                           class="h-4 w-4 text-[var(--color-giryad-green)] focus:ring-[var(--color-giryad-green)] border-gray-300">
                                    <label class="ml-2 text-gray-700">{{ $option }}</label>
                                </div>
                            @endforeach
                        @elseif($question->questionType->name === 'dropdown')
                            <select name="answers[{{ $question->id }}]"
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--color-giryad-green)] focus:border-[var(--color-giryad-green)]"
                                    @if($question->is_required) required @endif>
                                <option value="">Sélectionnez une option</option>
                                @foreach($question->options ?? [] as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                        @elseif($question->questionType->name === 'multiple choice grid')
                            <div class="grid gap-2">
                                @foreach($question->rows ?? [] as $rowIndex => $row)
                                    <div class="flex items-center">
                                        <span class="w-1/4 text-gray-700">{{ $row }}</span>
                                        <div class="flex space-x-4">
                                            @foreach($question->columns ?? [] as $columnIndex => $column)
                                                <div class="flex items-center">
                                                    <input type="radio" name="answers[{{ $question->id }}][{{ $rowIndex }}]" value="{{ $column }}"
                                                           class="h-4 w-4 text-[var(--color-giryad-green)] focus:ring-[var(--color-giryad-green)] border-gray-300"
                                                           @if($question->is_required) required @endif>
                                                    <label class="ml-2 text-gray-700">{{ $column }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @elseif($question->questionType->name === 'checkbox grid')
                            <div class="grid gap-2">
                                @foreach($question->rows ?? [] as $rowIndex => $row)
                                    <div class="flex items-center">
                                        <span class="w-1/4 text-gray-700">{{ $row }}</span>
                                        <div class="flex space-x-4">
                                            @foreach($question->columns ?? [] as $columnIndex => $column)
                                                <div class="flex items-center">
                                                    <input type="checkbox" name="answers[{{ $question->id }}][{{ $rowIndex }}][]" value="{{ $column }}"
                                                           class="h-4 w-4 text-[var(--color-giryad-green)] focus:ring-[var(--color-giryad-green)] border-gray-300">
                                                    <label class="ml-2 text-gray-700">{{ $column }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @elseif($question->questionType->name === 'date')
                            <input type="date" name="answers[{{ $question->id }}]"
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--color-giryad-green)] focus:border-[var(--color-giryad-green)]"
                                   @if($question->is_required) required @endif>
                        @elseif($question->questionType->name === 'time')
                            <input type="time" name="answers[{{ $question->id }}]"
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--color-giryad-green)] focus:border-[var(--color-giryad-green)]"
                                   @if($question->is_required) required @endif>
                        @endif
                        @error("answers.{$question->id}")
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
                <div class="mt-8">
                    <button type="submit"
                            class="px-6 py-3 bg-[var(--color-giryad-green)] text-white rounded-lg hover:bg-green-600 transition flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Soumettre la réponse
                    </button>
                </div>
            </form>
        </div>
    </div>
    <footer class="bg-[var(--color-giryad-dark-blue)] text-white py-4 mt-12">
        <div class="container mx-auto px-6 text-center">
            <p>Résidence Giryad - Enquêtes de satisfaction pour une vie meilleure</p>
            <p class="text-sm mt-2">© 2025 Résidence Giryad. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>