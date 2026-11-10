<?php

namespace Tests\Feature;

use App\Models\GoogleBusinessConnection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GoogleReviewHomepagePreviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'cache.default' => 'array',
            'services.instagram.access_token' => null,
            'services.google_business.client_id' => 'test-client-id',
            'services.google_business.client_secret' => 'test-client-secret',
            'services.google_business.redirect_uri' => 'http://localhost/admin/google-business/callback',
        ]);
    }

    public function test_homepage_does_not_show_google_reviews_even_when_preview_flag_is_set(): void
    {
        Http::fake();

        $this->get('/?pratinjau_ulasan=1')
            ->assertOk()
            ->assertDontSee('Pratinjau tampilan')
            ->assertDontSee('Ulasan pelanggan JasKita')
            ->assertDontSee('Contoh pelanggan 1')
            ->assertDontSee('Ulasan Google');

        Http::assertNothingSent();
    }

    public function test_homepage_does_not_show_sample_google_reviews_by_default(): void
    {
        Http::fake();

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Pratinjau tampilan')
            ->assertDontSee('Contoh pelanggan 1')
            ->assertDontSee('Ulasan pelanggan JasKita');
    }

    public function test_homepage_does_not_request_google_reviews_when_business_profile_is_connected(): void
    {
        $this->createConnection();
        Http::fake();

        $this->get('/')
            ->assertOk()
            ->assertDontSee('FotoReviewer')
            ->assertDontSee('Ulasan 0')
            ->assertDontSee('Ulasan pelanggan JasKita');
        Http::assertNothingSent();
    }

    private function createConnection(): GoogleBusinessConnection
    {
        return GoogleBusinessConnection::create([
            'refresh_token' => 'test-refresh-token',
            'location_name' => 'accounts/123/locations/456',
            'location_title' => 'Sewa Jas Medan Johor',
        ]);
    }
}
