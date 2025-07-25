<?php

namespace App\Http\Requests;

/**
 * General rule trait for all api request validation
 */
trait ApiRuleTrait
{
    /**
     * Get the rules for the per_page parameter on pagination
     */
    public function getPerPageRules(): array
    {
        return [
            'bail',
            'nullable',
            'integer',
            'numeric',
            'min:1',
            'max: 100',
        ];
    }

    /**
     * Get the rules for the page parameter on pagination
     */
    public function getPageRules(): array
    {
        return [
            'bail',
            'nullable',
            'integer',
            'numeric',
            'min:1',
        ];
    }

    /**
     * Get the rules for the search parameter on pagination
     */
    public function getSearchRules(): array
    {
        return [
            'bail',
            'nullable',
            'string',
            'max: 255',
        ];
    }

    /**
     * Get the rules for the order_by parameter on pagination
     */
    public function getOrderByRules(): array
    {
        return [
            'bail',
            'nullable',
            'string',
            'in:asc,desc',
        ];
    }
}
