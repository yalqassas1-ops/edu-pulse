<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use Illuminate\Http\Request;

class ClassRoomController extends Controller
{
    /**
     * عرض قائمة جميع القاعات
     */
    public function index()
    {
        $classRooms = ClassRoom::all();
        return view('class_rooms.index', compact('classRooms'));
    }

    /**
     * عرض نموذج إضافة قاعة جديدة
     */
    public function create()
    {
        return view('class_rooms.create');
    }

    /**
     * حفظ القاعة الجديدة في قاعدة البيانات
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'     => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
        ]);

        ClassRoom::create($validatedData);

        return redirect()->route('class-rooms.index')
            ->with('success', 'تمت إضافة القاعة بنجاح');
    }

    /**
     * عرض تفاصيل قاعة محددة (اختياري)
     */
    public function show(ClassRoom $classRoom)
    {
        return view('class_rooms.show', compact('classRoom'));
    }

    /**
     * عرض نموذج تعديل بيانات القاعة
     */
    public function edit(ClassRoom $classRoom)
    {
        return view('class_rooms.edit', compact('classRoom'));
    }

    /**
     * تحديث بيانات القاعة في قاعدة البيانات
     */
    public function update(Request $request, ClassRoom $classRoom)
    {
        $validatedData = $request->validate([
            'name'     => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
        ]);

        $classRoom->update($validatedData);

        return redirect()->route('class-rooms.index')
            ->with('success', 'تم تعديل القاعة بنجاح');
    }

    /**
     * حذف القاعة من قاعدة البيانات
     */
    public function destroy(ClassRoom $classRoom)
    {
        $classRoom->delete();

        return redirect()->route('class-rooms.index')
            ->with('success', 'تم حذف القاعة بنجاح');
    }
}