<?php

namespace App\Filament\Widgets;

use App\Models\InvoiceItem;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class BestSellers extends ChartWidget
{
    protected static ?string $heading = 'أكثر المنتجات مبيعات هذا الشهر';

    protected function getData(): array
    {
        $tenantId = filament()->getTenant()->id;

        $bestSellers = InvoiceItem::query()
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->where('invoices.store_id', $tenantId)
            ->whereMonth('invoices.created_at', now()->month)
            ->select('products.name', DB::raw('SUM(invoice_items.quantity) as total_quantity'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'الكمية المباعة',
                    'data' => $bestSellers->pluck('total_quantity')->toArray(),
                    'backgroundColor' => '#f59e0b',
                ],
            ],
            'labels' => $bestSellers->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
