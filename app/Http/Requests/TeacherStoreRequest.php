<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeacherStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:teachers,email',
            'phone'          => 'nullable|string|max:20',
            'specialization' => 'required|string|max:255',
            'attachment'     => 'nullable|file|mimes:jpeg,png,pdf|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'           => 'اسم المحاضر مطلوب.',
            'email.required'          => 'البريد الإلكتروني مطلوب.',
            'email.unique'            => 'البريد الإلكتروني مُسجل مسبقاً.',
            'specialization.required' => 'التخصص مطلوب.',
            'attachment.mimes'        => 'نوع الملف غير مسموح. المسموح فقط: jpeg, png, pdf.',
            'attachment.max'          => 'حجم الملف كبير جداً، الحد الأقصى هو 2 ميجابايت.',
        ];
    }
}