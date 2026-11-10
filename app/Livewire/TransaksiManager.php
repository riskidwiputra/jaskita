<?php

namespace App\Livewire;

use App\Models\Barang;
use App\Models\TransaksiSewa;
use App\Models\TransaksiSewaBarang;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class TransaksiManager extends Component
{
    use WithPagination;

    public bool $form = false;

    public $nama_penyewa_manual = '';

    public $no_hp_manual = '';

    public $jenis_jaminan = '';

    public $foto_peminjam = '';

    public $keterangan = '';

    public $tanggal_sewa = '';

    public $tanggal_kembali_rencana = '';

    public $harga_sewa = 0;

    public array $barangIds = [];

    public string $filterStatus = '';

    public string $barangSearch = '';

    public function updated($property): void
    {
        if ($property === 'filterStatus') {
            $this->resetPage();
        }
    }

    public function removeBarang(int $id): void
    {
        $this->barangIds = array_values(array_filter($this->barangIds, fn ($barangId) => (int) $barangId !== $id));
    }

    public function clearBarangSelection(): void
    {
        $this->barangIds = [];
    }

    public function save(): void
    {
        $this->validate([
            'nama_penyewa_manual' => 'required|string|max:255',
            'no_hp_manual' => 'nullable|string|max:30',
            'jenis_jaminan' => 'required|in:KTP,SIM,KTM,Deposit',
            'tanggal_sewa' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date|after_or_equal:tanggal_sewa',
            'harga_sewa' => 'required|integer|min:0',
            'barangIds' => 'required|array|min:1',
            'barangIds.*' => 'integer|distinct|exists:barangs,id',
        ]);

        $created = DB::transaction(function (): bool {
            $barangs = Barang::whereIn('id', $this->barangIds)->lockForUpdate()->get();

            if ($barangs->count() !== count(array_unique($this->barangIds)) || $barangs->contains(fn (Barang $barang) => $barang->status !== 'tersedia')) {
                $this->addError('barangIds', 'Salah satu barang sudah tidak tersedia. Perbarui pilihan barang.');

                return false;
            }

            $transaksi = TransaksiSewa::create([
                'penyewa_id' => null,
                'nama_penyewa_manual' => $this->nama_penyewa_manual,
                'no_hp_manual' => $this->no_hp_manual,
                'jenis_jaminan' => $this->jenis_jaminan,
                'foto_peminjam' => $this->foto_peminjam,
                'keterangan' => $this->keterangan,
                'tanggal_sewa' => $this->tanggal_sewa,
                'tanggal_kembali_rencana' => $this->tanggal_kembali_rencana,
                'harga_sewa' => $this->harga_sewa,
                'status' => 'berjalan',
            ]);

            foreach ($barangs as $barang) {
                TransaksiSewaBarang::create([
                    'transaksi_sewa_id' => $transaksi->id,
                    'barang_id' => $barang->id,
                    'harga_item' => $barang->harga_satuan,
                ]);
                $barang->update(['status' => 'disewa']);
            }

            return true;
        });

        if (! $created) {
            return;
        }

        $this->reset(['form', 'nama_penyewa_manual', 'no_hp_manual', 'jenis_jaminan', 'foto_peminjam', 'keterangan', 'tanggal_sewa', 'tanggal_kembali_rencana', 'harga_sewa', 'barangIds', 'barangSearch']);
        $this->resetPage();
        $this->resetValidation();
        $this->dispatch('notify', type: 'success', title: 'Transaksi ditambahkan', message: 'Transaksi sewa berhasil dibuat.');
    }

    public function kembalikan(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $transaksi = TransaksiSewa::with('barang')->findOrFail($id);
            $transaksi->update(['status' => 'selesai', 'tanggal_kembali_aktual' => now()->toDateString()]);

            foreach ($transaksi->barang as $item) {
                $item->barang?->update(['status' => 'tersedia']);
            }
        });

        $this->resetPage();
        $this->dispatch('notify', type: 'success', title: 'Barang dikembalikan', message: 'Status transaksi berhasil diperbarui.');
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $transaksi = TransaksiSewa::with('barang')->findOrFail($id);

            if ($transaksi->status !== 'selesai') {
                foreach ($transaksi->barang as $item) {
                    $item->barang?->update(['status' => 'tersedia']);
                }
            }

            $transaksi->deleted_by = auth()->user()->email;
            $transaksi->save();
            $transaksi->delete();
        });

        $this->resetPage();
        $this->dispatch('notify', type: 'success', title: 'Transaksi dihapus', message: 'Data transaksi berhasil dihapus.');
    }

    public function render()
    {
        $transaksiQuery = TransaksiSewa::with(['penyewa', 'barang.barang']);

        if ($this->filterStatus === 'terlambat') {
            $transaksiQuery->where('status', 'berjalan')->whereDate('tanggal_kembali_rencana', '<', today());
        } elseif ($this->filterStatus !== '' && in_array($this->filterStatus, ['berjalan', 'selesai'], true)) {
            $transaksiQuery->where('status', $this->filterStatus);
        }

        return view('livewire.transaksi-manager', [
            'transaksis' => $transaksiQuery->latest()->paginate(10),
            'barangs' => Barang::where('status', 'tersedia')
                ->when($this->barangSearch !== '', function ($query) {
                    $query->where(function ($searchQuery) {
                        $searchQuery->whereIn('id', $this->barangIds)
                            ->orWhere('nama_barang', 'like', '%'.$this->barangSearch.'%')
                            ->orWhere('kode_barang', 'like', '%'.$this->barangSearch.'%');
                    });
                })
                ->orderBy('nama_barang')
                ->get(),
            'selectedBarangs' => $this->barangIds === [] ? collect() : Barang::whereIn('id', $this->barangIds)->orderBy('nama_barang')->get(),
        ]);
    }
}
