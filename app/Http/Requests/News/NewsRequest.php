<?php

namespace App\Http\Requests\News;

use App\Models\NewsCategory;
use App\Models\NewsSource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NewsRequest extends FormRequest
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
            'search' => ['nullable', 'string'],
            'date.from' => ['required', 'date', 'date_format:Y-m-d', 'before:data.to'],
            'date.to' => ['required', 'date', 'date_format:Y-m-d', 'after:data.from'],
            'category' => ['nullable', Rule::exists(NewsCategory::class, 'id')],
            'source' => ['nullable', Rule::exists(NewsSource::class, 'id')]
        ];
    }
}
