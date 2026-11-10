<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sewa Jas') }}</title>@vite(['resources/css/app.css', 'resources/js/app.js'])@livewireStyles
</head>
<body class="font-sans antialiased">
<div class="min-h-screen flex bg-background">
    <aside class="sticky top-0 h-screen w-56 shrink-0 border-r border-border bg-surface flex flex-col shadow-[1px_0_20px_rgba(31,42,68,0.03)]">
        <div class="px-5 py-5"><p class="font-display text-xl text-navy">Sewa Jas</p></div>
        <nav class="flex-1 px-3 space-y-0.5">
            @php($menu=[['dashboard','Dashboard','/dashboard'],['barang.index','Barang','/barang'],['penyewa.index','Penyewa','/penyewa'],['transaksi.index','Transaksi','/transaksi'],['laporan','Buku Kas','/laporan'],['denda.index','Denda','/denda'],['pengeluaran.index','Pengeluaran','/pengeluaran']])
            @foreach($menu as [$route,$label,$url])
                @php($active=$route==='dashboard' ? request()->routeIs('dashboard') : request()->path() === ltrim($url,'/'))
                <a href="{{ url($url) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ $active ? 'bg-navy/10 text-navy font-medium' : 'text-foreground/70 hover:bg-background' }}">
                    <span class="w-[17px] text-center">{{ $loop->iteration===1?'▦':($loop->iteration===2?'▣':($loop->iteration===3?'♙':($loop->iteration===4?'≡':($loop->iteration===5?'★':($loop->iteration===6?'▤':($loop->iteration===7?'▧':'◇')))))) }}</span>{{ $label }}
                </a>
            @endforeach
        </nav>
        <div class="px-5 py-4 border-t border-border">
            <p class="text-sm font-medium truncate">{{ Auth::user()->name }}</p><p class="text-xs text-muted truncate mb-3">{{ Auth::user()->email }}</p>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="text-xs text-danger hover:underline">Keluar</button></form>
        </div>
    </aside>
    <main class="min-w-0 flex-1 px-5 py-7 md:px-8 xl:px-10">
        @isset($header)<header class="mb-7">{{ $header }}</header>@endisset
        {{ $slot }}
    </main>
</div>@livewireScripts</body>
</html>
