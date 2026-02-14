<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">
                تفاصيل الاشتراك الحالي
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1">
                    <span class="text-sm text-gray-500">حالة الاشتراك</span>
                    <span @class([
                        'px-2 py-1 text-xs font-semibold rounded-full w-fit',
                        'bg-success-100 text-success-700' => $subscription?->is_active,
                        'bg-danger-100 text-danger-700' => !$subscription?->is_active,
                    ])>
                        {{ $subscription?->is_active ? 'فعّال' : 'منتهي' }}
                    </span>
                </div>

                <div class="flex flex-col gap-1">
                    <span class="text-sm text-gray-500">تاريخ الانتهاء</span>
                    <span class="font-medium">
                        {{ $subscription ? $subscription->end_date->format('Y-m-d') : 'لا يوجد بيانات' }}
                    </span>
                </div>

                <div class="flex flex-col gap-1">
                    <span class="text-sm text-gray-500">اسم الباقة</span>
                    <span class="font-medium">{{ $subscription?->plan_name ?? 'Monthly' }}</span>
                </div>

                <div class="flex flex-col gap-1">
                    <span class="text-sm text-gray-500">حالة الدفع</span>
                    <span class="font-medium text-success-600">
                        {{ $subscription?->payment_status === 'paid' ? 'مدفوع' : 'غير مدفوع' }}
                    </span>
                </div>
            </div>

            <x-slot name="footer">
                <div class="flex justify-end gap-3">
                    <x-filament::button color="warning">
                        تجديد الاشتراك الآن
                    </x-filament::button>
                </div>
            </x-slot>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                سجل المدفوعات
            </x-slot>

            <div class="text-center py-4 text-gray-500 text-sm">
                لا توجد مدفوعات سابقة للعرض حالياً.
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>