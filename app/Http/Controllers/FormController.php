<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Question;
use App\Models\QuestionType;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class FormController extends Controller
{
    public function __construct()
    {
        $this->middleware(\App\Http\Middleware\EnsureUserIsAdmin::class)->except(['show', 'storeResponse']);
    }

    public function index()
    {
        $forms = Form::withCount('responses')->paginate(10);
        return view('forms.index', compact('forms'));
    }

    public function create()
    {
        $questionTypes = QuestionType::all();
        return view('forms.create', compact('questionTypes'));
    }

    public function store(Request $request)
    {
        Log::info('Données reçues dans store:', $request->all());

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|exists:question_types,id',
            'questions.*.order' => 'required|integer|min:1',
            'questions.*.is_required' => 'nullable|boolean',
            'questions.*.options' => 'nullable|array',
            'questions.*.options.*' => 'nullable|string',
            'questions.*.rows' => 'nullable|array|required_if:questions.*.type,6,7',
            'questions.*.rows.*' => 'nullable|string',
            'questions.*.columns' => 'nullable|array|required_if:questions.*.type,6,7',
            'questions.*.columns.*' => 'nullable|string',
        ]);

        $form = Form::create([
            'title' => $request->title,
            'description' => $request->description,
            'slug' => \Illuminate\Support\Str::slug($request->title) . '-' . uniqid(),
        ]);

        foreach ($request->questions as $index => $questionData) {
            $rows = isset($questionData['rows']) ? array_values(array_filter($questionData['rows'], fn($row) => !empty(trim($row)))) : null;
            $columns = isset($questionData['columns']) ? array_values(array_filter($questionData['columns'], fn($col) => !empty(trim($col)))) : null;

            if (in_array($questionData['type'], [6, 7])) {
                if (empty($rows)) {
                    return back()->withErrors(['questions.' . $index . '.rows' => 'At least one non-empty row is required for grids.']);
                }
                if (empty($columns)) {
                    return back()->withErrors(['questions.' . $index . '.columns' => 'At least one non-empty column is required for grids.']);
                }
            }

            $questionDataToCreate = [
                'form_id' => $form->id,
                'question_type_id' => $questionData['type'],
                'text' => $questionData['text'],
                'is_required' => isset($questionData['is_required']) && $questionData['is_required'],
                'options' => in_array($questionData['type'], [3, 4, 5]) ? array_values(array_filter($questionData['options'] ?? [], fn($opt) => !empty(trim($opt)))) : null,
                'rows' => $rows,
                'columns' => $columns,
                'order' => $questionData['order'],
            ];

            Log::info('Données avant création de la question:', $questionDataToCreate);

            try {
                $question = Question::create($questionDataToCreate);
                Log::info('Question créée:', $question->toArray());
            } catch (\Exception $e) {
                Log::error('Erreur lors de la création de la question:', ['error' => $e->getMessage(), 'data' => $questionDataToCreate]);
                return back()->withErrors(['questions.' . $index => 'Error creating question: ' . $e->getMessage()]);
            }
        }

        return redirect()->route('forms.index')->with('success', 'Formulaire créé avec succès.');
    }

    public function show($slug)
    {
        $form = Form::where('slug', $slug)->with(['questions' => function ($query) {
            $query->orderBy('order');
        }])->firstOrFail();
        return view('forms.show', compact('form'));
    }

    public function edit($slug)
    {
        $form = Form::where('slug', $slug)->with(['questions' => function ($query) {
            $query->orderBy('order');
        }])->firstOrFail();
        $questionTypes = QuestionType::all();
        return view('forms.edit', compact('form', 'questionTypes'));
    }

    public function update(Request $request, $slug)
    {
        Log::info('Données reçues dans update:', $request->all());

        $form = Form::where('slug', $slug)->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|exists:question_types,id',
            'questions.*.order' => 'required|integer|min:1',
            'questions.*.is_required' => 'nullable|boolean',
            'questions.*.options' => 'nullable|array',
            'questions.*.options.*' => 'nullable|string',
            'questions.*.rows' => 'nullable|array|required_if:questions.*.type,6,7',
            'questions.*.rows.*' => 'nullable|string',
            'questions.*.columns' => 'nullable|array|required_if:questions.*.type,6,7',
            'questions.*.columns.*' => 'nullable|string',
        ]);

        $form->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        $form->questions()->delete();

        foreach ($request->questions as $index => $questionData) {
            $rows = isset($questionData['rows']) ? array_values(array_filter($questionData['rows'], fn($row) => !empty(trim($row)))) : null;
            $columns = isset($questionData['columns']) ? array_values(array_filter($questionData['columns'], fn($col) => !empty(trim($col)))) : null;

            if (in_array($questionData['type'], [6, 7])) {
                if (empty($rows)) {
                    return back()->withErrors(['questions.' . $index . '.rows' => 'At least one non-empty row is required for grids.']);
                }
                if (empty($columns)) {
                    return back()->withErrors(['questions.' . $index . '.columns' => 'At least one non-empty column is required for grids.']);
                }
            }

            $questionDataToCreate = [
                'form_id' => $form->id,
                'question_type_id' => $questionData['type'],
                'text' => $questionData['text'],
                'is_required' => isset($questionData['is_required']) && $questionData['is_required'],
                'options' => in_array($questionData['type'], [3, 4, 5]) ? array_values(array_filter($questionData['options'] ?? [], fn($opt) => !empty(trim($opt)))) : null,
                'rows' => $rows,
                'columns' => $columns,
                'order' => $questionData['order'],
            ];

            Log::info('Données avant création de la question (update):', $questionDataToCreate);

            try {
                $question = Question::create($questionDataToCreate);
                Log::info('Question créée (update):', $question->toArray());
            } catch (\Exception $e) {
                Log::error('Erreur lors de la création de la question (update):', ['error' => $e->getMessage(), 'data' => $questionDataToCreate]);
                return back()->withErrors(['questions.' . $index => 'Error creating question: ' . $e->getMessage()]);
            }
        }

        return redirect()->route('forms.index')->with('success', 'Formulaire mis à jour avec succès.');
    }

