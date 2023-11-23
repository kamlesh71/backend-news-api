<?php

namespace App\Http\Requests\User;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\NewsSource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePreferenceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'preference_sources' => ['nullable', 'array'],
            'preference_sources.*' => ['required', Rule::exists(NewsSource::class, 'id')],

            'preference_categories' => ['nullable', 'array'],
            'preference_categories.*' => ['required', Rule::exists(NewsCategory::class, 'id')],

            'preference_authors' => ['nullable', 'array'],
            'preference_authors.*' => [
                'required',
                Rule::exists(News::class, 'author')
                    ->whereNotNull('author')
                    ->where('author', '!=', '')
            ],
        ];
    }
}
