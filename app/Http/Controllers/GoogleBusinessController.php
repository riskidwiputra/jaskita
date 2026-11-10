<?php

namespace App\Http\Controllers;

use App\Models\GoogleBusinessConnection;
use App\Services\GoogleBusinessProfile;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;

class GoogleBusinessController extends Controller
{
    public function setup(GoogleBusinessProfile $google): View
    {
        $this->ensureOwner();

        $connection = GoogleBusinessConnection::first();
        $locations = [];
        $error = session('google_business_error');

        if ($connection && $google->configured()) {
            try {
                $locations = $google->locations($google->accessToken($connection));
            } catch (RuntimeException|ConnectionException $exception) {
                Log::warning('Google Business Profile locations could not be loaded.', [
                    'exception' => $exception::class,
                ]);
                $error ??= $exception->getMessage();
            }
        }

        return view('google-business.setup', compact('connection', 'locations', 'error', 'google'));
    }

    public function connect(Request $request, GoogleBusinessProfile $google): RedirectResponse
    {
        $this->ensureOwner();

        if (! $google->configured()) {
            return to_route('google-business.setup')
                ->with('google_business_error', 'Isi GOOGLE_BUSINESS_CLIENT_ID dan GOOGLE_BUSINESS_CLIENT_SECRET di .env terlebih dahulu.');
        }

        $state = Str::random(64);
        $request->session()->put('google_business_oauth_state', $state);

        return redirect()->away($google->authorizationUrl($state));
    }

    public function callback(Request $request, GoogleBusinessProfile $google): RedirectResponse
    {
        $this->ensureOwner();

        $expectedState = $request->session()->pull('google_business_oauth_state');
        $receivedState = $request->query('state');

        if (! is_string($expectedState) || ! is_string($receivedState) || ! hash_equals($expectedState, $receivedState)) {
            abort(419, 'Sesi OAuth Google tidak valid atau telah kedaluwarsa. Silakan coba hubungkan kembali.');
        }

        if ($request->query('error')) {
            return to_route('google-business.setup')
                ->with('google_business_error', 'Persetujuan Google dibatalkan atau ditolak.');
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:4096'],
        ]);

        try {
            $connection = GoogleBusinessConnection::query()->first() ?? new GoogleBusinessConnection;
            $connection->refresh_token = $google->exchangeAuthorizationCode($validated['code']);
            $connection->location_name = null;
            $connection->location_title = null;
            $connection->save();
        } catch (RuntimeException|ConnectionException $exception) {
            Log::warning('Google Business Profile OAuth could not be completed.', [
                'exception' => $exception::class,
            ]);

            return to_route('google-business.setup')
                ->with('google_business_error', $exception->getMessage());
        }

        return to_route('google-business.setup')
            ->with('status', 'Google berhasil dihubungkan. Pilih lokasi bisnis untuk menampilkan ulasannya.');
    }

    public function selectLocation(Request $request, GoogleBusinessProfile $google): RedirectResponse
    {
        $this->ensureOwner();

        $validated = $request->validate([
            'location_name' => ['required', 'string', 'max:255', 'regex:/^accounts\/[^\/]+\/locations\/[^\/]+$/'],
        ]);
        $connection = GoogleBusinessConnection::firstOrFail();

        try {
            $locations = $google->locations($google->accessToken($connection));
        } catch (RuntimeException|ConnectionException $exception) {
            Log::warning('Google Business Profile location selection could not be verified.', [
                'exception' => $exception::class,
            ]);

            return to_route('google-business.setup')
                ->with('google_business_error', $exception->getMessage());
        }

        $location = collect($locations)->firstWhere('name', $validated['location_name']);

        if (! $location) {
            return to_route('google-business.setup')
                ->with('google_business_error', 'Lokasi tersebut tidak ditemukan di akun Google yang terhubung.');
        }

        $connection->update([
            'location_name' => $location['name'],
            'location_title' => $location['title'],
        ]);

        return to_route('google-business.setup')
            ->with('status', 'Lokasi '.$location['title'].' berhasil dipilih. Ulasan tersedia di halaman publik.');
    }

    public function disconnect(): RedirectResponse
    {
        $this->ensureOwner();
        GoogleBusinessConnection::query()->delete();

        return to_route('google-business.setup')
            ->with('status', 'Koneksi Google Business Profile telah dihapus.');
    }

    private function ensureOwner(): void
    {
        abort_unless(auth()->check() && auth()->user()->role === 'owner', 403);
    }
}
