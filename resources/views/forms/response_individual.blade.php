@extends('layouts.app')

@section('title', 'Réponse #' . ($index + 1) . ' - ' . $form->title)

@section('content')
    <div class="bg-white shadow-2xl rounded-xl p-8">
        <h2 class="text-3xl font-bold text-giryad-dark-blue mb-6">Réponse #{{ $index + 1 }} pour {{ $form->title }}</h2>
        <p class="text-gray-600 mb-6">Soumis le {{ $response->created_at->format('d/m/Y H:i') }} par {{ $response->user_id ? 'Utilisateur #' . $response->user_id : 'Anonyme' }}</p>
        @if($response->answers && $response->answers->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full border-collapse bg-white rounded-lg shadow-md">
                    <thead>
                        <tr class="bg-giryad-medium-blue text-white">
                            <th class="p-4 text-left font-semibold rounded-tl-lg">Question</th>
                            <th class="p-4 text-left font-semibold rounded-tr-lg">Réponse</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($form->questions as $question)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                <td class="p-4 text-giryad-dark-blue font-medium">{{ $question->text }}
                                    @if ($question->is_required)
                                        <span class="text-red-500">*</span>
                                    @endif
                                </td>
                                <td class="p-4 text-gray-700">
                                    @php
                                        $answer = $response->answers->where('question_id', $question->id)->first();
                                    @endphp
                                    @if($answer)
                                        @if(in_array($question->question_type_id, [1, 2]))
                                            <span class="block bg-gray-100 p-2 rounded-md">{{ nl2br(e($answer->response)) }}</span>
                                        @elseif(in_array($question->question_type_id, [3, 5]))
                                            <span class="block bg-gray-100 p-2 rounded-md">{{ $answer->response }}</span>
                                        @elseif($question->question_type_id == 4)
                                            <ul class="list-disc list-inside space-y-1">
                                                @foreach(is_array($answer->response) ? $answer->response : [$answer->response] as $option)
                                                    <li>{{ $option }}</li>
                                                @endforeach
                                            </ul>
                                        @elseif(in_array($question->question_type_id, [6, 7]))
                                            <div class="overflow-x-auto">
                                                <table class="w-full border-collapse bg-gray-50 rounded-md mt-2">
                                                    <thead>
                                                        <tr class="bg-gray-100">
                                                            <th class="p-2 text-left text-giryad-dark-blue font-semibold"></th>
                                                            @foreach($question->columns as $column)
                                                                <th class="p-2 text-left text-giryad-dark-blue font-semibold">{{ $column }}</th>
                                                            @endforeach
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $responseData = is_array($answer->response) ? $answer->response : json_decode($answer->response, true) ?? [];
                                                        @endphp
                                                        @foreach($question->rows as $row)
                                                            <tr class="border-t border-gray-200">
                                                                <td class="p-2 text-gray-700">{{ $row }}</td>
                                                                @foreach($question->columns as $column)
                                                                    <td class="p-2">
                                                                        <input type="{{ $question->question_type_id == 6 ? 'checkbox' : 'radio' }}"
                                                                               class="h-5 w-5 text-giryad-green focus:ring-giryad-green"
                                                                               {{ isset($responseData[$row]) && (is_array($responseData[$row]) ? in_array($column, $responseData[$row]) : $responseData[$row] == $column) ? 'checked' : '' }}
                                                                               disabled>
                                                                    </td>
                                                                @endforeach
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @elseif(in_array($question->question_type_id, [8, 9]))
                                            <span class="block bg-gray-100 p-2 rounded-md">{{ $answer->response }}</span>
                                        @endif
                                    @else
                                        <span class="text-gray-500 italic">Aucune réponse</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-600 text-center py-6 bg-gray-100 rounded-md">Aucune réponse disponible pour ce formulaire.</p>
        @endif
        <div class="mt-8 flex space-x-4">
            <a href="{{ route('forms.responses', $form->slug) }}"
               class="px-6 py-3 bg-giryad-medium-blue text-white rounded-lg hover:bg-giryad-dark-blue transition duration-300 flex items-center shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Retour aux réponses
            </a>
        </div>
    </div>
@endsection
