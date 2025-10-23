<?php

namespace App\Http\Requests\Tag;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateTagRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:50'],
            'slug' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('tags', 'slug')->ignore($this->route('tag'))
            ],
        ];
    }
}
