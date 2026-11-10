<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Integrasi Google Business Profile — JasKita</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-[#f7f5f0] px-4 py-10 text-[#22251f]">
    <main class="mx-auto max-w-3xl rounded-3xl border border-[#e8e2d8] bg-white p-6 shadow-sm sm:p-10">
        <a href="/" class="text-sm font-semibold text-[#765b3b] hover:text-[#1f2a44]">← Kembali ke landing page</a>
        <p class="mt-8 text-xs font-bold uppercase tracking-[0.18em] text-[#9c6b2e]">Pengaturan integrasi</p>
        <h1 class="mt-2 font-display text-3xl font-semibold text-[#1f2a44] sm:text-4xl">Google Business Profile</h1>
        <p class="mt-3 leading-7 text-[#706c63]">Hubungkan akun Google pemilik/pengelola untuk menampilkan ulasan bisnis di halaman publik.</p>

        @if (session('status'))
            <div class="mt-6 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
        @endif
        @if ($error)
            <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">{{ $error }}</div>
        @endif

        <section class="mt-8 rounded-2xl bg-[#f7f5f0] p-5">
            <h2 class="font-semibold text-[#1f2a44]">Sebelum menghubungkan</h2>
            <ul class="mt-3 list-disc space-y-2 pl-5 text-sm leading-6 text-[#706c63]">
                <li>Proyek Google Cloud harus disetujui untuk Business Profile APIs dan API terkait sudah diaktifkan.</li>
                <li>Profil bisnis harus terverifikasi; akun Google yang dipakai harus memiliki akses pemilik atau pengelola.</li>
                <li>OAuth client harus menggunakan redirect URI: <code class="break-all rounded bg-white px-1.5 py-0.5 text-[#1f2a44]">{{ $google->redirectUri() }}</code></li>
                <li>OAuth consent screen harus meminta scope <code class="break-all rounded bg-white px-1.5 py-0.5 text-[#1f2a44]">https://www.googleapis.com/auth/business.manage</code></li>
            </ul>
        </section>

        @if (! $google->configured())
            <p class="mt-6 text-sm leading-6 text-[#706c63]">Kredensial OAuth belum diisi. Tambahkan <code>GOOGLE_BUSINESS_CLIENT_ID</code> dan <code>GOOGLE_BUSINESS_CLIENT_SECRET</code> di file <code>.env</code>, lalu jalankan <code>php artisan config:clear</code> jika konfigurasi di-cache.</p>
        @else
            <a href="{{ route('google-business.connect') }}" class="mt-6 inline-flex min-h-12 items-center justify-center rounded-full bg-[#1f2a44] px-6 py-3 text-sm font-bold text-white transition hover:bg-[#344363]">
                {{ $connection ? 'Hubungkan ulang akun Google' : 'Hubungkan akun Google' }}
            </a>
        @endif

        @if ($connection)
            <section class="mt-8 border-t border-[#e8e2d8] pt-7">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h2 class="font-semibold text-[#1f2a44]">Akun Google terhubung</h2>
                        <p class="mt-1 text-sm text-[#706c63]">
                            Lokasi aktif:
                            <strong>{{ $connection->location_title ?: 'Belum dipilih' }}</strong>
                        </p>
                    </div>
                    <form method="POST" action="{{ route('google-business.disconnect') }}" onsubmit="return confirm('Putuskan koneksi Google Business Profile?')">
                        @csrf
                        @method('DELETE')
                        <button class="text-sm font-semibold text-red-700 hover:text-red-900">Putuskan koneksi</button>
                    </form>
                </div>

                @if ($locations !== [])
                    <form method="POST" action="{{ route('google-business.location') }}" class="mt-5 flex flex-col gap-3 sm:flex-row">
                        @csrf
                        <label class="sr-only" for="location_name">Pilih lokasi bisnis</label>
                        <select id="location_name" name="location_name" required class="min-h-12 flex-1 rounded-xl border border-[#d8d2c8] px-4">
                            <option value="">Pilih lokasi bisnis</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location['name'] }}" @selected($connection->location_name === $location['name'])>{{ $location['title'] }}</option>
                            @endforeach
                        </select>
                        <button class="min-h-12 rounded-xl bg-[#9c6b2e] px-5 font-bold text-white hover:bg-[#805522]">Simpan lokasi</button>
                    </form>
                @elseif (! $error)
                    <p class="mt-4 text-sm text-[#706c63]">Tidak ada lokasi yang dapat diakses akun ini. Pastikan Google Business Profile sudah terverifikasi dan akunmu berstatus pemilik/pengelola.</p>
                @endif

                @if ($connection->location_name)

                @endif
            </section>
        @endif
    </main>
    @livewireScripts
</body>
</html>
