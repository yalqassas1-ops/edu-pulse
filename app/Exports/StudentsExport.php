<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize; // 👈 لضبط عرض الأعمدة تلقائياً
use Maatwebsite\Excel\Concerns\WithEvents;     // 👈 لتطبيق اتجاه RTL
use Maatwebsite\Excel\Events\AfterSheet;

class StudentsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function query()
    {
        return Student::query()
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%");
            })
            ->latest();
    }

    public function headings(): array
    {
        return [
            '#',
            'الاسم الكامل',
            'البريد الإلكتروني',
            'رقم الهاتف',
            'الجنس',
            'تاريخ التسجيل',
        ];
    }

    public function map($student): array
    {
        static $i = 0;
        $i++;

        return [
            $i,
            $student->name,
            $student->email,
            $student->phone ?? 'غير مدخل',
            $student->gender == 'female' ? 'أنثى' : 'ذكر',
            $student->created_at ? $student->created_at->format('Y-m-d') : '',
        ];
    }

    /**
     * جعل اتجاه ملف Excel من اليمين لليمين (Right-to-Left)
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
            },
        ];
    }
}