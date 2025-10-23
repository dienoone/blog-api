<?php

namespace App\Http\Requests;

use App\Exceptions\ValidationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

abstract class BaseRequest extends FormRequest
{
    /**
     * Indicates if the validator should stop on the first rule failure.
     * @var bool
     */
    // protected $stopOnFirstFailure = true;

    protected function failedValidation(Validator $validator)
    {
        $errors = collect($validator->errors()->messages())
            ->map(fn($messages) => match (count($messages)) {
                1 => $messages[0],
                default => $messages[0] . ' (+' . (count($messages) - 1) . ' more)'
            })
            ->toArray();

        throw new ValidationException('Validation faild', null, $errors);
    }
}
