<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketReplyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by Policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'min:5', 'max:5000'],
            'is_internal' => ['sometimes', 'boolean'],

            // Attachments validation
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => [
                'required',
                'file',
                'max:10240', // 10MB max per file
                'mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt,log,zip',
            ],
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'message.required' => 'نص الرد مطلوب',
            'message.min' => 'يجب أن يكون الرد 5 أحرف على الأقل',
            'message.max' => 'يجب ألا يتجاوز الرد 5000 حرف',
            'is_internal.boolean' => 'قيمة الملاحظة الداخلية غير صحيحة',
            'attachments.max' => 'الحد الأقصى للمرفقات هو 5 ملفات',
            'attachments.*.required' => 'الملف مطلوب',
            'attachments.*.file' => 'يجب أن يكون المرفق ملفاً صحيحاً',
            'attachments.*.max' => 'يجب ألا يتجاوز حجم الملف 10 ميجابايت',
            'attachments.*.mimes' => 'نوع الملف غير مسموح به. الأنواع المسموحة: jpg, jpeg, png, gif, pdf, doc, docx, xls, xlsx, txt, log, zip',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize input to prevent XSS
        $this->merge([
            'message' => strip_tags($this->message, '<br><p><ul><ol><li><strong><em>'),
            'is_internal' => $this->boolean('is_internal', false),
        ]);
    }
}
