<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiRequest;

class ApiLoginRequest extends APIRequest
{
    use RuleTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    public function prepareForValidation()
    {
        $this->merge([
            'email' => strtolower($this->email), // typically email must be lowercase all
        ]);
    }

    /**
     * Handle a passed validation attempt.
     */
    public function passedValidation()
    {
        $this->merge([
            'email' => strtolower($this->email), // makesure again input must have all lowercase data
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            'email' => $this->getEmailRules(),
            'password' => $this->getPasswordRules(),
        ];
    }
}
