@php
    $invoices = \App\Models\Invoice::where('customer_id', $customer->id)->get();
    $payments = \App\Models\DebtPayment::where('customer_id', $customer->id)->get();

    $transactions = collect();

    foreach ($invoices as $invoice) {
        $transactions->push([
            'date' => $invoice->created_at,
            'type' => 'فاتورة مبيعات',
            'amount' => -$invoice->total_amount,
            'note' => '#' . $invoice->invoice_number,
        ]);

        if ($invoice->paid_amount > 0) {
            $transactions->push([
                'date' => $invoice->created_at,
                'type' => 'دفعة نقدية (فاتورة)',
                'amount' => $invoice->paid_amount,
                'note' => '#' . $invoice->invoice_number,
            ]);
        }
    }

    foreach ($payments as $payment) {
        $transactions->push([
            'date' => $payment->payment_date,
            'type' => 'تسديد دين',
            'amount' => $payment->amount,
            'note' => $payment->note,
        ]);
    }

    $transactions = $transactions->sortBy('date');
    $currentBalance = 0;
@endphp

<div class="space-y-4">
    <div class="p-4 bg-gray-50 rounded-lg border flex justify-between items-center">
        <div>
            <span class="text-sm text-gray-500">الرصيد الحالي (الدين)</span>
            <div class="text-2xl font-bold text-danger-600">
                {{ number_format(abs($customer->total_debt)) }} د.ع
            </div>
        </div>
        <x-filament::button color="gray" icon="heroicon-m-printer" onclick="window.print()">
            طباعة الكشف
        </x-filament::button>
    </div>

    <table class="w-full text-sm text-right border-collapse">
        <thead>
            <tr class="bg-gray-100 border-b">
                <th class="p-2 border">التاريخ</th>
                <th class="p-2 border">النوع</th>
                <th class="p-2 border">المبلغ</th>
                <th class="p-2 border">التفاصيل</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $trans)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-2 border">{{ $trans['date']->format('Y-m-d') }}</td>
                    <td class="p-2 border">{{ $trans['type'] }}</td>
                    <td @class([
                        'p-2 border font-medium',
                        'text-danger-600' => $trans['amount'] < 0,
                        'text-success-600' => $trans['amount'] >= 0,
                    ])>
                        {{ number_format($trans['amount']) }}
                    </td>
                    <td class="p-2 border text-gray-500">{{ $trans['note'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>