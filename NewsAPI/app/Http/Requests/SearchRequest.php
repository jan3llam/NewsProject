<?php

namespace App\Http\Requests;

use App\Enums\ApiErrorEnum;
use App\Enums\HttpResponseEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SearchRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        Log::channel('auth')->info('Validating Search Keyword');

        return [
            'keyword' => 'required|string|max:25',
        ];
    }


    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        Log::channel('auth')->error('Validation Search Error');

        throw new HttpResponseException(response()->json([
            'message' => ApiErrorEnum::UNPROCESSABLE_CONTENT->value,
            'errors' => $validator->errors(),
        ], HttpResponseEnum::UNPROCESSABLE_CONTENT->value));
    }

}
