<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\DebtPayment;
use Illuminate\Support\Facades\DB;

class Reports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';

    protected static string $view = 'filament.pages.reports';

    protected static ?string $navigationGroup = 'الماليات';

    protected static ?string $title = 'التقارير الإحصائية';

    public static function getNavigationLabel(): string
    {
        return 'التقارير';
    }

    protected static bool $isScopedToTenant = true;

    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function getReportData()
    {
        $tenantId = filament()->getTenant()->id;

        $totalSales = Invoice::where('store_id', $tenantId)
            ->whereBetween('created_at', [$this->startDate, $this->endDate . ' 23:59:59'])
            ->sum('total_amount');

        $totalExpenses = Expense::where('store_id', $tenantId)
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->sum('amount');

        $totalPaymentsCollected = DebtPayment::where('store_id', $tenantId)
            ->whereBetween('payment_date', [$this->startDate, $this->endDate])
            ->sum('amount');

        return [
            'total_sales' => $totalSales,
            'total_expenses' => $totalExpenses,
            'net_cash_flow' => $totalSales - $totalExpenses + $totalPaymentsCollected,
            'collected_payments' => $totalPaymentsCollected,
        ];
    }
}
