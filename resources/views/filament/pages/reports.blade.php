<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">
                تصفية التقارير
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-filament::input.wrapper label="من تاريخ">
                    <x-filament::input type="date" wire:model.live="startDate" />
                </x-filament::input.wrapper>

                <x-filament::input.wrapper label="إلى تاريخ">
                    <x-filament::input type="date" wire:model.live="endDate" />
                </x-filament::input.wrapper>
            </div>
        </x-filament::section>

        @php
            $data = $this->getReportData();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                <span class="text-sm text-gray-500">إجمالي المبيعات</span>
                <div class="text-xl font-bold text-primary-600">{{ number_format($data['total_sales']) }} د.ع</div>
            </div>

            <div class="p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                <span class="text-sm text-gray-500">إجمالي المصروفات</span>
                <div class="text-xl font-bold text-danger-600">{{ number_format($data['total_expenses']) }} د.ع</div>
            </div>

            <div class="p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                <span class="text-sm text-gray-500">التحصيلات (ديون/أقساط)</span>
                <div class="text-xl font-bold text-success-600">{{ number_format($data['collected_payments']) }} د.ع
                </div>
            </div>

            <div class="p-4 bg-blue-50 rounded-xl shadow-sm border border-blue-100">
                <span class="text-sm text-blue-600 font-medium">صافي التدفق المالي</span>
                <div class="text-xl font-bold text-blue-800">{{ number_format($data['net_cash_flow']) }} د.ع</div>
            </div>
        </div>

        <x-filament::section>
            <x-slot name="heading">
                ملاحظة
            </x-slot>
            <p class="text-sm text-gray-500">
                هذه الأرقام تعتمد على البيانات المدخلة في النظام خلال الفترة المحددة أعلاه. يرجى التأكد من مزامنة كافة
                أجهزة الموبايل للحصول على أدق النتائج.
            </p>
        </x-filament::section>
    </div>
</x-filament-panels::page>