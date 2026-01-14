<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MenuRequest extends FormRequest
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
        $id = $this->route('menu');
        return [
            'category_id' => 'required|exists:categories,id',
            'name' => [
                'required',
                'string',
                'min:3',
                Rule::unique('menus')
                    ->where(function ($query) {
                        return $query->where('category_id', $this->category_id)->whereNull('deleted_at');
                    })
                    ->ignore($id)
            ],
            'description' => 'nullable|string',
            'price' => 'required|integer',
            'image' => 'nullable|image|mimes:png,jpg,jpeg',
            'is_available' => 'required|boolean'
        ];
    }
}
