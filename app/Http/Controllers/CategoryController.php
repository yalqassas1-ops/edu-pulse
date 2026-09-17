<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class CategoryController extends Controller
{
    /**
     * عرض قائمة التصنيفات النشطة (غير المؤرشفة)
     */
    public function index()
    {
        $categories = Category::latest()->get();
        return view('categories.index', compact('categories'));
    }

    /**
     * عرض قائمة التصنيفات المؤرشفة (Soft Deleted)
     */
    public function archive()
    {
        $categories = Category::onlyTrashed()->latest()->get();
        return view('categories.archive', compact('categories'));
    }

    /**
     * عرض نموذج إضافة تصنيف جديد
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * حفظ التصنيف الجديد وإرسال الإشعار
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($validatedData);

        // 🔔 إشعار إضافة تصنيف جديد
        $users = User::all();
        if ($users->isNotEmpty()) {
            try {
                Notification::send($users, new SystemNotification(
                    'إضافة تصنيف جديد',
                    'تمت إضافة التصنيف (' . $category->name . ') بنجاح.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Category Store: ' . $e->getMessage());
            }
        }

        return redirect()->route('categories.index')->with('success', 'تم إضافة التصنيف بنجاح وإرسال الإشعار!');
    }

    /**
     * عرض نموذج تعديل بيانات التصنيف
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * تحديث بيانات التصنيف وإرسال الإشعار
     */
    public function update(Request $request, Category $category)
    {
        $validatedData = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($validatedData);

        // 🔔 إشعار تعديل بيانات التصنيف
        $users = User::all();
        if ($users->isNotEmpty()) {
            try {
                Notification::send($users, new SystemNotification(
                    'تعديل بيانات تصنيف',
                    'تم تحديث بيانات التصنيف (' . $category->name . ') بنجاح.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Category Update: ' . $e->getMessage());
            }
        }

        return redirect()->route('categories.index')->with('success', 'تم تعديل التصنيف بنجاح!');
    }

    /**
     * أرشفة/حذف التصنيف مؤقتاً (Soft Delete) وإرسال الإشعار
     */
    public function destroy(Category $category)
    {
        $categoryName = $category->name;
        $category->delete(); // أرشفة خفيفة

        // 🔔 إشعار أرشفة التصنيف
        $users = User::all();
        if ($users->isNotEmpty()) {
            try {
                Notification::send($users, new SystemNotification(
                    'أرشفة تصنيف',
                    'تم نقل التصنيف (' . $categoryName . ') إلى الأرشيف.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Category Delete: ' . $e->getMessage());
            }
        }

        return redirect()->route('categories.index')->with('success', 'تم نقل التصنيف إلى الأرشيف بنجاح!');
    }

    /**
     * استعادة التصنيف من الأرشيف
     */
    public function restore($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();

        return redirect()->route('categories.archive')->with('success', 'تمت استعادة التصنيف من الأرشيف بنجاح!');
    }

    /**
     * الحذف النهائي للتصنيف من قاعدة البيانات
     */
    public function forceDelete($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->forceDelete();

        return redirect()->route('categories.archive')->with('success', 'تم حذف التصنيف نهائياً من النظام!');
    }

    /**
     * تفريغ الأرشيف بالكامل (حذف نهائي لجميع التصنيفات المؤرشفة)
     */
    public function forceDeleteAllArchive()
    {
        $trashedCategories = Category::onlyTrashed()->get();
        $count = $trashedCategories->count();

        if ($count === 0) {
            return redirect()->route('categories.archive')
                ->with('error', 'الأرشيف فارغ بالفعل، لا توجد تصنيفات للحذف.');
        }

        // تفريغ سجلات الأرشيف كلياً
        Category::onlyTrashed()->forceDelete();

        // 🔔 إرسال إشعار تفريغ الأرشيف
        $users = User::all();
        if ($users->isNotEmpty()) {
            try {
                Notification::send($users, new SystemNotification(
                    'تفريغ أرشيف التصنيفات',
                    'تم تفريغ أرشيف التصنيفات بالكامل وحذف عدد (' . $count . ') تصنيف نهائياً.'
                ));
            } catch (\Exception $e) {
                Log::error('Broadcast Connection Failed on Category Force Delete All: ' . $e->getMessage());
            }
        }

        return redirect()->route('categories.archive')
            ->with('success', 'تم تفريغ أرشيف التصنيفات بالكامل بحذف ' . $count . ' تصنيف نهائياً!');
    }
}