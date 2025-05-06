<?php

namespace App\Http\Requests;

use App\Enums\TransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount'        => ['required', 'numeric', 'min:0.01', 'max:1000000'],
            'type'          => ['required', Rule::enum(TransactionType::class)],
            'description'   => ['required', 'string', 'max:255'],
            'per_page'      => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort'          => ['sometimes', 'string', 'in:created_at,amount,type'],
            'order'         => ['sometimes', 'string', 'in:asc,desc']
        ];
    }
}
