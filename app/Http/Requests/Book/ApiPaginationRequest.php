<?php

namespace App\Http\Requests\Book;

use App\Http\Requests\APIRequest;

class ApiPaginationRequest extends APIRequest
{
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
    protected function prepareForValidation(): void
    {
        // adjust query paramter for preparation to hit data filter
        $this->merge([
            'per_page' => empty($this->per_page) ? config('values.pagination.default_per_page') : (int) $this->per_page,
            'page' => empty($this->page) ? config('values.pagination.default_page') : (int) $this->page,
            'search' => empty($this->search) ? null : $this->search,
            'sort_by' => empty($this->sort_by) ? 'title' : $this->sort_by,
            'order_by' => empty($this->order_by) ? 'asc' : $this->order_by,
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
            'search' => $this->getSearchRules(),
            'sort_by' => $this->getSortByRules(),
            'order_by' => $this->getOrderByRules(),
        ];
    }

    /**
     * Get the rules for the sort_by parameter on pagination
     */
    public function getSortByRules(): array
    {
        return [
            'bail',
            'nullable',
            'string',
            'in:title,author,available_copies,created_at',
        ];
    }
}
