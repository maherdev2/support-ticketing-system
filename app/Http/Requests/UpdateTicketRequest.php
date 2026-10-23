<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketRequest extends FormRequest
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
            'title' => ['sometimes', 'required', 'string', 'max:255', 'min:5'],
            'description' => ['sometimes', 'required', 'string', 'min:10', 'max:5000'],
            'priority' => ['sometimes', 'required', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'status' => ['sometimes', 'required', Rule::in(['open', 'assigned', 'in_progress', 'waiting_user', 'resolved', 'closed'])],
            'category' => ['sometimes', 'nullable', 'string', 'max:100'],
            'assigned_to' => ['sometimes', 'nullable', 'exists:users,id'],
            'equipment_id' => ['sometimes', 'nullable', 'exists:equipment,id'],
            'department_id' => ['sometimes', 'nullable', 'exists:departments,id'],
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
            'status.required' => 'الحالة مطلوبة',
            'status.in' => 'الحالة المحددة غير صحيحة',
            'assigned_to.exists' => 'المستخدم المحدد غير موجود',
            'equipment_id.exists' => 'المعدات المحددة غير موجودة',
            'department_id.exists' => 'القسم المحدد غير موجود',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('title')) {
            $this->merge([
                'title' => strip_tags($this->title),
            ]);
        }

        if ($this->has('description')) {
            $this->merge([
                'description' => strip_tags($this->description, '<br><p><ul><ol><li><strong><em>'),
            ]);
        }
    }
}
