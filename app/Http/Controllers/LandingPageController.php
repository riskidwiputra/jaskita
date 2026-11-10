<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    public function __invoke(): View
    {
        $token = trim((string) config('services.instagram.access_token'));
        $posts = [];
        $status = 'not_configured';

        if ($token !== '') {
            $version = (string) config('services.instagram.graph_api_version', 'v22.0');

            if (! preg_match('/^v\d+\.\d+$/D', $version)) {
                Log::error('INSTAGRAM_GRAPH_API_VERSION must use the format vNN.0.');
                $status = 'unavailable';
            } else {
                $cacheKey = 'instagram.media.'.hash('sha256', $token).'.'.$version;
                $cachedFeed = Cache::get($cacheKey);

                if (is_array($cachedFeed) && isset($cachedFeed['posts'], $cachedFeed['status'])) {
                    $posts = $cachedFeed['posts'];
                    $status = $cachedFeed['status'];
                } else {
                    [$posts, $status] = $this->fetchPosts($token, $version);

                    if (in_array($status, ['connected', 'empty'], true)) {
                        Cache::put($cacheKey, compact('posts', 'status'), now()->addHour());
                    }
                }
            }
        }

        return view('landing', compact('posts', 'status'));
    }

    /**
     * @return array{0: array<int, array{id: string, caption: string, image_url: string, post_url: string}>, 1: string}
     */
    private function fetchPosts(string $token, string $version): array
    {
        try {
            $response = Http::acceptJson()
                ->withToken($token)
                ->timeout(10)
                ->get("https://graph.instagram.com/{$version}/me/media", [
                    'fields' => 'id,caption,media_type,media_url,permalink,thumbnail_url,timestamp',
                    'limit' => 12,
                ]);
        } catch (ConnectionException $exception) {
            Log::warning('Instagram media request could not connect.', [
                'exception' => $exception::class,
            ]);

            return [[], 'unavailable'];
        }

        if (! $response->successful()) {
            $this->logApiFailure($response);

            return [[], 'unavailable'];
        }

        $data = $response->json('data');

        if (! is_array($data)) {
            Log::warning('Instagram media response did not include a data list.');

            return [[], 'unavailable'];
        }

        $posts = collect($data)
            ->filter(fn ($post) => is_array($post))
            ->map(function (array $post): ?array {
                $imageUrl = ($post['media_type'] ?? null) === 'VIDEO'
                    ? ($post['thumbnail_url'] ?? null)
                    : ($post['media_url'] ?? $post['thumbnail_url'] ?? null);
                $postUrl = $post['permalink'] ?? null;

                if (
                    empty($post['id'])
                    || ! $this->isHttpsUrl($imageUrl)
                    || ! $this->isHttpsUrl($postUrl)
                ) {
                    return null;
                }

                return [
                    'id' => (string) $post['id'],
                    'caption' => (string) ($post['caption'] ?? ''),
                    'image_url' => $imageUrl,
                    'post_url' => $postUrl,
                ];
            })
            ->filter()
            ->take(12)
            ->values()
            ->all();

        return [$posts, $posts === [] ? 'empty' : 'connected'];
    }

    private function isHttpsUrl(mixed $url): bool
    {
        return is_string($url)
            && filter_var($url, FILTER_VALIDATE_URL) !== false
            && parse_url($url, PHP_URL_SCHEME) === 'https';
    }

    private function logApiFailure(Response $response): void
    {
        $error = $response->json('error');

        Log::warning('Instagram media request failed.', [
            'status' => $response->status(),
            'message' => is_array($error) ? ($error['message'] ?? 'Meta API returned an error') : 'Meta API returned an error',
            'code' => is_array($error) ? ($error['code'] ?? null) : null,
            'type' => is_array($error) ? ($error['type'] ?? null) : null,
        ]);
    }
}
