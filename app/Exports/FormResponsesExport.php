<?php

namespace App\Exports;

use App\Models\Form;
use Rap2hpoutre\FastExcel\FastExcel;

class FormResponsesExport
{
    protected $form;

    public function __construct($form)
    {
        $this->form = $form;
    }

    public function export()
    {
        $responses = $this->form->responses()->with('answers')->get()->map(function ($response) {
            $answers = $response->answers->pluck('response', 'question_id')->toArray();
            $row = [
                'Timestamp' => $response->created_at->format('Y-m-d H:i:s'),
            ];

            foreach ($this->form->questions()->orderBy('order')->get() as $question) {
                $value = $answers[$question->id] ?? '';
                if (in_array($question->question_type_id, [4, 7])) {
                    $value = is_string($value) ? json_decode($value, true) : $value;
                    $value = is_array($value) ? implode(', ', $value) : $value;
                }
                $row[$question->text] = $value;
            }

            return $row;
        });

        return (new FastExcel($responses))->export('responses_' . $this->form->slug . '_' . now()->format('Ymd_His') . '.xlsx');
    }
}
