<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;

class NytBestSellerSearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'author' => ['nullable', 'string'],
            'title' => ['nullable', 'string'],
            'offset' => [
                'nullable',
                'numeric',
                function ($attribute, $value, $fail) {
                    if (is_numeric($value) && ! (0 === $value % 5 && 0 === $value % 4)) {
                        $fail("{$attribute} must be divisible by 20.");
                    }
                },
            ],
            'isbn' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    foreach (explode(';', $value) as $isbn) {
                        $isbnLength = Str::length($isbn);

                        if (! $isbnLength) {
                            return $fail("{$attribute} must not end with a semicolon.");
                        }

                        if (10 !== $isbnLength && 13 !== $isbnLength) {
                            return $fail("{$attribute} must have 10 or 13 digits.");
                        }
                    }
                },
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
        ], 422));
    }
}
