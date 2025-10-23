<?php

namespace App\Http\Requests\Comment;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class StoreCommentRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'article_id' => [
                'required',
                Rule::exists('articles', 'id')
            ],
            'content' => ['required', 'string', 'max:1000'],
            'parent_id' => [
                'nullable',
                Rule::exists('comments', 'id')
            ],
        ];
    }
}
