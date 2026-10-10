<?php

namespace App\Http\Requests\Tag;

use App\Support\OrganizationSession;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CreateTagRequest extends FormRequest
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
        $orgId = OrganizationSession::getCurrentOrg();

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tags', 'name')->where('organization_id', $orgId),
            ],
            'slug' => [
                Rule::unique('tags', 'slug')->where('organization_id', $orgId),
            ],
            'color' => 'required|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tag name is required.',
            'name.string'   => 'Tag name must be a valid string.',
            'name.max'      => 'Tag name cannot exceed 255 characters.',
            'name.unique'   => 'A tag with this name already exists in your organization.',
            'slug.unique'   => 'A tag with this slug already exists in your organization.',
            'color.required' => 'Tag color is required.',
            'color.string'   => 'Tag color must be a valid string.',
            'color.max'      => 'Tag color cannot exceed 7 characters.',
            'color.regex'    => 'Tag color must be a valid hex color code (e.g. #6B7280).',
            'description.string' => 'Tag description must be a valid string.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->input('name', '')),
        ]); 
    }
}
