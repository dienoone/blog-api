<?php

namespace App\Http\Requests\Comment;

use App\Http\Requests\BaseRequest;

class UpdateCommentRequest extends BaseRequest
{
    public function authorize(): bool
    {
        // TODO: Verify user owns this comment
        // return $this->user()->id === $this->comment->user_id;
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:1000'],
        ];
    }
}
