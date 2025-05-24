<?php

namespace App\Http\Requests\PostRequests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
        return [
            'title'          => 'required|string|max:255',
            'content'        => 'required|string',
            'image_url'      => 'nullable|url',
            'scheduled_time' => 'required|date|after:now',
            'status'         => 'required|in:draft,scheduled,published',
            'platforms'      => 'required|array',
            'platforms.*'    => 'exists:platforms,id',
        ];
    }
}
