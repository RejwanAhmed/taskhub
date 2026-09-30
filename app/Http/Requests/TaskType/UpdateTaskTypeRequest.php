<?php

namespace App\Http\Requests\TaskType;

use App\Support\OrganizationSession;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskTypeRequest extends FormRequest
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
        $taskType = $this->route('task_type');
        return [
             'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('task_types', 'name')->where('organization_id', OrganizationSession::getCurrentOrg())->ignore($taskType),
            ],
            'color' => 'required|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string',
            'is_default' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Task Type name is required.',
            'name.string'   => 'Task Type name must be a valid string.',
            'name.max'      => 'Task Type name cannot exceed 255 characters.',
            'name.unique'   => 'A Task Type with this name already exists in your organization.',
            'color.required' => 'Task Type color is required.',
            'color.string'   => 'Task Type color must be a valid string.',
            'color.max'      => 'Task Type color cannot exceed 7 characters.',
            'color.regex'    => 'Task Type color must be a valid hex color code (e.g. #6B7280).',
            'icon.string'    => 'Task Type icon must be a valid string.',
            'is_default.boolean' => 'Task Type default status must be a boolean value.',
        ];
    }
}
