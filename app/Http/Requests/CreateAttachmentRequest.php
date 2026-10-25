<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAttachmentRequest extends FormRequest
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
            'files' => 'required|array|max:5',
            'files.*' => 'required|file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,txt|mimetypes:image/jpeg,image/png,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/plain',
        ];
    }

    public function messages(): array
    {
        return [
            'files.required'      => 'Please select at least one file.',
            'files.max'           => 'You can upload a maximum of 5 files.',
            'files.*.required'    => 'File is required.',
            'files.*.file'        => 'Invalid file.',
            'files.*.max'         => 'Each file must not exceed 5MB.',
            'files.*.mimes'       => 'Allowed file types: jpg, jpeg, png, pdf, doc, docx, xls, xlsx, txt.',
            'files.*.mimetypes'   => 'Invalid file content. File type does not match its extension.',
        ];
    }
}
