<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PenjualanHarian extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    public ?string $tanggal = null;

    protected function getStats(): array
    {
        $tanggal = $this->tanggal ?? today()->toDateString();

        $totalTransaksi = Order::whereDate('created_at', $tanggal)->count();
        $transaksiPending = Order::whereDate('created_at', $tanggal)->where('status', 'pending')->count();
        $transaksiSelesai = Order::whereDate('created_at', $tanggal)->where('status', 'completed')->count();

        // Last 7 days chart
        $chart = [];
        for ($i = 6; $i >= 0; $i--) {
            $chart[] = Order::whereDate('created_at', now()->subDays($i)->toDateString())->count();
        }

        return [
            Stat::make('Total Transaksi', $totalTransaksi)
                ->description('Tanggal: ' . date('d M Y', strtotime($tanggal)))
                ->descriptionIcon('heroicon-o-calendar')
                ->chart($chart)
                ->color('primary'),

            Stat::make('Order Pending', $transaksiPending)
                ->description('Menunggu konfirmasi')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Order Selesai', $transaksiSelesai)
                ->description('Sudah selesai')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}
