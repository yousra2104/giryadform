@extends('layouts.app')

@section('title', $form->title)

@section('content')
    <div class="bg-white shadow-2xl rounded-xl p-8">
        <h2 class="text-3xl font-bold text-giryad-dark-blue mb-6">{{ $form->title }}</h2>
        @if($form->description)
            <p class="text-gray-600 mb-6 text-lg bg-gray-100 p-4 rounded-md" style="white-space: pre-wrap;">{!! nl2br($form->description) !!}</p>
        @endif
        <form method="POST" action="{{ route('responses.store', $form->slug) }}" class="space-y-8" id="form-response">
            @csrf
            @foreach($form->questions as $question)
                <div class="border-l-4 border-giryad-medium-blue pl-6 bg-gray-50 p-6 rounded-lg">
                    <label class="block text-giryad-dark-blue font-semibold text-lg mb-3">
                        {{ $question->text }}
                        @if ($question->is_required)
                            <span class="text-red-500">*</span>
                        @endif
                    </label>
                    @if($question->question_type_id == 1) <!-- Short Answer -->
                        <input type="text" name="responses[{{ $question->id }}]"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-giryad-green focus:border-transparent transition"
                               {{ $question->is_required ? 'required' : '' }}
                               placeholder="Réponse courte">
                    @elseif($question->question_type_id == 2) <!-- Paragraph -->
                        <textarea name="responses[{{ $question->id }}]"
                                  class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-giryad-green focus:border-transparent transition h-32"
                                  {{ $question->is_required ? 'required' : '' }}
                                  placeholder="Entrez votre réponse ici..."></textarea>
                    @elseif($question->question_type_id == 8) <!-- Date -->
                        <input type="date" name="responses[{{ $question->id }}]"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-giryad-green focus:border-transparent transition"
                               {{ $question->is_required ? 'required' : '' }}>
                    @elseif($question->question_type_id == 9) <!-- Time -->
                        <input type="time" name="responses[{{ $question->id }}]"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-giryad-green focus:border-transparent transition"
                               {{ $question->is_required ? 'required' : '' }}>
                    @elseif($question->question_type_id == 3)
                        <div class="space-y-3">
                            @foreach($question->options as $option)
                                <label class="flex items-center">
                                    <input type="radio" name="responses[{{ $question->id }}]" value="{{ $option }}"
                                           class="h-5 w-5 text-giryad-green focus:ring-giryad-green" {{ $question->is_required ? 'required' : '' }}>
                                    <span class="ml-3 text-gray-700">{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>
                    @elseif($question->question_type_id == 4)
                        <div class="space-y-3">
                            @foreach($question->options as $option)
                                <label class="flex items-center">
                                    <input type="checkbox" name="responses[{{ $question->id }}][]" value="{{ $option }}"
                                           class="h-5 w-5 text-giryad-green focus:ring-giryad-green">
                                    <span class="ml-3 text-gray-700">{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>
                    @elseif($question->question_type_id == 5) <!-- Dropdown -->
                        <select name="responses[{{ $question->id }}]"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-giryad-green focus:border-transparent transition"
                                {{ $question->is_required ? 'required' : '' }}>
                            <option value="">Sélectionnez une option</option>
                            @foreach($question->options as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </select>
                    @elseif($question->question_type_id == 7)
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse bg-white rounded-lg shadow-sm">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="p-3 text-left text-giryad-dark-blue font-semibold"></th>
                                        @foreach($question->columns as $column)
                                            <th class="p-3 text-left text-giryad-dark-blue font-semibold">{{ $column }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($question->rows as $row)
                                        <tr class="border-t border-gray-200">
                                            <td class="p-3 text-gray-700">{{ $row }}</td>
                                            @foreach($question->columns as $column)
                                                <td class="p-3">
                                                    <input type="radio" name="responses[{{ $question->id }}][{{ $row }}]"
                                                           value="{{ $column }}" class="h-5 w-5 text-giryad-green focus:ring-giryad-green"
                                                           {{ $question->is_required ? 'required' : '' }}>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif($question->question_type_id == 6)
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse bg-white rounded-lg shadow-sm">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="p-3 text-left text-giryad-dark-blue font-semibold"></th>
                                        @foreach($question->columns as $column)
                                            <th class="p-3 text-left text-giryad-dark-blue font-semibold">{{ $column }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($question->rows as $row)
                                        <tr class="border-t border-gray-200">
                                            <td class="p-3 text-gray-700">{{ $row }}</td>
                                            @foreach($question->columns as $column)
                                                <td class="p-3">
                                                    <input type="checkbox" name="responses[{{ $question->id }}][{{ $row }}][]"
                                                           value="{{ $column }}" class="h-5 w-5 text-giryad-green focus:ring-giryad-green">
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    @error("responses.{$question->id}")
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach
            <div class="mt-8 flex space-x-4">
                <button type="submit"
                        class="px-6 py-3 bg-giryad-green text-white rounded-lg hover:bg-green-600 transition duration-300 flex items-center shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Soumettre le formulaire
                </button>
            </div>
        </form>
    </div>
    <script>
        document.getElementById('form-response').addEventListener('submit', function (e) {
            const formData = new FormData(this);
            console.log('Données du formulaire avant soumission :', Object.fromEntries(formData));
            let isValid = true;
            document.querySelectorAll('table').forEach(table => {
                const questionId = table.closest('div').querySelector('input[name*="responses"]').name.match(/responses\[(\d+)\]/)[1];
                const isRequired = table.closest('div').querySelector('label').innerHTML.includes('*');
                if (isRequired) {
                    const rows = table.querySelectorAll('tbody tr');
                    rows.forEach(row => {
                        const inputs = row.querySelectorAll('input[type="radio"], input[type="checkbox"]');
                        const rowName = inputs[0].name.match(/responses\[\d+\]\[(.*?)\]/)[1];
                        const isChecked = Array.from(inputs).some(input => input.checked);
                        if (!isChecked) {
                            isValid = false;
                            const error = document.createElement('p');
                            error.className = 'text-red-500 text-sm mt-2';
                            error.textContent = `Veuillez sélectionner une option pour la ligne "${rowName}".`;
                            table.parentElement.appendChild(error);
                        }
                    });
                }
            });
            if (!isValid) {
                e.preventDefault();
                alert('Veuillez répondre à toutes les questions obligatoires.');
            }
        });
    </script>
@endsection