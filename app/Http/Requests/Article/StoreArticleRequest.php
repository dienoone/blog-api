<?php

namespace App\Http\Requests\Article;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class StoreArticleRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')
            ],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'featured_image' => ['nullable', 'image', 'max:2048'],
            'status' => [
                'sometimes',
                Rule::in(['draft', 'published', 'archived'])
            ],
            'published_at' => ['nullable', 'date', 'after_or_equal:now'],
        ];
    }
}
