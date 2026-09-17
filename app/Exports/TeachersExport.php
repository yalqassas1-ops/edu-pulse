<?php

namespace App\Exports;

use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class TeachersExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function query()
    {
        return Teacher::query()
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%")
                  ->orWhere('specialization', 'like', "%{$this->search}%");
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
            'التخصص',
            'تاريخ التسجيل',
        ];
    }

    public function map($teacher): array
    {
        static $i = 0;
        $i++;

        return [
            $i,
            $teacher->name,
            $teacher->email,
            $teacher->phone ?? 'غير مدخل',
            $teacher->specialization ?? 'غير محدد',
            $teacher->created_at ? $teacher->created_at->format('Y-m-d') : '',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
            },
        ];
    }
}