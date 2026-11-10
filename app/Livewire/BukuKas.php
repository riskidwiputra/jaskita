<?php

namespace App\Livewire;

use App\Models\Denda;
use App\Models\Pengeluaran;
use App\Models\TransaksiSewa;
use Carbon\Carbon;
use Livewire\Component;

class BukuKas extends Component
{
    public string $tab = 'harian';

    public function updatedTab(): void
    {
        if (! in_array($this->tab, ['harian', 'bulanan', 'tahunan'], true)) {
            $this->tab = 'harian';
        }
    }

    public function render()
    {
        $entries = [];

        foreach (TransaksiSewa::all() as $transaksi) {
            if ($transaksi->tanggal_sewa) {
                $entries[] = [
                    'tanggal' => $transaksi->tanggal_sewa->toDateString(),
                    'jenis' => 'masuk',
                    'kategori' => 'Sewa',
                    'keterangan' => $transaksi->namaPenyewa() ?: 'Tanpa nama',
                    'jumlah' => (int) ($transaksi->harga_sewa ?? 0),
                ];
            }
        }

        foreach (Denda::with('transaksi')->get() as $denda) {
            if ($denda->created_at) {
                $entries[] = [
                    'tanggal' => $denda->created_at->toDateString(),
                    'jenis' => 'masuk',
                    'kategori' => 'Denda',
                    'keterangan' => ($denda->transaksi?->namaPenyewa() ?: 'Tanpa nama').' · '.$denda->alasan,
                    'jumlah' => (int) ($denda->jumlah ?? 0),
                ];
            }
        }

        foreach (Pengeluaran::where('kategori', '!=', 'Modal')->get() as $pengeluaran) {
            if ($pengeluaran->tanggal) {
                $entries[] = [
                    'tanggal' => $pengeluaran->tanggal->toDateString(),
                    'jenis' => 'keluar',
                    'kategori' => $pengeluaran->kategori,
                    'keterangan' => $pengeluaran->keterangan ?: $pengeluaran->dikeluarkan_oleh ?: '-',
                    'jumlah' => (int) ($pengeluaran->jumlah ?? 0),
                ];
            }
        }

        usort($entries, fn (array $a, array $b) => strcmp($b['tanggal'], $a['tanggal']));

        $groupFormat = match ($this->tab) {
            'bulanan' => 'Y-m',
            'tahunan' => 'Y',
            default => 'Y-m-d',
        };
        $groups = collect($entries)
            ->groupBy(fn (array $entry) => Carbon::parse($entry['tanggal'])->format($groupFormat))
            ->map(function ($items, string $key) use ($groupFormat) {
                $date = Carbon::createFromFormat($groupFormat, $key);
                $title = match ($groupFormat) {
                    'Y-m' => $date->locale('id')->translatedFormat('F Y'),
                    'Y' => $date->format('Y'),
                    default => $date->locale('id')->translatedFormat('l, d F Y'),
                };

                return ['key' => $key, 'title' => $title, 'entries' => $items];
            });

        $masuk = collect($entries)->where('jenis', 'masuk')->sum('jumlah');
        $keluar = collect($entries)->where('jenis', 'keluar')->sum('jumlah');

        return view('livewire.buku-kas', [
            'groups' => $groups,
            'masuk' => $masuk,
            'keluar' => $keluar,
        ]);
    }
}
