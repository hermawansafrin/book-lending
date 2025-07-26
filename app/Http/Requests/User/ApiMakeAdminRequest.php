<?php

namespace App\Http\Requests\User;

use App\Http\Requests\APIRequest;
use App\Rules\UserAlreadyAsAdmin;

class ApiMakeAdminRequest extends APIRequest
{
    use RuleTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // already defined on middleware (api.onlyAdmin)
    }

    /**
     * Prepare the data for validation.
     */
    public function prepareForValidation(): void
    {
        $this->merge([
            'id' => (int) $this->route('id'),
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
            'id' => $this->getUserIdAlreadyAsAdminRules(),
        ];
    }

    /**
     * Get the validation rules for the user id.
     */
    private function getUserIdAlreadyAsAdminRules(): array
    {
        $rules = $this->getUserIdRules();
        $rules[] = new UserAlreadyAsAdmin;

        return $rules;
    }
}
