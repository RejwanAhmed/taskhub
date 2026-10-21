<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
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
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',
            'status'          => 'required|in:to_do,in_progress,in_review,on_hold,completed',
            'priority'        => 'required|in:low,medium,high,urgent',
            'task_type_id'    => 'nullable|exists:task_types,id',
            'assigned_to'     => 'nullable|exists:users,id',
            'due_date'        => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0',
            'tags'            => 'nullable|array',
            'tags.*'          => 'exists:tags,id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'          => 'Task title is required.',
            'title.string'            => 'Task title must be a valid string.',
            'title.max'               => 'Task title must not exceed 255 characters.',

            'status.required'         => 'Status is required.',
            'status.in'               => 'Invalid status value.',

            'priority.required'       => 'Priority is required.',
            'priority.in'             => 'Invalid priority value.',

            'task_type_id.exists'     => 'Selected task type does not exist.',

            'assigned_to.exists'      => 'Selected user does not exist.',

            'due_date.date'           => 'Due date must be a valid date.',

            'estimated_hours.numeric' => 'Estimated hours must be a number.',
            'estimated_hours.min'     => 'Estimated hours must be at least 0.',

            'tags.array'              => 'Tags must be an array.',
            'tags.*.exists'           => 'One or more selected tags do not exist.',
        ];
    }
}
