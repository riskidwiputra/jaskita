<?php

namespace Tests\Feature;

use App\Livewire\BarangManager;
use App\Livewire\BukuKas;
use App\Livewire\Dashboard;
use App\Livewire\DendaManager;
use App\Livewire\PengeluaranManager;
use App\Livewire\TransaksiManager;
use App\Models\Barang;
use App\Models\Denda;
use App\Models\Pengeluaran;
use App\Models\TransaksiSewa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }

    public function test_barang_filters_can_be_combined(): void
    {
        Barang::create([
            'kode_barang' => 'JP001',
            'nama_barang' => 'Jas ukuran M',
            'ukuran' => 'M',
            'jumlah_barang' => 1,
            'harga_satuan' => 100000,
            'type' => 'Jas Pria',
            'status' => 'tersedia',
            'dibeli_oleh' => 'Putra',
        ]);
        Barang::create([
            'kode_barang' => 'JW001',
            'nama_barang' => 'Jas ukuran L',
            'ukuran' => 'L',
            'jumlah_barang' => 1,
            'harga_satuan' => 120000,
            'type' => 'Jas Wanita',
            'status' => 'tersedia',
            'dibeli_oleh' => 'Tia',
        ]);

        Livewire::test(BarangManager::class)
            ->set('filterUkuran', 'M')
            ->set('filterType', 'Jas Pria')
            ->set('filterPembeli', 'Putra')
            ->assertSee('Jas ukuran M')
            ->assertDontSee('Jas ukuran L');
    }

    public function test_barang_pagination_shows_consistent_controls_and_changes_page(): void
    {
        foreach (range(1, 12) as $number) {
            Barang::create([
                'kode_barang' => 'JP'.str_pad((string) $number, 3, '0', STR_PAD_LEFT),
                'nama_barang' => 'Produk '.str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                'ukuran' => 'M',
                'jumlah_barang' => 1,
                'harga_satuan' => 100000,
                'type' => 'Jas Pria',
                'status' => 'tersedia',
            ]);
        }

        Livewire::test(BarangManager::class)
            ->assertSee('Menampilkan 1–10 dari 12 data')
            ->call('gotoPage', 2)
            ->assertSee('Produk 12')
            ->assertDontSee('Produk 01');
    }

    public function test_transaction_uses_manual_renter_and_marks_selected_item_as_rented(): void
    {
        $barang = Barang::create([
            'kode_barang' => 'JP001',
            'nama_barang' => 'Jas Hitam',
            'ukuran' => 'M',
            'jumlah_barang' => 1,
            'harga_satuan' => 100000,
            'type' => 'Jas Pria',
            'status' => 'tersedia',
        ]);

        Livewire::test(TransaksiManager::class)
            ->set('nama_penyewa_manual', 'Pelanggan Manual')
            ->set('jenis_jaminan', 'KTP')
            ->set('tanggal_sewa', today()->toDateString())
            ->set('tanggal_kembali_rencana', today()->addDays(2)->toDateString())
            ->set('harga_sewa', 50000)
            ->set('barangIds', [$barang->id])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('transaksi_sewas', [
            'nama_penyewa_manual' => 'Pelanggan Manual',
            'penyewa_id' => null,
            'jenis_jaminan' => 'KTP',
            'status' => 'berjalan',
        ]);
        $this->assertDatabaseHas('barangs', ['id' => $barang->id, 'status' => 'disewa']);
    }

    public function test_transaction_overdue_filter_only_shows_unreturned_past_due_rentals(): void
    {
        TransaksiSewa::create([
            'nama_penyewa_manual' => 'Penyewa Terlambat',
            'tanggal_sewa' => today()->subDays(4),
            'tanggal_kembali_rencana' => today()->subDay(),
            'status' => 'berjalan',
            'harga_sewa' => 30000,
        ]);
        TransaksiSewa::create([
            'nama_penyewa_manual' => 'Penyewa Aktif',
            'tanggal_sewa' => today(),
            'tanggal_kembali_rencana' => today()->addDay(),
            'status' => 'berjalan',
            'harga_sewa' => 30000,
        ]);

        Livewire::test(TransaksiManager::class)
            ->set('filterStatus', 'terlambat')
            ->assertSee('Penyewa Terlambat')
            ->assertDontSee('Penyewa Aktif');
    }

    public function test_expense_filters_match_date_range_and_creator(): void
    {
        Pengeluaran::create([
            'tanggal' => today(),
            'kategori' => 'Operasional',
            'jumlah' => 50000,
            'dikeluarkan_oleh' => 'Putra',
        ]);
        Pengeluaran::create([
            'tanggal' => today()->subDay(),
            'kategori' => 'Operasional',
            'jumlah' => 20000,
            'dikeluarkan_oleh' => 'Tia',
        ]);

        Livewire::test(PengeluaranManager::class)
            ->set('tanggalMulai', today()->toDateString())
            ->set('filterDikeluarkanOleh', 'Putra')
            ->assertSee('Rp 50.000')
            ->assertDontSee('Rp 20.000');
    }

    public function test_dashboard_revenue_filter_sums_transactions_for_selected_period(): void
    {
        TransaksiSewa::create([
            'nama_penyewa_manual' => 'Hari Ini',
            'tanggal_sewa' => today(),
            'tanggal_kembali_rencana' => today()->addDay(),
            'status' => 'berjalan',
            'harga_sewa' => 75000,
        ]);
        TransaksiSewa::create([
            'nama_penyewa_manual' => 'Bulan Lalu',
            'tanggal_sewa' => today()->subMonth(),
            'tanggal_kembali_rencana' => today()->subMonth()->addDay(),
            'status' => 'selesai',
            'harga_sewa' => 25000,
        ]);

        Livewire::test(Dashboard::class)
            ->set('revenuePeriod', 'hari_ini')
            ->assertViewHas('grossRevenue', 75000)
            ->set('revenuePeriod', 'tahun_ini')
            ->assertViewHas('grossRevenue', 100000)
            ->set('revenuePeriod', 'keseluruhan')
            ->assertViewHas('grossRevenue', 100000);
    }

    public function test_cashbook_groups_entries_by_day(): void
    {
        TransaksiSewa::create([
            'nama_penyewa_manual' => 'Kas Hari Ini',
            'tanggal_sewa' => today(),
            'tanggal_kembali_rencana' => today()->addDay(),
            'status' => 'berjalan',
            'harga_sewa' => 50000,
        ]);
        TransaksiSewa::create([
            'nama_penyewa_manual' => 'Kas Kemarin',
            'tanggal_sewa' => today()->subDay(),
            'tanggal_kembali_rencana' => today(),
            'status' => 'selesai',
            'harga_sewa' => 30000,
        ]);

        Livewire::test(BukuKas::class)
            ->assertSee('Kas Hari Ini')
            ->assertSee('Kas Kemarin')
            ->assertSee('Total pemasukan')
            ->set('tab', 'bulanan')
            ->assertSee('Kas Hari Ini')
            ->set('tab', 'tahunan')
            ->assertSee('Kas Kemarin');
    }

    public function test_fines_page_shows_total_amount_and_count(): void
    {
        Denda::create(['jumlah' => 10000, 'alasan' => 'Terlambat']);
        Denda::create(['jumlah' => 25000, 'alasan' => 'Kerusakan']);

        Livewire::test(DendaManager::class)
            ->assertSee('Rp35.000')
            ->assertSee('2');
    }
}
