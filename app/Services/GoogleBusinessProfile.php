<?php

namespace App\Services;

use App\Models\GoogleBusinessConnection;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GoogleBusinessProfile
{
    public const REVIEW_SCOPE = 'https://www.googleapis.com/auth/business.manage';

    public function configured(): bool
    {
        return filled(config('services.google_business.client_id'))
            && filled(config('services.google_business.client_secret'));
    }

    public function redirectUri(): string
    {
        return (string) (config('services.google_business.redirect_uri') ?: route('google-business.callback'));
    }

    public function authorizationUrl(string $state): string
    {
        return 'https://accounts.google.com/o/oauth2/v2/auth?'.http_build_query([
            'client_id' => config('services.google_business.client_id'),
            'redirect_uri' => $this->redirectUri(),
            'response_type' => 'code',
            'scope' => self::REVIEW_SCOPE,
            'access_type' => 'offline',
            'prompt' => 'consent',
            'include_granted_scopes' => 'true',
            'state' => $state,
        ], '', '&', PHP_QUERY_RFC3986);
    }

    public function exchangeAuthorizationCode(string $code): string
    {
        $response = $this->postToken([
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirectUri(),
        ]);
        $refreshToken = $response->json('refresh_token');

        if (! is_string($refreshToken) || $refreshToken === '') {
            throw new RuntimeException('Google tidak mengirim refresh token. Cabut akses aplikasi di Google Account, lalu hubungkan kembali.');
        }

        return $refreshToken;
    }

    public function accessToken(GoogleBusinessConnection $connection): string
    {
        $response = $this->postToken([
            'grant_type' => 'refresh_token',
            'refresh_token' => $connection->refresh_token,
        ]);
        $accessToken = $response->json('access_token');

        if (! is_string($accessToken) || $accessToken === '') {
            throw new RuntimeException('Google tidak mengembalikan access token yang valid.');
        }

        return $accessToken;
    }

    /**
     * @return array<int, array{name: string, title: string}>
     */
    public function locations(string $accessToken): array
    {
        $locations = [];
        foreach ($this->accounts($accessToken) as $accountName) {
            $pageToken = null;
            $seenPageTokens = [];

            do {
                $query = [
                    'readMask' => 'name,title',
                    'pageSize' => 100,
                ];

                if ($pageToken !== null) {
                    $query['pageToken'] = $pageToken;
                }

                $response = $this->get(
                    "https://mybusinessbusinessinformation.googleapis.com/v1/{$accountName}/locations",
                    $accessToken,
                    $query
                );
                $page = $response->json('locations');

                if (! is_array($page)) {
                    throw new RuntimeException('Google mengembalikan daftar lokasi dengan format yang tidak valid.');
                }

                foreach ($page as $location) {
                    if (
                        is_array($location)
                        && is_string($location['name'] ?? null)
                        && is_string($location['title'] ?? null)
                    ) {
                        $locationId = basename($location['name']);
                        $locations[] = [
                            'name' => "{$accountName}/locations/{$locationId}",
                            'title' => $location['title'],
                        ];
                    }
                }

                $pageToken = $response->json('nextPageToken');

                if (is_string($pageToken) && $pageToken !== '') {
                    if (isset($seenPageTokens[$pageToken]) || count($seenPageTokens) >= 100) {
                        throw new RuntimeException('Pagination lokasi Google tidak berakhir secara normal.');
                    }

                    $seenPageTokens[$pageToken] = true;
                } else {
                    $pageToken = null;
                }
            } while ($pageToken !== null);
        }

        return $locations;
    }

    /**
     * @return array{reviews: array<int, array<string, mixed>>, average_rating: float, total_count: int, next_page_token: ?string}
     */
    public function reviews(
        GoogleBusinessConnection $connection,
        ?string $pageToken = null
    ): array {
        $accessToken = $this->accessToken($connection);

        return $this->fetchReviewsPage($connection, $accessToken, $pageToken);
    }

    /**
     * @return array{reviews: array<int, array<string, mixed>>, average_rating: float, total_count: int, next_page_token: null}
     */
    public function homepageReviews(GoogleBusinessConnection $connection, int $limit = 12): array
    {
        if ($limit < 1 || $limit > 50) {
            throw new RuntimeException('Jumlah ulasan beranda harus antara 1 dan 50.');
        }

        $accessToken = $this->accessToken($connection);
        $reviewsWithMedia = [];
        $reviewsWithoutMedia = [];
        $pageToken = null;
        $seenPageTokens = [];
        $firstPage = null;
        $pageCount = 0;

        do {
            if (++$pageCount > 100) {
                throw new RuntimeException('Pagination ulasan Google tidak berakhir secara normal.');
            }

            $page = $this->fetchReviewsPage($connection, $accessToken, $pageToken);
            $firstPage ??= $page;

            foreach ($page['reviews'] as $review) {
                $mediaItems = array_values(array_filter(
                    is_array($review['reviewMediaItems'] ?? null) ? $review['reviewMediaItems'] : [],
                    fn ($media): bool => is_array($media) && $this->isHttpsUrl($media['thumbnailUrl'] ?? null)
                ));
                $review['reviewMediaItems'] = $mediaItems;

                if ($mediaItems !== []) {
                    $reviewsWithMedia[] = $review;
                } else {
                    $reviewsWithoutMedia[] = $review;
                }
            }

            $nextPageToken = $page['next_page_token'];

            if ($nextPageToken !== null && isset($seenPageTokens[$nextPageToken])) {
                throw new RuntimeException('Pagination ulasan Google tidak berakhir secara normal.');
            }

            if ($nextPageToken !== null) {
                $seenPageTokens[$nextPageToken] = true;
            }

            $pageToken = $nextPageToken;
        } while ($pageToken !== null && count($reviewsWithMedia) < $limit);

        $firstPage ??= [
            'average_rating' => 0,
            'total_count' => 0,
        ];

        return [
            'reviews' => array_slice([...$reviewsWithMedia, ...$reviewsWithoutMedia], 0, $limit),
            'average_rating' => $firstPage['average_rating'],
            'total_count' => $firstPage['total_count'],
            'next_page_token' => null,
        ];
    }

    /**
     * @return array{reviews: array<int, array<string, mixed>>, average_rating: float, total_count: int, next_page_token: ?string}
     */
    private function fetchReviewsPage(
        GoogleBusinessConnection $connection,
        string $accessToken,
        ?string $pageToken = null
    ): array {
        $query = [
            'pageSize' => 50,
            'orderBy' => 'updateTime desc',
        ];

        if ($pageToken !== null) {
            $query['pageToken'] = $pageToken;
        }

        $response = $this->get(
            "https://mybusiness.googleapis.com/v4/{$connection->location_name}/reviews",
            $accessToken,
            $query
        );
        $data = $response->json();
        $reviews = $data['reviews'] ?? null;

        if (! is_array($reviews)) {
            throw new RuntimeException('Google mengembalikan daftar ulasan dengan format yang tidak valid.');
        }

        $nextPageToken = $data['nextPageToken'] ?? null;

        return [
            'reviews' => array_values(array_filter($reviews, 'is_array')),
            'average_rating' => (float) ($data['averageRating'] ?? 0),
            'total_count' => (int) ($data['totalReviewCount'] ?? 0),
            'next_page_token' => is_string($nextPageToken) && $nextPageToken !== '' ? $nextPageToken : null,
        ];
    }

    private function isHttpsUrl(mixed $url): bool
    {
        return is_string($url)
            && filter_var($url, FILTER_VALIDATE_URL) !== false
            && parse_url($url, PHP_URL_SCHEME) === 'https';
    }

    private function postToken(array $data): Response
    {
        $response = Http::asForm()
            ->acceptJson()
            ->timeout(15)
            ->post('https://oauth2.googleapis.com/token', [
                ...$data,
                'client_id' => config('services.google_business.client_id'),
                'client_secret' => config('services.google_business.client_secret'),
            ]);

        return $this->requireSuccess($response, 'Google OAuth');
    }

    private function get(string $url, string $accessToken, array $query = []): Response
    {
        $response = Http::acceptJson()
            ->withToken($accessToken)
            ->timeout(15)
            ->get($url, $query);

        return $this->requireSuccess($response, 'Google Business Profile');
    }

    /**
     * @return array<int, string>
     */
    private function accounts(string $accessToken): array
    {
        $accounts = [];
        $pageToken = null;
        $seenPageTokens = [];

        do {
            $query = ['pageSize' => 20];

            if ($pageToken !== null) {
                $query['pageToken'] = $pageToken;
            }

            $response = $this->get(
                'https://mybusinessaccountmanagement.googleapis.com/v1/accounts',
                $accessToken,
                $query
            );
            $page = $response->json('accounts');

            if (! is_array($page)) {
                throw new RuntimeException('Google mengembalikan daftar akun dengan format yang tidak valid.');
            }

            foreach ($page as $account) {
                if (is_array($account) && is_string($account['name'] ?? null)) {
                    $accounts[] = $account['name'];
                }
            }

            $pageToken = $response->json('nextPageToken');

            if (is_string($pageToken) && $pageToken !== '') {
                if (isset($seenPageTokens[$pageToken]) || count($seenPageTokens) >= 100) {
                    throw new RuntimeException('Pagination akun Google tidak berakhir secara normal.');
                }

                $seenPageTokens[$pageToken] = true;
            } else {
                $pageToken = null;
            }
        } while ($pageToken !== null);

        return $accounts;
    }

    private function requireSuccess(Response $response, string $service): Response
    {
        if (! $response->successful()) {
            Log::warning("{$service} API request failed.", [
                'status' => $response->status(),
                'message' => $response->json('error.message', 'Google API returned an error'),
            ]);

            throw new RuntimeException("Permintaan ke {$service} gagal (HTTP {$response->status()}). Pastikan akses API, izin, dan kredensial sudah benar.");
        }

        return $response;
    }
}