public function responses($slug)
    {
        $form = Form::where('slug', $slug)->with(['questions', 'responses.user'])->firstOrFail();
        $responses = Response::where('form_id', $form->id)->with('user')->orderBy('id', 'asc')->paginate(10);
        return view('forms.responses', compact('form', 'responses'));
    }

    public function response($slug, $responseId)
    {
        $form = Form::where('slug', $slug)->with(['questions', 'responses' => function ($query) use ($responseId) {
            $query->with(['answers' => function ($query) {
                $query->with('question');
            }])->where('id', $responseId);
        }])->firstOrFail();

        $response = $form->responses->first(); // Récupère la première réponse correspondant à responseId
        if (!$response) {
            abort(404, 'Réponse non trouvée.');
        }

        $index = $form->responses->search(function ($r) use ($response) {
            return $r->id === $response->id;
        }) + 1; // Ajoute 1 pour un index humain (1-based)

        Log::info('Détails de la réponse récupérée:', ['response' => $response->toArray(), 'index' => $index]);

        return view('forms.response_individual', compact('form', 'response', 'index'));
    }

    public function storeResponse(Request $request, $slug)
    {
        Log::info('Données reçues dans storeResponse:', $request->all());

        $form = Form::where('slug', $slug)->with('questions')->firstOrFail();

        $rules = [];
        foreach ($form->questions as $question) {
            if ($question->is_required) {
                if ($question->question_type_id == 6) {
                    foreach ($question->rows ?? [] as $row) {
                        $rules["responses.{$question->id}.{$row}"] = 'required|string|in:' . implode(',', $question->columns ?? []);
                    }
                } elseif ($question->question_type_id == 7) {
                    foreach ($question->rows ?? [] as $row) {
                        $rules["responses.{$question->id}.{$row}.*"] = 'nullable|string|in:' . implode(',', $question->columns ?? []);
                    }
                } elseif ($question->question_type_id == 3) {
                    $rules["responses.{$question->id}"] = 'required|string|in:' . implode(',', $question->options ?? []);
                } elseif ($question->question_type_id == 4) {
                    $rules["responses.{$question->id}.*"] = 'nullable|string|in:' . implode(',', $question->options ?? []);
                } elseif ($question->question_type_id == 5) {
                    $rules["responses.{$question->id}"] = 'required|string|in:' . implode(',', $question->options ?? []);
                } elseif ($question->question_type_id == 8) {
                    $rules["responses.{$question->id}"] = 'required|date_format:Y-m-d';
                } elseif ($question->question_type_id == 9) {
                    $rules["responses.{$question->id}"] = 'required|date_format:H:i';
                } elseif ($question->question_type_id == 1) {
                    $rules["responses.{$question->id}"] = 'required|string';
                } elseif ($question->question_type_id == 2) {
                    $rules["responses.{$question->id}"] = 'required|string'; // Pas de transformation, conserve les \n
                }
            }
        }

        try {
            $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erreur de validation dans storeResponse:', ['errors' => $e->errors()]);
            throw $e;
        }

        $response = Response::create([
            'form_id' => $form->id,
            'user_id' => auth()->id() ?? null,
        ]);

        foreach ($request->responses as $questionId => $responseData) {
            try {
                $question = $form->questions->where('id', $questionId)->first();
                if (!$question) {
                    Log::error('Question non trouvée:', ['question_id' => $questionId]);
                    continue;
                }

                $finalResponse = $responseData;
                if ($question->question_type_id == 8 && $responseData) {
                    try {
                        $parsedDate = Carbon::createFromFormat('Y-m-d', $responseData, 'Africa/Algiers');
                        $finalResponse = $parsedDate->toDateString();
                    } catch (\Exception $e) {
                        Log::error('Erreur de parsing de date:', ['error' => $e->getMessage(), 'value' => $responseData]);
                        $finalResponse = $responseData;
                    }
                } elseif ($question->question_type_id == 9 && $responseData) { 
                    try {
                        $parsedTime = Carbon::createFromFormat('H:i', $responseData, 'Africa/Algiers');
                        $finalResponse = $parsedTime->format('H:i:s');
                    } catch (\Exception $e) {
                        Log::error('Erreur de parsing de temps:', ['error' => $e->getMessage(), 'value' => $responseData]);
                        $finalResponse = $responseData;
                    }
                }

                // Pour le paragraphe (type 2), on conserve les retours à la ligne tels quels
                $response->answers()->create([
                    'question_id' => $questionId,
                    'response' => $finalResponse,
                ]);

                Log::info('Réponse créée:', ['question_id' => $questionId, 'response' => $finalResponse]);
            } catch (\Exception $e) {
                Log::error('Erreur lors de la création de la réponse:', ['error' => $e->getMessage(), 'question_id' => $questionId, 'response_data' => $responseData]);
            }
        }

        return redirect()->route('forms.show', $form->slug)->with('success', 'Réponses soumises avec succès.');
    }

    public function exportResponses($slug)
    {
        $form = Form::where('slug', $slug)->with(['questions' => function ($query) {
            $query->orderBy('order');
        }, 'responses.answers'])->firstOrFail();

        $filename = 'responses_' . $form->slug . '_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($form) {
            $file = fopen('php://output', 'w');

            // Prepare headers
            $columns = ['Response ID', 'User ID', 'Submitted At'];
            $questionMap = [];
            foreach ($form->questions as $question) {
                $columns[] = $question->text;
                $questionMap[$question->id] = $question->text;
            }
            fputcsv($file, $columns);

            // Prepare responses
            foreach ($form->responses as $response) {
                $row = [
                    $response->id,
                    $response->user_id ?? 'Anonymous',
                    $response->created_at->format('Y-m-d H:i:s'),
                ];

                // Initialize answer array based on question order
                $answers = array_fill_keys(array_keys($questionMap), '');
                foreach ($response->answers as $answer) {
                    if (isset($questionMap[$answer->question_id])) {
                        $answers[$answer->question_id] = $answer->response; // Conserver les \n
                    }
                }

                // Add answers in the order of questions
                foreach ($form->questions as $question) {
                    $row[] = $answers[$question->id] ?? '';
                }

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


public function dashboard()
{
    Log::info('Méthode dashboard appelée avec succès.');
    file_put_contents(storage_path('logs/dashboard_test.txt'), 'Méthode dashboard exécutée à ' . now() . "\n", FILE_APPEND);

    $forms = Form::with(['questions', 'responses.answers'])->get();

    $formData = [];
    foreach ($forms as $form) {
        Log::info('Traitement du formulaire:', ['title' => $form->title, 'questions_count' => $form->questions->count(), 'responses_count' => $form->responses->count()]);
        $responseData = [];
        if ($form->questions->isEmpty()) {
            Log::warning('Aucun question trouvée pour le formulaire: ' . $form->title);
            continue;
        }
        foreach ($form->questions as $question) {
            Log::info('Traitement de la question:', ['question_id' => $question->id, 'text' => $question->text, 'type_id' => $question->question_type_id]);
            $answers = $form->responses->flatMap->answers->where('question_id', $question->id)->pluck('response');
            Log::info('Réponses brutes:', ['question_id' => $question->id, 'answers' => $answers->toArray()]);
            if ($answers->isEmpty()) {
                Log::warning('Aucune réponse trouvée pour la question ID: ' . $question->id . ' dans le formulaire: ' . $form->title);
                continue;
            }

            if (in_array($question->question_type_id, [6, 7])) { // Multiple Choice Grid (6) et Checkbox Grid (7)
                $rows = $question->rows ?? [];
                $columns = $question->columns ?? [];
                $gridData = [];
                foreach ($rows as $row) {
                    $gridData[$row] = array_fill_keys($columns, 0);
                }

                foreach ($answers as $response) {
                    if (is_array($response)) {
                        foreach ($response as $row => $colValue) {
                            if (isset($gridData[$row]) && is_array($colValue)) {
                                $col = $colValue[0]; // Prendre la première valeur de la colonne sélectionnée
                                if (in_array($col, $columns)) {
                                    $gridData[$row][$col] += 1;
                                }
                            }
                        }
                    }
                }
                Log::info('Comptages de la grille:', ['question_id' => $question->id, 'grid_data' => $gridData]);
                $responseData[$question->text] = [
                    'type' => $question->question_type_id == 6 ? 'multiple_choice_grid' : 'checkbox_grid',
                    'grid_data' => $gridData,
                    'columns' => $columns,
                ];
            } elseif (in_array($question->question_type_id, [1, 2])) { // Short answer (1) et Paragraph (2)
                $validAnswers = $answers->map(function ($response) {
                    if (is_array($response) && isset($response[0])) {
                        return $response[0];
                    } elseif (is_object($response) || is_array($response)) {
                        return json_encode($response);
                    }
                    return (string)$response;
                })->filter()->values();

                $responseData[$question->text] = [
                    'type' => $question->question_type_id == 1 ? 'short_answer' : 'paragraph',
                    'responses' => $validAnswers->toArray(),
                ];
            } else { // Autres types (multiple_choice, etc.)
                $validAnswers = $answers->map(function ($response) {
                    if (is_array($response) && isset($response[0])) {
                        return $response[0];
                    } elseif (is_object($response) || is_array($response)) {
                        return json_encode($response);
                    }
                    return (string)$response;
                })->filter()->values();

                $answerCounts = [];
                foreach ($validAnswers as $answer) {
                    if (is_string($answer) && (strpos($answer, '{') === 0 || strpos($answer, '[') === 0)) {
                        $decoded = json_decode($answer, true);
                        if (is_array($decoded)) {
                            foreach ($decoded as $key => $value) {
                                $answerCounts[$key] = ($answerCounts[$key] ?? 0) + 1;
                            }
                        }
                    } else {
                        $answerCounts[$answer] = ($answerCounts[$answer] ?? 0) + 1;
                    }
                }
                $responseData[$question->text] = $answerCounts;
            }
        }
        if (!empty($responseData)) {
            $formData[$form->title] = $responseData;
        } else {
            Log::warning('Aucune donnée de réponse valide pour le formulaire: ' . $form->title);
        }
    }

    Log::info('Données des formulaires avant envoi à la vue:', ['formData' => $formData]);

    return view('dashboard', compact('formData'));
}
}
