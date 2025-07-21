<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier {{ $form->title }} - Résidence Giryad</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/heroicons@2.0.13/dist/heroicons.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans antialiased">
    <header class="bg-giryad-dark-blue text-white py-4 shadow-lg">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold flex items-center">
                <svg class="w-8 h-8 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Résidence Giryad
            </h1>
            <nav class="space-x-4">
                <a href="{{ route('forms.index') }}" class="hover:text-giryad-green transition">Formulaires</a>
                <a href="{{ route('dashboard') }}" class="hover:text-giryad-green transition">Tableau de bord</a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="hover:text-giryad-green transition">Déconnexion</button>
                </form>
            </nav>
        </div>
    </header>
    <div class="container mx-auto px-6 py-8 max-w-5xl">
        <h2 class="text-3xl font-semibold text-giryad-dark-blue mb-6">Modifier "{{ $form->title }}"</h2>
        <p class="text-gray-600 mb-6 text-lg">Modifiez cette enquête pour la Résidence Giryad. Mettez à jour les questions ou ajoutez-en de nouvelles.</p>
        <form method="POST" action="{{ route('forms.update', $form->slug) }}" class="bg-white shadow-lg rounded-lg p-6 space-y-6" id="form-edit">
            @csrf
            @method('PUT')
            <div class="border-l-4 border-giryad-medium-blue pl-4">
                <label for="title" class="block text-giryad-dark-blue font-semibold text-lg mb-2">Titre du formulaire <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title', $form->title) }}"
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-giryad-green focus:border-giryad-green"
                       required>
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="border-l-4 border-giryad-medium-blue pl-4">
                <label for="description" class="block text-giryad-dark-blue font-semibold text-lg mb-2">Description</label>
                <textarea name="description" id="description"
                          class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-giryad-green focus:border-giryad-green"
                          rows="4">{{ old('description', $form->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="border-l-4 border-giryad-medium-blue pl-4">
                <h3 class="text-xl font-medium text-giryad-medium-blue mb-4">Questions</h3>
                <div id="questions-container" class="space-y-4">
                    @foreach($form->questions as $index => $question)
                        <div class="question-block bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center mb-2">
                                <label class="text-giryad-dark-blue font-semibold">Question {{ $index + 1 }}</label>
                                <button type="button" class="ml-auto text-red-500 hover:text-red-700 remove-question">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <input type="hidden" name="questions[{{ $index }}][order]" value="{{ old('questions.' . $index . '.order', $question->order) }}">
                            <input type="text" name="questions[{{ $index }}][text]" value="{{ old('questions.' . $index . '.text', $question->text) }}"
                                   placeholder="Texte de la question"
                                   class="w-full p-3 border border-gray-300 rounded-lg mb-2 focus:ring-2 focus:ring-giryad-green" required>
                            <select name="questions[{{ $index }}][type]" class="w-full p-3 border border-gray-300 rounded-lg mb-2 focus:ring-2 focus:ring-giryad-green"
                                    onchange="toggleOptions(this)">
                                @foreach($questionTypes as $type)
                                    <option value="{{ $type->id }}" {{ $question->question_type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                            <div class="options-container {{ in_array($question->question_type_id, [3, 4, 5]) ? '' : 'hidden' }} space-y-2">
                                @foreach($question->options ?? [] as $optionIndex => $option)
                                    <input type="text" name="questions[{{ $index }}][options][]" value="{{ $option }}"
                                           placeholder="Option {{ $optionIndex + 1 }}"
                                           class="w-full p-3 border border-gray-300 rounded-lg">
                                @endforeach
                                <button type="button" class="text-giryad-green hover:text-green-600 add-option">+ Ajouter une option</button>
                            </div>
                            <div class="grid-container {{ in_array($question->question_type_id, [6, 7]) ? '' : 'hidden' }} space-y-2">
                                <div class="rows-container space-y-2">
                                    @foreach($question->rows ?? [] as $rowIndex => $row)
                                        <input type="text" name="questions[{{ $index }}][rows][]" value="{{ $row }}"
                                               placeholder="Ligne {{ $rowIndex + 1 }}"
                                               class="w-full p-3 border border-gray-300 rounded-lg row-input">
                                    @endforeach
                                    <button type="button" class="text-giryad-green hover:text-green-600 add-row">+ Ajouter une ligne</button>
                                </div>
                                <p class="text-red-500 text-sm hidden row-error">Veuillez ajouter au moins une ligne non vide.</p>
                                <div class="columns-container space-y-2">
                                    @foreach($question->columns ?? [] as $columnIndex => $column)
                                        <input type="text" name="questions[{{ $index }}][columns][]" value="{{ $column }}"
                                               placeholder="Colonne {{ $columnIndex + 1 }}"
                                               class="w-full p-3 border border-gray-300 rounded-lg column-input">
                                    @endforeach
                                    <button type="button" class="text-giryad-green hover:text-green-600 add-column">+ Ajouter une colonne</button>
                                </div>
                                <p class="text-red-500 text-sm hidden column-error">Veuillez ajouter au moins une colonne non vide.</p>
                            </div>
                            <label class="flex items-center mt-2">
                                <input type="checkbox" name="questions[{{ $index }}][is_required]" value="1"
                                       class="h-4 w-4 text-giryad-green" {{ $question->is_required ? 'checked' : '' }}>
                                <span class="ml-2 text-gray-700">Obligatoire</span>
                            </label>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="add-question"
                        class="mt-4 px-4 py-2 bg-giryad-medium-blue text-white rounded-lg hover:bg-giryad-dark-blue transition">
                    Ajouter une question
                </button>
            </div>
            <div class="mt-8">
                <button type="submit"
                        class="px-6 py-3 bg-giryad-green text-white rounded-lg hover:bg-green-600 transition flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Mettre à jour le formulaire
                </button>
            </div>
        </form>
    </div>
    <footer class="bg-giryad-dark-blue text-white py-4 mt-12">
        <div class="container mx-auto px-6 text-center">
            <p>Résidence Giryad - Enquêtes de satisfaction pour une vie meilleure</p>
            <p class="text-sm mt-2">© 2025 Résidence Giryad. Tous droits réservés.</p>
        </div>
    </footer>
    <script>
        let questionIndex = {{ count($form->questions) }};

        document.getElementById('add-question').addEventListener('click', function () {
            const container = document.getElementById('questions-container');
            const questionBlock = document.createElement('div');
            questionBlock.className = 'question-block bg-gray-50 p-4 rounded-lg';
            questionBlock.innerHTML = `
                <div class="flex items-center mb-2">
                    <label class="text-giryad-dark-blue font-semibold">Question ${questionIndex + 1}</label>
                    <button type="button" class="ml-auto text-red-500 hover:text-red-700 remove-question">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <input type="hidden" name="questions[${questionIndex}][order]" value="${questionIndex + 1}">
                <input type="text" name="questions[${questionIndex}][text]" placeholder="Texte de la question"
                       class="w-full p-3 border border-gray-300 rounded-lg mb-2 focus:ring-2 focus:ring-giryad-green" required>
                <select name="questions[${questionIndex}][type]" class="w-full p-3 border border-gray-300 rounded-lg mb-2 focus:ring-2 focus:ring-giryad-green"
                        onchange="toggleOptions(this)">
                    @foreach($questionTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
                <div class="options-container hidden space-y-2">
                    <input type="text" name="questions[${questionIndex}][options][]" placeholder="Option 1"
                           class="w-full p-3 border border-gray-300 rounded-lg">
                    <input type="text" name="questions[${questionIndex}][options][]" placeholder="Option 2"
                           class="w-full p-3 border border-gray-300 rounded-lg">
                    <button type="button" class="text-giryad-green hover:text-green-600 add-option">+ Ajouter une option</button>
                </div>
                <div class="grid-container hidden space-y-2">
                    <div class="rows-container space-y-2">
                        <input type="text" name="questions[${questionIndex}][rows][]" placeholder="Ligne 1"
                               class="w-full p-3 border border-gray-300 rounded-lg row-input">
                        <input type="text" name="questions[${questionIndex}][rows][]" placeholder="Ligne 2"
                               class="w-full p-3 border border-gray-300 rounded-lg row-input">
                        <button type="button" class="text-giryad-green hover:text-green-600 add-row">+ Ajouter une ligne</button>
                    </div>
                    <p class="text-red-500 text-sm hidden row-error">Veuillez ajouter au moins une ligne non vide.</p>
                    <div class="columns-container space-y-2">
                        <input type="text" name="questions[${questionIndex}][columns][]" placeholder="Colonne 1"
                               class="w-full p-3 border border-gray-300 rounded-lg column-input">
                        <input type="text" name="questions[${questionIndex}][columns][]" placeholder="Colonne 2"
                               class="w-full p-3 border border-gray-300 rounded-lg column-input">
                        <button type="button" class="text-giryad-green hover:text-green-600 add-column">+ Ajouter une colonne</button>
                    </div>
                    <p class="text-red-500 text-sm hidden column-error">Veuillez ajouter au moins une colonne non vide.</p>
                </div>
                <label class="flex items-center mt-2">
                    <input type="checkbox" name="questions[${questionIndex}][is_required]" value="1" class="h-4 w-4 text-giryad-green">
                    <span class="ml-2 text-gray-700">Obligatoire</span>
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
                optionInput.className = 'w-full p-3 border border-gray-300 rounded-lg';
                container.insertBefore(optionInput, e.target);
            }
            if (e.target.classList.contains('add-row')) {
                const container = e.target.parentElement;
                const index = container.parentElement.parentElement.querySelector('input[name*="[text]"]').name.match(/\d+/)[0];
                const rowInput = document.createElement('input');
                rowInput.type = 'text';
                rowInput.name = `questions[${index}][rows][]`;
                rowInput.placeholder = `Ligne ${container.querySelectorAll('input[type="text"]').length + 1}`;
                rowInput.className = 'w-full p-3 border border-gray-300 rounded-lg row-input';
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
                columnInput.className = 'w-full p-3 border border-gray-300 rounded-lg column-input';
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

        document.getElementById('form-edit').addEventListener('submit', function (e) {
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

        // Initialiser les options au chargement
        document.querySelectorAll('select[name*="[type]"]').forEach(toggleOptions);
    </script>
</body>
</html>
