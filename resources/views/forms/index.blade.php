@extends('layouts.app')

@section('title', 'Liste des Formulaires')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-6 mx-auto max-w-4xl">
        <div class="bg-giryad-dark-blue text-white p-4 rounded-t-lg">
            <h2 class="text-3xl font-bold">Liste des Formulaires</h2>
        </div>
        <div class="mt-6">
            <a href="{{ route('forms.create') }}" class="px-4 py-2 bg-giryad-green text-white rounded-lg hover:bg-green-700">Créer un nouveau formulaire</a>
            <div class="mt-4">
                @foreach($forms as $form)
                    <div class="bg-gray-50 p-4 rounded-lg mb-4 border-l-4 border-giryad-medium-blue">
                        <h3 class="text-xl font-semibold text-giryad-dark-blue">{{ $form->title }}</h3>
                        <p class="text-gray-600">{{ $form->description }}</p>
                        <div class="mt-2 flex items-center space-x-4">
                            <a href="{{ route('forms.show', $form->slug) }}" class="text-giryad-green hover:underline">Voir</a>
                            <a href="{{ route('forms.edit', $form->slug) }}" class="text-giryad-medium-blue hover:underline">Modifier</a>
                            <button type="button" class="text-giryad-medium-blue hover:underline cursor-pointer" onclick="navigator.clipboard.writeText('{{ route('forms.show', $form->slug) }}'); alert('Lien copié : {{ route('forms.show', $form->slug) }}')">Copier le lien</button>
                            <a href="{{ route('forms.responses', $form->slug) }}" class="text-giryad-dark-blue hover:underline">Voir les Réponses</a>
                            <button onclick="downloadQRCode('{{ $form->slug }}')" class="text-giryad-green hover:underline">Télécharger le QR Code</button>
                        </div>
                    </div>
                @endforeach
                {{ $forms->links() }}
            </div>
        </div>
    </div>
    <script>
        function downloadQRCode(slug) {
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent('{{ route('forms.show', ':slug') }}'.replace(':slug', slug))}`;
            const link = document.createElement('a');
            link.href = qrUrl;
            link.download = `qr-code-${slug}.png`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
@endsection