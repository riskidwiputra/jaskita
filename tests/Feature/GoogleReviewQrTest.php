<?php

namespace Tests\Feature;

use Tests\TestCase;

class GoogleReviewQrTest extends TestCase
{
    public function test_printable_google_maps_review_qr_page_is_available(): void
    {
        $this->get(route('google-business.qr'))
            ->assertOk()
            ->assertSee('Bantu kami menjadi lebih baik.')
            ->assertSee('images/qr-ulasan-google.png')
            ->assertSee('Pilih <strong>Ulasan</strong>, lalu <strong>Tulis ulasan</strong>.', false)
            ->assertSee('https://maps.app.goo.gl/PZz5ig1BHgK4cW1r5')
            ->assertSee('qr-ulasan-jaskita.pdf')
            ->assertSee('window.print()', false);

        $this->assertFileExists(public_path('images/qr-ulasan-google.png'));
        $this->assertFileExists(public_path('qr-ulasan-jaskita.pdf'));
    }
}
