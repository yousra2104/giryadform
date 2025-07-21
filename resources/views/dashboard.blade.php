@extends('layouts.app')

@section('title', 'Tableau de Bord')

@section('content')
    <div class="grid grid-cols-1 gap-6">
        @if (isset($formData) && is_array($formData))
            @foreach ($formData as $formTitle => $responses)
                @if (is_array($responses) && !empty($responses))
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <h3 class="text-2xl font-semibold text-giryad-dark-blue mb-4">{{ $formTitle }}</h3>
                        <div class="space-y-6">
                            @foreach ($responses as $questionText => $data)
                                @php
                                    $isGrid = isset($data['type']) && in_array($data['type'], ['multiple_choice_grid', 'checkbox_grid']);
                                    $isTextual = isset($data['type']) && in_array($data['type'], ['short_answer', 'paragraph']);
                                    $counts = !$isGrid && !$isTextual ? $data : ($isGrid ? $data['grid_data'] : array_count_values($data['responses'] ?? []));
                                    $responsesList = $isTextual ? $data['responses'] ?? [] : [];
                                    $gridColumns = $isGrid ? $data['columns'] ?? [] : [];
                                    $totalResponses = !$isGrid && !$isTextual ? array_sum(array_values($counts)) : ($isGrid ? array_sum(array_map('array_sum', $counts)) : count($responsesList));
                                @endphp
                                @if ($isGrid && is_array($counts))
                                    <div>
                                        <h4 class="text-lg font-medium text-giryad-dark-blue mb-2" id="question_{{ Str::slug($formTitle) }}_{{ Str::slug($questionText) }}">{{ $questionText }}</h4>
                                        <div class="relative h-64">
                                            <canvas id="chart_{{ Str::slug($formTitle) }}_{{ Str::slug($questionText) }}" 
                                                    style="width: 100%; height: 100%;"
                                                    data-grid-data="{{ json_encode($counts) }}"
                                                    data-columns="{{ json_encode($gridColumns) }}"
                                                    data-total="{{ $totalResponses }}"
                                                    data-type="bar"></canvas>
                                        </div>
                                        <button onclick="downloadChart('chart_{{ Str::slug($formTitle) }}_{{ Str::slug($questionText) }}')" 
                                                class="mt-2 text-giryad-medium-blue hover:text-giryad-dark-blue transition">Télécharger le graphique</button>
                                    </div>
                                @elseif (!$isGrid && !$isTextual && is_array($counts) && !empty($counts))
                                    <div>
                                        <h4 class="text-lg font-medium text-giryad-dark-blue mb-2" id="question_{{ Str::slug($formTitle) }}_{{ Str::slug($questionText) }}">{{ $questionText }}</h4>
                                        <div class="relative h-48">
                                            <canvas id="chart_{{ Str::slug($formTitle) }}_{{ Str::slug($questionText) }}" 
                                                    style="width: 100%; height: 100%;"
                                                    data-labels="{{ json_encode(array_keys($counts)) }}"
                                                    data-data="{{ json_encode(array_values($counts)) }}"
                                                    data-total="{{ $totalResponses }}"
                                                    data-type="{{ count($counts) > 5 ? 'bar' : 'pie' }}"></canvas>
                                        </div>
                                        <button onclick="downloadChart('chart_{{ Str::slug($formTitle) }}_{{ Str::slug($questionText) }}')" 
                                                class="mt-2 text-giryad-medium-blue hover:text-giryad-dark-blue transition">Télécharger le graphique</button>
                                    </div>
                                @elseif (!empty($responsesList))
                                    <div>
                                        <h4 class="text-lg font-medium text-giryad-dark-blue mb-2" id="question_{{ Str::slug($formTitle) }}_{{ Str::slug($questionText) }}">{{ $questionText }}</h4>
                                        <ul class="list-disc pl-5 text-gray-700">
                                            @foreach (array_slice($responsesList, 0, 2) as $response)
                                                <li>{{ $response }}</li>
                                            @endforeach
                                        </ul>
                                        @if (count($responsesList) > 2)
                                            <button onclick="toggleResponses('responses_{{ Str::slug($formTitle) }}_{{ Str::slug($questionText) }}')" 
                                                    class="mt-2 text-giryad-medium-blue hover:text-giryad-dark-blue transition">
                                                Afficher la suite ({{ count($responsesList) - 2 }} restantes)
                                            </button>
                                            <ul id="responses_{{ Str::slug($formTitle) }}_{{ Str::slug($questionText) }}" class="list-disc pl-5 text-gray-700 hidden">
                                                @foreach (array_slice($responsesList, 2) as $response)
                                                    <li>{{ $response }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-gray-600">Aucune donnée disponible pour la question : {{ $questionText }}</p>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <p class="text-gray-600 text-center">Aucune donnée disponible pour le formulaire : {{ $formTitle }}</p>
                    </div>
                @endif
            @endforeach
        @else
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <p class="text-gray-600 text-center">Aucune donnée disponible pour afficher les graphiques.</p>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Chart === 'undefined') {
                console.error('Chart.js non chargé après DOMContentLoaded');
                return;
            }
            document.querySelectorAll('canvas').forEach(canvas => {
                const labels = JSON.parse(canvas.getAttribute('data-labels') || '[]');
                const data = JSON.parse(canvas.getAttribute('data-data') || '[]');
                const chartType = canvas.getAttribute('data-type') || 'bar';
                const total = parseInt(canvas.getAttribute('data-total') || '0');
                const ctx = canvas.getContext('2d');
                console.log('Initialisation pour', canvas.id, { labels, data, chartType, total });

                if (chartType === 'bar' && labels.length === 0) {
                    // Cas des grilles
                    const gridData = JSON.parse(canvas.getAttribute('data-grid-data') || '{}');
                    const columns = JSON.parse(canvas.getAttribute('data-columns') || '[]');
                    const rowLabels = Object.keys(gridData);

                    const datasets = columns.map((col, index) => {
                        const colData = rowLabels.map(row => gridData[row][col] || 0);
                        return {
                            label: col,
                            data: colData,
                            backgroundColor: [
                                '#A8D5A8', '#A3BFFA', '#87CEEB', '#FFD700', '#FFA07A', '#98FB98', '#ADD8E6'
                            ][index % 7],
                            borderColor: '#A3BFFA',
                            borderWidth: 1
                        };
                    });

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: rowLabels,
                            datasets: datasets
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: { title: { display: true, text: 'Lignes', color: '#A3BFFA' }, grid: { color: '#E0E0E0' }, stacked: false },
                                y: { beginAtZero: true, title: { display: true, text: 'Nombre de Réponses', color: '#A3BFFA' }, grid: { color: '#E0E0E0' }, stacked: false }
                            },
                            plugins: {
                                legend: { labels: { color: '#A3BFFA' } },
                                title: { display: true, text: canvas.id.replace('chart_', '').replace(/_/g, ' '), color: '#A3BFFA' },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const value = context.raw;
                                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                            return `${context.dataset.label}: ${value} (${percentage}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                } else {
                    const colors = ['#A8D5A8', '#A3BFFA', '#87CEEB', '#FFD700', '#FFA07A', '#98FB98', '#ADD8E6'];
                    const backgroundColors = labels.map((_, index) => colors[index % colors.length]);
                    const borderColors = labels.map(() => '#A3BFFA');

                    new Chart(ctx, {
                        type: chartType,
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Réponses',
                                data: data,
                                backgroundColor: backgroundColors,
                                borderColor: borderColors,
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: chartType === 'bar' ? {
                                y: { beginAtZero: true, title: { display: true, text: 'Nombre de Réponses', color: '#A3BFFA' }, grid: { color: '#E0E0E0' } },
                                x: { title: { display: true, text: 'Options', color: '#A3BFFA' }, grid: { color: '#E0E0E0' } }
                            } : {},
                            plugins: {
                                legend: { labels: { color: '#A3BFFA' } },
                                title: { display: true, text: ctx.canvas.id.replace('chart_', '').replace(/_/g, ' '), color: '#A3BFFA' },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const value = context.raw;
                                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                            return `${context.label}: ${value} (${percentage}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            });
        });

        function downloadChart(canvasId) {
            const canvas = document.getElementById(canvasId);
            const link = document.createElement('a');
            link.download = `${canvasId}_chart.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();
        }

        function toggleResponses(id) {
            const element = document.getElementById(id);
            if (element.classList.contains('hidden')) {
                element.classList.remove('hidden');
            } else {
                element.classList.add('hidden');
            }
        }
    </script>
@endsection