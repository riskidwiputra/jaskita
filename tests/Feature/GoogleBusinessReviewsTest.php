<?php

namespace Tests\Feature;

use App\Models\GoogleBusinessConnection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GoogleBusinessReviewsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'cache.default' => 'array',
            'services.google_business.client_id' => 'test-client-id',
            'services.google_business.client_secret' => 'test-client-secret',
            'services.google_business.redirect_uri' => 'http://localhost/admin/google-business/callback',
        ]);
        Cache::flush();
    }

    public function test_customer_review_pages_are_not_available(): void
    {
        $this->get('/ulasan')->assertNotFound();
        $this->get('/ulasan-google')->assertNotFound();
        $this->get('/ulasan-google?pageToken=next-cursor')->assertNotFound();
    }

    public function test_owner_can_choose_a_business_location_from_their_google_account(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $this->createConnection();
        Http::fake([
            'oauth2.googleapis.com/token' => Http::response(['access_token' => 'test-access-token']),
            'mybusinessaccountmanagement.googleapis.com/v1/accounts*' => Http::response([
                'accounts' => [['name' => 'accounts/123']],
            ]),
            'mybusinessbusinessinformation.googleapis.com/v1/accounts/123/locations*' => Http::response([
                'locations' => [[
                    'name' => 'locations/456',
                    'title' => 'Sewa Jas Medan Johor',
                ]],
            ]),
        ]);

        $this->actingAs($owner)
            ->post('/admin/google-business/location', [
                'location_name' => 'accounts/123/locations/456',
            ])
            ->assertRedirect(route('google-business.setup'))
            ->assertSessionHas('status');

        $this->assertSame(
            'accounts/123/locations/456',
            GoogleBusinessConnection::firstOrFail()->location_name
        );
    }

    public function test_owner_can_complete_google_oauth_and_refresh_token_is_encrypted(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        Http::fake([
            'oauth2.googleapis.com/token' => Http::response([
                'refresh_token' => 'sensitive-refresh-token',
                'access_token' => 'short-lived-access-token',
            ]),
        ]);

        $this->actingAs($owner)
            ->withSession(['google_business_oauth_state' => 'expected-state'])
            ->get('/admin/google-business/callback?code=authorization-code&state=expected-state')
            ->assertRedirect(route('google-business.setup'))
            ->assertSessionHas('status');

        $connection = GoogleBusinessConnection::firstOrFail();

        $this->assertSame('sensitive-refresh-token', $connection->refresh_token);
        $this->assertNotSame('sensitive-refresh-token', $connection->getRawOriginal('refresh_token'));
    }

    public function test_oauth_callback_rejects_an_invalid_state(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $this->actingAs($owner)
            ->withSession(['google_business_oauth_state' => 'expected-state'])
            ->get('/admin/google-business/callback?code=authorization-code&state=wrong-state')
            ->assertStatus(419);
    }

    private function createConnection(): GoogleBusinessConnection
    {
        return GoogleBusinessConnection::create([
            'refresh_token' => 'sensitive-refresh-token',
            'location_name' => 'accounts/123/locations/456',
            'location_title' => 'Sewa Jas Medan Johor',
        ]);
    }
}
