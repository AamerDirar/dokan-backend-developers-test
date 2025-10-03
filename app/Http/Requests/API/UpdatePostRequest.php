<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class UpdatePostRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'user_id' => 'nullable|numeric|exists:users,id',
            'category_id' => 'required|numeric|exists:categories,id',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
    */
    public function messages(): array
    {
        return [
            'title.required' => 'The title field is required.',
            'title.string' => 'The title must be a string.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'content.required' => 'The content field is required.',
            'content.string' => 'The content must be a string.',
            'user_id.numeric' => 'The user ID must be a number.',
            'user_id.exists' => 'The selected user ID is invalid.',
            'category_id.required' => 'The category ID field is required.',
            'category_id.numeric' => 'The category ID must be a number.',
            'category_id.exists' => 'The selected category ID is invalid.',
        ];
    }

    /**
     * Handle a failed validation attempt.
    */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'statusCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => __('messages.validation_failed'),
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
