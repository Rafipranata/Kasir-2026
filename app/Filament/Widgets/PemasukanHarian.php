<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PemasukanHarian extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $today = today()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        $thisMonth = now()->format('Y-m');

        $pemasukanHariIni = Order::whereDate('created_at', $today)
            ->whereIn('status', ['paid', 'completed'])
            ->sum('total_harga');

        $pemasukanKemarin = Order::whereDate('created_at', $yesterday)
            ->whereIn('status', ['paid', 'completed'])
            ->sum('total_harga');

        $pemasukanBulanIni = Order::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->whereIn('status', ['paid', 'completed'])
            ->sum('total_harga');

        // Chart 7 hari terakhir
        $chart = [];
        for ($i = 6; $i >= 0; $i--) {
            $chart[] = (int) Order::whereDate('created_at', now()->subDays($i)->toDateString())
                ->whereIn('status', ['paid', 'completed'] )
                ->sum('total_harga');
        }

        $diff = $pemasukanKemarin > 0
            ? round((($pemasukanHariIni - $pemasukanKemarin) / $pemasukanKemarin) * 100, 1)
            : 0;

        return [
            Stat::make('Pemasukan Hari Ini', 'Rp '.number_format($pemasukanHariIni, 0, ',', '.'))
                ->description($diff >= 0 ? "+{$diff}% dari kemarin" : "{$diff}% dari kemarin")
                ->descriptionIcon($diff >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->chart($chart)
                ->color($diff >= 0 ? 'success' : 'danger'),

            Stat::make('Pemasukan Kemarin', 'Rp '.number_format($pemasukanKemarin, 0, ',', '.'))
                ->description('Order sudah dibayar')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('info'),

            Stat::make('Pemasukan Bulan Ini', 'Rp '.number_format($pemasukanBulanIni, 0, ',', '.'))
                ->description(now()->format('F Y'))
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('primary'),
        ];
    }
}
