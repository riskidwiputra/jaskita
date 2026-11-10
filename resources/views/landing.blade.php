<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Temukan jas untuk momen spesial bersama JasKita Medan Johor. Lihat galeri Instagram, lokasi toko, dan hubungi kami melalui WhatsApp.">
    <meta name="theme-color" content="#f7f5f0">
    <title>JasKita Medan Johor | Sewa Jas untuk Momen Spesial</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="landing-page">
    @php
        $instagramUrl = 'https://www.instagram.com/sewajasmedanjohor/';
        $mapsUrl = 'https://www.google.com/maps/place/Sewa+Jas+Medan+Johor/@3.5312405,98.6722515,17z/data=!3m1!4b1!4m6!3m5!1s0x30313bdadcbbefc5:0x9c4f284daf5d5e61!8m2!3d3.5312405!4d98.6748318!16s%2Fg%2F11nv71r99k?entry=ttu';
        $mapsEmbedUrl = 'https://www.google.com/maps?q=3.5312405,98.6748318&output=embed';
        $whatsAppUrl = 'https://wa.me/628216692650?text=Halo%20JasKita%2C%20saya%20ingin%20bertanya%20tentang%20sewa%20jas.';
    @endphp

    <main>
        <header class="landing-header">
            <div class="landing-container header-inner">
                <a class="brand" href="/" aria-label="JasKita beranda">
                    <img src="{{ asset('jaskita-logo.jpg') }}" alt="Logo JasKita" width="42" height="42">
                    <span>JasKita</span>
                </a>
                <nav class="main-nav" aria-label="Navigasi utama">
                    <a href="#galeri">Galeri</a>
                    <a href="#lokasi">Lokasi</a>
                </nav>
                <a class="button button-navy button-small" href="{{ $whatsAppUrl }}" target="_blank" rel="noreferrer">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 11.7a8.5 8.5 0 0 1-12.6 7.4L3 20l.9-4.7A8.5 8.5 0 1 1 20.5 11.7Z"/><path d="M8.1 7.8c.2-.4.4-.4.7-.4h.5c.2 0 .4.1.5.4l.7 1.7c.1.3.1.5-.1.7l-.5.6c-.2.2-.2.4 0 .6.6 1 1.4 1.8 2.5 2.3.2.1.4.1.6-.1l.7-.8c.2-.2.4-.2.7-.1l1.7.8c.3.1.4.3.4.5 0 .4-.2 1.3-.8 1.7-.6.4-1.3.6-2.1.4-1.1-.3-2.5-.9-3.9-2.2-1.2-1.1-2-2.5-2.2-3.5-.2-.9.1-1.8.6-2.6Z"/></svg>
                    <span class="button-label-desktop">Chat WhatsApp</span>
                    <span class="button-label-mobile">Chat kami</span>
                </a>
            </div>
        </header>

        <section class="hero">
            <div class="landing-container hero-inner">
                <div class="hero-copy">
                    <p class="eyebrow eyebrow-pill"><span></span> Sewa jas · Medan Johor</p>
                    <h1>Tampil percaya diri <span>di momen istimewa.</span></h1>
                    <p class="hero-description">Temukan jas untuk hari yang ingin selalu dikenang. Tanya pilihan model dan ketersediaan langsung melalui WhatsApp.</p>
                    <div class="hero-actions">
                        <a class="button button-navy" href="{{ $whatsAppUrl }}" target="_blank" rel="noreferrer">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 11.7a8.5 8.5 0 0 1-12.6 7.4L3 20l.9-4.7A8.5 8.5 0 1 1 20.5 11.7Z"/><path d="M8.1 7.8c.2-.4.4-.4.7-.4h.5c.2 0 .4.1.5.4l.7 1.7c.1.3.1.5-.1.7l-.5.6c-.2.2-.2.4 0 .6.6 1 1.4 1.8 2.5 2.3.2.1.4.1.6-.1l.7-.8c.2-.2.4-.2.7-.1l1.7.8c.3.1.4.3.4.5 0 .4-.2 1.3-.8 1.7-.6.4-1.3.6-2.1.4-1.1-.3-2.5-.9-3.9-2.2-1.2-1.1-2-2.5-2.2-3.5-.2-.9.1-1.8.6-2.6Z"/></svg>
                            Konsultasi via WhatsApp
                        </a>
                        <a class="button button-outline" href="#galeri">Lihat galeri <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></a>
                    </div>
                    <div class="hero-meta">
                        <span class="location-meta"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 10c0 5-7 11-7 11S5 15 5 10a7 7 0 1 1 14 0Z"/><circle cx="12" cy="10" r="2.3"/></svg>Berlokasi di Medan Johor</span>
                    </div>
                </div>

                <div class="suit-art" role="img" aria-label="Ilustrasi pria mengenakan jas dan memberi acungan jempol">
                    <img class="suit-illustration" src="{{ asset('images/jaskita-hero-character.png') }}" alt="" width="618" height="650">
                </div>
            </div>
        </section>

        <section class="gallery-section" id="galeri">
            <div class="landing-container">
                <div class="section-heading gallery-heading">
                    <div>
                        <p class="eyebrow">#MomenJasKita</p>
                        <h2>Cerita dari Instagram</h2>
                        <p>Intip momen dan inspirasi gaya dari pelanggan JasKita.</p>
                    </div>
                    <a class="instagram-link" href="{{ $instagramUrl }}" target="_blank" rel="noreferrer">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="18" cy="6" r="1" class="icon-fill"/></svg>
                        @sewajasmedanjohor
                        <svg class="arrow-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                    </a>
                </div>
                @if ($posts !== [])
                    <div class="instagram-posts">
                        @foreach ($posts as $post)
                            <a class="instagram-post" href="{{ $post['post_url'] }}" target="_blank" rel="noreferrer" aria-label="Lihat postingan Instagram JasKita{{ $post['caption'] !== '' ? ': '.\Illuminate\Support\Str::limit($post['caption'], 90) : '' }}">
                                <img src="{{ $post['image_url'] }}" alt="{{ $post['caption'] !== '' ? $post['caption'] : 'Foto pelanggan JasKita dari Instagram' }}" loading="lazy">
                                <span class="instagram-post-overlay" aria-hidden="true">
                                    <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="18" cy="6" r="1" class="icon-fill"/></svg>
                                </span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="instagram-empty">
                        <div class="instagram-badge">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="18" cy="6" r="1" class="icon-fill"/></svg>
                        </div>
                        <h3>{{ $status === 'not_configured' ? 'Hubungkan Instagram JasKita' : ($status === 'empty' ? 'Belum ada postingan yang bisa ditampilkan' : 'Feed Instagram belum tersedia') }}</h3>
                        <p>{{ $status === 'not_configured' ? 'Foto postingan terbaru akan tampil di sini setelah koneksi resmi Instagram diaktifkan.' : ($status === 'empty' ? 'Akun Instagram sudah terhubung, tetapi belum ada postingan foto atau video yang dapat ditampilkan.' : 'Postingan Instagram tidak dapat dimuat sekarang. Kunjungi akun kami untuk melihat foto terbaru.') }}</p>
                        <a class="button button-outline" href="{{ $instagramUrl }}" target="_blank" rel="noreferrer">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="18" cy="6" r="1" class="icon-fill"/></svg>
                            Buka @sewajasmedanjohor
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                        </a>
                    </div>
                @endif
            </div>
        </section>

        <section class="location-section" id="lokasi">
            <div class="landing-container location-inner">
                <div class="location-copy">
                    <p class="eyebrow">Kami menunggu kunjunganmu</p>
                    <h2>Temukan JasKita</h2>
                    <p class="location-description">Kunjungi toko kami di Medan Johor untuk bertanya tentang pilihan jas dan penyewaan.</p>
                    <div class="address-card">
                        <span class="address-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 10c0 5-7 11-7 11S5 15 5 10a7 7 0 1 1 14 0Z"/><circle cx="12" cy="10" r="2.3"/></svg></span>
                        <div><strong>Sewa Jas Medan Johor</strong><span>Medan, Sumatera Utara</span></div>
                    </div>
                    <a class="button button-gold directions-button" href="{{ $mapsUrl }}" target="_blank" rel="noreferrer">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 10c0 5-7 11-7 11S5 15 5 10a7 7 0 1 1 14 0Z"/><circle cx="12" cy="10" r="2.3"/></svg>
                        Petunjuk arah
                        <svg class="arrow-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                    </a>
                </div>
                <div class="map-frame">
                    <iframe title="Lokasi Sewa Jas Medan Johor di Google Maps" src="{{ $mapsEmbedUrl }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </section>

        <section class="contact-section" id="kontak">
            <div class="contact-card">
                <div class="contact-ring contact-ring-top"></div>
                <div class="contact-ring contact-ring-bottom"></div>
                <div class="contact-copy">
                    <p class="eyebrow">Siap untuk momen spesialmu?</p>
                    <h2>Yuk, temukan jas yang pas.</h2>
                    <p>Hubungi kami untuk bertanya tentang model, ukuran, dan ketersediaan.</p>
                    <a class="button button-champagne" href="{{ $whatsAppUrl }}" target="_blank" rel="noreferrer">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 11.7a8.5 8.5 0 0 1-12.6 7.4L3 20l.9-4.7A8.5 8.5 0 1 1 20.5 11.7Z"/><path d="M8.1 7.8c.2-.4.4-.4.7-.4h.5c.2 0 .4.1.5.4l.7 1.7c.1.3.1.5-.1.7l-.5.6c-.2.2-.2.4 0 .6.6 1 1.4 1.8 2.5 2.3.2.1.4.1.6-.1l.7-.8c.2-.2.4-.2.7-.1l1.7.8c.3.1.4.3.4.5 0 .4-.2 1.3-.8 1.7-.6.4-1.3.6-2.1.4-1.1-.3-2.5-.9-3.9-2.2-1.2-1.1-2-2.5-2.2-3.5-.2-.9.1-1.8.6-2.6Z"/></svg>
                        Chat JasKita di WhatsApp
                        <svg class="arrow-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                    </a>
                </div>
            </div>
        </section>

        <footer class="landing-footer">
            <div class="landing-container footer-inner">
                <p>© {{ now()->year }} JasKita Medan Johor</p>
                <nav aria-label="Tautan lainnya">
                    <a href="{{ $instagramUrl }}" target="_blank" rel="noreferrer">Instagram</a>
                    <a href="{{ $mapsUrl }}" target="_blank" rel="noreferrer">Google Maps</a>
                    <a href="{{ route('login') }}">Masuk admin</a>
                </nav>
            </div>
        </footer>

        <a class="whatsapp-float" href="{{ $whatsAppUrl }}" target="_blank" rel="noreferrer" aria-label="Chat JasKita melalui WhatsApp">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 11.7a8.5 8.5 0 0 1-12.6 7.4L3 20l.9-4.7A8.5 8.5 0 1 1 20.5 11.7Z"/><path d="M8.1 7.8c.2-.4.4-.4.7-.4h.5c.2 0 .4.1.5.4l.7 1.7c.1.3.1.5-.1.7l-.5.6c-.2.2-.2.4 0 .6.6 1 1.4 1.8 2.5 2.3.2.1.4.1.6-.1l.7-.8c.2-.2.4-.2.7-.1l1.7.8c.3.1.4.3.4.5 0 .4-.2 1.3-.8 1.7-.6.4-1.3.6-2.1.4-1.1-.3-2.5-.9-3.9-2.2-1.2-1.1-2-2.5-2.2-3.5-.2-.9.1-1.8.6-2.6Z"/></svg>
        </a>
    </main>
</body>
</html>
