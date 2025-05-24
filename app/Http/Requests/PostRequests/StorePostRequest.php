<?php

namespace App\Http\Requests\PostRequests;

use App\Models\Platform;
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

    /**
     * Configure the validator instance.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'platforms' => $this->platforms ?? [],
        ]);
    }

    /**
     * Add custom validation logic for platform-specific requirements.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $platforms = $this->input('platforms', []);
            $content = $this->input('content');

            // Example: Add platform-specific validation
            foreach ($platforms as $platformId) {
                $platform = Platform::find($platformId);

                if ($platform && $platform->type === 'twitter' && strlen($content) > 280) {
                    $validator->errors()->add('content', 'Content exceeds the 280-character limit for Twitter.');
                }

                if ($platform && $platform->type === 'instagram' && strlen($content) > 2200) {
                    $validator->errors()->add('content', 'Content exceeds the 2200-character limit for Instagram.');
                }
                if ($platform && $platform->type === 'facebook' && strlen($content) > 63206) {
                    $validator->errors()->add('content', 'Content exceeds the 63206-character limit for Facebook.');
                }
                if ($platform && $platform->type === 'linkedin' && strlen($content) > 1300) {
                    $validator->errors()->add('content', 'Content exceeds the 1300-character limit for LinkedIn.');
                }
                if ($platform && $platform->type === 'tiktok' && strlen($content) > 1000) {
                    $validator->errors()->add('content', 'Content exceeds the 1000-character limit for TikTok.');
                }
                if ($platform && $platform->type === 'youtube' && strlen($content) > 5000) {
                    $validator->errors()->add('content', 'Content exceeds the 5000-character limit for YouTube.');
                }
            }
        });
    }
}
