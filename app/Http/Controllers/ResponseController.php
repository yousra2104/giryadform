<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Response;
use App\Models\QuestionResponse;
use Illuminate\Http\Request;

class ResponseController extends Controller
{
         public function create($slug)
    {
        $form = Form::where('slug', $slug)->with(['questions' => function ($query) {
            $query->orderBy('order');
        }])->firstOrFail();
        return view('forms.show', compact('form'));
    }
    public function store(Request $request, $slug)
    {
        $form = Form::where('slug', $slug)->with('questions.questionType')->firstOrFail();

        $validationRules = $form->questions->mapWithKeys(function ($question) {
            return [
                "answers.{$question->id}" => $question->is_required ? 'required' : 'nullable',
            ];
        })->toArray();

        $request->validate($validationRules, ['answers.*.required' => 'Ce champ est requis.']);

        $response = Response::create(['form_id' => $form->id]);

        foreach ($form->questions as $question) {
            $value = $request->input("answers.{$question->id}");
            if ($value !== null) {
                if (in_array($question->questionType->name, ['checkboxes', 'checkbox grid']) && is_array($value)) {
                    $value = json_encode($value);
                } elseif ($question->questionType->name === 'multiple choice grid' && is_array($value)) {
                    $value = json_encode($value);
                }
                QuestionResponse::create([
                    'response_id' => $response->id,
                    'question_id' => $question->id,
                    'value' => $value,
                ]);
            }
        }

        return redirect()->route('forms.show', $form->slug)->with('success', 'Votre réponse a été enregistrée. Merci !');
    }
}
