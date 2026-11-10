<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class InstagramFeedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.instagram.access_token' => null,
            'services.instagram.graph_api_version' => 'v22.0',
        ]);
    }

    public function test_landing_page_displays_instagram_posts_from_the_graph_api(): void
    {
        config(['services.instagram.access_token' => 'test-instagram-token']);

        Http::fake([
            'graph.instagram.com/*' => Http::response([
                'data' => [
                    [
                        'id' => 'post-123',
                        'caption' => 'Momen spesial bersama JasKita',
                        'media_type' => 'IMAGE',
                        'media_url' => 'https://cdn.example.test/post-123.jpg',
                        'permalink' => 'https://www.instagram.com/p/post-123/',
                    ],
                    [
                        'id' => 'video-456',
                        'caption' => 'Video pelanggan',
                        'media_type' => 'VIDEO',
                        'media_url' => 'https://cdn.example.test/video-456.mp4',
                        'thumbnail_url' => 'https://cdn.example.test/video-456.jpg',
                        'permalink' => 'https://www.instagram.com/p/video-456/',
                    ],
                ],
            ]),
        ]);

        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('Momen spesial bersama JasKita')
            ->assertSee('src="https://cdn.example.test/post-123.jpg"', false)
            ->assertSee('src="https://cdn.example.test/video-456.jpg"', false);

        $this->get('/')->assertOk();

        Http::assertSent(fn (Request $request) => $request->hasHeader('Authorization', 'Bearer test-instagram-token')
            && str_contains($request->url(), '/v22.0/me/media')
            && str_contains($request->url(), 'limit=12'));
        Http::assertSentCount(1);
    }

    public function test_feed_is_not_requested_without_an_access_token(): void
    {
        Http::fake();

        $this->get('/')
            ->assertOk()
            ->assertSee('Hubungkan Instagram JasKita');

        Http::assertNothingSent();
    }

    public function test_api_errors_keep_the_landing_page_available(): void
    {
        config(['services.instagram.access_token' => 'test-instagram-token']);

        Http::fake([
            'graph.instagram.com/*' => Http::response([
                'error' => ['message' => 'Invalid access token'],
            ], 401),
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Feed Instagram belum tersedia');
    }
}
