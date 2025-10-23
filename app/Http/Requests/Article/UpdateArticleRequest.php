<?php

namespace App\Http\Requests\Article;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use Auth;

class UpdateArticleRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['sometimes', 'string'],
            'category_id' => [
                'sometimes',
                Rule::exists('categories', 'id')
            ],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'featured_image' => ['nullable', 'image', 'max:2048'],
            'status' => [
                'sometimes',
                Rule::in(['draft', 'published', 'archived'])
            ],
            'published_at' => ['nullable', 'date'],
        ];
    }
}
