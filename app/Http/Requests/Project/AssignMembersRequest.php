<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class AssignMembersRequest extends FormRequest
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
            'members'        => ['present', 'array'],
            'members.*.id'   => ['required', 'integer', 'exists:users,id'],
            'members.*.role' => ['required', 'string', 'in:member,manager,owner'],
        ];
    }

    public function messages(): array
    {
        return [
            'members.present'        => 'Members list is required.',
            'members.array'          => 'Members must be a list.',
            'members.*.id.required'  => 'Each member must have a user.',
            'members.*.id.integer'   => 'Member user id must be a number.',
            'members.*.id.exists'    => 'Selected user does not exist.',
            'members.*.role.required'=> 'Each member must have a role.',
            'members.*.role.in'      => 'Role must be member, manager, or owner.',
        ];
    }
}
