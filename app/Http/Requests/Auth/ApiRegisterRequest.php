<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiRequest;

class ApiRegisterRequest extends APIRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => $this->getNameRules(),
            'email' => $this->getEmailRules(isLogin: false),
            'password' => $this->getPasswordRegisterRules(mustConfirm: true),
            'password_confirmation' => $this->getPasswordRegisterRules(mustConfirm: false),
        ];
    }
}
