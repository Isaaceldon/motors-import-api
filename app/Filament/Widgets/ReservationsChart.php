<?php

namespace App\Filament\Widgets;

use App\Models\Reservation;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ReservationsChart extends ChartWidget
{
    protected static ?int $sort = -1;

    protected int | string | array $columnSpan = 'full';

    public function getHeading(): string
    {
        return 'Réservations des 6 derniers mois';
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $months = collect(range(5, 0))->map(fn ($i) => Carbon::now()->subMonths($i)->startOfMonth());

        $counts = $months->map(
            fn (Carbon $month) => Reservation::whereBetween('created_at', [$month, $month->copy()->endOfMonth()])->count()
        );

        return [
            'datasets' => [
                [
                    'label' => 'Réservations',
                    'data' => $counts->toArray(),
                    'borderColor' => '#F79400',
                    'backgroundColor' => 'rgba(247, 148, 0, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $months->map(fn (Carbon $month) => ucfirst($month->translatedFormat('M Y')))->toArray(),
        ];
    }
}
