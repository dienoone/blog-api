<?php

namespace App\Http\Requests\Tag;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class StoreTagRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'slug' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('tags', 'slug')
            ],
        ];
    }
}
