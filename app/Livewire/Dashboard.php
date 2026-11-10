<?php

namespace App\Livewire;

use App\Models\Barang;
use App\Models\Denda;
use App\Models\Pengeluaran;
use App\Models\Penyewa;
use App\Models\TransaksiSewa;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public string $revenuePeriod = 'bulan_ini';

    public function updatedRevenuePeriod(): void
    {
        if (! in_array($this->revenuePeriod, ['hari_ini', 'bulan_ini', 'tahun_ini', 'keseluruhan'], true)) {
            $this->revenuePeriod = 'bulan_ini';
        }
    }

    public function render()
    {
        $now = now();
        $period = in_array($this->revenuePeriod, ['hari_ini', 'bulan_ini', 'tahun_ini', 'keseluruhan'], true)
            ? $this->revenuePeriod
            : 'bulan_ini';
        $range = $this->periodRange($period, $now);
        $rentalRevenue = $this->wherePeriod(TransaksiSewa::query(), 'tanggal_sewa', $range)->sum('harga_sewa');
        $fineRevenue = $this->wherePeriod(Denda::query(), 'created_at', $range)->sum('jumlah');
        $grossRevenue = (int) $rentalRevenue + (int) $fineRevenue;
        $operatingExpenses = (int) $this->wherePeriod(
            Pengeluaran::query()->where('kategori', '!=', 'Modal'),
            'tanggal',
            $range
        )->sum('jumlah');

        $chart = $this->revenueChart($now);
        $currentMonth = $chart->last()['total'];
        $previousMonth = $chart->slice(-2, 1)->first()['total'];
        $trend = $previousMonth > 0
            ? round((($currentMonth - $previousMonth) / $previousMonth) * 100)
            : null;
        $latestTransactions = TransaksiSewa::with(['penyewa', 'barang.barang'])->latest()->take(5)->get();

        return view('livewire.dashboard', [
            'tersedia' => Barang::where('status', 'tersedia')->count(),
            'disewa' => Barang::where('status', 'disewa')->count(),
            'totalPernahSewa' => TransaksiSewa::count(),
            'totalModal' => Barang::sum(DB::raw('COALESCE(harga_satuan, 0) * COALESCE(jumlah_barang, 1)'))
                + Pengeluaran::where('kategori', 'Modal')->sum('jumlah'),
            'totalDenda' => Denda::sum('jumlah'),
            'totalPenyewa' => Penyewa::count(),
            'grossRevenue' => $grossRevenue,
            'operatingExpenses' => $operatingExpenses,
            'netRevenue' => $grossRevenue - $operatingExpenses,
            'chart' => $chart,
            'chartPath' => $this->chartPath($chart),
            'trend' => $trend,
            'terbaru' => $latestTransactions,
            'jatuhTempo' => TransaksiSewa::where('status', 'berjalan')
                ->whereDate('tanggal_kembali_rencana', '<', today())
                ->count(),
        ]);
    }

    private function periodRange(string $period, Carbon $now): ?array
    {
        return match ($period) {
            'hari_ini' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'tahun_ini' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            'keseluruhan' => null,
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };
    }

    private function wherePeriod(Builder $query, string $column, ?array $range): Builder
    {
        if ($range === null) {
            return $query;
        }

        return $query
            ->whereDate($column, '>=', $range[0]->toDateString())
            ->whereDate($column, '<=', $range[1]->toDateString());
    }

    private function revenueChart(Carbon $now): Collection
    {
        return collect(range(5, 0))
            ->map(function (int $offset) use ($now): array {
                $month = $now->copy()->startOfMonth()->subMonths($offset);
                $range = [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()];
                $rental = $this->wherePeriod(TransaksiSewa::query(), 'tanggal_sewa', $range)->sum('harga_sewa');
                $fine = $this->wherePeriod(Denda::query(), 'created_at', $range)->sum('jumlah');
                $expense = $this->wherePeriod(Pengeluaran::where('kategori', '!=', 'Modal'), 'tanggal', $range)->sum('jumlah');

                return [
                    'label' => $month->locale('id')->translatedFormat('M y'),
                    'total' => (int) $rental + (int) $fine - (int) $expense,
                ];
            });
    }

    private function chartPath(Collection $chart): string
    {
        $min = min(0, (int) $chart->min('total'));
        $max = max(1, (int) $chart->max('total'));
        $range = max(1, $max - $min);

        return $chart
            ->values()
            ->map(function (array $point, int $index) use ($min, $range): string {
                $x = 30 + ($index * 540 / 5);
                $y = 132 - (($point['total'] - $min) / $range * 106);

                return ($index === 0 ? 'M' : 'L').number_format($x, 1, '.', '').' '.number_format($y, 1, '.', '');
            })
            ->implode(' ');
    }
}
