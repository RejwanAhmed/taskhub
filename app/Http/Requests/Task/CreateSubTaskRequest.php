<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class CreateSubTaskRequest extends FormRequest
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
            'priority'        => 'required|in:low,medium,high,urgent',
            'task_type_id'    => 'nullable|exists:task_types,id',
            'assigned_to'     => 'nullable|exists:users,id',
            'due_date'        => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'          => 'Subtask title is required.',
            'title.string'            => 'Subtask title must be a valid string.',
            'title.max'               => 'Subtask title must not exceed 255 characters.',

            'priority.required'       => 'Priority is required.',
            'priority.in'             => 'Invalid priority value.',

            'task_type_id.exists'     => 'Selected task type does not exist.',

            'assigned_to.exists'      => 'Selected user does not exist.',

            'due_date.date'           => 'Due date must be a valid date.',

            'estimated_hours.numeric' => 'Estimated hours must be a number.',
            'estimated_hours.min'     => 'Estimated hours must be at least 0.',
        ];
    }
}
