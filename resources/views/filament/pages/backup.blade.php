<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">
                حالة النسخ الاحتياطي
            </x-slot>

            <div class="flex items-center gap-4">
                <div class="p-3 bg-success-100 text-success-700 rounded-full">
                    <x-heroicon-o-check-circle class="w-6 h-6" />
                </div>
                <div>
                    <div class="font-medium">البيانات مؤمنة</div>
                    <p class="text-sm text-gray-500">آخر نسخة احتياطية ناجحة: اليوم، 02:30 م</p>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                قائمة النسخ المتوفرة
            </x-slot>

            <table class="w-full text-sm text-right border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b">
                        <th class="p-2 border font-medium">اسم الملف</th>
                        <th class="p-2 border font-medium">التاريخ</th>
                        <th class="p-2 border font-medium">الحجم</th>
                        <th class="p-2 border font-medium">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b">
                        <td class="p-2 border">backup-2026-02-14.sql</td>
                        <td class="p-2 border">2026-02-14 14:30</td>
                        <td class="p-2 border">1.2 MB</td>
                        <td class="p-2 border text-center">
                            <x-filament::link href="#" icon="heroicon-m-arrow-down-tray">تحميل</x-filament::link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </x-filament::section>

        <x-filament::section class="border-danger-200">
            <x-slot name="heading">
                <span class="text-danger-600">منطقة الخطر - استرجاع البيانات</span>
            </x-slot>

            <p class="text-sm text-gray-500 mb-4">
                تحذير: استرجاع نسخة قديمة سيؤدي إلى حذف كافة البيانات الحالية التي أضيفت بعد تاريخ تلك النسخة. هذا
                الإجراء لا يمكن التراجع عنه.
            </p>

            <x-filament::button color="danger" icon="heroicon-m-arrow-path">
                بدء عملية الاسترجاع
            </x-filament::button>
        </x-filament::section>
    </div>
</x-filament-panels::page>