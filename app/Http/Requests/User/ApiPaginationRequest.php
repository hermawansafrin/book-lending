<?php

namespace App\Http\Requests\User;

use App\Http\Requests\APIRequest;

class ApiPaginationRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // already defined on middleware (api.onlyAdmin)
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'per_page' => empty($this->per_page) ? config('values.pagination.default_per_page') : (int) $this->per_page,
            'page' => empty($this->page) ? config('values.pagination.default_page') : (int) $this->page,
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
            'per_page' => $this->getPerPageRules(),
            'page' => $this->getPageRules(),
        ];
    }
}
