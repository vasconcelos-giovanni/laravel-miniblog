<?php
declare(strict_types=1);
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|integer|exists:categories,id',
            'tags' => 'present|array',
            'tags.*.id' => 'nullable|integer|exists:tags,id',
            'tags.*.name' => 'required|string|max:255|distinct',
        ];
    }
}
