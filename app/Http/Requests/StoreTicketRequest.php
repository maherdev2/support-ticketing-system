<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255', 'min:5'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'category' => ['nullable', 'string', 'max:100'],
            'equipment_id' => ['nullable', 'exists:equipment,id'],
            'department_id' => ['nullable', 'exists:departments,id'],

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
            'title.required' => 'عنوان التذكرة مطلوب',
            'title.min' => 'يجب أن يكون العنوان 5 أحرف على الأقل',
            'title.max' => 'يجب ألا يتجاوز العنوان 255 حرفاً',
            'description.required' => 'وصف المشكلة مطلوب',
            'description.min' => 'يجب أن يكون الوصف 10 أحرف على الأقل',
            'description.max' => 'يجب ألا يتجاوز الوصف 5000 حرف',
            'priority.required' => 'الأولوية مطلوبة',
            'priority.in' => 'الأولوية المحددة غير صحيحة',
            'equipment_id.exists' => 'المعدات المحددة غير موجودة',
            'department_id.exists' => 'القسم المحدد غير موجود',
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
            'title' => strip_tags($this->title),
            'description' => strip_tags($this->description, '<br><p><ul><ol><li><strong><em>'),
        ]);
    }
}
