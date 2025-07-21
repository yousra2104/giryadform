@extends('layouts.app')

@section('title', 'Réponses pour ' . $form->title)

@section('content')
    <div class="bg-white shadow-2xl rounded-xl p-8">
        <h2 class="text-3xl font-bold text-giryad-dark-blue mb-6">Réponses pour {{ $form->title }}</h2>
        <p class="text-gray-600 mb-6">Nombre total de réponses : {{ $form->responses_count ?? $responses->total() }}</p>
        @if($responses->isEmpty())
            <p class="text-gray-600 text-center py-6 bg-gray-100 rounded-md">Aucune réponse pour ce formulaire.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full border-collapse bg-white rounded-lg shadow-md">
                    <thead>
                        <tr class="bg-giryad-medium-blue text-white">
                            <th class="p-4 text-left font-semibold rounded-tl-lg">Numéro</th>
                            <th class="p-4 text-left font-semibold">Utilisateur</th>
                            <th class="p-4 text-left font-semibold">Date de soumission</th>
                            <th class="p-4 text-left font-semibold rounded-tr-lg">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($responses as $response)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                <td class="p-4">{{ ($responses->currentPage() - 1) * $responses->perPage() + $loop->iteration }}</td>
                                <td class="p-4">
                                    {{ $response->user ? ($response->user->name ?? 'Utilisateur #' . $response->user_id) : 'Anonyme' }}
                                </td>
                                <td class="p-4">{{ $response->created_at ? $response->created_at->format('d/m/Y H:i') : 'Non défini' }}</td>
                                <td class="p-4">
                                    <a href="{{ route('forms.response', [$form->slug, $response->id]) }}"
                                       class="text-giryad-dark-blue hover:text-giryad-green transition font-medium">Voir</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">
                {{ $responses->links('pagination::tailwind') }}
            </div>
        @endif
        <div class="mt-8 flex space-x-4">
           
            <a href="{{ route('forms.index') }}"
               class="px-6 py-3 bg-giryad-medium-blue text-white rounded-lg hover:bg-giryad-dark-blue transition duration-300 flex items-center shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Retour aux formulaires
            </a>
        </div>
    </div>
@endsection
