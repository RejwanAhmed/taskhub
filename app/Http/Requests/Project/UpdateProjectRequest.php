<?php

namespace App\Http\Requests\Project;

use App\Support\OrganizationSession;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
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
        $project = $this->route('project');
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('projects', 'name')->where('organization_id', OrganizationSession::getCurrentOrg())->ignore($project),
            ],
            'description' => 'nullable|string',
            'status' => 'required|in:planning,active,on_hold,completed',
            'color' => 'required|string|max:7',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Project name is required.',
            'name.string' => 'Project name must be a valid string.',
            'name.max' => 'Project name cannot exceed 255 characters.',
            'name.unique' => 'A project with this name already exists in your organization.',
            'description.string' => 'Description must be a valid string.',
            'status.required' => 'Project status is required.',
            'status.in' => 'Selected project status is invalid.',
            'color.required' => 'Project color is required.',
            'color.string' => 'Project color must be a valid string.',
            'color.max' => 'Project color code cannot exceed 7 characters.',
            'start_date.date' => 'Start date must be a valid date.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after_or_equal' => 'End date must be after or equal to the start date.',
        ];
    }
}
