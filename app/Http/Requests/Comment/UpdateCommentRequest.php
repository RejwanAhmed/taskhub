<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
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
            'content'                  => 'nullable|string|required_without:new_files',
            'removed_attachment_ids'   => 'nullable|array',
            'removed_attachment_ids.*' => 'exists:attachments,id',
            'new_files'                => 'nullable|array|max:5',
            'new_files.*'              => 'file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,txt|mimetypes:image/jpeg,image/png,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/plain',
        ];
    }

    public function messages(): array
    {
        return [
            'content.required_without'       => 'Please write a comment or attach a file.',
            'removed_attachment_ids.*.exists' => 'Invalid attachment.',
            'new_files.max'                   => 'You can upload a maximum of 5 files.',
            'new_files.*.file'                => 'Invalid file.',
            'new_files.*.max'                 => 'Each file must not exceed 5MB.',
            'new_files.*.mimes'               => 'Allowed types: jpg, jpeg, png, pdf, doc, docx, xls, xlsx, txt.',
            'new_files.*.mimetypes'           => 'Invalid file content. File type does not match its extension.',
        ];
    }
}
