<?php

namespace App\Http\Requests\Organization;

use Illuminate\Foundation\Http\FormRequest;

class SwitchOrganizationRequest extends FormRequest
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
        $organization = $this->route('organization');
        return [
            'organization' => [
                'required',
                function ($attribute, $value, $fail) use ($organization) {
                    if ($organization->status != 'active') {
                        $fail('This organization is not active');
                    }
                }
            ]
        ];
    }

    // as the organization isnt in the request body, we need to add the following
    protected function prepareForValidation(): void
    {
        $this->merge([
            'organization' => $this->route('organization')?->id,
        ]);
    }
}
