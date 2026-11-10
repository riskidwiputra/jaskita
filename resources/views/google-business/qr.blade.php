<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="QR code untuk membuka profil Google Maps JasKita dan menulis ulasan.">
    <title>QR Ulasan Google — JasKita Medan Johor</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="google-qr-page">
    <main class="google-qr-sheet">
        <header class="google-qr-brand">
            <img src="{{ asset('jaskita-logo.jpg') }}" alt="" width="52" height="52">
            <div>
                <p>SEWA JAS · MEDAN JOHOR</p>
                <h1>JasKita</h1>
            </div>
        </header>

        <section class="google-qr-content">
            <p class="google-qr-eyebrow">Pengalaman Anda berarti</p>
            <h2>Bantu kami menjadi lebih baik.</h2>
            <p class="google-qr-description">Pindai kode ini untuk membuka profil JasKita di Google Maps dan berbagi pengalaman Anda.</p>

            <img class="google-qr-code" src="{{ asset('images/qr-ulasan-google.png') }}" alt="QR code untuk membuka profil JasKita di Google Maps" width="1200" height="1200">

            <ol class="google-qr-steps">
                <li>Arahkan kamera ponsel ke QR code.</li>
                <li>Buka profil JasKita di Google Maps.</li>
                <li>Pilih <strong>Ulasan</strong>, lalu <strong>Tulis ulasan</strong>.</li>
            </ol>

            <a class="google-qr-link" href="https://maps.app.goo.gl/PZz5ig1BHgK4cW1r5" target="_blank" rel="noreferrer">Buka profil Google Maps</a>
        </section>

        <footer class="google-qr-footer">
            <span>Terima kasih telah berkunjung.</span>
            <span>Ulasan jujur Anda sangat kami hargai.</span>
        </footer>
    </main>

    <div class="google-qr-toolbar">
        <a href="{{ route('home') }}">← Kembali ke JasKita</a>
        <a class="button button-outline" href="{{ asset('qr-ulasan-jaskita.pdf') }}" download>Unduh PDF siap cetak</a>
        <button class="button button-navy" type="button" onclick="window.print()">Cetak / Simpan sebagai PDF</button>
    </div>
</body>
</html>
