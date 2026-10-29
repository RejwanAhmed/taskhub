<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class CreateReorderRequest extends FormRequest
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
            'task_id'  => 'required|exists:tasks,id',
            'status'   => 'required|in:to_do,in_progress,in_review,on_hold,completed',
            'position' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'task_id.required'  => 'Task is required.',
            'task_id.exists'    => 'Selected task does not exist.',

            'status.required'   => 'Status is required.',
            'status.in'         => 'Invalid status value.',

            'position.required' => 'Position is required.',
            'position.integer'  => 'Position must be a valid number.',
            'position.min'      => 'Position must be at least 0.',
        ];
    }
}
