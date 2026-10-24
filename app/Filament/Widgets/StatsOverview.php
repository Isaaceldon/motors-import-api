<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Reservation;
use App\Models\SparePart;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = -2;

    protected function getStats(): array
    {
        $lowStockThreshold = 5;

        $revenueUsd = Reservation::whereIn('status', ['acompte_paye', 'terminee'])->sum('deposit_amount_usd')
            + Order::whereIn('status', ['payee', 'expediee', 'terminee'])->sum('total_usd');

        $pendingReservations = Reservation::where('status', 'en_attente')->count();
        $pendingOrders = Order::where('status', 'en_attente')->count();
        $lowStockParts = SparePart::where('online_sale_enabled', true)
            ->where('stock_quantity', '<=', $lowStockThreshold)
            ->count();

        return [
            Stat::make('Chiffre d\'affaires encaissé', '$'.number_format((float) $revenueUsd, 2))
                ->description('Acomptes reçus + commandes payées')
                ->color('success')
                ->icon('heroicon-o-banknotes'),

            Stat::make('Réservations en attente', $pendingReservations)
                ->description('En attente de paiement de l\'acompte')
                ->color($pendingReservations > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-calendar-days'),

            Stat::make('Commandes en attente', $pendingOrders)
                ->description('En attente de traitement')
                ->color($pendingOrders > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-shopping-bag'),

            Stat::make('Pièces en stock faible', $lowStockParts)
                ->description("Stock ≤ {$lowStockThreshold} unités")
                ->color($lowStockParts > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-exclamation-triangle'),
        ];
    }
}
