<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QuestionType;

class QuestionTypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            ['id' => 1, 'name' => 'short answer'],
            ['id' => 2, 'name' => 'paragraph'],
            ['id' => 3, 'name' => 'multiple choice'],
            ['id' => 4, 'name' => 'checkboxes'],
            ['id' => 5, 'name' => 'dropdown'],
            ['id' => 6, 'name' => 'multiple choice grid'],
            ['id' => 7, 'name' => 'checkbox grid'],
            ['id' => 8, 'name' => 'date'],
            ['id' => 9, 'name' => 'time'],
        ];

        foreach ($types as $type) {
            QuestionType::updateOrCreate(['id' => $type['id']], ['name' => $type['name']]);
        }
    }
}