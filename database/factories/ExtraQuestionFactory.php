<?php

namespace Database\Factories;

use App\Models\ExtraMatchCategory;
use App\Models\ExtraMatchItem;
use App\Models\ExtraQuestion;
use App\Models\ExtraQuestionOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExtraQuestion>
 */
class ExtraQuestionFactory extends Factory
{
    protected $model = ExtraQuestion::class;

    public function definition(): array
    {
        return [
            'typ' => ExtraQuestion::TYP_MATCHING,
            'lernabschnitt' => '5',
            'frage' => 'Default Test-Frage',
            'image_path' => null,
        ];
    }

    /**
     * Matching-Frage (Default: 3 Kategorien + 3 Items).
     */
    public function matching(int $categories = 3, int $items = 3): static
    {
        return $this->state(fn () => ['typ' => ExtraQuestion::TYP_MATCHING])
            ->afterCreating(function (ExtraQuestion $q) use ($categories, $items) {
                $cats = [];
                for ($i = 1; $i <= $categories; $i++) {
                    $cats[] = ExtraMatchCategory::create([
                        'extra_question_id' => $q->id,
                        'name' => 'Kategorie ' . $i,
                        'sort_order' => $i,
                    ]);
                }
                for ($i = 1; $i <= $items; $i++) {
                    $cat = $cats[($i - 1) % count($cats)];
                    ExtraMatchItem::create([
                        'extra_question_id' => $q->id,
                        'text' => 'Item ' . $i,
                        'correct_category_id' => $cat->id,
                        'sort_order' => $i,
                    ]);
                }
            });
    }

    /**
     * image_name-Frage (3 Options, 1 korrekt).
     */
    public function imageName(): static
    {
        return $this->state(fn () => [
            'typ' => ExtraQuestion::TYP_IMAGE_NAME,
            'image_path' => 'extra-questions/dummy.jpg',
        ])->afterCreating(function (ExtraQuestion $q) {
            ExtraQuestionOption::create([
                'extra_question_id' => $q->id, 'text' => 'Hammer',
                'is_correct' => true, 'sort_order' => 1,
            ]);
            ExtraQuestionOption::create([
                'extra_question_id' => $q->id, 'text' => 'Säge',
                'is_correct' => false, 'sort_order' => 2,
            ]);
            ExtraQuestionOption::create([
                'extra_question_id' => $q->id, 'text' => 'Zange',
                'is_correct' => false, 'sort_order' => 3,
            ]);
        });
    }

    /**
     * image_select-Frage (3 Options mit Image-Pfaden, 1 korrekt).
     */
    public function imageSelect(): static
    {
        return $this->state(fn () => [
            'typ' => ExtraQuestion::TYP_IMAGE_SELECT,
            'image_path' => null,
        ])->afterCreating(function (ExtraQuestion $q) {
            ExtraQuestionOption::create([
                'extra_question_id' => $q->id,
                'image_path' => 'extra-questions/opt1.jpg',
                'is_correct' => true, 'sort_order' => 1,
            ]);
            ExtraQuestionOption::create([
                'extra_question_id' => $q->id,
                'image_path' => 'extra-questions/opt2.jpg',
                'is_correct' => false, 'sort_order' => 2,
            ]);
            ExtraQuestionOption::create([
                'extra_question_id' => $q->id,
                'image_path' => 'extra-questions/opt3.jpg',
                'is_correct' => false, 'sort_order' => 3,
            ]);
        });
    }
}
