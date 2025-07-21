@extends('layouts.app')

@section('title', 'Créer un formulaire')

@section('content')
    <div class="bg-white shadow-2xl rounded-xl p-8">
        <h2 class="text-3xl font-bold text-giryad-dark-blue mb-6">Créer un nouveau formulaire</h2>
        <p class="text-gray-600 mb-6 text-lg bg-gray-100 p-4 rounded-md">Créez une enquête de satisfaction pour la Résidence Giryad. Ajoutez des questions pour recueillir les avis des résidents.</p>
        <form method="POST" action="{{ route('forms.store') }}" class="space-y-8" id="form-create">
            @csrf
            <div class="border-l-4 border-giryad-medium-blue pl-6">
                <label for="title" class="block text-giryad-dark-blue font-semibold text-lg mb-2">Titre du formulaire <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title') }}"
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-giryad-green focus:border-transparent transition" required>
                @error('title')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="border-l-4 border-giryad-medium-blue pl-6">
                <label for="description" class="block text-giryad-dark-blue font-semibold text-lg mb-2">Description (optionnel)</label>
                <textarea name="description" id="description" rows="4"
                          class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-giryad-green focus:border-transparent transition">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="border-l-4 border-giryad-medium-blue pl-6">
                <h3 class="text-xl font-semibold text-giryad-dark-blue mb-4">Questions</h3>
                <div id="questions-container" class="space-y-6">
                    <div class="question-block bg-gray-50 p-6 rounded-lg">
                        <div class="flex items-center mb-3">
                            <label class="text-giryad-dark-blue font-semibold text-lg">Question 1</label>
                            <button type="button" class="ml-auto text-red-500 hover:text-red-700 remove-question" style="display: none;">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <input type="hidden" name="questions[0][order]" value="1">
                        <input type="text" name="questions[0][text]" placeholder="Texte de la question"
                               class="w-full p-3 border border-gray-300 rounded-lg mb-3 focus:ring-2 focus:ring-giryad-green focus:border-transparent transition" required>
                        <select name="questions[0][type]" class="w-full p-3 border border-gray-300 rounded-lg mb-3 focus:ring-2 focus:ring-giryad-green focus:border-transparent transition"
                                onchange="toggleOptions(this)">
                            @foreach($questionTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        <div class="options-container hidden space-y-3">
                            <input type="text" name="questions[0][options][]" placeholder="Option 1"
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-giryad-green focus:border-transparent transition">
                            <input type="text" name="questions[0][options][]" placeholder="Option 2"
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-giryad-green focus:border-transparent transition">
                            <button type="button" class="text-giryad-green hover:text-green-600 add-option transition">+ Ajouter une option</button>
                        </div>
                        <div class="grid-container hidden space-y-3">
                            <div class="rows-container space-y-3">
                                <input type="text" name="questions[0][rows][]" placeholder="Ligne 1"
                                       class="w-full p-3 border border-gray-300 rounded-lg row-input focus:ring-2 focus:ring-giryad-green focus:border-transparent transition">
                                <input type="text" name="questions[0][rows][]" placeholder="Ligne 2"
                                       class="w-full p-3 border border-gray-300 rounded-lg row-input focus:ring-2 focus:ring-giryad-green focus:border-transparent transition">
                                <button type="button" class="text-giryad-green hover:text-green-600 add-row transition">+ Ajouter une ligne</button>
                            </div>
                            <p class="text-red-500 text-sm hidden row-error">Veuillez ajouter au moins une ligne non vide.</p>
                            <div class="columns-container space-y-3">
                                <input type="text" name="questions[0][columns][]" placeholder="Colonne 1"
                                       class="w-full p-3 border border-gray-300 rounded-lg column-input focus:ring-2 focus:ring-giryad-green focus:border-transparent transition">
                                <input type="text" name="questions[0][columns][]" placeholder="Colonne 2"
                                       class="w-full p-3 border border-gray-300 rounded-lg column-input focus:ring-2 focus:ring-giryad-green focus:border-transparent transition">
                                <button type="button" class="text-giryad-green hover:text-green-600 add-column transition">+ Ajouter une colonne</button>
                            </div>
                            <p class="text-red-500 text-sm hidden column-error">Veuillez ajouter au moins une colonne non vide.</p>
                        </div>
                        <label class="flex items-center mt-3">
                            <input type="checkbox" name="questions[0][is_required]" value="1" class="h-5 w-5 text-giryad-green focus:ring-giryad-green">
                            <span class="ml-3 text-gray-700">Obligatoire</span>
                        </label>
                    </div>
                </div>
                <button type="button" id="add-question"
                        class="mt-6 px-6 py-3 bg-giryad-medium-blue text-white rounded-lg hover:bg-giryad-dark-blue transition duration-300 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Ajouter une question
                </button>
            </div>
            <div class="mt-8 flex space-x-4">
                <button type="submit" class="px-6 py-3 bg-giryad-green text-white rounded-lg hover:bg-green-600 transition duration-300 flex items-center shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Créer le formulaire
                </button>
                <a href="{{ route('forms.index') }}"
                   class="px-6 py-3 bg-giryad-medium-blue text-white rounded-lg hover:bg-giryad-dark-blue transition duration-300 flex items-center shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Annuler
                </a>
            </div>
        </form>
    </div>
    <script>
        let questionIndex = 1;

        document.getElementById('add-question').addEventListener('click', function () {
            const container = document.getElementById('questions-container');
            const questionBlock = document.createElement('div');
            questionBlock.className = 'question-block bg-gray-50 p-6 rounded-lg';
            questionBlock.innerHTML = `
                <div class="flex items-center mb-3">
                    <label class="text-giryad-dark-blue font-semibold text-lg">Question ${questionIndex + 1}</label>
                    <button type="button" class="ml-auto text-red-500 hover:text-red-700 remove-question">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <input type="hidden" name="questions[${questionIndex}][order]" value="${questionIndex + 1}">
                <input type="text" name="questions[${questionIndex}][text]" placeholder="Texte de la question"
                       class="w-full p-3 border border-gray-300 rounded-lg mb-3 focus:ring-2 focus:ring-giryad-green focus:border-transparent transition" required>
                <select name="questions[${questionIndex}][type]" class="w-full p-3 border border-gray-300 rounded-lg mb-3 focus:ring-2 focus:ring-giryad-green focus:border-transparent transition"
                        onchange="toggleOptions(this)">
                    @foreach($questionTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
                <div class="options-container hidden space-y-3">
                    <input type="text" name="questions[${questionIndex}][options][]" placeholder="Option 1"
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-giryad-green focus:border-transparent transition">
                    <input type="text" name="questions[${questionIndex}][options][]" placeholder="Option 2"
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-giryad-green focus:border-transparent transition">
                    <button type="button" class="text-giryad-green hover:text-green-600 add-option transition">+ Ajouter une option</button>
                </div>
                <div class="grid-container hidden space-y-3">
                    <div class="rows-container space-y-3">
                        <input type="text" name="questions[${questionIndex}][rows][]" placeholder="Ligne 1"
                               class="w-full p-3 border border-gray-300 rounded-lg row-input focus:ring-2 focus:ring-giryad-green focus:border-transparent transition">
                        <input type="text" name="questions[${questionIndex}][rows][]" placeholder="Ligne 2"
                               class="w-full p-3 border border-gray-300 rounded-lg row-input focus:ring-2 focus:ring-giryad-green focus:border-transparent transition">
                        <button type="button" class="text-giryad-green hover:text-green-600 add-row transition">+ Ajouter une ligne</button>
                    </div>
                    <p class="text-red-500 text-sm hidden row-error">Veuillez ajouter au moins une ligne non vide.</p>
                    <div class="columns-container space-y-3">
                        <input type="text" name="questions[${questionIndex}][columns][]" placeholder="Colonne 1"
                               class="w-full p-3 border border-gray-300 rounded-lg column-input focus:ring-2 focus:ring-giryad-green focus:border-transparent transition">
                        <input type="text" name="questions[${questionIndex}][columns][]" placeholder="Colonne 2"
                               class="w-full p-3 border border-gray-300 rounded-lg column-input focus:ring-2 focus:ring-giryad-green focus:border-transparent transition">
                        <button type="button" class="text-giryad-green hover:text-green-600 add-column transition">+ Ajouter une colonne</button>
                    </div>
                    <p class="text-red-500 text-sm hidden column-error">Veuillez ajouter au moins une colonne non vide.</p>
                </div>
                <label class="flex items-center mt-3">
                    <input type="checkbox" name="questions[${questionIndex}][is_required]" value="1" class="h-5 w-5 text-giryad-green focus:ring-giryad-green">
                    <span class="ml-3 text-gray-700">Obligatoire</span>
                </label>
            `;
            container.appendChild(questionBlock);
            questionIndex++;
            updateRemoveButtons();
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('add-option')) {
                const container = e.target.parentElement;
                const index = container.parentElement.querySelector('input[name*="[text]"]').name.match(/\d+/)[0];
                const optionInput = document.createElement('input');
                optionInput.type = 'text';
                optionInput.name = `questions[${index}][options][]`;
                optionInput.placeholder = `Option ${container.querySelectorAll('input[type="text"]').length + 1}`;
                optionInput.className = 'w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-giryad-green focus:border-transparent transition';
                container.insertBefore(optionInput, e.target);
            }
            if (e.target.classList.contains('add-row')) {
                const container = e.target.parentElement;
                const index = container.parentElement.parentElement.querySelector('input[name*="[text]"]').name.match(/\d+/)[0];
                const rowInput = document.createElement('input');
                rowInput.type = 'text';
                rowInput.name = `questions[${index}][rows][]`;
                rowInput.placeholder = `Ligne ${container.querySelectorAll('input[type="text"]').length + 1}`;
                rowInput.className = 'w-full p-3 border border-gray-300 rounded-lg row-input focus:ring-2 focus:ring-giryad-green focus:border-transparent transition';
                container.insertBefore(rowInput, e.target);
                validateGrid(container.parentElement);
            }
            if (e.target.classList.contains('add-column')) {
                const container = e.target.parentElement;
                const index = container.parentElement.parentElement.querySelector('input[name*="[text]"]').name.match(/\d+/)[0];
                const columnInput = document.createElement('input');
                columnInput.type = 'text';
                columnInput.name = `questions[${index}][columns][]`;
                columnInput.placeholder = `Colonne ${container.querySelectorAll('input[type="text"]').length + 1}`;
                columnInput.className = 'w-full p-3 border border-gray-300 rounded-lg column-input focus:ring-2 focus:ring-giryad-green focus:border-transparent transition';
                container.insertBefore(columnInput, e.target);
                validateGrid(container.parentElement);
            }
            if (e.target.closest('.remove-question')) {
                e.target.closest('.question-block').remove();
                updateQuestionOrders();
                updateRemoveButtons();
            }
        });

        function toggleOptions(select) {
            const container = select.parentElement;
            const optionsContainer = container.querySelector('.options-container');
            const gridContainer = container.querySelector('.grid-container');
            if ([3, 4, 5].includes(parseInt(select.value))) {
                optionsContainer.classList.remove('hidden');
                gridContainer.classList.add('hidden');
            } else if ([6, 7].includes(parseInt(select.value))) {
                optionsContainer.classList.add('hidden');
                gridContainer.classList.remove('hidden');
                validateGrid(gridContainer);
            } else {
                optionsContainer.classList.add('hidden');
                gridContainer.classList.add('hidden');
            }
        }

        function validateGrid(gridContainer) {
            const rowInputs = gridContainer.querySelectorAll('.row-input');
            const columnInputs = gridContainer.querySelectorAll('.column-input');
            const rowError = gridContainer.querySelector('.row-error');
            const columnError = gridContainer.querySelector('.column-error');
            rowError.classList.toggle('hidden', rowInputs.length > 0 && Array.from(rowInputs).some(input => input.value.trim()));
            columnError.classList.toggle('hidden', columnInputs.length > 0 && Array.from(columnInputs).some(input => input.value.trim()));
        }

        function updateRemoveButtons() {
            const buttons = document.querySelectorAll('.remove-question');
            buttons.forEach(button => button.style.display = buttons.length > 1 ? 'block' : 'none');
        }

        function updateQuestionOrders() {
            const questionBlocks = document.querySelectorAll('.question-block');
            questionBlocks.forEach((block, index) => {
                block.querySelector('label').textContent = `Question ${index + 1}`;
                block.querySelector('input[name*="[order]"]').value = index + 1;
                block.querySelector('input[name*="[text]"]').name = `questions[${index}][text]`;
                block.querySelector('select[name*="[type]"]').name = `questions[${index}][type]`;
                block.querySelector('input[name*="[is_required]"]').name = `questions[${index}][is_required]`;
                const options = block.querySelectorAll('input[name*="[options][]"]');
                options.forEach(input => input.name = `questions[${index}][options][]`);
                const rows = block.querySelectorAll('input[name*="[rows][]"]');
                rows.forEach(input => input.name = `questions[${index}][rows][]`);
                const columns = block.querySelectorAll('input[name*="[columns][]"]');
                columns.forEach(input => input.name = `questions[${index}][columns][]`);
            });
            questionIndex = questionBlocks.length;
        }

        document.getElementById('form-create').addEventListener('submit', function (e) {
            const formData = new FormData(this);
            console.log('Données du formulaire avant soumission :', Object.fromEntries(formData));
            let isValid = true;
            document.querySelectorAll('.grid-container:not(.hidden)').forEach(gridContainer => {
                const rowInputs = gridContainer.querySelectorAll('.row-input');
                const columnInputs = gridContainer.querySelectorAll('.column-input');
                const rowError = gridContainer.querySelector('.row-error');
                const columnError = gridContainer.querySelector('.column-error');
                const hasRows = Array.from(rowInputs).some(input => input.value.trim());
                const hasColumns = Array.from(columnInputs).some(input => input.value.trim());
                rowError.classList.toggle('hidden', hasRows);
                columnError.classList.toggle('hidden', hasColumns);
                if (!hasRows || !hasColumns) {
                    isValid = false;
                }
            });
            if (!isValid) {
                e.preventDefault();
                alert('Veuillez remplir au moins une ligne et une colonne non vides pour chaque grille.');
            }
        });
    </script>
@endsection
