<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Ulasan pelanggan JasKita Medan Johor dari Google Maps.">
    <title>Ulasan Google — JasKita Medan Johor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="google-reviews-page">
    <main class="google-reviews-container">
        <a class="google-reviews-back" href="/">← Kembali ke JasKita</a>
        <header class="google-reviews-heading">
            <p class="google-reviews-eyebrow">Google Maps</p>
            <h1>Ulasan pelanggan</h1>
            @if ($locationTitle)
                <p>{{ $locationTitle }}</p>
            @endif
        </header>

        @if ($status === 'not_configured')
            <section class="google-reviews-empty">
                <h2>Ulasan Google belum disiapkan</h2>
                <p>Hubungkan Google Business Profile pemilik bisnis untuk menampilkan ulasan.</p>
            </section>
        @elseif ($status === 'location_required')
            <section class="google-reviews-empty">
                <h2>Pilih lokasi bisnis terlebih dahulu</h2>
                <p>Akun Google sudah terhubung, tetapi belum ada lokasi bisnis yang dipilih.</p>
            </section>
        @elseif ($status === 'unavailable')
            <section class="google-reviews-empty">
                <h2>Ulasan Google belum tersedia</h2>
                <p>Google tidak dapat mengirim ulasan sekarang. Silakan coba kembali nanti atau lihat profil Google Maps kami.</p>
            </section>
        @else
            @php
                $averageStars = (int) round($average_rating);
            @endphp
            <section class="google-review-summary" aria-label="Ringkasan ulasan">
                <div class="google-review-rating">
                    <strong>{{ number_format($average_rating, 1) }}</strong>
                    <span class="google-stars" aria-label="{{ number_format($average_rating, 1) }} dari 5 bintang">{{ str_repeat('★', $averageStars) }}<span>{{ str_repeat('☆', 5 - $averageStars) }}</span></span>
                    <span>{{ number_format($total_count) }} ulasan di Google</span>
                </div>
                <a href="https://www.google.com/maps/place/Sewa+Jas+Medan+Johor/@3.5312405,98.6722515,17z/data=!3m1!4b1!4m6!3m5!1s0x30313bdadcbbefc5:0x9c4f284daf5d5e61!8m2!3d3.5312405!4d98.6748318!16s%2Fg%2F11nv71r99k?entry=ttu" target="_blank" rel="noreferrer">Buka profil di Google Maps ↗</a>
            </section>

            @if ($reviews === [])
                <section class="google-reviews-empty">
                    <h2>Belum ada ulasan untuk ditampilkan</h2>
                    <p>Jumlah ulasan yang dilaporkan Google: {{ number_format($total_count) }}.</p>
                </section>
            @else
                <section class="google-review-list" aria-label="Daftar ulasan Google">
                    @foreach ($reviews as $review)
                        @php
                            $rating = match ($review['starRating'] ?? '') {
                                'FIVE' => 5,
                                'FOUR' => 4,
                                'THREE' => 3,
                                'TWO' => 2,
                                'ONE' => 1,
                                default => 0,
                            };
                            $reviewer = $review['reviewer']['displayName'] ?? 'Pengguna Google';
                        @endphp
                        <article class="google-review-card">
                            <div class="google-review-author">
                                <span class="google-review-avatar" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($reviewer, 0, 1)) }}</span>
                                <div>
                                    <h2>{{ $reviewer }}</h2>
                                    @if (! empty($review['createTime']))
                                        <time datetime="{{ $review['createTime'] }}">{{ \Illuminate\Support\Carbon::parse($review['createTime'])->locale('id')->translatedFormat('j F Y') }}</time>
                                    @endif
                                </div>
                            </div>
                            @if ($rating > 0)
                                <p class="google-stars" aria-label="{{ $rating }} dari 5 bintang">{{ str_repeat('★', $rating) }}<span>{{ str_repeat('☆', 5 - $rating) }}</span></p>
                            @endif
                            <p class="google-review-comment">{{ $review['comment'] ?? 'Memberi rating tanpa komentar.' }}</p>
                            @if (! empty($review['reviewReply']['comment']))
                                <div class="google-review-reply">
                                    <strong>Tanggapan JasKita</strong>
                                    <p>{{ $review['reviewReply']['comment'] }}</p>
                                </div>
                            @endif
                        </article>
                    @endforeach
                </section>
            @endif

            <nav class="google-review-pagination" aria-label="Halaman ulasan">
                @if (request()->filled('pageToken'))
                    @php
                        $history = request()->query('history', []);
                        $history = is_array($history) ? array_values(array_filter($history, 'is_string')) : [];
                        $previousToken = array_pop($history);
                    @endphp
                    <a href="{{ route('google-business.reviews', $previousToken ? ['pageToken' => $previousToken, 'history' => $history] : []) }}">← Ulasan sebelumnya</a>
                @endif
                @if ($next_page_token)
                    @php
                        $history = request()->input('history', []);
                        $history = is_array($history) ? array_values(array_filter($history, 'is_string')) : [];
                        $currentToken = request()->query('pageToken');
                        if (is_string($currentToken)) {
                            $history[] = $currentToken;
                        }
                    @endphp
                    <a href="{{ route('google-business.reviews', ['pageToken' => $next_page_token, 'history' => $history]) }}">Ulasan berikutnya →</a>
                @endif
            </nav>
        @endif

        <footer class="google-reviews-footer">
            <a href="{{ route('login') }}">Masuk admin</a>
        </footer>
    </main>
    @livewireScripts
</body>
</html>
