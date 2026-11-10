<x-guest-layout>
    <div class="mb-10 text-center"><p class="font-display text-3xl text-navy">Sewa Jas</p><p class="mt-1 text-sm text-muted">Masuk untuk kelola usaha Anda</p></div>
    <form method="POST" action="{{ route('login') }}" class="bg-surface border border-border rounded-2xl p-7 space-y-5">@csrf
        <x-auth-session-status class="text-sm rounded-lg px-3 py-2 bg-danger/10 text-danger border border-danger/20" :status="session('status')" />
        @if ($errors->any())<div class="text-sm rounded-lg px-3 py-2 bg-danger/10 text-danger border border-danger/20">{{ $errors->first() }}</div>@endif
        <div><label for="email" class="block text-sm text-foreground/70 mb-1.5">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="owner@tokosewajas.com" class="w-full rounded-lg border border-border bg-background px-3.5 py-2.5 text-sm outline-none focus:border-navy transition-colors"></div>
        <div><label for="password" class="block text-sm text-foreground/70 mb-1.5">Kata sandi</label><input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" class="w-full rounded-lg border border-border bg-background px-3.5 py-2.5 text-sm outline-none focus:border-navy transition-colors"></div>
        <button type="submit" class="w-full rounded-lg bg-navy text-white text-sm font-medium py-2.5 hover:bg-navy-light transition-colors">Masuk</button>
    </form>
    <p class="mt-6 text-center text-xs text-muted">Belum punya akun? Daftar dulu lewat menu registrasi atau minta admin membuatkan akun.</p>
</x-guest-layout>
